// products.js
// DeviceShop — данные о товарах, корзина, вспомогательные функции

// ==================== ЦВЕТА ====================
const COLOR_META = {
  "black": {
    "label": "Черный",
    "hex": "#111111"
  },
  "white": {
    "label": "Белый",
    "hex": "#f5f5f5"
  },
  "silver": {
    "label": "Серебристый",
    "hex": "#bfc3c9"
  },
  "gray": {
    "label": "Серый",
    "hex": "#6b7280"
  },
  "purple": {
    "label": "Фиолетовый",
    "hex": "#7c3aed"
  },
  "green": {
    "label": "Зеленый",
    "hex": "#22c55e"
  },
  "pink": {
    "label": "Розовый",
    "hex": "#ec4899"
  },
  "red": {
    "label": "Красный",
    "hex": "#ef4444"
  },
  "blue": {
    "label": "Синий",
    "hex": "#3b82f6"
  },
  "orange": {
    "label": "Оранжевый",
    "hex": "#f97316"
  }
};

// ==================== ТОВАРЫ ====================
const products = [
  {
    "id": 1,
    "name": "Keychron K2",
    "category": "Клавиатуры",
    "brand": "Keychron",
    "price": 8990,
    "rating": 4.8,
    "popular": 98,
    "isNew": true,
    "colors": ["black", "white"],
    "description": "Беспроводная механическая клавиатура 75%",
    "fullDescription": "Keychron K2 — популярная механическая клавиатура формата 75%, подходящая для работы и игр. Поддерживает Bluetooth и USB-C.",
    "specs": {
      "form": "75%",
      "type": "Mechanical",
      "lighting": "RGB",
      "connection": "Bluetooth / USB-C",
      "material": "ABS + Aluminum",
      "battery": "До 72 часов"
    },
    "images": {
      "black": "img/keychron_k2.jpg",
      "white": "img/keychron_k2.jpg"
    }
  },
  {
    "id": 2,
    "name": "Keychron Q1",
    "category": "Клавиатуры",
    "brand": "Keychron",
    "price": 15990,
    "rating": 4.9,
    "popular": 95,
    "isNew": false,
    "colors": ["black", "silver", "purple"],
    "description": "Премиальная кастомная клавиатура",
    "fullDescription": "Keychron Q1 — премиальная кастомная клавиатура с цельным алюминиевым корпусом.",
    "specs": {
      "form": "75%",
      "type": "Hot-Swap",
      "lighting": "RGB",
      "connection": "USB-C",
      "material": "CNC Aluminum",
      "battery": "Проводная"
    },
    "images": {
      "black": "img/keychron_q1.jpg",
      "silver": "img/keychron_q1.jpg",
      "purple": "img/keychron_q1.jpg"
    }
  },
  {
    "id": 3,
    "name": "Logitech G Pro X",
    "category": "Клавиатуры",
    "brand": "Logitech",
    "price": 12990,
    "rating": 4.7,
    "popular": 91,
    "isNew": false,
    "colors": ["black"],
    "description": "Компактная механическая клавиатура",
    "fullDescription": "Logitech G Pro X создана для киберспортсменов: компактный корпус, быстрые переключатели.",
    "specs": {
      "form": "TKL",
      "type": "Mechanical",
      "lighting": "LIGHTSYNC RGB",
      "connection": "USB",
      "material": "Plastic + metal",
      "battery": "Проводная"
    },
    "images": {
      "black": "img/logitech_g_pro_x.jpg"
    }
  },
  {
    "id": 4,
    "name": "Razer BlackWidow V4",
    "category": "Клавиатуры",
    "brand": "Razer",
    "price": 14990,
    "rating": 4.8,
    "popular": 93,
    "isNew": true,
    "colors": ["black", "green"],
    "description": "Игровая механическая клавиатура",
    "fullDescription": "Razer BlackWidow V4 с фирменной подсветкой Razer Chroma RGB.",
    "specs": {
      "form": "Full Size",
      "type": "Mechanical",
      "lighting": "Chroma RGB",
      "connection": "USB",
      "material": "Plastic / Metal",
      "battery": "Проводная"
    },
    "images": {
      "black": "img/razer_blackwidow_v4.jpg",
      "green": "img/razer_blackwidow_v4.jpg"
    }
  },
  {
    "id": 5,
    "name": "SteelSeries Apex Pro",
    "category": "Клавиатуры",
    "brand": "SteelSeries",
    "price": 18990,
    "rating": 4.9,
    "popular": 96,
    "isNew": false,
    "colors": ["black"],
    "description": "Флагманская клавиатура OmniPoint",
    "fullDescription": "SteelSeries Apex Pro с регулируемой глубиной нажатия.",
    "specs": {
      "form": "Full Size",
      "type": "OmniPoint",
      "lighting": "RGB",
      "connection": "USB",
      "material": "Aircraft-grade aluminum",
      "battery": "Проводная"
    },
    "images": {
      "black": "img/steelseries_apex_pro.jpg"
    }
  },
  {
    "id": 7,
    "name": "Logitech G Pro X Superlight",
    "category": "Мыши",
    "brand": "Logitech",
    "price": 11990,
    "rating": 4.9,
    "popular": 99,
    "isNew": false,
    "colors": ["black", "white", "pink"],
    "description": "Лёгкая беспроводная мышь",
    "fullDescription": "Logitech G Pro X Superlight — сверхлёгкая мышь для киберспорта.",
    "specs": {
      "form": "Ambidextrous",
      "type": "Optical",
      "lighting": "Нет",
      "connection": "Wireless",
      "material": "Ultra-light shell",
      "battery": "До 70 часов"
    },
    "images": {
      "black": "img/logitech_g_pro_superlight.jpg",
      "white": "img/logitech_g_pro_superlight.jpg",
      "pink": "img/logitech_g_pro_superlight.jpg"
    }
  },
  {
    "id": 8,
    "name": "Razer DeathAdder V3 Pro",
    "category": "Мыши",
    "brand": "Razer",
    "price": 10990,
    "rating": 4.8,
    "popular": 95,
    "isNew": true,
    "colors": ["black", "white"],
    "description": "Эргономичная беспроводная мышь",
    "fullDescription": "Razer DeathAdder V3 Pro с высокой точностью.",
    "specs": {
      "form": "Ergonomic",
      "type": "Optical",
      "lighting": "Нет",
      "connection": "Wireless",
      "material": "Matte plastic",
      "battery": "До 90 часов"
    },
    "images": {
      "black": "img/razer_deathadder_v3_pro.jpg",
      "white": "img/razer_deathadder_v3_pro.jpg"
    }
  },
  {
    "id": 13,
    "name": "SteelSeries Arctis Nova Pro",
    "category": "Наушники",
    "brand": "SteelSeries",
    "price": 24990,
    "rating": 4.9,
    "popular": 97,
    "isNew": true,
    "colors": ["black", "white"],
    "description": "Премиальная гарнитура с ANC",
    "fullDescription": "SteelSeries Arctis Nova Pro с активным шумоподавлением.",
    "specs": {
      "form": "Over-ear",
      "type": "Гарнитура",
      "lighting": "Нет",
      "connection": "Wireless",
      "material": "Metal + cushions",
      "battery": "Сменная"
    },
    "images": {
      "black": "img/steelseries_arctis_nova_pro.jpg",
      "white": "img/steelseries_arctis_nova_pro.jpg"
    }
  },
  {
    "id": 14,
    "name": "Logitech G Pro X 2",
    "category": "Наушники",
    "brand": "Logitech",
    "price": 17990,
    "rating": 4.8,
    "popular": 93,
    "isNew": false,
    "colors": ["black", "white", "pink"],
    "description": "Беспроводная гарнитура",
    "fullDescription": "Logitech G Pro X 2 с чистым звуком.",
    "specs": {
      "form": "Over-ear",
      "type": "Гарнитура",
      "lighting": "Нет",
      "connection": "Wireless",
      "material": "Plastic + metal",
      "battery": "До 50 часов"
    },
    "images": {
      "black": "img/logitech_g_pro_x2.jpg",
      "white": "img/logitech_g_pro_x2.jpg",
      "pink": "img/logitech_g_pro_x2.jpg"
    }
  },
  {
    "id": 19,
    "name": "Xbox Wireless Controller",
    "category": "Геймпады",
    "brand": "Microsoft",
    "price": 6490,
    "rating": 4.9,
    "popular": 98,
    "isNew": false,
    "colors": ["black", "white", "blue"],
    "description": "Универсальный беспроводной геймпад",
    "fullDescription": "Xbox Wireless Controller для Xbox и ПК.",
    "specs": {
      "form": "Gamepad",
      "type": "Контроллер",
      "lighting": "Нет",
      "connection": "Wireless",
      "material": "Textured plastic",
      "battery": "AA / аккумулятор"
    },
    "images": {
      "black": "img/xbox_controller.jpg",
      "white": "img/xbox_controller.jpg",
      "blue": "img/xbox_controller.jpg"
    }
  },
  {
    "id": 20,
    "name": "DualSense Wireless",
    "category": "Геймпады",
    "brand": "Sony",
    "price": 6990,
    "rating": 4.9,
    "popular": 97,
    "isNew": false,
    "colors": ["white", "black", "red", "blue"],
    "description": "Контроллер PlayStation",
    "fullDescription": "DualSense с адаптивными триггерами.",
    "specs": {
      "form": "Gamepad",
      "type": "Контроллер",
      "lighting": "LED",
      "connection": "Wireless",
      "material": "Textured plastic",
      "battery": "Встроенный"
    },
    "images": {
      "white": "img/dualsense.jpg",
      "black": "img/dualsense.jpg",
      "red": "img/dualsense.jpg",
      "blue": "img/dualsense.jpg"
    }
  }
];

// ==================== ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ====================
function getColorLabel(colorKey) { return COLOR_META[colorKey]?.label || colorKey; }
function getColorHex(colorKey) { return COLOR_META[colorKey]?.hex || "#999999"; }
function getDefaultColor(product) { return product?.colors?.[0] || "black"; }
function getProductImage(product, color = null) {
  if (!product) return "";
  const selected = color || getDefaultColor(product);
  return product.images?.[selected] || Object.values(product.images || {})[0] || "";
}
function getProductById(id) { return products.find(p => p.id === Number(id)); }
function getCartItemImage(item) {
  const product = getProductById(item?.id);
  return (product && getProductImage(product, item.color)) || item?.image || "";
}
function formatDeviceShopPrice(price) { return Number(price || 0).toLocaleString('ru-RU') + ' ₽'; }

// ==================== КОРЗИНА (localStorage) ====================
function getCart() {
  try {
    const raw = localStorage.getItem("deviceshop-cart");
    const parsed = raw ? JSON.parse(raw) : [];
    return Array.isArray(parsed) ? parsed : [];
  } catch (error) {
    localStorage.removeItem("deviceshop-cart");
    return [];
  }
}
function saveCart(cart) { localStorage.setItem("deviceshop-cart", JSON.stringify(Array.isArray(cart) ? cart : [])); }
function getCartCount() { return getCart().reduce((sum, item) => sum + Number(item.quantity || 0), 0); }
function addToCartById(productId, selectedColor = null) {
  const product = getProductById(productId);
  if (!product) return false;
  const color = selectedColor || getDefaultColor(product);
  const image = getProductImage(product, color);
  const itemKey = `${product.id}-${color}`;
  const cart = getCart();
  const existing = cart.find(item => item.itemKey === itemKey);
  if (existing) existing.quantity = Number(existing.quantity || 0) + 1;
  else cart.push({ itemKey, id: product.id, name: product.name, price: product.price, quantity: 1, color, image, category: product.category });
  saveCart(cart);
  return true;
}
function updateCartItemQuantity(itemKey, delta) {
  const cart = getCart();
  const item = cart.find(entry => entry.itemKey === itemKey);
  if (item) {
    item.quantity = Number(item.quantity || 0) + Number(delta || 0);
    saveCart(cart.filter(entry => Number(entry.quantity || 0) > 0));
  }
}
function removeCartItem(itemKey) { saveCart(getCart().filter(entry => entry.itemKey !== itemKey)); }