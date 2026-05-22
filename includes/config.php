<?php
require_once __DIR__ . '/env.php';
load_env(__DIR__ . '/../.env');

define('GEMINI_API_KEY',     $_ENV['GEMINI_API_KEY']       ?? '');
define('AI_MODEL',           $_ENV['GEMINI_MODEL']          ?? 'gemini-2.5-flash');
define('AI_MODEL_FALLBACK',  $_ENV['GEMINI_MODEL_FALLBACK'] ?? 'gemini-2.0-flash');
define('ESEWA_SECRET',       $_ENV['ESEWA_SECRET']          ?? '');
define('ESEWA_PRODUCT_CODE', $_ENV['ESEWA_PRODUCT_CODE']    ?? 'EPAYTEST');

/**
 * USE_SIMULATION:
 * Set to false when using a real API key.
 */
define('USE_SIMULATION', false);
?>
