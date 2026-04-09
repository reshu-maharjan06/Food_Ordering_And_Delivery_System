<?php
require_once __DIR__ . '/includes/db.php';

echo "<style>
    @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@400;700;900&display=swap');
    body { font-family:'Outfit',sans-serif; padding:3rem; max-width:900px; margin:0 auto; background:#fafaf9; color:#111; line-height:1.6; }
    h2 { font-size:2.5rem; font-weight:900; letter-spacing:-1.5px; margin-bottom:2rem; color:#ff3b00; }
    .log-card { background:#fff; border-radius:24px; padding:2.5rem; box-shadow:0 10px 40px rgba(0,0,0,.04); border:1px solid #eee; margin-bottom:2rem; }
    .ok { color:#10b981; font-weight:700; margin-bottom:0.8rem; display:flex; align-items:center; gap:10px; font-size:1.1rem; }
    .err { color:#ef4444; font-weight:700; margin-bottom:0.8rem; }
    .sync { color:#8b5cf6; font-weight:700; margin-bottom:0.8rem; display:flex; align-items:center; gap:10px; font-size:1.1rem; }
    .auth-info { background:#111; color:#fff; padding:1.5rem; border-radius:18px; margin-top:1.5rem; font-size:1rem; border-left:4px solid #ff3b00; }
    .auth-info b { color:#ff3b00; }
    .btn-wrap { margin-top:2.5rem; }
    a.launch { display:inline-block; background:#ff3b00; color:white; padding:1.2rem 3rem; border-radius:50px; text-decoration:none; font-weight:800; box-shadow:0 15px 30px rgba(255,59,0,0.25); transition:0.3s; font-size:1.1rem; letter-spacing:0.5px; }
    a.launch:hover { transform:translateY(-3px); box-shadow:0 20px 40px rgba(255,59,0,0.35); background:#e63500; }
</style>";

echo "<h2>Sauni Engine — Initializing Platform 3.0 Schema</h2>";
echo "<div class='log-card'>";

try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("DROP TABLE IF EXISTS review, order_item, `order_`, menu_item, category, cuisine_type, restaurant, user, ratings, orders, menu_items, users");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");

    // 1. user Table (WITH FIXES: profile_pic and password_hash)
    $pdo->exec("CREATE TABLE user (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NULL,
        email VARCHAR(100) NULL UNIQUE,
        username VARCHAR(50) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        role ENUM('customer','admin','delivery') NOT NULL DEFAULT 'customer',
        phone VARCHAR(20) NULL,
        address VARCHAR(255) NULL,
        profile_pic VARCHAR(255) NULL,
        lat DECIMAL(10,8) NULL,
        lng DECIMAL(11,8) NULL,
        password_reset_token VARCHAR(100) NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
    echo "<div class='ok'>✓ `user` Table Created (v3.0 Secure)</div>";

    // 2. restaurant
    $pdo->exec("CREATE TABLE restaurant (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(150),
        description TEXT
    )");
    $pdo->exec("INSERT INTO restaurant (name, description) VALUES ('Sauni Cloud Kitchen', 'The ultimate ethnic food hub')");

    // 3. cuisine_type
    $pdo->exec("CREATE TABLE cuisine_type (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        description TEXT
    )");

    // 4. category
    $pdo->exec("CREATE TABLE category (
        id INT AUTO_INCREMENT PRIMARY KEY,
        cuisine_type_id INT,
        name VARCHAR(100),
        FOREIGN KEY (cuisine_type_id) REFERENCES cuisine_type(id) ON DELETE CASCADE
    )");

    // 5. menu_item
    $pdo->exec("CREATE TABLE menu_item (
        id INT AUTO_INCREMENT PRIMARY KEY,
        category_id INT NOT NULL,
        name VARCHAR(120) NOT NULL,
        description TEXT,
        price DECIMAL(10,2) NOT NULL,
        image_url VARCHAR(400),
        is_available TINYINT(1) DEFAULT 1,
        is_popular TINYINT(1) DEFAULT 0,
        FOREIGN KEY (category_id) REFERENCES category(id) ON DELETE CASCADE
    )");
    echo "<div class='ok'>✓ `menu_item`, `category`, `cuisine_type` Vault Created</div>";

    // 8. order_ 
    $pdo->exec("CREATE TABLE `order_` (
        id INT AUTO_INCREMENT PRIMARY KEY,
        customer_id INT NOT NULL,
        delivery_person_id INT NULL,
        status ENUM('pending', 'confirmed', 'prepared', 'assigned', 'picked_up', 'delivered', 'cancelled') NOT NULL DEFAULT 'pending',
        delivery_address VARCHAR(500) NULL,
        dest_lat DECIMAL(10,8) NULL,
        dest_lng DECIMAL(11,8) NULL,
        payment_method VARCHAR(50) DEFAULT 'COD',
        total_amount DECIMAL(10,2) NOT NULL,
        note TEXT,
        placed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status_updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (customer_id) REFERENCES user(id) ON DELETE CASCADE,
        FOREIGN KEY (delivery_person_id) REFERENCES user(id) ON DELETE SET NULL
    )");

    // 9. order_item
    $pdo->exec("CREATE TABLE order_item (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        menu_item_id INT NULL,
        quantity INT NOT NULL DEFAULT 1,
        unit_price DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (order_id) REFERENCES `order_`(id) ON DELETE CASCADE,
        FOREIGN KEY (menu_item_id) REFERENCES menu_item(id) ON DELETE SET NULL
    )");

    // 10. review
    $pdo->exec("CREATE TABLE review (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_id INT NOT NULL,
        customer_id INT NOT NULL,
        stars INT NOT NULL DEFAULT 5,
        comment TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (order_id) REFERENCES `order_`(id) ON DELETE CASCADE,
        FOREIGN KEY (customer_id) REFERENCES user(id) ON DELETE CASCADE
    )");
    echo "<div class='ok'>✓ `order_` and `review` Relations Synchronized</div>";

    // Demo Accounts (Using pass123 hashed)
    $hash = password_hash('pass123', PASSWORD_DEFAULT);
    $pdo->prepare("INSERT INTO user (username,email,password_hash,role,name) VALUES (?,?,?,?,?)")->execute(['admin1', 'admin@sauni.com', $hash, 'admin', 'Sauni Admin']);
    $pdo->prepare("INSERT INTO user (username,email,password_hash,role,name) VALUES (?,?,?,?,?)")->execute(['customer1', 'customer@test.com', $hash, 'customer', 'John Customer']);
    $pdo->prepare("INSERT INTO user (username,password_hash,role,name,lat,lng) VALUES (?,?,?,?,?,?)")->execute(['driver1', $hash, 'delivery', 'Rider One', '27.71534', '85.31440']);
    $pdo->prepare("INSERT INTO user (username,password_hash,role,name,lat,lng) VALUES (?,?,?,?,?,?)")->execute(['driver2', $hash, 'delivery', 'Rider Two', '27.71891', '85.31895']);

    echo "<div class='auth-info'><b>Credentials Ready:</b> admin1, customer1, driver1 | Password: <b>pass123</b></div>";

    // ----------------- SEEDING ETHNIC CATEGORIES AND MENU ITEMS -----------------
    $cuisines = [
        'Newari' => 'Bold spices, fermentation techniques, and ceremonial feast culture.',
        'Tharu' => 'Simplicity, freshwater creatures, local rice, unique preservation.',
        'Tamang' => 'Relies heavily on millet, maize, buckwheat, pork, and fermented foods.',
        'Gurung' => 'Hearty mountain food including millet, chicken pickle, and fiddlehead fern.',
        'Rai/Kirat' => 'Known for fermented soybean dishes, millet staples, and pork preparations.',
        'Limbu' => 'Known for Tongba, fermented foods, pork dishes, and Yangben (wild lichen).',
        'Sherpa' => 'High calorie, warming, and energy-dense foods with Tibetan influence.',
        'Thakali' => 'One of the most refined cuisines, the gold standard of Nepali dal bhat.',
        'Magar' => 'Known for pork and wild food culture, taro preparations, and hearty stews.',
        'Beverages' => 'Traditional and refreshing thirst quenchers suited for every meal.'
    ];

    $catIdMap = [];
    foreach ($cuisines as $cname => $cdesc) {
        $pdo->prepare("INSERT INTO cuisine_type (name, description) VALUES (?, ?)")->execute([$cname, $cdesc]);
        $cid = $pdo->lastInsertId();
        
        $catName = ($cname === 'Beverages') ? 'Drinks' : 'Main Dishes';
        $pdo->prepare("INSERT INTO category (cuisine_type_id, name) VALUES (?, ?)")->execute([$cid, $catName]);
        $catIdMap[$cname] = $pdo->lastInsertId();
    }

    $menu = [
        // Newari (5)
        [$catIdMap['Newari'], 'Samay Baji Set', 'The ultimate Newari feast: beaten rice, smoked meat, lentils.', 450, 'assets/img/foods/newari/SamayBaji.jpg', 1, 1],
        [$catIdMap['Newari'], 'Haku Choila', 'Spicy marinated open-flame roasted buffalo.', 380, 'assets/img/foods/newari/choila.webp', 1, 1],
        [$catIdMap['Newari'], 'Chatamari', 'Rice flour crepe topped with minced meat and egg.', 250, 'assets/img/foods/newari/chatamari.jpg', 1, 1],
        [$catIdMap['Newari'], 'Yomari', 'Steamed pointed dumplings with molasses and sesame filling.', 150, 'assets/img/foods/newari/yomari.jpg', 1, 0],
        [$catIdMap['Newari'], 'Bara (Wo)', 'Fluffy black lentil pancakes topped with spiced minced meat.', 320, 'assets/img/foods/newari/bara.jpg', 1, 0],

        // Tharu (5)
        [$catIdMap['Tharu'], 'Ghonghi Curry', 'Freshwater snail curry, a Tharu delicacy.', 350, 'assets/img/foods/tharu/ghonghi_curry.jpg', 1, 1],
        [$catIdMap['Tharu'], 'Dhikri', 'Steamed rice dumplings, plain and comforting.', 180, 'assets/img/foods/tharu/dhikri.jpg', 1, 0],
        [$catIdMap['Tharu'], 'Freshwater River Fish Curry', 'Local river fish cooked with mustard seed paste.', 420, 'assets/img/foods/tharu/fish_curry.jpg', 1, 1],
        [$catIdMap['Tharu'], 'Chichar', 'Traditional puffed rice snack.', 120, 'assets/img/foods/tharu/Chichar.png', 1, 0],
        [$catIdMap['Tharu'], 'Sidhara', 'Fermented taro and dried fish cake.', 260, 'assets/img/foods/tharu/Sidhara.jpg', 1, 0],

        // Tamang (5)
        [$catIdMap['Tamang'], 'Tamang Khapse', 'Deep fried Tibetan-style dough snacks.', 150, 'assets/img/foods/tamang/Khapse.jpg', 1, 0],
        [$catIdMap['Tamang'], 'Buckwheat Roti with Pork', 'Pan-roasted buckwheat flatbread with rich pork curry.', 450, 'assets/img/foods/tamang/buckwheat_roti.jpg', 1, 1],
        [$catIdMap['Tamang'], 'Mountain Dhindo', 'Thick millet porridge served with stinging nettle soup.', 340, 'assets/img/foods/tamang/dhindo.jpg', 1, 1],
        [$catIdMap['Tamang'], 'Yangben Faaksa', 'Pork cooked with wild lichen and blood.', 520, 'assets/img/foods/tamang/yangben_faaksa.jpg', 1, 0],
        [$catIdMap['Tamang'], 'Kinema Curry', 'Fermented soybean curry with a distinctive pungent aroma.', 240, 'assets/img/foods/tamang/kinema_curry.jpg', 1, 0],

        // Gurung (5)
        [$catIdMap['Gurung'], 'Gurung Thali Set', 'Rice, dal, seasonal veg, and chicken pickle.', 480, 'assets/img/foods/gurung/thali.jpg', 1, 1],
        [$catIdMap['Gurung'], 'Kukura ko Achar', 'Spicy and tangy preserved chicken pickle.', 280, 'assets/img/foods/gurung/kukura_achar.jpg', 1, 1],
        [$catIdMap['Gurung'], 'Fiddlehead Fern Sauté', 'Wild fiddlehead ferns sautéed with dried anchovies.', 220, 'assets/img/foods/gurung/fiddlehead_fern.jpg', 1, 0],
        [$catIdMap['Gurung'], 'Kodo ko Roti', 'Nutritious finger millet flatbread.', 140, 'assets/img/foods/gurung/kodo_roti.jpg', 1, 0],
        [$catIdMap['Gurung'], 'Mohi Chop', 'Seasoned skimmed milk dish, tangy and fresh.', 160, 'assets/img/foods/gurung/mohi_chop.jpg', 1, 0],

        // Rai/Kirat (5)
        [$catIdMap['Rai/Kirat'], 'Wachipa / Wamrik', 'Unique roasted chicken dish seasoned with burnt rooster feather ash.', 420, 'assets/img/foods/raikirat/wachipa.jpg', 1, 1],
        [$catIdMap['Rai/Kirat'], 'Pig Leg Achaar', 'Spiced pig leg pickle, gelatinous and flavorful.', 380, 'assets/img/foods/raikirat/pig_leg.jpg', 1, 0],
        [$catIdMap['Rai/Kirat'], 'Rayo ko Saag with Pork', 'Mustard greens cooked down with fatty pork portions.', 360, 'assets/img/foods/raikirat/rayo_pork.jpg', 1, 1],
        [$catIdMap['Rai/Kirat'], 'Yakthung Thupka', 'Hearty meat and vegetable noodle soup.', 320, 'assets/img/foods/raikirat/thupka.jpg', 1, 1],
        [$catIdMap['Rai/Kirat'], 'Churpi Soup', 'Soup made from hard dried yak cheese.', 240, 'assets/img/foods/raikirat/churpi_soup.jpg', 1, 0],

        // Limbu (5)
        [$catIdMap['Limbu'], 'Pork Ribs with Bamboo Shoot', 'Succulent pork ribs braised with sour bamboo shoots.', 540, 'assets/img/foods/limbu/pork_bamboo.jpg', 1, 1],
        [$catIdMap['Limbu'], 'Yangben Soup', 'Earthy soup made from wild high-altitude lichen.', 280, 'assets/img/foods/limbu/yangben_soup.jpg', 1, 0],
        [$catIdMap['Limbu'], 'Akabare Khorsani Achar', 'Extremely hot cherry pepper pickle to side with any meal.', 120, 'assets/img/foods/limbu/akabare_achar.jpg', 1, 1],
        [$catIdMap['Limbu'], 'Phokso ko Jhol', 'Traditional lung soup, highly spiced.', 320, 'assets/img/foods/limbu/phokso.jpg', 1, 0],
        [$catIdMap['Limbu'], 'Sekuwa Buffalo', 'Grilled skewers of marinated buffalo meat.', 380, 'assets/img/foods/limbu/sekuwa_buffalo.jpg', 1, 1],

        // Sherpa (5)
        [$catIdMap['Sherpa'], 'Sherpa Stew (Shyakpa)', 'Thick hearty potato and yak meat stew for cold climates.', 450, 'assets/img/foods/sherpa/stew.jpg', 1, 1],
        [$catIdMap['Sherpa'], 'Tsampa Porridge', 'Roasted barley flour porridge paired with yak butter tea.', 250, 'assets/img/foods/sherpa/tsampa.jpg', 1, 0],
        [$catIdMap['Sherpa'], 'Yak Sukuti', 'Dried, spiced yak meat jerky, chewy and intensely flavored.', 550, 'assets/img/foods/sherpa/yak_sukuti.jpg', 1, 1],
        [$catIdMap['Sherpa'], 'Sherpa Thukpa', 'Noodle soup packed with high-altitude seasonal greens and meat.', 320, 'assets/img/foods/sherpa/thukpa.jpg', 1, 1],
        [$catIdMap['Sherpa'], 'Serkam Achar', 'Tangy pickle made from fermented buttermilk dregs.', 180, 'assets/img/foods/sherpa/serkam.jpg', 1, 0],

        // Thakali (5)
        [$catIdMap['Thakali'], 'Thakali Thali Set', 'The gold standard Dal Bhat: rice, black lentil dal, veg, pickle, and meat.', 650, 'assets/img/foods/gurung/thali.jpg', 1, 1],
        [$catIdMap['Thakali'], 'Kanchemba', 'Crispy fried buckwheat fingers served with timur dip.', 250, 'assets/img/foods/thakali/kanchemb.jpg', 1, 0],
        [$catIdMap['Thakali'], 'Dhopra Soup', 'Warm soup crafted from ground buckwheat greens.', 220, 'assets/img/foods/thakali/dhopra.jpg', 1, 0],
        [$catIdMap['Thakali'], 'Gyang-to', 'Spinach soup beautifully infused with searing Szechuan pepper (timur).', 210, 'assets/img/foods/thakali/gyangto.jpg', 1, 0],
        [$catIdMap['Thakali'], 'Phopke', 'Mildly sweet fermented rice dessert.', 180, 'assets/img/foods/thakali/phopke.jpg', 1, 0],

        // Magar (5)
        [$catIdMap['Magar'], 'Batuk Bara', 'Traditional Magar black lentil donuts, crispy with a soft heart.', 220, 'assets/img/foods/magar/batuk.jpg', 1, 1],
        [$catIdMap['Magar'], 'Sutkeri Kukhura ko Jhol', 'Incredibly nutritious, slow-cooked local chicken broth.', 380, 'assets/img/foods/magar/sutkeri.jpg', 1, 1],
        [$catIdMap['Magar'], 'Fried Tarul (Yam)', 'Deep-fried wild yam tossed in robust mountain spices.', 240, 'assets/img/foods/magar/tarul.jpg', 1, 0],
        [$catIdMap['Magar'], 'Karkalo ko Achar', 'Creamy and tangy pickled taro leaves.', 160, 'assets/img/foods/magar/karkalo.jpg', 1, 0],
        [$catIdMap['Magar'], 'Frog Curry (Paha)', 'A unique seasonal delicacy from the mid-western rivers.', 500, 'assets/img/foods/magar/frog.jpg', 1, 0],

        // Beverages (5)
        [$catIdMap['Beverages'], 'Kathmandu Chilled Beer', 'Local refreshing craft beer, served ice cold.', 350, 'assets/img/foods/beverages/beer.jpg', 1, 1],
        [$catIdMap['Beverages'], 'Mustang Coffee', 'Local coffee fortified with mountain butter and local rum.', 280, 'assets/img/foods/beverages/coffee.jpg', 1, 1],
        [$catIdMap['Beverages'], 'Tongba', 'Traditional warm fermented millet beer served in a bamboo container.', 400, 'assets/img/foods/beverages/tongba.jpg', 1, 1],
        [$catIdMap['Beverages'], 'Chilled Mohi', 'Refreshing iced and lightly spiced buttermilk.', 150, 'assets/img/foods/beverages/Mohi.jpg', 1, 0],
        [$catIdMap['Beverages'], 'Himalayan Apple Juice', '100% natural cold-pressed apple juice from the Mustang region.', 250, 'assets/img/foods/beverages/apple_juice.jpg', 1, 1],
    ];

    $ins = $pdo->prepare("INSERT INTO menu_item (category_id, name, description, price, image_url, is_available, is_popular) VALUES (?,?,?,?,?,?,?)");
    foreach ($menu as $m) {
        $ins->execute($m);
    }
    echo "<div class='ok'>✓ Catalog Populated with 50 Premium Items</div>";

    // ----------------- LOCAL IMAGE SYNCHRONIZATION -----------------
    echo "<div class='sync'>⚡ Initiating Local Image Synchronization Engine...</div>";
    
    function normalize($name) {
        return strtolower(preg_replace('/[^a-z0-9]/i', '', $name));
    }
    function get_words($name) {
        return explode(' ', strtolower(preg_replace('/[^a-z ]/i', '', $name)));
    }

    $food_dir = __DIR__ . '/assets/img/foods';
    $image_map = [];
    if (is_dir($food_dir)) {
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($food_dir));
        foreach ($it as $file) {
            if ($file->isDir()) continue;
            $path = $file->getPathname();
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) continue;
            
            // Generate relative path accurately
            $path_fixed = str_replace('\\', '/', $path);
            $dir_fixed = str_replace('\\', '/', __DIR__);
            $rel = str_ireplace($dir_fixed . '/', '', $path_fixed);
            
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $norm_fn = normalize($filename);
            
            // Detect category from path
            $parts = explode('/', $rel);
            if (count($parts) >= 3) {
                 // assets/img/foods/category/file -> index 3 is category
                 // After str_ireplace, rel is like assets/img/foods/newari/SamayBaji.jpg
                 // parts: [assets, img, foods, newari, SamayBaji.jpg]
                 if (isset($parts[3])) {
                     $cat_folder = normalize($parts[3]);
                     $image_map[$cat_folder][$norm_fn] = $rel;
                 }
            }
            if (!isset($image_map['global'][$norm_fn])) {
                $image_map['global'][$norm_fn] = $rel;
            }
        }
    }

    $stmt = $pdo->query("SELECT m.id, m.name, ct.name as cuisine_name FROM menu_item m JOIN category c ON m.category_id = c.id JOIN cuisine_type ct ON c.cuisine_type_id = ct.id");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $sync_count = 0;
    foreach ($items as $item) {
        $norm_name = normalize($item['name']);
        $norm_cuisine = normalize($item['cuisine_name']);
        $words = array_filter(get_words($item['name']), function($w) { return strlen($w) > 2; }); // Filter out small words like 'ko', 'and'
        $found = null;
        
        // 1. Direct match in cuisine folder
        if (isset($image_map[$norm_cuisine][$norm_name])) {
            $found = $image_map[$norm_cuisine][$norm_name];
        }

        // 2. Fuzzy match in cuisine folder
        if (!$found && isset($image_map[$norm_cuisine])) {
            foreach ($image_map[$norm_cuisine] as $fn => $p) {
                // Check if filename is in food name or vice-versa
                if (strpos($norm_name, $fn) !== false || strpos($fn, $norm_name) !== false) {
                    $found = $p;
                    break;
                }
                // Word match: If any significant word of the food is in the filename
                foreach ($words as $w) {
                    if (strpos($fn, $w) !== false) {
                        $found = $p;
                        break;
                    }
                }
                if ($found) break;
            }
        }

        // 3. Global Exact match
        if (!$found && isset($image_map['global'][$norm_name])) {
            $found = $image_map['global'][$norm_name];
        }

        // 4. Global Fuzzy match
        if (!$found) {
            foreach ($image_map['global'] as $fn => $p) {
                if (strpos($norm_name, $fn) !== false || strpos($fn, $norm_name) !== false) {
                    $found = $p;
                    break;
                }
                foreach ($words as $w) {
                    if (strpos($fn, $w) !== false) {
                        $found = $p;
                        break;
                    }
                }
                if ($found) break;
            }
        }
        
        if ($found) {
            $pdo->prepare("UPDATE menu_item SET image_url = ? WHERE id = ?")->execute([$found, $item['id']]);
            $sync_count++;
        } else {
            echo "<div class='err'>⚠ Match Missing: [{$item['cuisine_name']}] {$item['name']}</div>";
        }
    }
    echo "<div class='ok'>✓ Local Sync: $sync_count/50 images matched and updated locally.</div>";

    // ----------------- DUMMY DATA SEEDING FOR ADMIN BOARD -----------------
    echo "<div class='sync'>📦 Seeding Dummy Orders & Reviews for Admin Analytics...</div>";
    $statuses = ['pending', 'prepared', 'assigned', 'picked_up', 'delivered'];
    $drivers = [3, 4]; // Rider One, Rider Two (from seeding code above)
    $menuItems = $pdo->query("SELECT id, price FROM menu_item LIMIT 20")->fetchAll(PDO::FETCH_ASSOC);
    
    for ($i = 1; $i <= 25; $i++) {
        $status = ($i <= 5) ? 'pending' : (($i <= 10) ? 'prepared' : $statuses[array_rand($statuses)]);
        $customer_id = 2; // John Customer
        $driver_id = (in_array($status, ['assigned', 'picked_up', 'delivered'])) ? $drivers[array_rand($drivers)] : null;
        $address = "Street " . rand(1, 100) . ", Kathmandu";
        $placed_at = date('Y-m-d H:i:s', strtotime("-" . rand(0, 5) . " days -" . rand(0, 23) . " hours"));
        
        $pdo->prepare("INSERT INTO `order_` (customer_id, delivery_person_id, status, delivery_address, total_amount, note, placed_at) VALUES (?,?,?,?,?,?,?)")
            ->execute([$customer_id, $driver_id, $status, $address, 0, "Dummy order #$i for testing", $placed_at]);
        $order_id = $pdo->lastInsertId();
        
        $numItems = rand(1, 4);
        $total = 0;
        for ($j = 0; $j < $numItems; $j++) {
            $item = $menuItems[array_rand($menuItems)];
            $qty = rand(1, 2);
            $pdo->prepare("INSERT INTO order_item (order_id, menu_item_id, quantity, unit_price) VALUES (?,?,?,?)")
                ->execute([$order_id, $item['id'], $qty, $item['price']]);
            $total += ($item['price'] * $qty);
        }
        $pdo->prepare("UPDATE `order_` SET total_amount = ? WHERE id = ?")->execute([$total, $order_id]);
        
        if ($status === 'delivered') {
            $pdo->prepare("INSERT INTO review (order_id, customer_id, stars, comment, created_at) VALUES (?,?,?,?,?)")
                ->execute([$order_id, $customer_id, rand(3, 5), "Automated test review for order #$order_id", $placed_at]);
        }
    }
    echo "<div class='ok'>✓ 25 Dummy Orders and Reviews Synced</div>";

    echo "</div>"; // end log-card
    echo "<div class='btn-wrap'><a class='launch' href='index.php'>ENTER SAUNI PLATFORM</a></div>";

} catch (PDOException $e) {
    echo "</div><div class='err'>❌ SEVERE ERROR: " . $e->getMessage() . "</div>";
}
?>
