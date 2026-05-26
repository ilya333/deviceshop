<?php
// api.php - Обработчик всех запросов к базе данных
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// НАСТРОЙКИ СЕССИИ
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_lifetime', 0);
ini_set('session.gc_maxlifetime', 3600);

// Подключение к БД
class Database {
    private static $db = null;
    
    public static function getConnection() {
        if (self::$db === null) {
            if (!file_exists('data')) {
                mkdir('data', 0777, true);
            }
            self::$db = new SQLite3('data/database.sqlite');
            self::$db->enableExceptions(true);
        }
        return self::$db;
    }
}

$db = Database::getConnection();

// ЗАПУСКАЕМ СЕССИЮ
session_start();

$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        
        // ========== РЕГИСТРАЦИЯ ==========
        case 'register':
            $data = json_decode(file_get_contents('php://input'), true);
            $name = $data['name'] ?? '';
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            
            if (empty($name) || empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
                break;
            }
            
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $db->prepare("INSERT INTO users (name, email, password) VALUES (:name, :email, :password)");
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $stmt->bindValue(':password', $hashedPassword, SQLITE3_TEXT);
            
            try {
                $stmt->execute();
                echo json_encode(['success' => true, 'message' => 'Регистрация успешна! Теперь войдите']);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Email уже зарегистрирован']);
            }
            break;
        
        // ========== ВХОД ==========
        case 'login':
            $data = json_decode(file_get_contents('php://input'), true);
            $email = $data['email'] ?? '';
            $password = $data['password'] ?? '';
            
            if (empty($email) || empty($password)) {
                echo json_encode(['success' => false, 'message' => 'Заполните все поля']);
                break;
            }
            
            $stmt = $db->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->bindValue(':email', $email, SQLITE3_TEXT);
            $result = $stmt->execute();
            $user = $result->fetchArray(SQLITE3_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                
                // Принудительно сохраняем сессию
                session_write_close();
                session_start();
                
                echo json_encode([
                    'success' => true, 
                    'user' => [
                        'id' => $user['id'], 
                        'name' => $user['name'],
                        'email' => $user['email']
                    ]
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Неверный email или пароль']);
            }
            break;
        
        // ========== ВЫХОД ==========
        case 'logout':
            $_SESSION = array();
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }
            session_destroy();
            echo json_encode(['success' => true]);
            break;
        
        // ========== ПРОВЕРКА АВТОРИЗАЦИИ ==========
        case 'check_auth':
            if (isset($_SESSION['user_id']) && $_SESSION['user_id'] > 0) {
                echo json_encode([
                    'authenticated' => true,
                    'user' => [
                        'id' => $_SESSION['user_id'],
                        'name' => $_SESSION['user_name'] ?? 'Пользователь'
                    ]
                ]);
            } else {
                echo json_encode(['authenticated' => false]);
            }
            break;
        
        // ========== ПОЛУЧИТЬ ДАННЫЕ ПОЛЬЗОВАТЕЛЯ ==========
        case 'get_user':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['error' => 'Не авторизован']);
                break;
            }
            $stmt = $db->prepare("SELECT id, name, email, phone, address, created_at FROM users WHERE id = :id");
            $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
            $result = $stmt->execute();
            $user = $result->fetchArray(SQLITE3_ASSOC);
            echo json_encode($user);
            break;
        
        // ========== ОБНОВИТЬ ПРОФИЛЬ ==========
        case 'update_profile':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Не авторизован']);
                break;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $name = $data['name'] ?? '';
            $phone = $data['phone'] ?? '';
            $address = $data['address'] ?? '';
            
            $stmt = $db->prepare("UPDATE users SET name = :name, phone = :phone, address = :address WHERE id = :id");
            $stmt->bindValue(':name', $name, SQLITE3_TEXT);
            $stmt->bindValue(':phone', $phone, SQLITE3_TEXT);
            $stmt->bindValue(':address', $address, SQLITE3_TEXT);
            $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
            $stmt->execute();
            
            $_SESSION['user_name'] = $name;
            echo json_encode(['success' => true]);
            break;
        
        // ========== СМЕНИТЬ ПАРОЛЬ ==========
        case 'change_password':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Не авторизован']);
                break;
            }
            $data = json_decode(file_get_contents('php://input'), true);
            $old_password = $data['old_password'] ?? '';
            $new_password = $data['new_password'] ?? '';
            
            $stmt = $db->prepare("SELECT password FROM users WHERE id = :id");
            $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
            $result = $stmt->execute();
            $user = $result->fetchArray(SQLITE3_ASSOC);
            
            if (!$user || !password_verify($old_password, $user['password'])) {
                echo json_encode(['success' => false, 'message' => 'Неверный текущий пароль']);
                break;
            }
            
            if (strlen($new_password) < 6) {
                echo json_encode(['success' => false, 'message' => 'Пароль должен быть минимум 6 символов']);
                break;
            }
            
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
            $stmt->bindValue(':password', $hashed, SQLITE3_TEXT);
            $stmt->bindValue(':id', $_SESSION['user_id'], SQLITE3_INTEGER);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'message' => 'Пароль успешно изменён']);
            break;
        
        // ========== ПОЛУЧИТЬ ВСЕ ТОВАРЫ ==========
        case 'get_products':
            $category = $_GET['category'] ?? '';
            $search = $_GET['search'] ?? '';
            
            $sql = "SELECT * FROM products WHERE 1=1";
            if ($category && $category !== 'all') {
                $sql .= " AND category = '$category'";
            }
            if ($search) {
                $sql .= " AND (name LIKE '%$search%' OR brand LIKE '%$search%')";
            }
            
            $result = $db->query($sql);
            $products = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                if ($row['colors']) {
                    $row['colors'] = explode(',', $row['colors']);
                } else {
                    $row['colors'] = [];
                }
                if ($row['specs']) {
                    $row['specs'] = json_decode($row['specs'], true);
                } else {
                    $row['specs'] = [];
                }
                $products[] = $row;
            }
            echo json_encode($products);
            break;
        
        // ========== ПОЛУЧИТЬ ОДИН ТОВАР ==========
        case 'get_product':
            $id = $_GET['id'] ?? 0;
            $stmt = $db->prepare("SELECT * FROM products WHERE id = :id");
            $stmt->bindValue(':id', $id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            $product = $result->fetchArray(SQLITE3_ASSOC);
            
            if ($product) {
                if ($product['colors']) {
                    $product['colors'] = explode(',', $product['colors']);
                }
                if ($product['specs']) {
                    $product['specs'] = json_decode($product['specs'], true);
                }
            }
            echo json_encode($product);
            break;
        
        // ========== ДОБАВИТЬ В КОРЗИНУ ==========
        case 'add_to_cart':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $user_id = $_SESSION['user_id'];
            $product_id = $data['product_id'] ?? 0;
            $quantity = $data['quantity'] ?? 1;
            $color = $data['color'] ?? '';
            
            $stmt = $db->prepare("SELECT * FROM cart WHERE user_id = :user_id AND product_id = :product_id AND selected_color = :color");
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $stmt->bindValue(':product_id', $product_id, SQLITE3_INTEGER);
            $stmt->bindValue(':color', $color, SQLITE3_TEXT);
            $result = $stmt->execute();
            $existing = $result->fetchArray(SQLITE3_ASSOC);
            
            if ($existing) {
                $stmt = $db->prepare("UPDATE cart SET quantity = quantity + :quantity WHERE id = :id");
                $stmt->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
                $stmt->bindValue(':id', $existing['id'], SQLITE3_INTEGER);
                $stmt->execute();
            } else {
                $stmt = $db->prepare("INSERT INTO cart (user_id, product_id, quantity, selected_color) VALUES (:user_id, :product_id, :quantity, :color)");
                $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
                $stmt->bindValue(':product_id', $product_id, SQLITE3_INTEGER);
                $stmt->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
                $stmt->bindValue(':color', $color, SQLITE3_TEXT);
                $stmt->execute();
            }
            
            echo json_encode(['success' => true]);
            break;
        
        // ========== ПОЛУЧИТЬ КОРЗИНУ ==========
        case 'get_cart':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode([]);
                break;
            }
            
            $stmt = $db->prepare("
                SELECT c.*, p.name, p.price, p.image_url 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :user_id
            ");
            $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
            $result = $stmt->execute();
            
            $cart = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $cart[] = $row;
            }
            echo json_encode($cart);
            break;
        
        // ========== ОБНОВИТЬ КОЛИЧЕСТВО В КОРЗИНЕ ==========
        case 'update_cart':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false]);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $cart_id = $data['cart_id'] ?? 0;
            $quantity = $data['quantity'] ?? 0;
            
            if ($quantity <= 0) {
                $stmt = $db->prepare("DELETE FROM cart WHERE id = :id AND user_id = :user_id");
                $stmt->bindValue(':id', $cart_id, SQLITE3_INTEGER);
                $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
                $stmt->execute();
            } else {
                $stmt = $db->prepare("UPDATE cart SET quantity = :quantity WHERE id = :id AND user_id = :user_id");
                $stmt->bindValue(':quantity', $quantity, SQLITE3_INTEGER);
                $stmt->bindValue(':id', $cart_id, SQLITE3_INTEGER);
                $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
                $stmt->execute();
            }
            
            echo json_encode(['success' => true]);
            break;
        
        // ========== УДАЛИТЬ ИЗ КОРЗИНЫ ==========
        case 'remove_from_cart':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false]);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $cart_id = $data['cart_id'] ?? 0;
            
            $stmt = $db->prepare("DELETE FROM cart WHERE id = :id AND user_id = :user_id");
            $stmt->bindValue(':id', $cart_id, SQLITE3_INTEGER);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
            $stmt->execute();
            
            echo json_encode(['success' => true]);
            break;
        
        // ========== СОЗДАТЬ ЗАКАЗ ==========
        case 'create_order':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'Требуется авторизация']);
                break;
            }
            
            $data = json_decode(file_get_contents('php://input'), true);
            $user_id = $_SESSION['user_id'];
            
            $stmt = $db->prepare("
                SELECT c.*, p.name, p.price 
                FROM cart c 
                JOIN products p ON c.product_id = p.id 
                WHERE c.user_id = :user_id
            ");
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $result = $stmt->execute();
            
            $total = 0;
            $items = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $subtotal = $row['price'] * $row['quantity'];
                $total += $subtotal;
                $items[] = $row;
            }
            
            if (empty($items)) {
                echo json_encode(['success' => false, 'message' => 'Корзина пуста']);
                break;
            }
            
            $delivery_cost = $total > 0 ? 490 : 0;
            $grand_total = $total + $delivery_cost;
            
            $stmt = $db->prepare("
                INSERT INTO orders (user_id, total_price, delivery_cost, delivery_method, payment_method, delivery_address, status) 
                VALUES (:user_id, :total, :delivery_cost, :delivery_method, :payment_method, :address, 'new')
            ");
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $stmt->bindValue(':total', $grand_total, SQLITE3_INTEGER);
            $stmt->bindValue(':delivery_cost', $delivery_cost, SQLITE3_INTEGER);
            $stmt->bindValue(':delivery_method', $data['delivery_method'] ?? 'Курьер', SQLITE3_TEXT);
            $stmt->bindValue(':payment_method', $data['payment_method'] ?? 'При получении', SQLITE3_TEXT);
            $stmt->bindValue(':address', $data['address'] ?? '', SQLITE3_TEXT);
            $stmt->execute();
            
            $order_id = $db->lastInsertRowID();
            
            foreach ($items as $item) {
                $stmt = $db->prepare("
                    INSERT INTO order_items (order_id, product_id, product_name, price_at_time, quantity, selected_color) 
                    VALUES (:order_id, :product_id, :name, :price, :quantity, :color)
                ");
                $stmt->bindValue(':order_id', $order_id, SQLITE3_INTEGER);
                $stmt->bindValue(':product_id', $item['product_id'], SQLITE3_INTEGER);
                $stmt->bindValue(':name', $item['name'], SQLITE3_TEXT);
                $stmt->bindValue(':price', $item['price'], SQLITE3_INTEGER);
                $stmt->bindValue(':quantity', $item['quantity'], SQLITE3_INTEGER);
                $stmt->bindValue(':color', $item['selected_color'] ?? '', SQLITE3_TEXT);
                $stmt->execute();
            }
            
            $stmt = $db->prepare("DELETE FROM cart WHERE user_id = :user_id");
            $stmt->bindValue(':user_id', $user_id, SQLITE3_INTEGER);
            $stmt->execute();
            
            echo json_encode(['success' => true, 'order_id' => $order_id]);
            break;
        
        // ========== ПОЛУЧИТЬ ЗАКАЗЫ С ТОВАРАМИ ==========
        case 'get_orders':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode([]);
                break;
            }
            
            $stmt = $db->prepare("SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC");
            $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
            $result = $stmt->execute();
            
            $orders = [];
            while ($row = $result->fetchArray(SQLITE3_ASSOC)) {
                $items_stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
                $items_stmt->bindValue(':order_id', $row['id'], SQLITE3_INTEGER);
                $items_result = $items_stmt->execute();
                
                $items = [];
                while ($item = $items_result->fetchArray(SQLITE3_ASSOC)) {
                    $items[] = $item;
                }
                $row['items'] = $items;
                $orders[] = $row;
            }
            echo json_encode($orders);
            break;
        
        // ========== ПОЛУЧИТЬ ОДИН ЗАКАЗ ==========
        case 'get_order':
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['error' => 'Не авторизован']);
                break;
            }
            $order_id = $_GET['id'] ?? 0;
            
            $stmt = $db->prepare("SELECT * FROM orders WHERE id = :id AND user_id = :user_id");
            $stmt->bindValue(':id', $order_id, SQLITE3_INTEGER);
            $stmt->bindValue(':user_id', $_SESSION['user_id'], SQLITE3_INTEGER);
            $result = $stmt->execute();
            $order = $result->fetchArray(SQLITE3_ASSOC);
            
            if ($order) {
                $items_stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
                $items_stmt->bindValue(':order_id', $order_id, SQLITE3_INTEGER);
                $items_result = $items_stmt->execute();
                
                $items = [];
                while ($item = $items_result->fetchArray(SQLITE3_ASSOC)) {
                    $items[] = $item;
                }
                $order['items'] = $items;
            }
            echo json_encode($order);
            break;
        
        // ========== НЕИЗВЕСТНОЕ ДЕЙСТВИЕ ==========
        default:
            echo json_encode(['error' => 'Неизвестное действие']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>