/**
 * Main JavaScript File
 * Topmate Clone - Core PHP Application
 */

// Utility Functions
const Utils = {
    // Show notification
    showNotification: function(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type}`;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            max-width: 400px;
            animation: slideIn 0.3s ease-out;
        `;
        notification.textContent = message;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-in';
            setTimeout(() => notification.remove(), 300);
        }, 3000);
    },

    // Format currency
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },

    // Format date
    formatDate: function(dateString) {
        return new Date(dateString).toLocaleDateString('en-US', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    },

    // Format time
    formatTime: function(timeString) {
        const [hours, minutes] = timeString.split(':');
        const hour = parseInt(hours);
        const ampm = hour >= 12 ? 'PM' : 'AM';
        const displayHour = hour % 12 || 12;
        return `${displayHour}:${minutes} ${ampm}`;
    },

    // Validate email
    validateEmail: function(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    },

    // Validate phone
    validatePhone: function(phone) {
        const re = /^[\+]?[1-9][\d]{0,15}$/;
        return re.test(phone.replace(/\s/g, ''));
    },

    // Debounce function
    debounce: function(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
};

// Form Validation
const FormValidator = {
    // Validate registration form
    validateRegistration: function(form) {
        const errors = [];
        
        const username = form.querySelector('input[name="username"]').value.trim();
        const email = form.querySelector('input[name="email"]').value.trim();
        const password = form.querySelector('input[name="password"]').value;
        const confirmPassword = form.querySelector('input[name="confirm_password"]').value;
        const firstName = form.querySelector('input[name="first_name"]').value.trim();
        const lastName = form.querySelector('input[name="last_name"]').value.trim();
        
        if (username.length < 3) {
            errors.push('Username must be at least 3 characters long');
        }
        
        if (!/^[a-zA-Z0-9_]+$/.test(username)) {
            errors.push('Username can only contain letters, numbers, and underscores');
        }
        
        if (!Utils.validateEmail(email)) {
            errors.push('Please enter a valid email address');
        }
        
        if (password.length < 6) {
            errors.push('Password must be at least 6 characters long');
        }
        
        if (password !== confirmPassword) {
            errors.push('Passwords do not match');
        }
        
        if (firstName.length < 2) {
            errors.push('First name must be at least 2 characters long');
        }
        
        if (lastName.length < 2) {
            errors.push('Last name must be at least 2 characters long');
        }
        
        return errors;
    },

    // Validate login form
    validateLogin: function(form) {
        const errors = [];
        
        const username = form.querySelector('input[name="username"]').value.trim();
        const password = form.querySelector('input[name="password"]').value;
        
        if (username.length < 3) {
            errors.push('Username must be at least 3 characters long');
        }
        
        if (password.length < 6) {
            errors.push('Password must be at least 6 characters long');
        }
        
        return errors;
    },

    // Validate booking form
    validateBooking: function(form) {
        const errors = [];
        
        const bookingDate = form.querySelector('input[name="booking_date"]').value;
        const bookingTime = form.querySelector('select[name="booking_time"]').value;
        const clientName = form.querySelector('input[name="client_name"]').value.trim();
        const clientEmail = form.querySelector('input[name="client_email"]').value.trim();
        
        if (!bookingDate) {
            errors.push('Please select a booking date');
        } else {
            const selectedDate = new Date(bookingDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                errors.push('Booking date cannot be in the past');
            }
        }
        
        if (!bookingTime) {
            errors.push('Please select a time slot');
        }
        
        if (clientName.length < 2) {
            errors.push('Please enter your full name');
        }
        
        if (!Utils.validateEmail(clientEmail)) {
            errors.push('Please enter a valid email address');
        }
        
        return errors;
    },

    // Show form errors
    showErrors: function(form, errors) {
        // Remove existing error messages
        form.querySelectorAll('.form-error').forEach(error => error.remove());
        
        // Add new error messages
        errors.forEach(error => {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'form-error';
            errorDiv.textContent = error;
            form.appendChild(errorDiv);
        });
        
        // Focus on first invalid field
        if (errors.length > 0) {
            const firstInvalidField = form.querySelector('input:invalid, select:invalid, textarea:invalid');
            if (firstInvalidField) {
                firstInvalidField.focus();
            }
        }
    }
};

// Booking System
const BookingSystem = {
    // Load available time slots for selected date
    loadTimeSlots: function(date, expertId) {
        const timeSelect = document.getElementById('booking_time');
        if (!timeSelect) return;
        
        // Clear existing options
        timeSelect.innerHTML = '<option value="">Loading available slots...</option>';
        
        // Simulate API call (in real app, this would be an AJAX request)
        setTimeout(() => {
            timeSelect.innerHTML = '<option value="">Choose a time slot</option>';
            
            // Generate time slots (9 AM to 5 PM, 1-hour intervals)
            const startHour = 9;
            const endHour = 17;
            
            for (let hour = startHour; hour < endHour; hour++) {
                const time = hour.toString().padStart(2, '0') + ':00';
                const option = document.createElement('option');
                option.value = time;
                option.textContent = Utils.formatTime(time);
                
                // Randomly mark some slots as booked (for demo)
                if (Math.random() < 0.3) {
                    option.disabled = true;
                    option.textContent += ' (Booked)';
                }
                
                timeSelect.appendChild(option);
            }
        }, 500);
    },

    // Initialize booking form
    init: function() {
        const dateInput = document.getElementById('booking_date');
        const timeSelect = document.getElementById('booking_time');
        
        if (dateInput && timeSelect) {
            // Set minimum date to today
            const today = new Date().toISOString().split('T')[0];
            dateInput.min = today;
            
            // Handle date change
            dateInput.addEventListener('change', function() {
                const selectedDate = this.value;
                const expertId = document.querySelector('input[name="expert_id"]')?.value;
                
                if (selectedDate && expertId) {
                    BookingSystem.loadTimeSlots(selectedDate, expertId);
                }
            });
        }
    }
};

// Payment System
const PaymentSystem = {
    // Initialize payment form
    init: function() {
        const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
        
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
        
        // Handle form submission
        const paymentForm = document.getElementById('paymentForm');
        if (paymentForm) {
            paymentForm.addEventListener('submit', function(e) {
                const selectedMethod = document.querySelector('input[name="payment_method"]:checked');
                const termsAccepted = document.getElementById('terms');
                
                if (!selectedMethod) {
                    e.preventDefault();
                    Utils.showNotification('Please select a payment method', 'error');
                    return false;
                }
                
                if (termsAccepted && !termsAccepted.checked) {
                    e.preventDefault();
                    Utils.showNotification('Please accept the terms and conditions', 'error');
                    return false;
                }
                
                // Show loading state
                const submitBtn = this.querySelector('button[type="submit"]');
                const originalText = submitBtn.textContent;
                submitBtn.textContent = 'Processing Payment...';
                submitBtn.disabled = true;
                
                // Re-enable button after 5 seconds (in case of error)
                setTimeout(() => {
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }, 5000);
            });
        }
    }
};

// Search and Filter System
const SearchFilter = {
    // Initialize search functionality
    init: function() {
        const searchInput = document.getElementById('search');
        if (searchInput) {
            searchInput.addEventListener('input', Utils.debounce(function() {
                SearchFilter.performSearch(this.value);
            }, 300));
        }
        
        // Initialize filters
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                SearchFilter.applyFilter(this.dataset.filter);
            });
        });
    },

    // Perform search
    performSearch: function(query) {
        const cards = document.querySelectorAll('.expert-card, .booking-card');
        const lowerQuery = query.toLowerCase();
        
        cards.forEach(card => {
            const text = card.textContent.toLowerCase();
            if (text.includes(lowerQuery)) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    },

    // Apply filter
    applyFilter: function(filter) {
        // Update active button
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        document.querySelector(`[data-filter="${filter}"]`).classList.add('active');
        
        // Apply filter to cards
        const cards = document.querySelectorAll('[data-booking-type], [data-status]');
        
        cards.forEach(card => {
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
            
            card.style.display = show ? 'block' : 'none';
        });
    }
};

// Image Upload Handler
const ImageUpload = {
    // Initialize image upload
    init: function() {
        const fileInput = document.getElementById('profile_image');
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    ImageUpload.previewImage(file);
                }
            });
        }
    },

    // Preview uploaded image
    previewImage: function(file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image_preview');
            if (preview) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
        };
        reader.readAsDataURL(file);
    }
};

// Initialize everything when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all systems
    BookingSystem.init();
    PaymentSystem.init();
    SearchFilter.init();
    ImageUpload.init();
    
    // Add smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
    
    // Add loading states to forms
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = this.querySelector('button[type="submit"]');
            if (submitBtn && !submitBtn.disabled) {
                submitBtn.style.opacity = '0.7';
                submitBtn.style.cursor = 'not-allowed';
            }
        });
    });
    
    // Add confirmation dialogs for destructive actions
    document.querySelectorAll('form[data-confirm]').forEach(form => {
        form.addEventListener('submit', function(e) {
            const message = this.dataset.confirm;
            if (!confirm(message)) {
                e.preventDefault();
            }
        });
    });
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .loading {
        position: relative;
        pointer-events: none;
    }
    
    .loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 20px;
        height: 20px;
        margin: -10px 0 0 -10px;
        border: 2px solid #f3f3f3;
        border-top: 2px solid var(--primary-color);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

