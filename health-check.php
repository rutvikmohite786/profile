<?php
/**
 * Health Check Script
 * Quick system health verification
 */

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

$health = [
    'status' => 'healthy',
    'timestamp' => date('Y-m-d H:i:s'),
    'checks' => []
];

try {
    // Check PHP version
    $health['checks']['php_version'] = [
        'status' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'pass' : 'fail',
        'value' => PHP_VERSION,
        'message' => version_compare(PHP_VERSION, '7.4.0', '>=') ? 'PHP version OK' : 'PHP version too old'
    ];
    
    // Check required extensions
    $required_extensions = ['pdo', 'pdo_mysql', 'session', 'json'];
    foreach ($required_extensions as $ext) {
        $health['checks']["extension_$ext"] = [
            'status' => extension_loaded($ext) ? 'pass' : 'fail',
            'message' => extension_loaded($ext) ? "$ext extension loaded" : "$ext extension missing"
        ];
    }
    
    // Check database connection
    try {
        require_once 'config/database.php';
        $db = getDB();
        $result = fetchOne("SELECT 1 as test");
        
        $health['checks']['database'] = [
            'status' => 'pass',
            'message' => 'Database connection successful'
        ];
    } catch (Exception $e) {
        $health['checks']['database'] = [
            'status' => 'fail',
            'message' => 'Database connection failed: ' . $e->getMessage()
        ];
        $health['status'] = 'unhealthy';
    }
    
    // Check file permissions
    $critical_files = ['config/database.php', 'includes/auth.php', 'index.php'];
    foreach ($critical_files as $file) {
        $health['checks']["file_$file"] = [
            'status' => (file_exists($file) && is_readable($file)) ? 'pass' : 'fail',
            'message' => (file_exists($file) && is_readable($file)) ? "$file accessible" : "$file missing or not readable"
        ];
    }
    
    // Check if any critical checks failed
    foreach ($health['checks'] as $check) {
        if ($check['status'] === 'fail') {
            $health['status'] = 'unhealthy';
            break;
        }
    }
    
} catch (Exception $e) {
    $health['status'] = 'error';
    $health['error'] = $e->getMessage();
}

echo json_encode($health, JSON_PRETTY_PRINT);
?>
