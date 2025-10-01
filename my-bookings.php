<?php
/**
 * My Bookings Page
 * Topmate Clone - Core PHP Application
 */

require_once 'includes/auth.php';
require_once 'config/database.php';

$pageTitle = 'My Bookings';

// Require login
requireLogin();

$userId = $_SESSION['user_id'];

// Get user's bookings (both as expert and client)
$bookings = fetchAll(
    "SELECT b.*, u.first_name, u.last_name, u.username, u.email as expert_email,
            CASE 
                WHEN b.user_id = ? THEN 'as_expert'
                ELSE 'as_client'
            END as booking_type
     FROM bookings b 
     JOIN users u ON b.user_id = u.id 
     WHERE b.user_id = ? OR b.client_email = ?
     ORDER BY b.created_at DESC",
    [$userId, $userId, $_SESSION['email']]
);

// Handle booking actions
$actionMessage = '';

if ($_POST) {
    $action = $_POST['action'] ?? '';
    $bookingId = intval($_POST['booking_id'] ?? 0);
    
    if ($bookingId) {
        switch ($action) {
            case 'cancel_booking':
                // Only allow cancellation if booking is pending or confirmed
                $booking = fetchOne("SELECT status FROM bookings WHERE id = ?", [$bookingId]);
                if ($booking && in_array($booking['status'], ['pending', 'confirmed'])) {
                    executeQuery("UPDATE bookings SET status = 'cancelled' WHERE id = ?", [$bookingId]);
                    $actionMessage = 'Booking cancelled successfully';
                }
                break;
                
            case 'mark_completed':
                // Only allow if user is the expert
                $booking = fetchOne("SELECT user_id, status FROM bookings WHERE id = ?", [$bookingId]);
                if ($booking && $booking['user_id'] == $userId && $booking['status'] == 'confirmed') {
                    executeQuery("UPDATE bookings SET status = 'completed' WHERE id = ?", [$bookingId]);
                    $actionMessage = 'Booking marked as completed';
                }
                break;
        }
    }
}

include 'includes/header.php';
?>

<div class="container">
    <div style="margin-bottom: 2rem;">
        <h1 style="color: var(--text-primary); margin-bottom: 0.5rem;">My Bookings</h1>
        <p style="color: var(--text-secondary);">View and manage your session bookings</p>
    </div>

    <?php if ($actionMessage): ?>
        <div class="alert alert-success"><?= htmlspecialchars($actionMessage) ?></div>
    <?php endif; ?>

    <?php if (empty($bookings)): ?>
        <div class="card">
            <div class="card-body" style="text-align: center; padding: 3rem;">
                <div style="color: var(--text-light); font-size: 3rem; margin-bottom: 1rem;">📅</div>
                <h3 style="color: var(--text-secondary); margin-bottom: 1rem;">No Bookings Yet</h3>
                <p style="color: var(--text-light); margin-bottom: 2rem;">
                    You haven't made any bookings yet. Browse our experts and book your first session!
                </p>
                <a href="index.php" class="btn btn-primary">Browse Experts</a>
            </div>
        </div>
    <?php else: ?>
        <!-- Booking Filters -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-body">
                <div style="display: flex; gap: 1rem; align-items: center; flex-wrap: wrap;">
                    <strong>Filter by:</strong>
                    <button class="btn btn-secondary btn-sm filter-btn active" data-filter="all">All</button>
                    <button class="btn btn-secondary btn-sm filter-btn" data-filter="as_expert">As Expert</button>
                    <button class="btn btn-secondary btn-sm filter-btn" data-filter="as_client">As Client</button>
                    <button class="btn btn-secondary btn-sm filter-btn" data-filter="pending">Pending</button>
                    <button class="btn btn-secondary btn-sm filter-btn" data-filter="confirmed">Confirmed</button>
                    <button class="btn btn-secondary btn-sm filter-btn" data-filter="completed">Completed</button>
                    <button class="btn btn-secondary btn-sm filter-btn" data-filter="cancelled">Cancelled</button>
                </div>
            </div>
        </div>

        <!-- Bookings List -->
        <div class="bookings-container">
            <?php foreach ($bookings as $booking): ?>
                <div class="card booking-card" style="margin-bottom: 1.5rem;" data-booking-type="<?= $booking['booking_type'] ?>" data-status="<?= $booking['status'] ?>">
                    <div class="card-body">
                        <div class="grid grid-2">
                            <div>
                                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                                    <h3 style="margin: 0; color: var(--text-primary);">
                                        <?= $booking['booking_type'] === 'as_expert' ? 'Session with ' . htmlspecialchars($booking['client_name']) : 'Session with ' . htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?>
                                    </h3>
                                    <span class="status-badge status-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span>
                                </div>
                                
                                <div style="margin-bottom: 0.5rem;">
                                    <strong>Date:</strong> <?= date('F j, Y', strtotime($booking['booking_date'])) ?>
                                </div>
                                <div style="margin-bottom: 0.5rem;">
                                    <strong>Time:</strong> <?= date('g:i A', strtotime($booking['booking_time'])) ?>
                                </div>
                                <div style="margin-bottom: 0.5rem;">
                                    <strong>Duration:</strong> <?= $booking['duration'] ?> minutes
                                </div>
                                <div style="margin-bottom: 0.5rem;">
                                    <strong>Type:</strong> 
                                    <span style="color: var(--primary-color); font-weight: 600;">
                                        <?= $booking['booking_type'] === 'as_expert' ? 'Expert Session' : 'Client Session' ?>
                                    </span>
                                </div>
                                
                                <?php if ($booking['notes']): ?>
                                    <div style="margin-top: 1rem;">
                                        <strong>Notes:</strong>
                                        <p style="margin-top: 0.5rem; color: var(--text-secondary); font-style: italic;">
                                            "<?= htmlspecialchars($booking['notes']) ?>"
                                        </p>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div>
                                <div style="text-align: right; margin-bottom: 1rem;">
                                    <div style="font-size: 1.5rem; font-weight: 700; color: var(--primary-color);">
                                        $<?= number_format($booking['total_amount'], 2) ?>
                                    </div>
                                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Total Amount</div>
                                </div>
                                
                                <?php if ($booking['booking_type'] === 'as_expert'): ?>
                                    <div style="margin-bottom: 1rem;">
                                        <strong>Client Information:</strong>
                                        <div style="margin-top: 0.5rem;">
                                            <div><?= htmlspecialchars($booking['client_name']) ?></div>
                                            <div style="color: var(--text-secondary); font-size: 0.9rem;"><?= htmlspecialchars($booking['client_email']) ?></div>
                                            <?php if ($booking['client_phone']): ?>
                                                <div style="color: var(--text-secondary); font-size: 0.9rem;"><?= htmlspecialchars($booking['client_phone']) ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div style="margin-bottom: 1rem;">
                                        <strong>Expert Information:</strong>
                                        <div style="margin-top: 0.5rem;">
                                            <div><?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?></div>
                                            <div style="color: var(--text-secondary); font-size: 0.9rem;">@<?= htmlspecialchars($booking['username']) ?></div>
                                            <div style="color: var(--text-secondary); font-size: 0.9rem;"><?= htmlspecialchars($booking['expert_email']) ?></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                
                                <!-- Action Buttons -->
                                <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                    <?php if ($booking['status'] === 'pending' || $booking['status'] === 'confirmed'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="cancel_booking">
                                            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to cancel this booking?')">
                                                Cancel
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <?php if ($booking['booking_type'] === 'as_expert' && $booking['status'] === 'confirmed'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="mark_completed">
                                            <input type="hidden" name="booking_id" value="<?= $booking['id'] ?>">
                                            <button type="submit" class="btn btn-success btn-sm">
                                                Mark Completed
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <a href="profile.php?id=<?= $booking['user_id'] ?>" class="btn btn-secondary btn-sm">
                                        View Profile
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border-light); color: var(--text-light); font-size: 0.9rem;">
                            Booking created: <?= date('F j, Y \a\t g:i A', strtotime($booking['created_at'])) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<script>
// Booking filters
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.filter-btn');
    const bookingCards = document.querySelectorAll('.booking-card');
    
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            // Update active button
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');
            
            // Filter bookings
            bookingCards.forEach(card => {
                const bookingType = card.dataset.bookingType;
                const status = card.dataset.status;
                
                let show = false;
                
                if (filter === 'all') {
                    show = true;
                } else if (filter === 'as_expert') {
                    show = bookingType === 'as_expert';
                } else if (filter === 'as_client') {
                    show = bookingType === 'as_client';
                } else {
                    show = status === filter;
                }
                
                if (show) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
});
</script>

<style>
.filter-btn.active {
    background-color: var(--primary-color);
    color: white;
}

.booking-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.booking-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
</style>

<?php include 'includes/footer.php'; ?>

