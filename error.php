<?php
/**
 * Error Handler Page
 * Topmate Clone - Core PHP Application
 */

// Get error information
$error_code = $_GET['code'] ?? '500';
$error_message = $_GET['message'] ?? 'An internal server error occurred.';

// Set appropriate HTTP status
http_response_code($error_code);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Error <?= $error_code ?> - Topmate Clone</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .error-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        .error-code {
            font-size: 4rem;
            font-weight: 700;
            color: #667eea;
            margin: 0;
            line-height: 1;
        }
        .error-title {
            font-size: 1.5rem;
            color: #2d3748;
            margin: 1rem 0;
        }
        .error-message {
            color: #718096;
            margin: 1rem 0 2rem;
            line-height: 1.6;
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 0.75rem 2rem;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0.5rem;
        }
        .btn:hover {
            background: #5a67d8;
            transform: translateY(-2px);
        }
        .btn-secondary {
            background: #e2e8f0;
            color: #4a5568;
        }
        .btn-secondary:hover {
            background: #cbd5e0;
        }
        .troubleshooting {
            background: #f7fafc;
            border-radius: 10px;
            padding: 1.5rem;
            margin-top: 2rem;
            text-align: left;
        }
        .troubleshooting h3 {
            margin-top: 0;
            color: #2d3748;
        }
        .troubleshooting ul {
            color: #4a5568;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="error-container">
        <div class="error-code"><?= $error_code ?></div>
        <h1 class="error-title">
            <?php if ($error_code == '404'): ?>
                Page Not Found
            <?php elseif ($error_code == '403'): ?>
                Access Forbidden
            <?php elseif ($error_code == '500'): ?>
                Server Error
            <?php else: ?>
                Error Occurred
            <?php endif; ?>
        </h1>
        <p class="error-message"><?= htmlspecialchars($error_message) ?></p>
        
        <a href="index.php" class="btn">Go Home</a>
        <a href="javascript:history.back()" class="btn btn-secondary">Go Back</a>
        
        <?php if ($error_code == '500'): ?>
            <div class="troubleshooting">
                <h3>Common Solutions:</h3>
                <ul>
                    <li>Check if the database is running and accessible</li>
                    <li>Verify database credentials in config/database.php</li>
                    <li>Ensure all required PHP extensions are installed</li>
                    <li>Check file permissions (should be 755 for directories, 644 for files)</li>
                    <li>Review server error logs for more details</li>
                </ul>
                <p><strong>Need help?</strong> Run <a href="debug.php">debug.php</a> for detailed diagnostics.</p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
