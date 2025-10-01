<?php
/**
 * Payment Processing Page
 * Topmate Clone - Core PHP Application
 * Simulates Razorpay/Stripe payment integration
 */

require_once 'includes/auth.php';
require_once 'config/database.php';

$pageTitle = 'Payment';
$error = '';
$success = '';

// Get booking ID from URL
$bookingId = intval($_GET['booking_id'] ?? 0);

if (!$bookingId) {
    header('Location: index.php');
    exit();
}

// Get booking details
$booking = fetchOne(
    "SELECT b.*, u.first_name, u.last_name, u.username, u.email as expert_email 
     FROM bookings b 
     JOIN users u ON b.user_id = u.id 
     WHERE b.id = ?",
    [$bookingId]
);

if (!$booking) {
    header('Location: index.php');
    exit();
}

// Get payment details
$payment = fetchOne(
    "SELECT * FROM payments WHERE booking_id = ? ORDER BY created_at DESC LIMIT 1",
    [$bookingId]
);

// Handle payment form submission
if ($_POST && isset($_POST['payment_method'])) {
    $paymentMethod = $_POST['payment_method'];
    $transactionId = 'TXN_' . time() . '_' . rand(1000, 9999);
    
    try {
        // Update payment record
        $paymentSql = "UPDATE payments SET payment_method = ?, transaction_id = ?, status = 'completed', payment_date = NOW() WHERE id = ?";
        executeQuery($paymentSql, [$paymentMethod, $transactionId, $payment['id']]);
        
        // Update booking status
        $bookingSql = "UPDATE bookings SET status = 'confirmed' WHERE id = ?";
        executeQuery($bookingSql, [$bookingId]);
        
        $success = 'Payment completed successfully! Your session has been confirmed.';
        
    } catch (Exception $e) {
        $error = 'Payment processing failed: ' . $e->getMessage();
    }
}

include 'includes/header.php';
?>

<div class="container-sm">
    <div style="max-width: 800px; margin: 2rem auto;">
        <!-- Booking Summary -->
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-header">
                <h3>Booking Summary</h3>
            </div>
            <div class="card-body">
                <div class="grid grid-2">
                    <div>
                        <h4>Session Details</h4>
                        <div style="margin-bottom: 1rem;">
                            <strong>Expert:</strong> <?= htmlspecialchars($booking['first_name'] . ' ' . $booking['last_name']) ?>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <strong>Date:</strong> <?= date('F j, Y', strtotime($booking['booking_date'])) ?>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <strong>Time:</strong> <?= date('g:i A', strtotime($booking['booking_time'])) ?>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <strong>Duration:</strong> <?= $booking['duration'] ?> minutes
                        </div>
                    </div>
                    
                    <div>
                        <h4>Client Information</h4>
                        <div style="margin-bottom: 1rem;">
                            <strong>Name:</strong> <?= htmlspecialchars($booking['client_name']) ?>
                        </div>
                        <div style="margin-bottom: 1rem;">
                            <strong>Email:</strong> <?= htmlspecialchars($booking['client_email']) ?>
                        </div>
                        <?php if ($booking['client_phone']): ?>
                            <div style="margin-bottom: 1rem;">
                                <strong>Phone:</strong> <?= htmlspecialchars($booking['client_phone']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php if ($booking['notes']): ?>
                    <div style="margin-top: 1rem; padding: 1rem; background-color: var(--secondary-color); border-radius: var(--radius-md);">
                        <strong>Session Notes:</strong>
                        <p style="margin-top: 0.5rem;"><?= nl2br(htmlspecialchars($booking['notes'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Section -->
        <div class="card">
            <div class="card-header">
                <h3>Complete Payment</h3>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                    <div style="text-align: center; margin-top: 2rem;">
                        <a href="index.php" class="btn btn-primary">Return to Home</a>
                    </div>
                <?php else: ?>
                    <!-- Payment Amount -->
                    <div style="background-color: var(--secondary-color); padding: 1.5rem; border-radius: var(--radius-md); margin-bottom: 2rem; text-align: center;">
                        <h2 style="margin: 0; color: var(--text-primary);">Total Amount</h2>
                        <div style="font-size: 2.5rem; font-weight: 700; color: var(--primary-color); margin: 0.5rem 0;">
                            $<?= number_format($booking['total_amount'], 2) ?>
                        </div>
                        <p style="margin: 0; color: var(--text-secondary);">USD</p>
                    </div>

                    <!-- Payment Methods -->
                    <form method="POST" action="" id="paymentForm">
                        <h4 style="margin-bottom: 1rem;">Select Payment Method</h4>
                        
                        <div style="display: grid; gap: 1rem; margin-bottom: 2rem;">
                            <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--border-color); border-radius: var(--radius-md); cursor: pointer; transition: border-color 0.2s;">
                                <input type="radio" name="payment_method" value="credit_card" style="margin-right: 1rem;" required>
                                <div>
                                    <strong>Credit/Debit Card</strong>
                                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Visa, Mastercard, American Express</div>
                                </div>
                            </label>
                            
                            <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--border-color); border-radius: var(--radius-md); cursor: pointer; transition: border-color 0.2s;">
                                <input type="radio" name="payment_method" value="paypal" style="margin-right: 1rem;" required>
                                <div>
                                    <strong>PayPal</strong>
                                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Pay with your PayPal account</div>
                                </div>
                            </label>
                            
                            <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--border-color); border-radius: var(--radius-md); cursor: pointer; transition: border-color 0.2s;">
                                <input type="radio" name="payment_method" value="bank_transfer" style="margin-right: 1rem;" required>
                                <div>
                                    <strong>Bank Transfer</strong>
                                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Direct bank transfer</div>
                                </div>
                            </label>
                            
                            <label style="display: flex; align-items: center; padding: 1rem; border: 2px solid var(--border-color); border-radius: var(--radius-md); cursor: pointer; transition: border-color 0.2s;">
                                <input type="radio" name="payment_method" value="demo" style="margin-right: 1rem;" required>
                                <div>
                                    <strong>Demo Payment (For Testing)</strong>
                                    <div style="color: var(--text-secondary); font-size: 0.9rem;">Simulate payment for testing purposes</div>
                                </div>
                            </label>
                        </div>

                        <!-- Security Notice -->
                        <div style="background-color: #f0f9ff; border: 1px solid #0ea5e9; border-radius: var(--radius-md); padding: 1rem; margin-bottom: 2rem;">
                            <div style="display: flex; align-items: center; gap: 0.5rem; color: #0c4a6e;">
                                <span style="font-size: 1.2rem;">🔒</span>
                                <div>
                                    <strong>Secure Payment</strong>
                                    <div style="font-size: 0.9rem; margin-top: 0.25rem;">Your payment information is encrypted and secure. We use industry-standard security measures.</div>
                                </div>
                            </div>
                        </div>

                        <!-- Terms and Conditions -->
                        <div style="margin-bottom: 2rem;">
                            <label style="display: flex; align-items: flex-start; gap: 0.5rem; cursor: pointer;">
                                <input type="checkbox" id="terms" required style="margin-top: 0.25rem;">
                                <div style="font-size: 0.9rem; color: var(--text-secondary);">
                                    I agree to the <a href="#" style="color: var(--primary-color);">Terms of Service</a> and <a href="#" style="color: var(--primary-color);">Privacy Policy</a>. 
                                    I understand that payments are non-refundable unless the session is cancelled by the expert.
                                </div>
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">
                            Complete Payment - $<?= number_format($booking['total_amount'], 2) ?>
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- Payment Status -->
        <div class="card" style="margin-top: 2rem;">
            <div class="card-header">
                <h3>Payment Status</h3>
            </div>
            <div class="card-body">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <strong>Booking Status:</strong>
                        <span class="status-badge status-<?= $booking['status'] ?>"><?= ucfirst($booking['status']) ?></span>
                    </div>
                    <div>
                        <strong>Payment Status:</strong>
                        <span class="status-badge status-<?= $payment['status'] ?? 'pending' ?>"><?= ucfirst($payment['status'] ?? 'pending') ?></span>
                    </div>
                </div>
                
                <?php if ($payment && $payment['transaction_id']): ?>
                    <div style="margin-top: 1rem; padding: 1rem; background-color: var(--secondary-color); border-radius: var(--radius-md);">
                        <strong>Transaction ID:</strong> <?= htmlspecialchars($payment['transaction_id']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
// Payment form enhancements
document.addEventListener('DOMContentLoaded', function() {
    const paymentForm = document.getElementById('paymentForm');
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    
    // Style payment method selection
    paymentMethods.forEach(method => {
        method.addEventListener('change', function() {
            // Remove selected class from all labels
            document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
                radio.closest('label').style.borderColor = 'var(--border-color)';
            });
            
            // Add selected class to current label
            if (this.checked) {
                this.closest('label').style.borderColor = 'var(--primary-color)';
            }
        });
    });
    
    // Form submission confirmation
    if (paymentForm) {
        paymentForm.addEventListener('submit', function(e) {
            const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
            const termsAccepted = document.getElementById('terms').checked;
            
            if (!selectedMethod) {
                e.preventDefault();
                alert('Please select a payment method');
                return false;
            }
            
            if (!termsAccepted) {
                e.preventDefault();
                alert('Please accept the terms and conditions');
                return false;
            }
            
            // Show loading state
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Processing Payment...';
            submitBtn.disabled = true;
            
            // Re-enable button after 3 seconds (in case of error)
            setTimeout(() => {
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            }, 3000);
        });
    }
});
</script>

<?php include 'includes/footer.php'; ?>

