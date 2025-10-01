<?php
/**
 * Registration Page
 * Topmate Clone - Core PHP Application
 */

require_once 'includes/auth.php';

$pageTitle = 'Sign Up';
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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $expertise = trim($_POST['expertise'] ?? '');
    $pricePerSession = floatval($_POST['price_per_session'] ?? 0);
    
    // Validation
    $errors = [];
    
    if (empty($username)) {
        $errors[] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors[] = 'Username must be at least 3 characters long';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        $errors[] = 'Username can only contain letters, numbers, and underscores';
    }
    
    if (empty($email)) {
        $errors[] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (empty($password)) {
        $errors[] = 'Password is required';
    } elseif (strlen($password) < 6) {
        $errors[] = 'Password must be at least 6 characters long';
    }
    
    if ($password !== $confirmPassword) {
        $errors[] = 'Passwords do not match';
    }
    
    if (empty($firstName)) {
        $errors[] = 'First name is required';
    }
    
    if (empty($lastName)) {
        $errors[] = 'Last name is required';
    }
    
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } else {
        $result = Auth::register($username, $email, $password, $firstName, $lastName, $bio, $expertise, $pricePerSession);
        
        if ($result['success']) {
            $success = $result['message'];
            // Auto-login after successful registration
            $loginResult = Auth::login($username, $password);
            if ($loginResult['success']) {
                header('Location: profile.php');
                exit();
            }
        } else {
            $error = $result['message'];
        }
    }
}

include 'includes/header.php';
?>

<div class="container-sm">
    <div style="max-width: 600px; margin: 2rem auto;">
        <div class="card">
            <div class="card-header" style="text-align: center;">
                <h1 style="margin: 0; color: var(--text-primary);">Create Your Account</h1>
                <p style="margin: 0.5rem 0 0; color: var(--text-secondary);">Join our community of experts and learners</p>
            </div>
            
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= $error ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>

                <form method="POST" action="" id="registerForm">
                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="first_name" class="form-label">First Name *</label>
                            <input 
                                type="text" 
                                id="first_name" 
                                name="first_name" 
                                class="form-input" 
                                value="<?= htmlspecialchars($firstName ?? '') ?>"
                                required
                                autocomplete="given-name"
                            >
                        </div>

                        <div class="form-group">
                            <label for="last_name" class="form-label">Last Name *</label>
                            <input 
                                type="text" 
                                id="last_name" 
                                name="last_name" 
                                class="form-input" 
                                value="<?= htmlspecialchars($lastName ?? '') ?>"
                                required
                                autocomplete="family-name"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username" class="form-label">Username *</label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            class="form-input" 
                            value="<?= htmlspecialchars($username ?? '') ?>"
                            required
                            autocomplete="username"
                            pattern="[a-zA-Z0-9_]+"
                            title="Only letters, numbers, and underscores allowed"
                        >
                        <small style="color: var(--text-light);">Only letters, numbers, and underscores allowed</small>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address *</label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            class="form-input" 
                            value="<?= htmlspecialchars($email ?? '') ?>"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="grid grid-2">
                        <div class="form-group">
                            <label for="password" class="form-label">Password *</label>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                class="form-input" 
                                required
                                autocomplete="new-password"
                                minlength="6"
                            >
                            <small style="color: var(--text-light);">Minimum 6 characters</small>
                        </div>

                        <div class="form-group">
                            <label for="confirm_password" class="form-label">Confirm Password *</label>
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                class="form-input" 
                                required
                                autocomplete="new-password"
                            >
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="bio" class="form-label">Bio/Description</label>
                        <textarea 
                            id="bio" 
                            name="bio" 
                            class="form-textarea" 
                            rows="3"
                            placeholder="Tell us about yourself and your expertise..."
                        ><?= htmlspecialchars($bio ?? '') ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="expertise" class="form-label">Areas of Expertise</label>
                        <input 
                            type="text" 
                            id="expertise" 
                            name="expertise" 
                            class="form-input" 
                            value="<?= htmlspecialchars($expertise ?? '') ?>"
                            placeholder="e.g., PHP, JavaScript, Web Development, Database Design"
                        >
                        <small style="color: var(--text-light);">Separate multiple skills with commas</small>
                    </div>

                    <div class="form-group">
                        <label for="price_per_session" class="form-label">Price per Session ($)</label>
                        <input 
                            type="number" 
                            id="price_per_session" 
                            name="price_per_session" 
                            class="form-input" 
                            value="<?= htmlspecialchars($pricePerSession ?? 0) ?>"
                            min="0"
                            step="0.01"
                            placeholder="0.00"
                        >
                        <small style="color: var(--text-light);">Set to 0 if you're joining as a learner only</small>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary" style="width: 100%;">
                            Create Account
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="card-footer" style="text-align: center;">
                <p style="margin: 0; color: var(--text-secondary);">
                    Already have an account? 
                    <a href="login.php" style="color: var(--primary-color); text-decoration: none; font-weight: 600;">Sign in here</a>
                </p>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const username = document.getElementById('username').value.trim();
    
    // Check password match
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match');
        return false;
    }
    
    // Check username pattern
    if (!/^[a-zA-Z0-9_]+$/.test(username)) {
        e.preventDefault();
        alert('Username can only contain letters, numbers, and underscores');
        return false;
    }
    
    // Check minimum length
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

// Real-time password confirmation validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    
    if (confirmPassword && password !== confirmPassword) {
        this.style.borderColor = 'var(--error-color)';
    } else {
        this.style.borderColor = 'var(--border-color)';
    }
});
</script>

<?php include 'includes/footer.php'; ?>

