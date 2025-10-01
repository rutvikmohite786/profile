# Topmate Clone - Core PHP Application

A fully functional user profile and booking system similar to Topmate, built with Core PHP, MySQL, and modern web technologies.

## 🚀 Features

### ✅ Core Features Implemented

1. **User Authentication System**
   - User registration with validation
   - Secure login with password hashing
   - Session management
   - Admin/user role separation

2. **User Profiles**
   - Profile image support
   - Name, bio, and expertise fields
   - Skills/areas of expertise display
   - Pricing per session
   - Availability calendar
   - Editable profiles for own account

3. **Booking System**
   - Date and time slot selection
   - Booking request submission
   - Client information collection
   - Session notes
   - Booking status management (pending, confirmed, cancelled, completed)

4. **Payment Integration**
   - Simulated payment processing
   - Multiple payment methods (Credit Card, PayPal, Bank Transfer, Demo)
   - Payment status tracking
   - Transaction ID generation

5. **Admin Panel**
   - User management
   - Booking management
   - System statistics
   - Booking status updates
   - User activation/deactivation

6. **Modern UI/UX**
   - Responsive design
   - Clean, modern interface
   - Interactive elements
   - Form validation
   - Status indicators

## 🛠️ Technology Stack

- **Backend**: Core PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Styling**: Custom CSS with CSS Variables
- **Security**: Password hashing, SQL injection prevention

## 📋 Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache/Nginx)
- Modern web browser

## 🔧 Installation & Setup

### 1. Clone/Download the Project

```bash
# If using git
git clone <repository-url> topmate-clone
cd topmate-clone

# Or extract the downloaded files to your web server directory
```

### 2. Database Setup

1. **Create Database**
   ```sql
   CREATE DATABASE topmate_clone;
   ```

2. **Import Database Schema**
   ```bash
   mysql -u your_username -p topmate_clone < database_schema.sql
   ```

3. **Update Database Configuration**
   
   Edit `config/database.php` and update the database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'topmate_clone');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   ```

### 3. Web Server Configuration

#### For Apache:
1. Place files in your web root directory (e.g., `/var/www/html/topmate`)
2. Ensure mod_rewrite is enabled
3. Create `.htaccess` file (optional for URL rewriting)

#### For Nginx:
1. Configure virtual host to point to the project directory
2. Ensure PHP-FPM is properly configured

### 4. File Permissions

```bash
# Set appropriate permissions
chmod 755 /path/to/topmate
chmod 644 /path/to/topmate/*.php
chmod 755 /path/to/topmate/assets
chmod 644 /path/to/topmate/assets/css/*.css
chmod 644 /path/to/topmate/assets/js/*.js
```

### 5. Test the Installation

1. Open your web browser
2. Navigate to `http://your-domain/topmate`
3. You should see the homepage

## 👥 Default User Accounts

The database includes sample accounts for testing:

### Admin Account
- **Username**: `admin`
- **Password**: `admin123`
- **Email**: `admin@topmate.com`
- **Role**: Administrator

### Expert Account
- **Username**: `john_doe`
- **Password**: `expert123`
- **Email**: `john@example.com`
- **Role**: Expert User
- **Price**: $50.00 per session

## 📁 Project Structure

```
topmate/
├── assets/
│   ├── css/
│   │   └── style.css          # Main stylesheet
│   ├── js/
│   │   └── main.js           # JavaScript functionality
│   └── images/               # Image assets
├── config/
│   └── database.php          # Database configuration
├── includes/
│   ├── auth.php              # Authentication system
│   ├── header.php            # HTML header template
│   └── footer.php            # HTML footer template
├── admin.php                 # Admin panel
├── booking.php               # Booking processing
├── database_schema.sql       # Database schema
├── index.php                 # Homepage
├── login.php                 # Login page
├── logout.php                # Logout handler
├── my-bookings.php           # User bookings page
├── payment.php               # Payment processing
├── profile.php               # User profile page
├── register.php              # Registration page
└── README.md                 # This file
```

## 🔐 Security Features

1. **Password Security**
   - Bcrypt password hashing
   - Minimum password length requirements
   - Password confirmation validation

2. **SQL Injection Prevention**
   - Prepared statements
   - Parameterized queries
   - Input sanitization

3. **Session Management**
   - Secure session handling
   - Session timeout
   - CSRF protection (basic)

4. **Input Validation**
   - Server-side validation
   - Client-side validation
   - XSS prevention

## 🎨 Customization

### Color Scheme
The application uses CSS variables for easy theming. Edit `assets/css/style.css`:

```css
:root {
    --primary-color: #6366f1;      /* Main brand color */
    --primary-dark: #4f46e5;       /* Darker shade */
    --secondary-color: #f8fafc;    /* Background color */
    --accent-color: #10b981;       /* Success/accent color */
    /* ... more variables */
}
```

### Adding New Features
1. Create new PHP files following the existing structure
2. Add routes to the navigation in `includes/header.php`
3. Update database schema if needed
4. Add corresponding CSS and JavaScript

## 🔧 Configuration Options

### Database Settings
Edit `config/database.php` to modify:
- Database connection parameters
- Connection pooling
- Error handling

### Application Settings
You can add configuration constants in `config/database.php`:
```php
// Application settings
define('SITE_NAME', 'Topmate Clone');
define('SITE_URL', 'http://your-domain.com/topmate');
define('ADMIN_EMAIL', 'admin@yourdomain.com');
```

## 📊 Database Schema

### Tables Overview

1. **users** - User accounts and profiles
2. **user_availability** - Expert availability schedules
3. **bookings** - Booking requests and details
4. **payments** - Payment transactions and status

### Key Relationships
- `users.id` → `user_availability.user_id`
- `users.id` → `bookings.user_id`
- `bookings.id` → `payments.booking_id`

## 🚀 Deployment

### Production Deployment Checklist

1. **Security**
   - [ ] Change default passwords
   - [ ] Enable HTTPS
   - [ ] Set secure file permissions
   - [ ] Disable error reporting in production

2. **Performance**
   - [ ] Enable PHP OPcache
   - [ ] Configure database connection pooling
   - [ ] Set up CDN for static assets
   - [ ] Enable Gzip compression

3. **Monitoring**
   - [ ] Set up error logging
   - [ ] Configure backup system
   - [ ] Monitor database performance
   - [ ] Set up uptime monitoring

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Failed**
   - Check database credentials in `config/database.php`
   - Ensure MySQL service is running
   - Verify database exists

2. **Session Issues**
   - Check PHP session configuration
   - Ensure session directory is writable
   - Clear browser cookies

3. **File Permission Errors**
   - Set correct file permissions
   - Check web server user permissions
   - Ensure PHP can write to session directory

4. **CSS/JS Not Loading**
   - Check file paths in HTML
   - Verify web server configuration
   - Check for file permission issues

## 📝 API Documentation

### Authentication Endpoints
- `POST /login.php` - User login
- `POST /register.php` - User registration
- `GET /logout.php` - User logout

### Profile Endpoints
- `GET /profile.php?id={user_id}` - View user profile
- `POST /profile.php` - Update profile (authenticated users only)

### Booking Endpoints
- `POST /booking.php` - Create booking
- `GET /my-bookings.php` - View user bookings
- `POST /payment.php` - Process payment

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📄 License

This project is open source and available under the [MIT License](LICENSE).

## 🆘 Support

For support and questions:
1. Check the troubleshooting section
2. Review the code comments
3. Create an issue in the repository
4. Contact the development team

## 🔄 Updates and Maintenance

### Regular Maintenance Tasks
1. **Database Backup** - Daily automated backups
2. **Security Updates** - Regular PHP and system updates
3. **Performance Monitoring** - Monitor database and server performance
4. **Log Review** - Regular review of error logs

### Future Enhancements
- Real payment gateway integration (Razorpay/Stripe)
- Email notification system
- Advanced calendar integration
- Mobile app development
- API for third-party integrations

---

**Built with ❤️ using Core PHP**

*This application demonstrates modern web development practices using Core PHP without frameworks, showcasing clean code architecture, security best practices, and responsive design.*

