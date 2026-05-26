<?php
// init_db.php - запустить 1 раз для создания таблиц

// Создаем папку data если её нет
if (!file_exists('data')) {
    mkdir('data', 0777, true);
    echo "📁 Папка 'data' создана<br>";
}

// Подключаемся к БД
$db = new SQLite3('data/database.sqlite');
echo "✅ Подключение к БД установлено<br>";

// SQL запросы для создания таблиц
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    email TEXT UNIQUE NOT NULL,
    password TEXT NOT NULL,
    phone TEXT,
    address TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

$sql_products = "CREATE TABLE IF NOT EXISTS products (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    category TEXT NOT NULL,
    brand TEXT NOT NULL,
    price INTEGER NOT NULL,
    rating REAL DEFAULT 0,
    popular INTEGER DEFAULT 0,
    image_url TEXT,
    description TEXT,
    full_description TEXT,
    specs TEXT,
    colors TEXT,
    is_new INTEGER DEFAULT 0
)";

$sql_cart = "CREATE TABLE IF NOT EXISTS cart (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    quantity INTEGER DEFAULT 1,
    selected_color TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (product_id) REFERENCES products(id),
    UNIQUE(user_id, product_id, selected_color)
)";

$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    total_price INTEGER NOT NULL,
    delivery_cost INTEGER DEFAULT 0,
    status TEXT DEFAULT 'new',
    delivery_method TEXT,
    payment_method TEXT,
    delivery_address TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

$sql_order_items = "CREATE TABLE IF NOT EXISTS order_items (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    order_id INTEGER NOT NULL,
    product_id INTEGER NOT NULL,
    product_name TEXT,
    price_at_time INTEGER,
    quantity INTEGER,
    selected_color TEXT,
    FOREIGN KEY (order_id) REFERENCES orders(id)
)";

// Выполняем запросы
$db->exec($sql_users);
echo "✅ Таблица users создана<br>";

$db->exec($sql_products);
echo "✅ Таблица products создана<br>";

$db->exec($sql_cart);
echo "✅ Таблица cart создана<br>";

$db->exec($sql_orders);
echo "✅ Таблица orders создана<br>";

$db->exec($sql_order_items);
echo "✅ Таблица order_items создана<br>";

// Добавляем тестовые товары
$check = $db->querySingle("SELECT COUNT(*) FROM products");
if ($check == 0) {
    $db->exec("INSERT INTO products (name, category, brand, price, rating, popular, image_url, description, is_new) VALUES
        ('Keychron Q1', 'Клавиатуры', 'Keychron', 15990, 4.8, 95, 'img/keychron_q1.jpg', 'Алюминиевый корпус, Hot-Swap, RGB', 1),
        ('Logitech G Pro X Superlight', 'Мыши', 'Logitech', 12990, 4.9, 98, 'img/gpro_superlight.jpg', '63g, беспроводная, для киберспорта', 1),
        ('Razer DeathAdder V3', 'Мыши', 'Razer', 8990, 4.7, 90, 'img/razer_deathadder.jpg', 'Эргономичная мышь для профессионалов', 0),
        ('HyperX Cloud II', 'Наушники', 'HyperX', 7990, 4.6, 85, 'img/hyperx_cloud2.jpg', 'Игровая гарнитура с виртуальным 7.1', 0)
    ");
    echo "📦 Добавлены тестовые товары<br>";
}

echo "<hr><strong>✅ База данных готова к работе!</strong>";
echo "<br><br><a href='index.html'>Перейти на сайт →</a>";
?>