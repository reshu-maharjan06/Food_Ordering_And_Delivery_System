<?php
require_once __DIR__ . '/includes/profile_db.php';
echo "<html><head><title>Sauni Setup</title><style>
    body { font-family: 'Outfit', sans-serif; padding: 40px; background: #fafaf9; color: #111; line-height: 1.6; }
    .card { background: #fff; padding: 30px; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); max-width: 600px; margin: auto; }
    h1 { color: #ff3b00; margin-top: 0; }
    .success { color: #10b981; font-weight: bold; margin: 10px 0; }
    .info { background: #f3f4f6; padding: 15px; border-radius: 12px; margin-top: 20px; border-left: 4px solid #ff3b00; }
    .btn { display: inline-block; background: #ff3b00; color: #fff; padding: 12px 24px; border-radius: 50px; text-decoration: none; font-weight: bold; margin-top: 25px; transition: 0.3s; }
    .btn:hover { background: #e63500; transform: translateY(-2px); }
</style></head><body>";
echo "<div class='card'>";
echo "<h1>Sauni Setup</h1>";
try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NULL,
        email VARCHAR(100) NULL UNIQUE,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('customer','admin','delivery') NOT NULL DEFAULT 'customer',
        phone VARCHAR(20) NULL,
        address VARCHAR(255) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<p class='success'>✓ Database and 'user' table are synchronized.</p>";
    $testUser = 'customer1';
    $testEmail = 'customer@test.com';
    $testName = 'John Customer';
    $testPass = 'pass123';
    $hash = password_hash($testPass, PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("SELECT id FROM user WHERE username = ?");
    $stmt->execute([$testUser]);
    if (!$stmt->fetch()) {
        $insert = $pdo->prepare("INSERT INTO user (name, email, username, password_hash, role) VALUES (?, ?, ?, ?, 'customer')");
        $insert->execute([$testName, $testEmail, $testUser, $hash]);
        echo "<p class='success'>✓ Default test user created successfully.</p>";
    } else {
        echo "<p>! Test user already exists. No changes made to users.</p>";
    }
    echo "<div class='info'>";
    echo "<strong>Test Credentials:</strong><br>";
    echo "Username: <code>$testUser</code><br>";
    echo "Password: <code>$testPass</code>";
    echo "</div>";
    echo "<a href='login.php' class='btn'>Launch Login →</a>";
} catch (PDOException $e) {
    echo "<p style='color: #ef4444;'>❌ Error: " . $e->getMessage() . "</p>";
}
echo "</div></body></html>";
?>
