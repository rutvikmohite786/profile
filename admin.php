<?php
/**
 * Admin Panel
 * Topmate Clone - Core PHP Application
 */

require_once 'includes/auth.php';
require_once 'config/database.php';

$pageTitle = 'Admin Panel';

// Require admin access
requireAdmin();

// Handle admin actions
$actionMessage = '';

if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'update_booking_status':
            $bookingId = intval($_POST['booking_id'] ?? 0);
            $status = $_POST['status'] ?? '';
            
            if ($bookingId && in_array($status, ['pending', 'confirmed', 'cancelled', 'completed'])) {
                executeQuery("UPDATE bookings SET status = ? WHERE id = ?", [$status, $bookingId]);
                $actionMessage = 'Booking status updated successfully';
            }
            break;
            
        case 'delete_user':
            $userId = intval($_POST['user_id'] ?? 0);
            
            if ($userId && $userId != $_SESSION['user_id']) {
                executeQuery("UPDATE users SET is_active = 0 WHERE id = ?", [$userId]);
                $actionMessage = 'User deactivated successfully';
            }
            break;
            
        case 'update_user_status':
            $userId = intval($_POST['user_id'] ?? 0);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            
            if ($userId && $userId != $_SESSION['user_id']) {
                executeQuery("UPDATE users SET is_active = ? WHERE id = ?", [$isActive, $userId]);
                $actionMessage = 'User status updated successfully';
            }
            break;
    }
}

// Get statistics
$stats = [
    'total_users' => fetchOne("SELECT COUNT(*) as count FROM users WHERE is_active = 1")['count'],
    'total_bookings' => fetchOne("SELECT COUNT(*) as count FROM bookings")['count'],
    'total_revenue' => fetchOne("SELECT SUM(total_amount) as total FROM bookings WHERE status = 'confirmed'")['total'] ?? 0,
    'pending_bookings' => fetchOne("SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'")['count']
];

// Get recent bookings
$recentBookings = fetchAll(
    "SELECT b.*, u.first_name, u.last_name, u.username 
     FROM bookings b 
     JOIN users u ON b.user_id = u.id 
     ORDER BY b.created_at DESC 
     LIMIT 10"
);

// Get all users
$users = fetchAll(
    "SELECT * FROM users WHERE is_active = 1 ORDER BY created_at DESC"
);

// Get booking statistics by status
$bookingStats = fetchAll(
    "SELECT status, COUNT(*) as count FROM bookings GROUP BY status"
);

include 'includes/header.php';
?>

<div class="container">
    <div style="margin-bottom: 2rem;">
        <h1 style="color: var(--text-primary); margin-bottom: 0.5rem;">Admin Dashboard</h1>
        <p style="color: var(--text-secondary);">Manage users, bookings, and system settings</p>
    </div>

    <?php if ($actionMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($actionMessage) ?></div>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="grid grid-4" style="margin-bottom: 3rem;">
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; color: var(--primary-color); margin-bottom: 0.5rem;">👥</div>
                <h3 style="margin-bottom: 0.5rem;"><?= $stats['total_users'] ?></h3>
                <p style="color: var(--text-secondary); margin: 0;">Total Users</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; color: var(--accent-color); margin-bottom: 0.5rem;">📅</div>
                <h3 style="margin-bottom: 0.5rem;"><?= $stats['total_bookings'] ?></h3>
                <p style="color: var(--text-secondary); margin: 0;">Total Bookings</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; color: var(--success-color); margin-bottom: 0.5rem;">💰</div>
                <h3 style="margin-bottom: 0.5rem;">$<?= number_format($stats['total_revenue'], 2) ?></h3>
                <p style="color: var(--text-secondary); margin: 0;">Total Revenue</p>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body" style="text-align: center;">
                <div style="font-size: 2rem; color: var(--warning-color); margin-bottom: 0.5rem;">⏳</div>
                <h3 style="margin-bottom: 0.5rem;"><?= $stats['pending_bookings'] ?></h3>
                <p style="color: var(--text-secondary); margin: 0;">Pending Bookings</p>
            </div>
        </div>
    </div>

    <div class="grid grid-2" style="gap: 2rem; margin-bottom: 3rem;">
        <!-- Recent Bookings -->
        <div class="card">
            <div class="card-header">
                <h3>Recent Bookings</h3>
            </div>
            <div class="card-body">
                <?php if (empty($recentBookings)): ?>
                    <p style="color: var(--text-secondary); text-align: center;">No bookings found</p>
                <?php else: ?>
                    <div class="bookings-list">
                        <?php foreach ($recentBookings as $booking): ?>
                            <div style="padding: 1rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
                                    <strong><?= htmlspecialchars($booking['client_name']) ?></strong>
                                    <span class="status-badge status-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span>
                                </div>
                                <div style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    Expert: <?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?>
                                </div>
                                <div style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 0.5rem;">
                                    <?= date('M j, Y', strtotime($booking['booking_date'])) ?> at <?= date('g:i A', strtotime($booking['booking_time'])) ?>
                                </div>
                                <div style="color: var(--primary-color); font-weight: 600;">
                                    $<?= number_format($booking['total_amount'], 2) ?>
                                </div>
                                
                                <!-- Status Update Form -->
                                <form method="POST" style="margin-top: 1rem;">
                                    <input type="hidden" name="action" value="update_booking_status">
                                    <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <select name="status" style="flex: 1; padding: 0.5rem; border: 1px solid var(--border-color); border-radius: var(--radius-sm);">
                                            <option value="pending" <?= $booking['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
                                            <option value="confirmed" <?= $booking['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                            <option value="cancelled" <?= $booking['status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            <option value="completed" <?= $booking['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary btn-sm">Update</button>
                                    </div>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Booking Statistics -->
        <div class="card">
            <div class="card-header">
                <h3>Booking Statistics</h3>
            </div>
            <div class="card-body">
                <?php if (empty($bookingStats)): ?>
                    <p style="color: var(--text-secondary); text-align: center;">No booking data available</p>
                <?php else: ?>
                    <div class="stats-list">
                        <?php foreach ($bookingStats as $stat): ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 0; border-bottom: 1px solid var(--border-light);">
                                <div>
                                    <strong><?= ucfirst($stat['status']) ?></strong>
                                </div>
                                <div style="color: var(--primary-color); font-weight: 600;">
                                    <?= $stat['count'] ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- User Management -->
    <div class="card">
        <div class="card-header">
            <h3>User Management</h3>
        </div>
        <div class="card-body">
            <?php if (empty($users)): ?>
                <p style="color: var(--text-secondary); text-align: center;">No users found</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Price/Session</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= $user['id'] ?></td>
                                    <td><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></td>
                                    <td>@<?= htmlspecialchars($user['username']) ?></td>
                                    <td><?= htmlspecialchars($user['email']) ?></td>
                                    <td>$<?= number_format($user['price_per_session'], 2) ?></td>
                                    <td>
                                        <span class="status-badge <?= $user['is_active'] ? 'status-confirmed' : 'status-cancelled' ?>">
                                            <?= $user['is_active'] ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 0.5rem;">
                                            <a href="profile.php?id=<?= $user['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                                            
                                            <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="update_user_status">
                                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                    <input type="checkbox" name="is_active" <?= $user['is_active'] ? 'checked' : '' ?> onchange="this.form.submit()" style="margin-right: 0.5rem;">
                                                    <label style="font-size: 0.8rem; color: var(--text-secondary);">Active</label>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- System Information -->
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3>System Information</h3>
        </div>
        <div class="card-body">
            <div class="grid grid-2">
                <div>
                    <h4>Application Details</h4>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Version:</strong> 1.0.0
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Framework:</strong> Core PHP
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Database:</strong> MySQL
                    </div>
                    <div>
                        <strong>PHP Version:</strong> <?= PHP_VERSION ?>
                    </div>
                </div>
                
                <div>
                    <h4>Database Statistics</h4>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Total Tables:</strong> 4
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Users Table:</strong> <?= $stats['total_users'] ?> records
                    </div>
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Bookings Table:</strong> <?= $stats['total_bookings'] ?> records
                    </div>
                    <div>
                        <strong>Last Updated:</strong> <?= date('Y-m-d H:i:s') ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-responsive {
    overflow-x: auto;
}

.grid-4 {
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
}

@media (max-width: 768px) {
    .grid-4 {
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    }
}
</style>

<?php include 'includes/footer.php'; ?>

