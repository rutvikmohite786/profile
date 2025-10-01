<?php
/**
 * Booking Processing Page
 * Topmate Clone - Core PHP Application
 */

require_once 'includes/auth.php';
require_once 'config/database.php';

$pageTitle = 'Book Session';
$error = '';
$success = '';

// Handle booking form submission
if ($_POST) {
    $expertId = intval($_POST['expert_id'] ?? 0);
    $bookingDate = $_POST['booking_date'] ?? '';
    $bookingTime = $_POST['booking_time'] ?? '';
    $clientName = trim($_POST['client_name'] ?? '');
    $clientEmail = trim($_POST['client_email'] ?? '');
    $clientPhone = trim($_POST['client_phone'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    
    // Validation
    $errors = [];
    
    if (!$expertId) {
        $errors[] = 'Expert ID is required';
    }
    
    if (empty($bookingDate)) {
        $errors[] = 'Booking date is required';
    } elseif (strtotime($bookingDate) < strtotime('today')) {
        $errors[] = 'Booking date cannot be in the past';
    }
    
    if (empty($bookingTime)) {
        $errors[] = 'Booking time is required';
    }
    
    if (empty($clientName)) {
        $errors[] = 'Client name is required';
    }
    
    if (empty($clientEmail)) {
        $errors[] = 'Client email is required';
    } elseif (!filter_var($clientEmail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address';
    }
    
    if (!empty($errors)) {
        $error = implode('<br>', $errors);
    } else {
        try {
            // Get expert details
            $expert = Auth::getUserById($expertId);
            
            if (!$expert) {
                $error = 'Expert not found';
            } else {
                // Check for existing booking at same time
                $existingBooking = fetchOne(
                    "SELECT id FROM bookings WHERE user_id = ? AND booking_date = ? AND booking_time = ? AND status != 'cancelled'",
                    [$expertId, $bookingDate, $bookingTime]
                );
                
                if ($existingBooking) {
                    $error = 'This time slot is already booked. Please choose another time.';
                } else {
                    // Create booking
                    $totalAmount = $expert['price_per_session'];
                    $duration = 60; // Default 60 minutes
                    
                    $sql = "INSERT INTO bookings (user_id, client_name, client_email, client_phone, booking_date, booking_time, duration, total_amount, notes, status) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
                    
                    executeQuery($sql, [
                        $expertId, $clientName, $clientEmail, $clientPhone, $bookingDate, $bookingTime, $duration, $totalAmount, $notes
                    ]);
                    
                    $bookingId = getLastInsertId();
                    
                    // Create payment record
                    $paymentSql = "INSERT INTO payments (booking_id, amount, payment_method, status) VALUES (?, ?, 'pending', 'pending')";
                    executeQuery($paymentSql, [$bookingId, $totalAmount]);
                    
                    // Redirect to payment page
                    header('Location: payment.php?booking_id=' . $bookingId);
                    exit();
                }
            }
        } catch (Exception $e) {
            $error = 'Booking failed: ' . $e->getMessage();
        }
    }
}

// If no POST data, redirect to home
if (!$_POST) {
    header('Location: index.php');
    exit();
}

include 'includes/header.php';
?>

<div class="container-sm">
    <div style="max-width: 600px; margin: 4rem auto;">
        <?php if ($error): ?>
            <div class="card">
                <div class="card-body" style="text-align: center;">
                    <div style="color: var(--error-color); font-size: 3rem; margin-bottom: 1rem;">⚠️</div>
                    <h2 style="color: var(--error-color); margin-bottom: 1rem;">Booking Failed</h2>
                    <div class="alert alert-error"><?= $error ?></div>
                    <div style="margin-top: 2rem;">
                        <a href="javascript:history.back()" class="btn btn-primary">Go Back</a>
                        <a href="index.php" class="btn btn-secondary">Home</a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="card">
                <div class="card-body" style="text-align: center;">
                    <div style="color: var(--success-color); font-size: 3rem; margin-bottom: 1rem;">✅</div>
                    <h2 style="color: var(--success-color); margin-bottom: 1rem;">Booking Created</h2>
                    <div class="alert alert-success">
                        Your booking request has been created successfully! Please complete the payment to confirm your session.
                    </div>
                    <div style="margin-top: 2rem;">
                        <a href="payment.php?booking_id=<?= $bookingId ?? '' ?>" class="btn btn-primary">Proceed to Payment</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

