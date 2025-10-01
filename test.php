<?php
/**
 * Simple Test Script
 * Basic functionality test
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Topmate Clone - Basic Test</h1>";

try {
    // Test 1: Include database config
    echo "1. Testing database config... ";
    require_once 'config/database.php';
    echo "✅ OK<br>";
    
    // Test 2: Database connection
    echo "2. Testing database connection... ";
    $db = getDB();
    echo "✅ OK<br>";
    
    // Test 3: Auth system
    echo "3. Testing auth system... ";
    require_once 'includes/auth.php';
    echo "✅ OK<br>";
    
    // Test 4: Sample query
    echo "4. Testing database query... ";
    $users = fetchAll("SELECT id, username, email FROM users LIMIT 1");
    echo "✅ OK (Found " . count($users) . " users)<br>";
    
    echo "<br><strong>✅ All basic tests passed! The application should work.</strong><br>";
    echo "<a href='index.php'>Go to Homepage</a>";
    
} catch (Exception $e) {
    echo "<br><strong>❌ Error:</strong> " . $e->getMessage();
    echo "<br><br>Please check the debug.php file for more details.";
}
?>
