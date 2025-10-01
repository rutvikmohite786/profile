<?php
/**
 * Authentication System
 * Topmate Clone - Core PHP Application
 */

require_once 'config/database.php';

/**
 * Authentication class for user management
 */
class Auth {
    
    /**
     * Register a new user
     */
    public static function register($username, $email, $password, $firstName, $lastName, $bio = '', $expertise = '', $pricePerSession = 0) {
        try {
            // Check if username or email already exists
            $existingUser = fetchOne(
                "SELECT id FROM users WHERE username = ? OR email = ?",
                [$username, $email]
            );
            
            if ($existingUser) {
                return ['success' => false, 'message' => 'Username or email already exists'];
            }
            
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            
            // Insert new user
            $sql = "INSERT INTO users (username, email, password, first_name, last_name, bio, expertise, price_per_session) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            executeQuery($sql, [
                $username, $email, $hashedPassword, $firstName, $lastName, $bio, $expertise, $pricePerSession
            ]);
            
            return ['success' => true, 'message' => 'Registration successful'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Registration failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Login user
     */
    public static function login($username, $password) {
        try {
            // Get user by username or email
            $user = fetchOne(
                "SELECT * FROM users WHERE (username = ? OR email = ?) AND is_active = 1",
                [$username, $username]
            );
            
            if (!$user) {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
            // Verify password
            if (password_verify($password, $user['password'])) {
                // Start session
                session_start();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                return ['success' => true, 'message' => 'Login successful', 'user' => $user];
            } else {
                return ['success' => false, 'message' => 'Invalid credentials'];
            }
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Login failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Logout user
     */
    public static function logout() {
        session_start();
        session_destroy();
        return ['success' => true, 'message' => 'Logged out successfully'];
    }
    
    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        session_start();
        return isset($_SESSION['user_id']);
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin() {
        session_start();
        return isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
    }
    
    /**
     * Get current user data
     */
    public static function getCurrentUser() {
        if (!self::isLoggedIn()) {
            return null;
        }
        
        session_start();
        return fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
    }
    
    /**
     * Update user profile
     */
    public static function updateProfile($userId, $data) {
        try {
            $fields = [];
            $values = [];
            
            // Build dynamic update query
            $allowedFields = ['first_name', 'last_name', 'bio', 'expertise', 'price_per_session', 'profile_image'];
            
            foreach ($data as $field => $value) {
                if (in_array($field, $allowedFields)) {
                    $fields[] = "$field = ?";
                    $values[] = $value;
                }
            }
            
            if (empty($fields)) {
                return ['success' => false, 'message' => 'No valid fields to update'];
            }
            
            $values[] = $userId;
            $sql = "UPDATE users SET " . implode(', ', $fields) . " WHERE id = ?";
            
            executeQuery($sql, $values);
            
            return ['success' => true, 'message' => 'Profile updated successfully'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Update failed: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get user by ID
     */
    public static function getUserById($userId) {
        return fetchOne("SELECT * FROM users WHERE id = ? AND is_active = 1", [$userId]);
    }
    
    /**
     * Get all users (for admin)
     */
    public static function getAllUsers() {
        return fetchAll("SELECT * FROM users WHERE is_active = 1 ORDER BY created_at DESC");
    }
}

/**
 * Helper function to require login
 */
function requireLogin() {
    if (!Auth::isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

/**
 * Helper function to require admin
 */
function requireAdmin() {
    requireLogin();
    if (!Auth::isAdmin()) {
        header('Location: index.php');
        exit();
    }
}
?>

