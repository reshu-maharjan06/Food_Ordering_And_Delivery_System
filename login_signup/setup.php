<?php
/**
 * Sauni Platform Database Initialization
 * Strictly focused on Authentication and User Roles.
 */
require_once __DIR__ . '/includes/db.php';

echo "<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
    body { font-family:'Outfit',sans-serif; padding:3rem; max-width:900px; margin:0 auto; background:#fafaf9; color:#111; line-height:1.6; }
    h2 { font-size:2.5rem; font-weight:900; letter-spacing:-1.5px; margin-bottom:2rem; color:#ff3b00; }
    .log-card { background:#fff; border-radius:24px; padding:2.5rem; box-shadow:0 10px 40px rgba(0,0,0,.04); border:1px solid #eee; margin-bottom:2rem; }
    .ok { color:#10b981; font-weight:700; margin-bottom:0.8rem; display:flex; align-items:center; gap:10px; font-size:1.1rem; }
    .err { color:#ef4444; font-weight:700; margin-bottom:0.8rem; }
    .auth-info { background:#111; color:#fff; padding:1.5rem; border-radius:18px; margin-top:1.5rem; font-size:1rem; border-left:4px solid #ff3b00; }
    .auth-info b { color:#ff3b00; }
    .btn-wrap { margin-top:2.5rem; }
    a.launch { display:inline-block; background:#ff3b00; color:white; padding:1.2rem 3rem; border-radius:50px; text-decoration:none; font-weight:800; box-shadow:0 15px 30px rgba(255,59,0,0.25); transition:0.3s; font-size:1.1rem; letter-spacing:0.5px; }
    a.launch:hover { transform:translateY(-3px); box-shadow:0 20px 40px rgba(255,59,0,0.35); background:#e63500; }
</style>";

echo "<h2>Sauni Platform — Initialization</h2>";
echo "<div class='log-card'>";

try {
    // ── Create Database if not exists ──────────────────────────────────
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `sauniauth_db` COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `sauniauth_db`");
    echo "<div class='ok'>✓ Database `sauniauth_db` is ready</div>";

    // ── Drop existing tables for a clean slate ─────────────────────────
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("DROP TABLE IF EXISTS `order_item`, `order_`, `user` ");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    echo "<div class='ok'>✓ Clean slate achieved</div>";

    // ── User Table ─────────────────────────────────────────────────────
    $pdo->exec("CREATE TABLE user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('customer','admin','delivery') NOT NULL DEFAULT 'customer',
        phone VARCHAR(20) NULL,
        address VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<div class='ok'>✓ `user` table initialized</div>";

    // ── Simple Order Tables (Core structure) ───────────────────────────
    $pdo->exec("CREATE TABLE `order_` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        delivery_person_id INT NULL,
        status ENUM('pending', 'confirmed', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
        total_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
        placed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES user(id) ON DELETE CASCADE,
        FOREIGN KEY (delivery_person_id) REFERENCES user(id) ON DELETE SET NULL
    )");
    echo "<div class='ok'>✓ `order_` table initialized</div>";

    // ── Seed users (admin, customer, delivery) ──────────────────────────
    $hash = password_hash('pass123', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare("INSERT INTO user (name, email, username, password_hash, role) VALUES (?, ?, ?, ?, ?)");
    
    $stmt->execute(['Sauni Admin',     'admin@sauni.com',    'admin1',     $hash, 'admin']);
    $stmt->execute(['John Customer',  'customer@test.com',  'customer1',  $hash, 'customer']);
    $stmt->execute(['Rider One',      'driver@sauni.com',   'driver1',    $hash, 'delivery']);

    echo "<div class='auth-info'><b>Initialization Successful!</b><br>
          Admin: <b>admin1</b><br>
          Customer: <b>customer1</b><br>
          Driver: <b>driver1</b><br>
          Password for all: <b>pass123</b></div>";

} catch (PDOException $e) {
    echo "<div class='err'>❌ SEVERE ERROR: " . $e->getMessage() . "</div>";
}

echo "</div>"; 
echo "<div class='btn-wrap'><a class='launch' href='index.php'>ENTER SAUNI AUTH</a></div>";