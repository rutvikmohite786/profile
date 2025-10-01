<?php
/**
 * Login Page
 * Topmate Clone - Core PHP Application
 */

require_once 'includes/auth.php';

$pageTitle = 'Sign In';
$error = '';
$success = '';

// Redirect if already logged in
if (Auth::isLoggedIn()) {
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_POST) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    // Basic validation
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $result = Auth::login($username, $password);
        
        if ($result['success']) {
            // Redirect to appropriate page
            $redirect = $_GET['redirect'] ?? 'index.php';
            header('Location: ' . $redirect);
            exit();
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<div class="container-sm">
    <div style="max-width: 400px; margin: 4rem auto;">
        <div class="card">
            <div class="card-header" style="text-align: center;">
                <h1 style="margin: 0; color: var(--text-primary);">Welcome Back</h1>
                <p style="margin: 0.5rem 0 0; color: var(--text-secondary);">Sign in to your account</p>
            </div>
            
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="" id="loginForm">
                    <div class="form-group">
                        <label for="username" class="form-label">Username or Email</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-input" 
                            value="<?= htmlspecialchars($username ?? '') ?>"
                            required
                            autocomplete="username"
                        >
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input 
                            type="password" 
                            id="password" 
                            name="password" 
                            class="form-input" 
                            required
                            autocomplete="current-password"
                        >
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Sign In
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card-footer" style="text-align: center;">
                <p style="margin: 0; color: var(--text-secondary);">
                    Don't have an account? 
                    <a href="register.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Sign up here</a>
                </p>
            </div>
        </div>
        
        <!-- Demo Credentials -->
        <div class="card" style="margin-top: 2rem; background-color: #f8fafc; border: 1px solid #e2e8f0;">
            <div class="card-body">
                <h4 style="margin-bottom: 1rem; color: var(--text-primary);">Demo Credentials</h4>
                <div style="background-color: white; padding: 1rem; border-radius: var(--radius-md); border: 1px solid var(--border-color);">
                    <p style="margin-bottom: 0.5rem; font-weight: 600;">Admin Account:</p>
                    <p style="margin-bottom: 1rem; font-family: monospace; color: var(--text-secondary);">Username: admin | Password: admin123</p>
                    
                    <p style="margin-bottom: 0.5rem; font-weight: 600;">Expert Account:</p>
                    <p style="font-family: monospace; color: var(--text-secondary);">Username: john_doe | Password: expert123</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const username = document.getElementById('username').value.trim();
    const password = document.getElementById('password').value;
    
    if (!username || !password) {
        e.preventDefault();
        alert('Please fill in all fields');
        return false;
    }
    
    if (username.length < 3) {
        e.preventDefault();
        alert('Username must be at least 3 characters long');
        return false;
    }
    
    if (password.length < 6) {
        e.preventDefault();
        alert('Password must be at least 6 characters long');
        return false;
    }
});
</script>

<?php include 'includes/footer.php'; ?>

