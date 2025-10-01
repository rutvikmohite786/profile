<?php
/**
 * Debug Script for Topmate Clone
 * This script helps diagnose server errors
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Topmate Clone - Debug Information</h1>";

// Check PHP version
echo "<h2>PHP Information</h2>";
echo "PHP Version: " . PHP_VERSION . "<br>";
echo "Server Software: " . $_SERVER['SERVER_SOFTWARE'] . "<br>";
echo "Document Root: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "Script Path: " . __FILE__ . "<br>";

// Check required PHP extensions
echo "<h2>PHP Extensions</h2>";
$required_extensions = ['pdo', 'pdo_mysql', 'session', 'json'];
foreach ($required_extensions as $ext) {
    $status = extension_loaded($ext) ? "✅ Loaded" : "❌ Missing";
    echo "$ext: $status<br>";
}

// Check file permissions
echo "<h2>File Permissions</h2>";
$files_to_check = [
    'config/database.php',
    'includes/auth.php',
    'index.php'
];

foreach ($files_to_check as $file) {
    if (file_exists($file)) {
        $perms = fileperms($file);
        $readable = is_readable($file) ? "✅" : "❌";
        echo "$file: $readable (permissions: " . substr(sprintf('%o', $perms), -4) . ")<br>";
    } else {
        echo "$file: ❌ File not found<br>";
    }
}

// Test database connection
echo "<h2>Database Connection Test</h2>";
try {
    if (file_exists('config/database.php')) {
        require_once 'config/database.php';
        
        // Test connection
        $db = getDB();
        echo "✅ Database connection successful<br>";
        
        // Test query
        $result = fetchOne("SELECT COUNT(*) as count FROM users");
        echo "✅ Database query successful (Users: " . $result['count'] . ")<br>";
        
    } else {
        echo "❌ Database config file not found<br>";
    }
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "<br>";
}

// Test includes
echo "<h2>Include Files Test</h2>";
$includes_to_test = [
    'includes/auth.php' => 'Auth class',
    'includes/header.php' => 'Header template',
    'includes/footer.php' => 'Footer template'
];

foreach ($includes_to_test as $file => $description) {
    if (file_exists($file)) {
        try {
            require_once $file;
            echo "$description: ✅ Loaded successfully<br>";
        } catch (Exception $e) {
            echo "$description: ❌ Error - " . $e->getMessage() . "<br>";
        }
    } else {
        echo "$description: ❌ File not found<br>";
    }
}

// Check for syntax errors in main files
echo "<h2>Syntax Check</h2>";
$php_files = [
    'index.php',
    'login.php',
    'register.php',
    'profile.php'
];

foreach ($php_files as $file) {
    if (file_exists($file)) {
        $output = shell_exec("php -l $file 2>&1");
        if (strpos($output, 'No syntax errors') !== false) {
            echo "$file: ✅ No syntax errors<br>";
        } else {
            echo "$file: ❌ Syntax error - " . htmlspecialchars($output) . "<br>";
        }
    }
}

// Memory and execution limits
echo "<h2>PHP Configuration</h2>";
echo "Memory Limit: " . ini_get('memory_limit') . "<br>";
echo "Max Execution Time: " . ini_get('max_execution_time') . "<br>";
echo "Max Input Time: " . ini_get('max_input_time') . "<br>";
echo "Post Max Size: " . ini_get('post_max_size') . "<br>";
echo "Upload Max Filesize: " . ini_get('upload_max_filesize') . "<br>";

// Error log location
echo "<h2>Error Logs</h2>";
echo "Error Log: " . ini_get('error_log') . "<br>";
echo "Log Errors: " . (ini_get('log_errors') ? 'Yes' : 'No') . "<br>";
echo "Display Errors: " . (ini_get('display_errors') ? 'Yes' : 'No') . "<br>";

echo "<hr>";
echo "<p><strong>If you see any ❌ errors above, those are likely causing the server error.</strong></p>";
echo "<p>Delete this debug.php file after troubleshooting for security.</p>";
?>
