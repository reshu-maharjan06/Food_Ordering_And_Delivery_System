<?php
require_once __DIR__ . '/includes/db.php';

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("DROP TABLE IF EXISTS menu_items, categories, cuisine_type, menu_item, category, order_item, `order_`, review");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    echo "<h3>Initializing Menu Database Layout</h3>";

    $pdo->exec("CREATE TABLE categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL
    )");
    echo "<div style='color:green;'>✓ `categories` table created successfully.</div>";

    $pdo->exec("CREATE TABLE menu_items (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(120) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_url VARCHAR(400),
        is_available TINYINT(1) DEFAULT 1,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
    )");
    echo "<div style='color:green;'>✓ `menu_items` table created with foreign key.</div>";

    $cats = ['Appetizers', 'Main Course', 'Desserts', 'Beverages'];
    $catMap = [];
    foreach ($cats as $c) {
        $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$c]);
        $catMap[$c] = $pdo->lastInsertId();
    }

    $sampleMenu = [
        [$catMap['Appetizers'], 'Spring Rolls', 'Crispy vegetarian rolls served with sweet chili sauce', 250, ''],
        [$catMap['Appetizers'], 'Chicken Momo', 'Steamed dumplings filled with minced spiced chicken', 200, ''],
        [$catMap['Main Course'], 'Steak Frites', 'Beef steak with fresh french fries', 1100, ''],
        [$catMap['Main Course'], 'Grilled Salmon', 'Fresh salmon with lemon butter sauce', 850, ''],
        [$catMap['Desserts'], 'Strawberry Cheesecake', 'Classic NY style cheesecake', 350, ''],
        [$catMap['Beverages'], 'Mint Lemonade', 'Refreshing cooler with fresh mint leaves', 150, '']
    ];

    $ins = $pdo->prepare("INSERT INTO menu_items (category_id, name, description, price, image_url) VALUES (?,?,?,?,?)");
    foreach ($sampleMenu as $item) {
        $ins->execute($item);
    }
    echo "<div style='color:green;'>✓ Inserted sample menu items.</div>";

} catch (PDOException $e) {
    echo "<div style='color:red;'>❌ Setup failed: " . $e->getMessage() . "</div>";
}
?>
