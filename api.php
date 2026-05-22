<?php
session_start();
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
}

define('RESTAURANT_LAT', 27.71534);
define('RESTAURANT_LNG', 85.31440);
define('RESTAURANT_NAME', 'Sauni Kitchen, Thamel');
$action = $_GET['action'] ?? '';
$uid = $_SESSION['user_id'] ?? 0;
$role = $_SESSION['role'] ?? '';

function require_admin(): void {
    global $role;
    if ($role !== 'admin') {
        http_response_code(403);
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
}

function rate_limit(string $key, int $max = 10, int $window_seconds = 60): void {
    $now = time();
    if (empty($_SESSION['rl'][$key])) {
        $_SESSION['rl'][$key] = ['count' => 0, 'start' => $now];
    }
    $rl = &$_SESSION['rl'][$key];
    if ($now - $rl['start'] > $window_seconds) {
        $rl = ['count' => 0, 'start' => $now];
    }
    $rl['count']++;
    if ($rl['count'] > $max) {
        http_response_code(429);
        echo json_encode(['error' => 'Too many requests. Please wait a moment.']);
        exit;
    }
}

function handle_menu_upload($files, $existing = '')
{
    if (empty($files['image_file']) || empty($files['image_file']['name']))
        return $existing;
    $target_dir = __DIR__ . '/assets/img/menu/';
    if (!is_dir($target_dir))
        mkdir($target_dir, 0755, true);
    
    // MIME validation
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'webp'];
    $allowed_mimes = ['image/jpeg', 'image/png', 'image/webp'];
    $ext = strtolower(pathinfo($files['image_file']['name'], PATHINFO_EXTENSION));
    $mime = mime_content_type($files['image_file']['tmp_name']);

    if (!in_array($ext, $allowed_extensions) || !in_array($mime, $allowed_mimes)) {
        http_response_code(400);
        echo json_encode(['error' => 'Invalid file type. Only JPG, PNG, and WEBP are allowed.']);
        exit;
    }

    $filename = uniqid('dish_') . '.' . $ext;

    $target_file = $target_dir . $filename;
    if (move_uploaded_file($files['image_file']['tmp_name'], $target_file)) {
        return 'assets/img/menu/' . $filename;
    }
    return $existing;
}
function attach_items($pdo, &$orders)
{
    foreach ($orders as &$o) {
        $stmt = $pdo->prepare("
            SELECT m.id, m.name, m.price, oi.quantity as qty, m.image_url, COALESCE(ct.name, c.name, 'Special') as category 
            FROM order_item oi 
            JOIN menu_item m ON oi.menu_item_id = m.id 
            JOIN category c ON m.category_id = c.id
            LEFT JOIN cuisine_type ct ON c.cuisine_type_id = ct.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$o['id']]);
        $o['items_json'] = json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
    }
}
if ($action === 'restaurant_info') {
    echo json_encode(['lat' => RESTAURANT_LAT, 'lng' => RESTAURANT_LNG, 'name' => RESTAURANT_NAME]);
    exit;
}
if ($action === 'menu') {
    $cat = $_GET['cat'] ?? 'all';
    if ($cat !== 'all') {
        $s = $pdo->prepare("SELECT m.*, COALESCE(ct.name, c.name, 'Other') as category FROM menu_item m JOIN category c ON m.category_id=c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id=ct.id WHERE (ct.name=? OR c.name=?) AND (c.name NOT LIKE '%Combo%' AND c.name NOT LIKE '%Discount%') ORDER BY m.is_popular DESC");
        $s->execute([$cat, $cat]);
    } else {
        $s = $pdo->query("SELECT m.*, COALESCE(ct.name, c.name, 'Other') as category FROM menu_item m JOIN category c ON m.category_id=c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id=ct.id WHERE c.name NOT LIKE '%Combo%' AND c.name NOT LIKE '%Discount%' ORDER BY m.is_popular DESC");
    }
    echo json_encode($s->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
if ($action === 'menu_categories') {
    echo json_encode(array_column($pdo->query("SELECT DISTINCT c.name as category FROM category c JOIN menu_item m ON m.category_id=c.id WHERE c.name NOT LIKE '%Combo%' AND c.name NOT LIKE '%Discount%' ORDER BY c.name")->fetchAll(PDO::FETCH_ASSOC), 'category'));
    exit;
}
if ($action === 'search_place') {
    $q = $_GET['q'] ?? '';
    if (!$q) {
        echo json_encode([]);
        exit;
    }
    $url = 'https://nominatim.openstreetmap.org/search?format=json&limit=5&q=' . urlencode($q . ', Kathmandu');
    $ctx = stream_context_create(['http' => ['header' => 'User-Agent: SauniApp/1.0']]);
    echo file_get_contents($url, false, $ctx) ?: json_encode([]);
    exit;
}
if ($action === 'place_order') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$uid || empty($data['items']) || !isset($data['lat'])) {
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }
    $chk=$pdo->prepare("SELECT id FROM `order_` WHERE customer_id=? AND (status IS NULL OR (status!='delivered' AND status!='cancelled'))");
    $chk->execute([$uid]);
    if ($chk->rowCount()>0) { echo json_encode(['error'=>'You already have an active order!']); exit; }
    try {
        $pdo->beginTransaction();
        $stmt = $pdo->prepare("INSERT INTO `order_` (customer_id,total_amount,dest_lat,dest_lng,delivery_address,note,payment_method,status) VALUES (?,?,?,?,?,?,?,'pending')");
        $stmt->execute([$uid, $data['total'], $data['lat'], $data['lng'], $data['address'] ?? 'Pinned on Map', $data['note'] ?? '', $data['payment_method'] ?? 'COD']);
        $oid = $pdo->lastInsertId();
        $istmt = $pdo->prepare("INSERT INTO order_item (order_id, menu_item_id, quantity, unit_price) VALUES (?,?,?,?)");
        foreach ($data['items'] as $it) {
            $istmt->execute([$oid, $it['id'] ?? null, $it['qty'], $it['price']]);
        }
        $pdo->commit();
        $res = ['success' => true, 'order_id' => $oid];
        if (($data['payment_method'] ?? '') === 'esewa') {
            $res['payment_url'] = 'esewa_payment.php?oid=' . $oid;
        }
        echo json_encode($res);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['error' => 'DB error: ' . $e->getMessage()]);
    }
    exit;
}
if ($action === 'cancel_order') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$uid || empty($data['order_id'])) {
        echo json_encode(['error' => 'Invalid data']);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE `order_` SET status='cancelled' WHERE id=? AND customer_id=? AND status='pending'");
    $stmt->execute([$data['order_id'], $uid]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Order cannot be cancelled at this stage']);
    }
    exit;
}
if ($action === 'customer_orders') {
    $stmt = $pdo->prepare("SELECT o.*, o.placed_at as created_at, u.username as driver_name, CASE WHEN r.id IS NOT NULL THEN 1 ELSE 0 END as has_reviewed FROM `order_` o LEFT JOIN user u ON o.delivery_person_id=u.id LEFT JOIN review r ON o.id=r.order_id AND o.customer_id=r.customer_id WHERE o.customer_id=? ORDER BY o.id DESC");
    $stmt->execute([$uid]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    attach_items($pdo, $orders);
    echo json_encode($orders);
    exit;
}
if ($action === 'active_order') {
    $stmt = $pdo->prepare("SELECT o.*, u.username as driver_name, u.lat as driver_lat, u.lng as driver_lng FROM `order_` o LEFT JOIN user u ON o.delivery_person_id=u.id WHERE o.customer_id=? AND o.status!='delivered' AND o.status!='cancelled' ORDER BY o.id DESC LIMIT 1");
    $stmt->execute([$uid]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    if ($order) {
        $o = [$order];
        attach_items($pdo, $o);
        $order = $o[0];
    }
    echo json_encode($order);
    exit;
}
if ($action === 'check_review') {
    $order_id = (int) ($_GET['order_id'] ?? 0);
    if (!$uid || !$order_id) {
        echo json_encode(['already_reviewed' => false]);
        exit;
    }
    $chk = $pdo->prepare("SELECT id FROM review WHERE order_id=? AND customer_id=?");
    $chk->execute([$order_id, $uid]);
    echo json_encode(['already_reviewed' => $chk->rowCount() > 0]);
    exit;
}
if ($action === 'rate_order') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$uid || !($data['order_id'] ?? 0)) {
        echo json_encode(['error' => 'Invalid']);
        exit;
    }
    $chk = $pdo->prepare("SELECT id FROM review WHERE order_id=? AND customer_id=?");
    $chk->execute([$data['order_id'], $uid]);
    if ($chk->rowCount() > 0) {
        echo json_encode(['error' => 'Already rated']);
        exit;
    }
    $combined = round((int) ($data['rating'] ?? 5));
    $stmt = $pdo->prepare("INSERT INTO review (order_id,customer_id,stars,comment) VALUES (?,?,?,?)");
    $stmt->execute([$data['order_id'], $uid, $combined, $data['comment'] ?? '']);
    echo json_encode(['success' => true]);
    exit;
}
if ($action === 'admin_dashboard') {
    require_admin();

    $active = (int) $pdo->query("SELECT COUNT(*) FROM `order_` WHERE status NOT IN ('delivered', 'cancelled')")->fetchColumn();
    $pending = (int) $pdo->query("SELECT COUNT(*) FROM `order_` WHERE status='pending'")->fetchColumn();
    $todayDel = (int) $pdo->query("SELECT COUNT(*) FROM `order_` WHERE status='delivered' AND DATE(placed_at)=CURDATE()")->fetchColumn();
    $revenue = (float) $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM `order_` WHERE status='delivered' AND DATE(placed_at)=CURDATE()")->fetchColumn();
    $drvOnline = (int) $pdo->query("SELECT COUNT(*) FROM user WHERE role='delivery' AND lat IS NOT NULL AND lat!=0")->fetchColumn();
    $recent = $pdo->query("SELECT o.id,cu.username as customer_name,o.total_amount,o.status,o.placed_at as created_at FROM `order_` o JOIN user cu ON o.customer_id=cu.id ORDER BY o.id DESC LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
    $weekly = $pdo->query("
        SELECT DATE(placed_at) as day, COUNT(*) as count,
               DAYNAME(placed_at) as day_name
        FROM `order_`
        WHERE placed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        GROUP BY DATE(placed_at), DAYNAME(placed_at)
        ORDER BY day ASC
    ")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'active_orders' => $active,
        'pending_count' => $pending,
        'delivered_today' => $todayDel,
        'revenue_today' => round($revenue),
        'drivers_online' => $drvOnline,
        'recent_orders' => $recent,
        'activity' => $recent,
        'weekly_data' => $weekly,
    ]);
    exit;
}
if ($action === 'admin_stats') {
    require_admin();

    $stats = [
        'total_orders' => (int) $pdo->query("SELECT COUNT(*) FROM `order_`")->fetchColumn(),
        'pending_orders' => (int) $pdo->query("SELECT COUNT(*) FROM `order_` WHERE status='pending'")->fetchColumn(),
        'active_drivers' => (int) $pdo->query("SELECT COUNT(*) FROM user WHERE role='delivery' AND lat IS NOT NULL")->fetchColumn(),
        'total_revenue' => (float) $pdo->query("SELECT COALESCE(SUM(total_amount),0) FROM `order_` WHERE status='delivered'")->fetchColumn(),
        'avg_rating' => min(5, round((float) $pdo->query("SELECT COALESCE(AVG(stars),0) FROM review")->fetchColumn(), 1))
    ];
    $recent = $pdo->query("SELECT o.id,u.username,o.total_amount,o.status,o.placed_at as created_at FROM `order_` o JOIN user u ON o.customer_id=u.id ORDER BY o.id DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(['stats' => $stats, 'recent' => $recent]);
    exit;
}
if ($action === 'admin_orders_kanban') {
    require_admin();

    $stmt = $pdo->query("SELECT o.*, o.placed_at as created_at, cu.username as customer_name, dr.username as driver_name FROM `order_` o JOIN user cu ON o.customer_id=cu.id LEFT JOIN user dr ON o.delivery_person_id=dr.id ORDER BY o.id DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    attach_items($pdo, $orders);
    echo json_encode($orders);
    exit;
}
if ($action === 'mark_prepared') {
    require_admin();

    $data = json_decode(file_get_contents('php://input'), true);
    $stmt = $pdo->prepare("UPDATE `order_` SET status='prepared' WHERE id=? AND status IN ('pending', 'confirmed')");
    $stmt->execute([$data['order_id']]);
    echo json_encode(['success' => $stmt->rowCount() > 0, 'error' => $stmt->rowCount() ? null : 'Not in pending/confirmed state']);
    exit;
}
if ($action === 'assign_driver') {
    require_admin();

    $data = json_decode(file_get_contents('php://input'), true);
    if (!($data['order_id'] ?? 0) || !($data['driver_id'] ?? 0)) {
        echo json_encode(['error' => 'Missing data']);
        exit;
    }
    $stmt = $pdo->prepare("UPDATE `order_` SET delivery_person_id=?,status='assigned' WHERE id=? AND status='prepared'");
    $stmt->execute([$data['driver_id'], $data['order_id']]);
    echo json_encode(['success' => $stmt->rowCount() > 0, 'error' => $stmt->rowCount() ? null : 'Order must be in prepared state']);
    exit;
}
if ($action === 'admin_menu') {
    require_admin();

    echo json_encode($pdo->query("SELECT m.*, COALESCE(ct.name, c.name, 'Other') as category FROM menu_item m JOIN category c ON m.category_id=c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id=ct.id ORDER BY category, m.name")->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
if ($action === 'add_menu') {
    require_admin();

    $data = $_POST;
    error_log("add_menu POST data: " . json_encode($data));
    if (!($data['name'] ?? '') || !($data['price'] ?? 0)) {
        echo json_encode(['error' => 'Fill required fields']);
        exit;
    }
    $cat = $data['category'] ?? 'Other';
    $cstmt = $pdo->prepare("SELECT id FROM category WHERE name=?");
    $cstmt->execute([$cat]);
    $cid = $cstmt->fetchColumn();
    if (!$cid) {
        $pdo->prepare("INSERT INTO category (name) VALUES (?)")->execute([$cat]);
        $cid = $pdo->lastInsertId();
        // If the category name matches an existing cuisine_type, link it
        $cst = $pdo->prepare("SELECT id FROM cuisine_type WHERE name=?");
        $cst->execute([$cat]);
        $ctid = $cst->fetchColumn();
        if ($ctid)
            $pdo->prepare("UPDATE category SET cuisine_type_id=? WHERE id=?")->execute([$ctid, $cid]);
    }
    $img_url = handle_menu_upload($_FILES);
    $stmt = $pdo->prepare("INSERT INTO menu_item (name,category_id,price,description,image_url,is_popular) VALUES (?,?,?,?,?,?)");
    $stmt->execute([$data['name'] ?? '', $cid, $data['price'] ?? 0, $data['description'] ?? '', $img_url, !empty($data['is_popular']) ? 1 : 0]);
    echo json_encode(['success' => true, 'id' => $pdo->lastInsertId()]);
    exit;
}
if ($action === 'edit_menu') {
    require_admin();

    $data = $_POST;
    error_log("edit_menu POST data: " . json_encode($data));
    if (!($data['id'] ?? 0)) {
        echo json_encode(['error' => 'Missing ID']);
        exit;
    }
    $cat = $data['category'] ?? 'Other';
    $cstmt = $pdo->prepare("SELECT id FROM category WHERE name=?");
    $cstmt->execute([$cat]);
    $cid = $cstmt->fetchColumn();
    if (!$cid) {
        $pdo->prepare("INSERT INTO category (name) VALUES (?)")->execute([$cat]);
        $cid = $pdo->lastInsertId();
        // If the category name matches an existing cuisine_type, link it
        $cst = $pdo->prepare("SELECT id FROM cuisine_type WHERE name=?");
        $cst->execute([$cat]);
        $ctid = $cst->fetchColumn();
        if ($ctid)
            $pdo->prepare("UPDATE category SET cuisine_type_id=? WHERE id=?")->execute([$ctid, $cid]);
    }
    $img_url = handle_menu_upload($_FILES, $data['existing_image'] ?? '');
    $stmt = $pdo->prepare("UPDATE menu_item SET name=?,category_id=?,price=?,description=?,image_url=?,is_popular=? WHERE id=?");
    $stmt->execute([$data['name'] ?? '', $cid, $data['price'] ?? 0, $data['description'] ?? '', $img_url, !empty($data['is_popular']) ? 1 : 0, $data['id']]);
    echo json_encode(['success' => true]);
    exit;
}
if ($action === 'delete_menu') {
    require_admin();

    $data = json_decode(file_get_contents('php://input'), true);
    $pdo->prepare("DELETE FROM menu_item WHERE id=?")->execute([$data['id']]);
    echo json_encode(['success' => true]);
    exit;
}
if ($action === 'admin_drivers') {
    require_admin();

    echo json_encode($pdo->query("SELECT id,username,lat,lng FROM user WHERE role='delivery'")->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
if ($action === 'admin_ratings') {
    require_admin();

    echo json_encode($pdo->query("SELECT r.*, r.stars as rating, u.username, o.total_amount FROM review r JOIN user u ON r.customer_id=u.id JOIN `order_` o ON r.order_id=o.id ORDER BY r.id DESC")->fetchAll(PDO::FETCH_ASSOC));
    exit;
}
if ($action === 'update_driver_location') {
    $data = json_decode(file_get_contents('php://input'), true);
    if ($uid && isset($data['lat']) && isset($data['lng'])) {
        $pdo->prepare("UPDATE user SET lat=?,lng=? WHERE id=?")->execute([$data['lat'], $data['lng'], $uid]);
        echo json_encode(['success' => true]);
    }
    exit;
}
if ($action === 'my_assigned_order') {
    $stmt = $pdo->prepare("SELECT o.*, cu.username as customer_name FROM `order_` o JOIN user cu ON o.customer_id=cu.id WHERE o.delivery_person_id=? AND o.status IN ('assigned','picked_up') LIMIT 1");
    $stmt->execute([$uid]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    if ($order) {
        $o = [$order];
        attach_items($pdo, $o);
        $order = $o[0];
    }
    echo json_encode($order);
    exit;
}
if ($action === 'update_order_status') {
    $data = json_decode(file_get_contents('php://input'), true);
    $status = $data['status'] ?? '';

    if ($role === 'admin') {
        if (!in_array($status, ['pending', 'prepared', 'assigned', 'picked_up', 'delivered', 'cancelled'])) {
            echo json_encode(['error' => 'Invalid status']);
            exit;
        }
        if ($status === 'cancelled') {
            $chk = $pdo->prepare("SELECT status FROM `order_` WHERE id=?");
            $chk->execute([$data['order_id']]);
            $curr = $chk->fetchColumn();
            if ($curr !== 'pending') {
                echo json_encode(['error' => 'Order cannot be cancelled after preparation has started.']);
                exit;
            }
        }
        $stmt = $pdo->prepare("UPDATE `order_` SET status=? WHERE id=?");
        $success = $stmt->execute([$status, $data['order_id']]);
        echo json_encode(['success' => $success]);
        exit;
    } else {
        if (!in_array($status, ['picked_up', 'delivered'])) {
            echo json_encode(['error' => 'Invalid status']);
            exit;
        }
        $stmt = $pdo->prepare("UPDATE `order_` SET status=? WHERE id=? AND delivery_person_id=?");
        $success = $stmt->execute([$status, $data['order_id'], $uid]);
        echo json_encode(['success' => $success]);
        exit;
    }
}
if ($action === 'driver_earnings') {
    if ($role !== 'delivery') {
        echo json_encode(['error' => 'Forbidden']);
        exit;
    }
    $cnt = $pdo->prepare("SELECT COUNT(*) FROM `order_` WHERE delivery_person_id=? AND status='delivered'");
    $cnt->execute([$uid]);
    $totalDelivered = (int) $cnt->fetchColumn();
    $cntToday = $pdo->prepare("SELECT COUNT(*) FROM `order_` WHERE delivery_person_id=? AND status='delivered' AND DATE(placed_at)=CURDATE()");
    $cntToday->execute([$uid]);
    $todayCount = (int) $cntToday->fetchColumn();
    $cntWeek = $pdo->prepare("SELECT COUNT(*) FROM `order_` WHERE delivery_person_id=? AND status='delivered' AND placed_at>=DATE_SUB(NOW(),INTERVAL 7 DAY)");
    $cntWeek->execute([$uid]);
    $weekCount = (int) $cntWeek->fetchColumn();
    $COMMISSION = 70;
    $todayVal = $todayCount * $COMMISSION;
    $weekVal = $weekCount * $COMMISSION;
    $cntVal = $totalDelivered;
    $avg = $pdo->prepare("SELECT AVG(r.stars) FROM review r JOIN `order_` o ON r.order_id=o.id WHERE o.delivery_person_id=?");
    $avgVal = 0;
    try {
        $avg->execute([$uid]);
        $avgVal = $avg->fetchColumn();
    } catch (PDOException $e) {
    }
    $hist = $pdo->prepare("SELECT o.*, o.placed_at as created_at, cu.username as customer_name FROM `order_` o JOIN user cu ON o.customer_id=cu.id WHERE o.delivery_person_id=? AND o.status='delivered' ORDER BY o.id DESC LIMIT 30");
    $hist->execute([$uid]);
    $history = $hist->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode([
        'today' => round($todayVal),
        'week' => round($weekVal),
        'total_count' => $cntVal,
        'avg_rating' => $avgVal ? min(5, round($avgVal, 1)) : null,
        'history' => $history
    ]);
    exit;
}
if ($action === 'ai_chat') {
    rate_limit('ai_chat', 10, 60);

    $data = json_decode(file_get_contents('php://input'), true);
    $userMsg = $data['message'] ?? '';
    if (!$userMsg) {
        echo json_encode(['error' => 'No message received.']);
        exit;
    }

    // 1. Fetch live menu data (only available items)
    $menu_stmt = $pdo->query("SELECT m.id, m.name, m.price, m.description, m.is_popular, m.image_url, COALESCE(ct.name, c.name, 'Other') as category FROM menu_item m JOIN category c ON m.category_id = c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id = ct.id WHERE m.is_available = 1");
    $menu_items = $menu_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Build structured Menu Context for AI
    $menu_list = "";
    foreach ($menu_items as $it) {
        $pop = $it['is_popular'] ? " (POPULAR)" : "";
        $menu_list .= "- NAME: {$it['name']}, PRICE: Rs. {$it['price']}, CATEGORY: {$it['category']}$pop\n";
    }

    // 3. Construct the Smart Prompt
    // 3. Construct the Smart Prompt
    $system_instructions = "You are FoodBot, an AI assistant for a food delivery system.

STRICT RULES:
* You MUST ONLY recommend items from the provided menu.
* You MUST strictly follow user constraints (like budget).
* If user asks for 'popular' or 'trending' items, recommend those marked with (POPULAR).
* If user says \"under 300\", DO NOT include items above 300.
* NEVER recommend items outside the price limit.
* NEVER guess or invent food.
* If you recommend a specific dish, wrap its name in brackets such as [Item Name] to help the system link it.";

    $final_prompt = "$system_instructions

MENU:
$menu_list

USER:
$userMsg

TASK:
* Filter items based on user request
* Only show matching items
* If none match, say: \"No food available under this budget\"

Keep response short and accurate.";

    // 4. Secure AI API Integration (Multi-Model Resilient Handler)
    require_once __DIR__ . '/includes/config.php';

    $has_key = (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY));
    if (!$has_key) {
        echo json_encode(['response' => "The assistant is in configuration mode.", 'error' => 'API Key not configured']);
        exit;
    }

    $ai_response = "";
    $models_to_try = [AI_MODEL];
    if (defined('AI_MODEL_FALLBACK'))
        $models_to_try[] = AI_MODEL_FALLBACK;

    foreach ($models_to_try as $idx => $model_slug) {
        $url = "https://generativelanguage.googleapis.com/v1/models/" . $model_slug . ":generateContent?key=" . GEMINI_API_KEY;
        $payload = ["contents" => [["parts" => [["text" => $final_prompt]]]]];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);


        $result = curl_exec($ch);
        $res_data = json_decode($result, true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $temp_response = $res_data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($temp_response) {
            $ai_response = $temp_response;
            break; // Success!
        }

        // Auto-Fallback on Quota/Rate Limit Issues
        $isQuotaError = ($http_code == 429 || stripos(json_encode($res_data), 'quota') !== false);
        if ($isQuotaError && $idx < count($models_to_try) - 1) {
            usleep(1000000); // 1-second delay before switching models
            continue;
        }

        // If it's the last model and we still have an error, report it
        if (!empty($res_data['error']) && $idx == count($models_to_try) - 1) {
            $err_msg = $res_data['error']['message'] ?? 'API Error';
            echo json_encode(['error' => $err_msg, 'response' => "I'm having trouble with my connection ($err_msg)."]);
            exit;
        }
    }

    // 4.1. Handle Smart Simulation (Fallback)
    if (empty($ai_response)) {
        // Strict fallback: avoid guessing food items to comply with rules.
        $ai_response = "I'm currently in 'Quick Mode' due to high traffic. Please check our menu above for live items that match your budget!";
    }

    // 5. Extract suggested item for 'Quick Order' linking
    $suggested_item = null;
    if (preg_match('/\[(.*?)\]/', $ai_response, $matches)) {
        $name = trim($matches[1]);
        foreach ($menu_items as $mi) {
            if (strcasecmp($mi['name'], $name) === 0) {
                $suggested_item = $mi;
                $suggested_item['image_url'] = $mi['image_url']; // Ensure image is passed
                $ai_response = str_replace("[{$name}]", $name, $ai_response);
                break;
            }
        }
    }

    echo json_encode([
        'response' => $ai_response,
        'suggested_item' => $suggested_item
    ]);
    exit;
}

echo json_encode(['error' => 'Invalid action: ' . $action]);
?>