<?php
$host   = 'localhost';
$dbname = 'admindash_db';
$user   = 'root';
$pass   = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `user` (
        `id`            INT AUTO_INCREMENT PRIMARY KEY,
        `username`      VARCHAR(100) NOT NULL UNIQUE,
        `email`         VARCHAR(150) DEFAULT NULL,
        `password_hash` VARCHAR(255) NOT NULL,
        `role`          ENUM('admin','customer','delivery') NOT NULL DEFAULT 'customer',
        `lat`           DECIMAL(10,7) DEFAULT NULL,
        `lng`           DECIMAL(10,7) DEFAULT NULL,
        `profile_pic`   VARCHAR(255) DEFAULT NULL,
        `created_at`    DATETIME DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `cuisine_type` (
        `id`   INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(100) NOT NULL UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `category` (
        `id`              INT AUTO_INCREMENT PRIMARY KEY,
        `name`            VARCHAR(100) NOT NULL UNIQUE,
        `cuisine_type_id` INT DEFAULT NULL,
        FOREIGN KEY (`cuisine_type_id`) REFERENCES `cuisine_type`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `menu_item` (
        `id`          INT AUTO_INCREMENT PRIMARY KEY,
        `name`        VARCHAR(150) NOT NULL,
        `category_id` INT NOT NULL,
        `price`       DECIMAL(10,2) NOT NULL,
        `description` TEXT DEFAULT NULL,
        `image_url`   VARCHAR(255) DEFAULT NULL,
        `is_popular`  TINYINT(1) DEFAULT 0,
        FOREIGN KEY (`category_id`) REFERENCES `category`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `order_` (
        `id`                 INT AUTO_INCREMENT PRIMARY KEY,
        `customer_id`        INT NOT NULL,
        `delivery_person_id` INT DEFAULT NULL,
        `total_amount`       DECIMAL(10,2) NOT NULL DEFAULT 0,
        `status`             ENUM('pending','confirmed','prepared','assigned','picked_up','delivered','cancelled') NOT NULL DEFAULT 'pending',
        `dest_lat`           DECIMAL(10,7) DEFAULT NULL,
        `dest_lng`           DECIMAL(10,7) DEFAULT NULL,
        `delivery_address`   VARCHAR(255) DEFAULT NULL,
        `note`               TEXT DEFAULT NULL,
        `placed_at`          DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`customer_id`)        REFERENCES `user`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`delivery_person_id`) REFERENCES `user`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `order_item` (
        `id`           INT AUTO_INCREMENT PRIMARY KEY,
        `order_id`     INT NOT NULL,
        `menu_item_id` INT NOT NULL,
        `quantity`     INT NOT NULL DEFAULT 1,
        `unit_price`   DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (`order_id`)     REFERENCES `order_`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`menu_item_id`) REFERENCES `menu_item`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `review` (
        `id`          INT AUTO_INCREMENT PRIMARY KEY,
        `order_id`    INT NOT NULL,
        `customer_id` INT NOT NULL,
        `stars`       TINYINT NOT NULL DEFAULT 5,
        `comment`     TEXT DEFAULT NULL,
        `created_at`  DATETIME DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (`order_id`)    REFERENCES `order_`(`id`) ON DELETE CASCADE,
        FOREIGN KEY (`customer_id`) REFERENCES `user`(`id`)   ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    /* ── seed users ── */
    $pdo->exec("INSERT IGNORE INTO `user` (username, email, password_hash, role) VALUES
        ('admin1', 'admin@sauni.com', '".password_hash('pass123', PASSWORD_BCRYPT)."', 'admin')");

    $pdo->exec("INSERT IGNORE INTO `user` (username, email, password_hash, role, lat, lng) VALUES
        ('rider1', 'rider1@sauni.com', '".password_hash('rider123', PASSWORD_BCRYPT)."', 'delivery', 27.71534, 85.31440)");

    $pdo->exec("INSERT IGNORE INTO `user` (username, email, password_hash, role) VALUES
        ('customer1', 'customer@sauni.com', '".password_hash('cust123', PASSWORD_BCRYPT)."', 'customer')");

    /* ── seed menu ── */
    $pdo->exec("INSERT IGNORE INTO `cuisine_type` (name) VALUES ('Newari'),('Tharu'),('Fast Food')");
    $pdo->exec("INSERT IGNORE INTO `category` (name, cuisine_type_id) VALUES
        ('Newari',    (SELECT id FROM cuisine_type WHERE name='Newari')),
        ('Tharu',     (SELECT id FROM cuisine_type WHERE name='Tharu')),
        ('Fast Food', (SELECT id FROM cuisine_type WHERE name='Fast Food'))");
    $pdo->exec("INSERT IGNORE INTO `menu_item` (name, category_id, price, is_popular) VALUES
        ('Samay Baji',  (SELECT id FROM category WHERE name='Newari'),    350, 1),
        ('Dhido Set',   (SELECT id FROM category WHERE name='Tharu'),     280, 1),
        ('Chatamari',   (SELECT id FROM category WHERE name='Newari'),    200, 1),
        ('Momo (8 pcs)',(SELECT id FROM category WHERE name='Fast Food'), 180, 1)");

    /* ── seed orders (last 7 days) ── */
    $pdo->exec("INSERT INTO `order_` (customer_id, delivery_person_id, total_amount, status, placed_at)
        SELECT
            (SELECT id FROM user WHERE role='customer' LIMIT 1),
            (SELECT id FROM user WHERE role='delivery' LIMIT 1),
            ROUND(200 + (RAND()*800)),
            'delivered',
            DATE_SUB(NOW(), INTERVAL FLOOR(RAND()*7) DAY)
        FROM information_schema.columns LIMIT 14");

    $pdo->exec("INSERT INTO `order_` (customer_id, total_amount, status, placed_at) VALUES
        ((SELECT id FROM user WHERE role='customer' LIMIT 1), 420, 'pending',   NOW()),
        ((SELECT id FROM user WHERE role='customer' LIMIT 1), 760, 'prepared',  NOW()),
        ((SELECT id FROM user WHERE role='customer' LIMIT 1), 280, 'picked_up', NOW())");

    echo "<pre style='font-family:sans-serif;padding:2rem'>
✅  admindash_db created successfully!

Tables : user, cuisine_type, category, menu_item, order_, order_item, review

Admin    : username=admin1     / password=pass123
Rider    : username=rider1    / password=rider123
Customer : username=customer1 / password=cust123

⚠️  Delete this file after setup.
</pre>";

} catch (PDOException $e) {
    die("<pre style='color:red'>DB Error: " . $e->getMessage() . "</pre>");
}
?>
