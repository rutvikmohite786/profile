<?php
/**
 * Setup Script for Topmate Clone
 * This script helps with initial setup and configuration
 */

// Prevent direct access in production
if (php_sapi_name() !== 'cli' && !isset($_GET['setup'])) {
    die('Access denied. Add ?setup=1 to URL to run setup.');
}

$step = $_GET['step'] ?? 1;
$error = '';
$success = '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Topmate Clone - Setup</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem;
            background-color: #f8fafc;
        }
        .setup-container {
            background: white;
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            padding: 2rem;
        }
        .step {
            display: none;
        }
        .step.active {
            display: block;
        }
        .btn {
            background-color: #6366f1;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0.5rem;
        }
        .btn:hover {
            background-color: #4f46e5;
        }
        .btn-secondary {
            background-color: #6b7280;
        }
        .btn-secondary:hover {
            background-color: #4b5563;
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 0.5rem;
            font-size: 1rem;
        }
        .alert {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
        }
        .alert-error {
            background-color: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: 1px solid #86efac;
        }
        .progress {
            display: flex;
            margin-bottom: 2rem;
        }
        .progress-step {
            flex: 1;
            text-align: center;
            padding: 0.5rem;
            background-color: #e5e7eb;
            margin: 0 0.25rem;
            border-radius: 0.5rem;
        }
        .progress-step.active {
            background-color: #6366f1;
            color: white;
        }
        .progress-step.completed {
            background-color: #10b981;
            color: white;
        }
        .checklist {
            list-style: none;
            padding: 0;
        }
        .checklist li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e5e7eb;
        }
        .checklist li:before {
            content: "✓ ";
            color: #10b981;
            font-weight: bold;
            margin-right: 0.5rem;
        }
        .checklist li.incomplete:before {
            content: "✗ ";
            color: #ef4444;
        }
    </style>
</head>
<body>
    <div class="setup-container">
        <h1 style="text-align: center; color: #1f2937; margin-bottom: 2rem;">Topmate Clone Setup</h1>
        
        <!-- Progress Bar -->
        <div class="progress">
            <div class="progress-step <?= $step >= 1 ? ($step > 1 ? 'completed' : 'active') : '' ?>">1. Requirements</div>
            <div class="progress-step <?= $step >= 2 ? ($step > 2 ? 'completed' : 'active') : '' ?>">2. Database</div>
            <div class="progress-step <?= $step >= 3 ? ($step > 3 ? 'completed' : 'active') : '' ?>">3. Configuration</div>
            <div class="progress-step <?= $step >= 4 ? ($step > 4 ? 'completed' : 'active') : '' ?>">4. Complete</div>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <!-- Step 1: Requirements Check -->
        <div class="step <?= $step == 1 ? 'active' : '' ?>">
            <h2>System Requirements Check</h2>
            <p>Let's verify that your system meets the requirements for running Topmate Clone.</p>
            
            <?php
            $requirements = [
                'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
                'PDO Extension' => extension_loaded('pdo'),
                'PDO MySQL Extension' => extension_loaded('pdo_mysql'),
                'Session Support' => function_exists('session_start'),
                'JSON Support' => function_exists('json_encode'),
                'File Upload Support' => ini_get('file_uploads'),
                'Write Permissions' => is_writable(__DIR__),
            ];
            ?>

            <ul class="checklist">
                <?php foreach ($requirements as $requirement => $status): ?>
                    <li class="<?= $status ? '' : 'incomplete' ?>"><?= $requirement ?></li>
                <?php endforeach; ?>
            </ul>

            <?php if (array_reduce($requirements, function($carry, $item) { return $carry && $item; }, true)): ?>
                <div class="alert alert-success">
                    <strong>Great!</strong> All requirements are met. You can proceed to the next step.
                </div>
                <a href="?setup=1&step=2" class="btn">Next Step</a>
            <?php else: ?>
                <div class="alert alert-error">
                    <strong>Warning!</strong> Some requirements are not met. Please fix these issues before proceeding.
                </div>
            <?php endif; ?>
        </div>

        <!-- Step 2: Database Setup -->
        <div class="step <?= $step == 2 ? 'active' : '' ?>">
            <h2>Database Configuration</h2>
            <p>Configure your database connection settings.</p>

            <?php
            // Handle database test
            if ($_POST && isset($_POST['test_connection'])) {
                $host = $_POST['db_host'] ?? 'localhost';
                $name = $_POST['db_name'] ?? 'topmate_clone';
                $user = $_POST['db_user'] ?? 'root';
                $pass = $_POST['db_pass'] ?? '';

                try {
                    $dsn = "mysql:host=$host;charset=utf8mb4";
                    $pdo = new PDO($dsn, $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Check if database exists
                    $stmt = $pdo->query("SHOW DATABASES LIKE '$name'");
                    $dbExists = $stmt->rowCount() > 0;
                    
                    if ($dbExists) {
                        $success = "Database connection successful! Database '$name' exists.";
                    } else {
                        // Create database
                        $pdo->exec("CREATE DATABASE IF NOT EXISTS `$name`");
                        $success = "Database connection successful! Database '$name' created.";
                    }
                } catch (PDOException $e) {
                    $error = "Database connection failed: " . $e->getMessage();
                }
            }
            ?>

            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Database Host</label>
                    <input type="text" name="db_host" class="form-input" value="localhost" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Database Name</label>
                    <input type="text" name="db_name" class="form-input" value="topmate_clone" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Database Username</label>
                    <input type="text" name="db_user" class="form-input" value="root" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Database Password</label>
                    <input type="password" name="db_pass" class="form-input" value="">
                </div>

                <button type="submit" name="test_connection" class="btn">Test Connection</button>
            </form>

            <?php if ($success): ?>
                <a href="?setup=1&step=3" class="btn">Next Step</a>
            <?php endif; ?>

            <a href="?setup=1&step=1" class="btn btn-secondary">Previous</a>
        </div>

        <!-- Step 3: Configuration -->
        <div class="step <?= $step == 3 ? 'active' : '' ?>">
            <h2>Final Configuration</h2>
            <p>Complete the setup by importing the database schema and creating the configuration file.</p>

            <?php
            // Handle final setup
            if ($_POST && isset($_POST['complete_setup'])) {
                $host = $_POST['db_host'] ?? 'localhost';
                $name = $_POST['db_name'] ?? 'topmate_clone';
                $user = $_POST['db_user'] ?? 'root';
                $pass = $_POST['db_pass'] ?? '';

                try {
                    // Test connection
                    $dsn = "mysql:host=$host;charset=utf8mb4";
                    $pdo = new PDO($dsn, $user, $pass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    // Import schema
                    $schemaFile = __DIR__ . '/database_schema.sql';
                    if (file_exists($schemaFile)) {
                        $sql = file_get_contents($schemaFile);
                        $pdo->exec($sql);
                    }
                    
                    // Update config file
                    $configFile = __DIR__ . '/config/database.php';
                    $configContent = file_get_contents($configFile);
                    
                    // Replace database constants
                    $configContent = preg_replace("/define\('DB_HOST', '[^']*'\)/", "define('DB_HOST', '$host')", $configContent);
                    $configContent = preg_replace("/define\('DB_NAME', '[^']*'\)/", "define('DB_NAME', '$name')", $configContent);
                    $configContent = preg_replace("/define\('DB_USER', '[^']*'\)/", "define('DB_USER', '$user')", $configContent);
                    $configContent = preg_replace("/define\('DB_PASS', '[^']*'\)/", "define('DB_PASS', '$pass')", $configContent);
                    
                    file_put_contents($configFile, $configContent);
                    
                    $success = "Setup completed successfully! You can now access your Topmate Clone application.";
                } catch (Exception $e) {
                    $error = "Setup failed: " . $e->getMessage();
                }
            }
            ?>

            <div style="background-color: #f3f4f6; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                <h3>What this step will do:</h3>
                <ul>
                    <li>Import the database schema</li>
                    <li>Create sample user accounts</li>
                    <li>Update configuration files</li>
                    <li>Set up initial data</li>
                </ul>
            </div>

            <form method="POST">
                <input type="hidden" name="db_host" value="<?= htmlspecialchars($_POST['db_host'] ?? 'localhost') ?>">
                <input type="hidden" name="db_name" value="<?= htmlspecialchars($_POST['db_name'] ?? 'topmate_clone') ?>">
                <input type="hidden" name="db_user" value="<?= htmlspecialchars($_POST['db_user'] ?? 'root') ?>">
                <input type="hidden" name="db_pass" value="<?= htmlspecialchars($_POST['db_pass'] ?? '') ?>">
                
                <button type="submit" name="complete_setup" class="btn" onclick="return confirm('This will import the database schema and create sample data. Continue?')">
                    Complete Setup
                </button>
            </form>

            <a href="?setup=1&step=2" class="btn btn-secondary">Previous</a>
        </div>

        <!-- Step 4: Complete -->
        <div class="step <?= $step == 4 ? 'active' : '' ?>">
            <h2>Setup Complete! 🎉</h2>
            <p>Your Topmate Clone application is now ready to use.</p>

            <div style="background-color: #d1fae5; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                <h3>Default Login Credentials:</h3>
                <div style="margin-top: 1rem;">
                    <strong>Admin Account:</strong><br>
                    Username: <code>admin</code><br>
                    Password: <code>admin123</code>
                </div>
                <div style="margin-top: 1rem;">
                    <strong>Expert Account:</strong><br>
                    Username: <code>john_doe</code><br>
                    Password: <code>expert123</code>
                </div>
            </div>

            <div style="background-color: #fef3c7; padding: 1.5rem; border-radius: 0.5rem; margin-bottom: 2rem;">
                <h3>Next Steps:</h3>
                <ul>
                    <li>Change the default admin password</li>
                    <li>Configure your email settings (if needed)</li>
                    <li>Set up SSL/HTTPS for production</li>
                    <li>Review the README.md for additional configuration options</li>
                </ul>
            </div>

            <div style="text-align: center;">
                <a href="index.php" class="btn" style="font-size: 1.1rem; padding: 1rem 2rem;">Go to Application</a>
            </div>

            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 1px solid #e5e7eb; text-align: center; color: #6b7280;">
                <p><strong>Important:</strong> Delete this setup.php file after completing the setup for security reasons.</p>
                <button onclick="deleteSetupFile()" class="btn btn-secondary" style="margin-top: 1rem;">Delete Setup File</button>
            </div>
        </div>
    </div>

    <script>
        function deleteSetupFile() {
            if (confirm('Are you sure you want to delete the setup file? This action cannot be undone.')) {
                fetch('setup.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'delete_setup=1'
                }).then(response => {
                    if (response.ok) {
                        alert('Setup file deleted successfully!');
                        window.location.href = 'index.php';
                    } else {
                        alert('Failed to delete setup file. Please delete it manually.');
                    }
                });
            }
        }

        // Handle delete setup file
        <?php
        if ($_POST && isset($_POST['delete_setup'])) {
            unlink(__FILE__);
            header('Location: index.php');
            exit();
        }
        ?>
    </script>
</body>
</html>

