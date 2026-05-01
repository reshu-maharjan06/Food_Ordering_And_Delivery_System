<?php
session_start();
require_once __DIR__ . '/includes/db.php';
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

$action = $_GET['action'] ?? '';

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

if ($action === 'ai_chat') {
    rate_limit('ai_chat', 10, 60);

    $data = json_decode(file_get_contents('php://input'), true);
    $userMsg = $data['message'] ?? '';
    if (!$userMsg) {
        echo json_encode(['error' => 'No message received.']);
        exit;
    }

    // 1. Fetch live menu data
    $menu_stmt = $pdo->query("SELECT m.id, m.name, m.price, m.description, m.image_url, COALESCE(ct.name, c.name, 'Other') as category FROM menu_item m JOIN category c ON m.category_id = c.id LEFT JOIN cuisine_type ct ON c.cuisine_type_id = ct.id WHERE m.is_available = 1");
    $menu_items = $menu_stmt->fetchAll(PDO::FETCH_ASSOC);

    // 2. Build structured Menu Context for AI
    $menu_list = "";
    foreach ($menu_items as $it) {
        $menu_list .= "- NAME: {$it['name']}, PRICE: Rs. {$it['price']}, CATEGORY: {$it['category']}\n";
    }

    // 3. Construct the Smart Prompt
    $system_instructions = "You are FoodBot, an AI assistant for a food delivery system.
STRICT RULES:
* You MUST ONLY recommend items from the provided menu.
* You MUST strictly follow user constraints (like budget).
* If user says \"under 300\", DO NOT include items above 300.
* NEVER recommend items outside the price limit.
* NEVER guess or invent food.
* If you recommend a specific dish, wrap its name in brackets such as [Item Name] to help the system link it.";

    $final_prompt = "$system_instructions\n\nMENU:\n$menu_list\n\nUSER:\n$userMsg\n\nTASK:\n* Filter items based on user request\n* Only show matching items\n* If none match, say: \"No food available under this budget\"\n\nKeep response short and accurate.";

    // 4. Secure AI API Integration
    require_once __DIR__ . '/includes/config.php';

    $has_key = (defined('GEMINI_API_KEY') && !empty(GEMINI_API_KEY));
    if (!$has_key) {
        echo json_encode(['response' => "The assistant is in configuration mode.", 'error' => 'API Key not configured']);
        exit;
    }

    $ai_response = "";
    $models_to_try = [AI_MODEL];
    if (defined('AI_MODEL_FALLBACK')) $models_to_try[] = AI_MODEL_FALLBACK;

    foreach ($models_to_try as $idx => $model_slug) {
        $url = "https://generativelanguage.googleapis.com/v1/models/" . $model_slug . ":generateContent?key=" . GEMINI_API_KEY;
        $payload = ["contents" => [["parts" => [["text" => $final_prompt]]]]];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $result = curl_exec($ch);
        $res_data = json_decode($result, true);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $temp_response = $res_data['candidates'][0]['content']['parts'][0]['text'] ?? null;

        if ($temp_response) {
            $ai_response = $temp_response;
            break;
        }

        if ($http_code == 429 && $idx < count($models_to_try) - 1) {
            usleep(1000000);
            continue;
        }

        if (!empty($res_data['error']) && $idx == count($models_to_try) - 1) {
            $err_msg = $res_data['error']['message'] ?? 'API Error';
            echo json_encode(['error' => $err_msg, 'response' => "I'm having trouble with my connection ($err_msg)."]);
            exit;
        }
    }

    if (empty($ai_response)) {
        $ai_response = "I'm currently in 'Quick Mode' due to high traffic. Please check our menu for live items that match your budget!";
    }

    // 5. Extract suggested item for 'Quick Order' linking
    $suggested_item = null;
    if (preg_match('/\[(.*?)\]/', $ai_response, $matches)) {
        $name = trim($matches[1]);
        foreach ($menu_items as $mi) {
            if (strcasecmp($mi['name'], $name) === 0) {
                $suggested_item = $mi;
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