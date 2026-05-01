<?php
require_once __DIR__ . '/env.php';
load_env(__DIR__ . '/../.env');

define('GEMINI_API_KEY',     $_ENV['GEMINI_API_KEY']       ?? '');
define('AI_MODEL',           $_ENV['GEMINI_MODEL']          ?? 'gemini-2.5-flash');
define('AI_MODEL_FALLBACK',  $_ENV['GEMINI_MODEL_FALLBACK'] ?? 'gemini-2.0-flash');
?>
