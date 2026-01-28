<?php
/**
 * Application Entry Point
 * نقطة دخول التطبيق
 */

// Start output buffering
ob_start();

// Enable error reporting (useful for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Load configuration
require_once __DIR__ . '/../config/database.php';

// Load autoloader
require_once __DIR__ . '/../autoload.php';

// Start session using Helper
\App\Helpers\Session::start();

// Define base URL dynamically
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseUrl = ($scriptName === '/' || $scriptName === '\\') ? '/' : $scriptName . '/';
define('BASE_URL', $baseUrl);

// Initialize router
use App\Core\Router;

$router = new Router();

// Flush output buffer
ob_end_flush();
