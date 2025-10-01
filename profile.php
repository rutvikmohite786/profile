<?php
/**
 * User Profile Page
 * Topmate Clone - Core PHP Application
 */

require_once 'includes/auth.php';
require_once 'config/database.php';

$pageTitle = 'Profile';

// Get user ID from URL or session
$userId = $_GET['id'] ?? ($_SESSION['user_id'] ?? null);

if (!$userId) {
    header('Location: index.php');
    exit();
}

// Get user data
$user = Auth::getUserById($userId);

if (!$user) {
    header('Location: index.php');
    exit();
}

// Get user availability
$availability = fetchAll(
    "SELECT * FROM user_availability WHERE user_id = ? ORDER BY day_of_week, start_time",
    [$userId]
);

// Get recent bookings
$bookings = fetchAll(
    "SELECT b.*, u.first_name, u.last_name FROM bookings b 
     LEFT JOIN users u ON b.user_id = u.id 
     WHERE b.user_id = ? 
     ORDER BY b.created_at DESC 
     LIMIT 5",
    [$userId]
);

// Check if current user is viewing their own profile
$isOwnProfile = Auth::isLoggedIn() && $_SESSION['user_id'] == $userId;

// Handle profile update (only for own profile)
$updateMessage = '';
if ($isOwnProfile && $_POST && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    $updateData = [
        'first_name' => trim($_POST['first_name'] ?? ''),
        'last_name' => trim($_POST['last_name'] ?? ''),
        'bio' => trim($_POST['bio'] ?? ''),
        'expertise' => trim($_POST['expertise'] ?? ''),
        'price_per_session' => floatval($_POST['price_per_session'] ?? 0)
    ];
    
    $result = Auth::updateProfile($userId, $updateData);
    if ($result['success']) {
        $updateMessage = 'Profile updated successfully';
        // Refresh user data
        $user = Auth::getUserById($userId);
    } else {
        $updateMessage = $result['message'];
    }
}

include 'includes/header.php';
?>

<div class="container">
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container">
            <div class="profile-info">
                <div class="profile-image-container">
                    <?php if ($user['profile_image']): ?>
                        <img src="<?= htmlspecialchars($user['profile_image']) ?>" alt="Profile" class="profile-image">
                    <?php else: ?>
                        <div class="profile-image" style="background: rgba(255,255,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 700;">
                            <?= strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="profile-details">
                    <h1><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></h1>
                    <p>@<?= htmlspecialchars($user['username']) ?></p>
                    <?php if ($user['bio']): ?>
                        <p style="margin-top: 1rem; font-size: 1rem;"><?= htmlspecialchars($user['bio']) ?></p>
                    <?php endif; ?>
                    
                    <?php if ($user['expertise']): ?>
                        <div class="skills-list">
                            <?php 
                            $skills = explode(',', $user['expertise']);
                            foreach ($skills as $skill): 
                                $skill = trim($skill);
                                if ($skill):
                            ?>
                                <span class="skill-tag"><?= htmlspecialchars($skill) ?></span>
                            <?php 
                                endif;
                            endforeach; 
                            ?>
                        </div>
                    <?php endif; ?>
                    
                    <div style="margin-top: 2rem;">
                        <div style="display: flex; align-items: center; gap: 2rem;">
                            <?php if ($user['price_per_session'] > 0): ?>
                                <div>
                                    <strong style="font-size: 2rem;">$<?= number_format($user['price_per_session'], 2) ?></strong>
                                    <span style="opacity: 0.8;">/session</span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if (!$isOwnProfile): ?>
                                <a href="#booking-section" class="btn btn-primary" style="background: white; color: var(--primary-color);">
                                    Book a Session
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-2" style="gap: 2rem; margin-bottom: 3rem;">
        <!-- Profile Information -->
        <div class="card">
            <div class="card-header">
                <h3>Profile Information</h3>
            </div>
            <div class="card-body">
                <?php if ($updateMessage): ?>
                    <div class="alert alert-<?= strpos($updateMessage, 'successfully') !== false ? 'success' : 'error' ?>">
                        <?= htmlspecialchars($updateMessage) ?>
                    </div>
                <?php endif; ?>

                <?php if ($isOwnProfile): ?>
                    <!-- Editable Profile Form -->
                    <form method="POST" action="">
                        <input type="hidden" name="action" value="update_profile">
                        
                        <div class="grid grid-2">
                            <div class="form-group">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-input" value="<?= htmlspecialchars($user['first_name']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-input" value="<?= htmlspecialchars($user['last_name']) ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-input" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                            <small style="color: var(--text-light);">Username cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-input" value="<?= htmlspecialchars($user['email']) ?>" disabled>
                            <small style="color: var(--text-light);">Email cannot be changed</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Bio/Description</label>
                            <textarea name="bio" class="form-textarea" rows="4" placeholder="Tell people about yourself and your expertise..."><?= htmlspecialchars($user['bio']) ?></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Areas of Expertise</label>
                            <input type="text" name="expertise" class="form-input" value="<?= htmlspecialchars($user['expertise']) ?>" placeholder="PHP, JavaScript, Web Development...">
                            <small style="color: var(--text-light);">Separate multiple skills with commas</small>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Price per Session ($)</label>
                            <input type="number" name="price_per_session" class="form-input" value="<?= $user['price_per_session'] ?>" min="0" step="0.01">
                        </div>

                        <button type="submit" class="btn btn-primary">Update Profile</button>
                    </form>
                <?php else: ?>
                    <!-- View-only Profile Information -->
                    <div class="profile-details-view">
                        <div style="margin-bottom: 1rem;">
                            <strong>Email:</strong> <?= htmlspecialchars($user['email']) ?>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <strong>Member since:</strong> <?= date('F Y', strtotime($user['created_at'])) ?>
                        </div>
                        <?php if ($user['bio']): ?>
                            <div>
                                <strong>About:</strong>
                                <p style="margin-top: 0.5rem; line-height: 1.6;"><?= nl2br(htmlspecialchars($user['bio'])) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Availability & Recent Bookings -->
        <div>
            <!-- Availability -->
            <div class="card" style="margin-bottom: 2rem;">
                <div class="card-header">
                    <h3>Availability</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($availability)): ?>
                        <p style="color: var(--text-secondary); text-align: center;">No availability set</p>
                    <?php else: ?>
                        <div class="availability-list">
                            <?php 
                            $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                            foreach ($availability as $slot): 
                            ?>
                                <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 0; border-bottom: 1px solid var(--border-light);">
                                    <div>
                                        <strong><?= $days[$slot['day_of_week']] ?></strong>
                                    </div>
                                    <div style="color: var(--text-secondary);">
                                        <?= date('g:i A', strtotime($slot['start_time'])) ?> - <?= date('g:i A', strtotime($slot['end_time'])) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Recent Bookings -->
            <div class="card">
                <div class="card-header">
                    <h3>Recent Bookings</h3>
                </div>
                <div class="card-body">
                    <?php if (empty($bookings)): ?>
                        <p style="color: var(--text-secondary); text-align: center;">No bookings yet</p>
                    <?php else: ?>
                        <div class="bookings-list">
                            <?php foreach ($bookings as $booking): ?>
                                <div style="padding: 1rem; border: 1px solid var(--border-light); border-radius: var(--radius-md); margin-bottom: 1rem;">
                                    <div style="display: flex; justify-content: between; align-items: center; margin-bottom: 0.5rem;">
                                        <strong><?= htmlspecialchars($booking['client_name']) ?></strong>
                                        <span class="status-badge status-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span>
                                    </div>
                                    <div style="color: var(--text-secondary); font-size: 0.9rem;">
                                        <?= date('M j, Y', strtotime($booking['booking_date'])) ?> at <?= date('g:i A', strtotime($booking['booking_time'])) ?>
                                    </div>
                                    <div style="color: var(--text-secondary); font-size: 0.9rem;">
                                        $<?= number_format($booking['total_amount'], 2) ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <?php if (!$isOwnProfile && $user['price_per_session'] > 0): ?>
        <!-- Booking Section -->
        <div id="booking-section" class="card" style="margin-bottom: 3rem; background: linear-gradient(135deg, rgba(99,102,241,0.02), rgba(168,85,247,0.02)); border: 2px solid transparent; background-clip: padding-box;">
            <div class="card-header" style="background: linear-gradient(135deg, #6366f1, #a855f7); color: white; border: none;">
                <h3 style="margin: 0; font-size: 1.5rem;">📅 Book a Session with <?= htmlspecialchars($user['first_name']) ?></h3>
                <p style="margin: 0.5rem 0 0 0; opacity: 0.9; font-size: 0.95rem;">Select your preferred date and time</p>
            </div>
            <div class="card-body" style="padding: 2rem;">
                <form id="bookingForm" method="POST" action="booking.php">
                    <input type="hidden" name="expert_id" value="<?= $user['id'] ?>">
                    <input type="hidden" id="booking_date" name="booking_date" required>
                    <input type="hidden" id="booking_time" name="booking_time" required>
                    
                    <!-- Step Indicator -->
                    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; justify-content: center;">
                        <div id="step1-indicator" class="step-indicator active" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: linear-gradient(135deg, #6366f1, #a855f7); color: white; border-radius: 50px; font-weight: 600; box-shadow: 0 4px 15px rgba(99,102,241,0.3);">
                            <span style="background: white; color: #6366f1; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">1</span>
                            <span>Choose Date</span>
                        </div>
                        <div id="step2-indicator" class="step-indicator" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #e5e7eb; color: #6b7280; border-radius: 50px; font-weight: 600;">
                            <span style="background: white; color: #6b7280; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">2</span>
                            <span>Choose Time</span>
                        </div>
                        <div id="step3-indicator" class="step-indicator" style="display: flex; align-items: center; gap: 0.5rem; padding: 0.75rem 1.5rem; background: #e5e7eb; color: #6b7280; border-radius: 50px; font-weight: 600;">
                            <span style="background: white; color: #6b7280; width: 28px; height: 28px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem;">3</span>
                            <span>Your Info</span>
                        </div>
                    </div>

                    <!-- Step 1: Calendar -->
                    <div id="step1" class="booking-step">
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <h4 style="font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--text-primary);">Select a Date</h4>
                            <p style="color: var(--text-secondary);">Choose from available dates below</p>
                        </div>
                        
                        <!-- Calendar Header -->
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; background: linear-gradient(135deg, rgba(99,102,241,0.05), rgba(168,85,247,0.05)); padding: 1rem 1.5rem; border-radius: var(--radius-lg);">
                            <button type="button" id="prevMonth" class="calendar-nav-btn" style="background: white; border: 2px solid #6366f1; color: #6366f1; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; transition: all 0.3s; font-weight: bold;">‹</button>
                            <div style="text-align: center;">
                                <h4 id="currentMonth" style="margin: 0; font-size: 1.4rem; font-weight: 700; background: linear-gradient(135deg, #6366f1, #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;"></h4>
                            </div>
                            <button type="button" id="nextMonth" class="calendar-nav-btn" style="background: white; border: 2px solid #6366f1; color: #6366f1; width: 40px; height: 40px; border-radius: 50%; cursor: pointer; font-size: 1.2rem; transition: all 0.3s; font-weight: bold;">›</button>
                        </div>

                        <!-- Calendar Grid -->
                        <div id="calendar" style="margin-bottom: 2rem;"></div>
                    </div>

                    <!-- Step 2: Time Slots -->
                    <div id="step2" class="booking-step" style="display: none;">
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <h4 style="font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--text-primary);">Select a Time</h4>
                            <p style="color: var(--text-secondary);">Available time slots for <strong id="selectedDateDisplay"></strong></p>
                        </div>
                        
                        <div id="timeSlots" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(140px, 1fr)); gap: 1rem; margin-bottom: 2rem;"></div>
                        
                        <button type="button" id="backToDate" class="btn btn-secondary" style="width: 100%;">← Back to Date Selection</button>
                    </div>

                    <!-- Step 3: Client Information -->
                    <div id="step3" class="booking-step" style="display: none;">
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <h4 style="font-size: 1.3rem; margin-bottom: 0.5rem; color: var(--text-primary);">Your Information</h4>
                            <p style="color: var(--text-secondary);">Please provide your contact details</p>
                        </div>

                        <div class="grid grid-2" style="margin-bottom: 1.5rem;">
                            <div class="form-group">
                                <label for="client_name" class="form-label">Your Name *</label>
                                <input type="text" id="client_name" name="client_name" class="form-input" required>
                            </div>

                            <div class="form-group">
                                <label for="client_email" class="form-label">Your Email *</label>
                                <input type="email" id="client_email" name="client_email" class="form-input" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="client_phone" class="form-label">Phone Number (Optional)</label>
                            <input type="tel" id="client_phone" name="client_phone" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="notes" class="form-label">Session Notes (Optional)</label>
                            <textarea id="notes" name="notes" class="form-textarea" rows="3" placeholder="What would you like to discuss in this session?"></textarea>
                        </div>

                        <!-- Booking Summary -->
                        <div style="background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(168,85,247,0.1)); padding: 1.5rem; border-radius: var(--radius-lg); margin-bottom: 1.5rem; border: 2px solid rgba(99,102,241,0.2);">
                            <h5 style="margin-bottom: 1rem; color: var(--text-primary); font-size: 1.1rem;">📋 Booking Summary</h5>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span style="color: var(--text-secondary);">Expert:</span>
                                <strong><?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span style="color: var(--text-secondary);">Date:</span>
                                <strong id="summaryDate">-</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span style="color: var(--text-secondary);">Time:</span>
                                <strong id="summaryTime">-</strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                                <span style="color: var(--text-secondary);">Duration:</span>
                                <strong>60 minutes</strong>
                            </div>
                            <hr style="border: none; border-top: 1px solid rgba(99,102,241,0.2); margin: 1rem 0;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <span style="font-size: 1.1rem; font-weight: 600;">Total Amount:</span>
                                <span style="font-size: 1.75rem; color: #6366f1; font-weight: 800;">$<?= number_format($user['price_per_session'], 2) ?></span>
                            </div>
                        </div>

                        <div style="display: flex; gap: 1rem;">
                            <button type="button" id="backToTime" class="btn btn-secondary" style="flex: 1;">← Back to Time</button>
                            <button type="submit" class="btn btn-primary" style="flex: 2; background: linear-gradient(135deg, #6366f1, #a855f7); border: none; font-size: 1.05rem; padding: 1rem;">
                                🎉 Confirm Booking
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
/* Calendar Styles */
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 0.5rem;
}

.calendar-day-header {
    text-align: center;
    font-weight: 700;
    padding: 1rem 0.5rem;
    color: #6366f1;
    font-size: 0.9rem;
    background: linear-gradient(135deg, rgba(99,102,241,0.1), rgba(168,85,247,0.1));
    border-radius: var(--radius-md);
}

.calendar-day {
    aspect-ratio: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-lg);
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    font-weight: 600;
    background: white;
    position: relative;
    overflow: hidden;
}

.calendar-day::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    opacity: 0;
    transition: opacity 0.3s;
}

.calendar-day span {
    position: relative;
    z-index: 1;
}

.calendar-day:hover:not(.disabled):not(.past) {
    transform: scale(1.1);
    border-color: #6366f1;
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
}

.calendar-day:hover:not(.disabled):not(.past)::before {
    opacity: 0.1;
}

.calendar-day.selected {
    background: linear-gradient(135deg, #6366f1, #a855f7);
    color: white;
    border-color: transparent;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    transform: scale(1.05);
}

.calendar-day.disabled, .calendar-day.past {
    background: #f3f4f6;
    color: #d1d5db;
    cursor: not-allowed;
    border-color: #f3f4f6;
}

.calendar-day.today {
    border-color: #ec4899;
    border-width: 3px;
}

.time-slot-btn {
    padding: 1rem;
    border: 2px solid #e5e7eb;
    border-radius: var(--radius-lg);
    background: white;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    text-align: center;
    font-weight: 600;
    color: var(--text-primary);
    position: relative;
    overflow: hidden;
}

.time-slot-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #6366f1, #a855f7);
    transition: left 0.3s;
}

.time-slot-btn span {
    position: relative;
    z-index: 1;
    display: block;
}

.time-slot-btn:hover {
    border-color: #6366f1;
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(99, 102, 241, 0.3);
}

.time-slot-btn.selected {
    background: linear-gradient(135deg, #6366f1, #a855f7);
    color: white;
    border-color: transparent;
    box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
}

.time-slot-btn.selected::before {
    left: 0;
}

.calendar-nav-btn:hover {
    background: linear-gradient(135deg, #6366f1, #a855f7) !important;
    color: white !important;
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
}

/* Step transition animation */
.booking-step {
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
// Booking Calendar & Form functionality
let currentDate = new Date();
let selectedDate = null;
let selectedTime = null;

document.addEventListener('DOMContentLoaded', function() {
    const calendar = document.getElementById('calendar');
    if (calendar) {
        renderCalendar();
        
        // Month navigation
        document.getElementById('prevMonth')?.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() - 1);
            renderCalendar();
        });
        
        document.getElementById('nextMonth')?.addEventListener('click', () => {
            currentDate.setMonth(currentDate.getMonth() + 1);
            renderCalendar();
        });
        
        // Step navigation
        document.getElementById('backToDate')?.addEventListener('click', () => showStep(1));
        document.getElementById('backToTime')?.addEventListener('click', () => showStep(2));
    }
});

function renderCalendar() {
    const calendar = document.getElementById('calendar');
    const monthDisplay = document.getElementById('currentMonth');
    
    const year = currentDate.getFullYear();
    const month = currentDate.getMonth();
    
    // Display month and year
    const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'];
    monthDisplay.textContent = `${monthNames[month]} ${year}`;
    
    // Get first day of month and number of days
    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    // Create calendar HTML
    let html = '<div class="calendar-grid">';
    
    // Day headers
    const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
    dayNames.forEach(day => {
        html += `<div class="calendar-day-header">${day}</div>`;
    });
    
    // Empty cells for days before month starts
    for (let i = 0; i < firstDay; i++) {
        html += '<div class="calendar-day disabled"><span></span></div>';
    }
    
    // Days of the month
    for (let day = 1; day <= daysInMonth; day++) {
        const date = new Date(year, month, day);
        date.setHours(0, 0, 0, 0);
        const isPast = date < today;
        const isToday = date.getTime() === today.getTime();
        const dateStr = formatDateForInput(date);
        const isSelected = selectedDate === dateStr;
        
        let classes = 'calendar-day';
        if (isPast) classes += ' past';
        if (isToday) classes += ' today';
        if (isSelected) classes += ' selected';
        
        html += `<div class="${classes}" onclick="selectDate('${dateStr}', this)" data-date="${dateStr}">
            <span>${day}</span>
        </div>`;
    }
    
    html += '</div>';
    calendar.innerHTML = html;
}

function selectDate(dateStr, element) {
    if (element.classList.contains('past') || element.classList.contains('disabled')) {
        return;
    }
    
    selectedDate = dateStr;
    document.getElementById('booking_date').value = dateStr;
    
    // Update selected state
    document.querySelectorAll('.calendar-day').forEach(day => {
        day.classList.remove('selected');
    });
    element.classList.add('selected');
    
    // Format and display date
    const date = new Date(dateStr);
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    document.getElementById('selectedDateDisplay').textContent = date.toLocaleDateString('en-US', options);
    document.getElementById('summaryDate').textContent = date.toLocaleDateString('en-US', options);
    
    // Load time slots and move to step 2
    setTimeout(() => {
        loadTimeSlots();
        showStep(2);
    }, 300);
}

function loadTimeSlots() {
    const timeSlotsContainer = document.getElementById('timeSlots');
    timeSlotsContainer.innerHTML = '';
    
    // Generate time slots (9 AM to 5 PM, 1-hour intervals)
    const timeSlots = [
        '09:00', '10:00', '11:00', '12:00',
        '13:00', '14:00', '15:00', '16:00', '17:00'
    ];
    
    timeSlots.forEach(time => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'time-slot-btn';
        btn.innerHTML = `
            <span style="display: block; font-size: 1.3rem; margin-bottom: 0.25rem;">🕐</span>
            <span style="font-size: 1rem;">${formatTime(time)}</span>
        `;
        btn.onclick = () => selectTime(time, btn);
        timeSlotsContainer.appendChild(btn);
    });
}

function selectTime(time, element) {
    selectedTime = time;
    document.getElementById('booking_time').value = time;
    document.getElementById('summaryTime').textContent = formatTime(time);
    
    // Update selected state
    document.querySelectorAll('.time-slot-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    element.classList.add('selected');
    
    // Move to step 3
    setTimeout(() => showStep(3), 300);
}

function showStep(stepNumber) {
    // Hide all steps
    for (let i = 1; i <= 3; i++) {
        const step = document.getElementById(`step${i}`);
        const indicator = document.getElementById(`step${i}-indicator`);
        if (step) step.style.display = 'none';
        
        if (indicator) {
            if (i <= stepNumber) {
                indicator.style.background = 'linear-gradient(135deg, #6366f1, #a855f7)';
                indicator.style.color = 'white';
                indicator.style.boxShadow = '0 4px 15px rgba(99,102,241,0.3)';
            } else {
                indicator.style.background = '#e5e7eb';
                indicator.style.color = '#6b7280';
                indicator.style.boxShadow = 'none';
            }
        }
    }
    
    // Show current step
    const currentStep = document.getElementById(`step${stepNumber}`);
    if (currentStep) {
        currentStep.style.display = 'block';
    }
}

function formatDateForInput(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}

function formatTime(time) {
    const [hours, minutes] = time.split(':');
    const hour = parseInt(hours);
    const ampm = hour >= 12 ? 'PM' : 'AM';
    const displayHour = hour % 12 || 12;
    return `${displayHour}:${minutes} ${ampm}`;
}
</script>

<?php include 'includes/footer.php'; ?>

