# Hanzo Cakery

A web-based bakery management system that helps manage orders, inventory, and customer information for Hanzo Cakery.

## Description

Hanzo Cakery is a comprehensive bakery management system that provides features for both customers and administrators. The system allows customers to browse products, place orders, and track their order status, while administrators can manage inventory, process orders, and handle customer information.

## Key Features

### Customer Features

- User registration and authentication with email verification
- Browse and search products with filters
- Shopping cart functionality
- Secure checkout process
- Order tracking and history
- Profile management
- Password reset functionality
- Social media login (Google)

### Admin Features

- Secure admin dashboard
- Product management (add, edit, delete)
- Inventory tracking
- Order management and processing
- Customer database management
- Sales reports and analytics
- Email notifications for new orders
- Bulk product updates

### Security Features

- JWT-based authentication
- reCAPTCHA integration for forms
- Secure password hashing
- Protected admin routes
- Input validation and sanitization
- XSS and CSRF protection
- Secure session management

### Additional Features

- Responsive design for all devices
- Real-time inventory updates
- Email notifications system
- Google integration for authentication
- Search functionality with filters
- Image upload and management
- Order status tracking
- Customer feedback system

## Tech Stack

- **Backend**: PHP
- **Database**: MySQL
- **Frontend**: HTML, CSS, JavaScript
- **Authentication**: JWT (JSON Web Tokens)
- **Email**: PHPMailer
- **Environment Variables**: PHP dotenv
- **Google Integration**: Google API Client

## Features

- User authentication and authorization
- Product catalog management
- Order processing and tracking
- Inventory management
- Customer management
- Email notifications
- Google integration

## API Routes

### Authentication

- `/auth/login.php` - User login
- `/auth/register.php` - User registration
- `/auth/logout.php` - User logout

### Home

- `/home/home.php` - Main landing page
- `/home/products.php` - Product catalog
- `/home/orders.php` - Order management

### Admin

- `/admin/dashboard.php` - Admin dashboard
- `/admin/inventory.php` - Inventory management
- `/admin/customers.php` - Customer management

## Dependencies

The project uses the following main packages:

- `vlucas/phpdotenv` (^5.6) - Environment variable management
- `firebase/php-jwt` (^6.11) - JWT authentication
- `phpmailer/phpmailer` (^6.10) - Email functionality
- `google/apiclient` (^2.18) - Google API integration

## Installation

1. Clone the repository:

   ```bash
   git clone [repository-url]
   cd hanzo-cakery
   ```

2. Install dependencies using Composer:

   ```bash
   composer install
   ```

3. Set up the database:

   - Import the `cakery.sql` file into your MySQL database
   - Create a `.env` file in the root directory with the following variables:
     ```
     DB_HOST=your_database_host
     DB_NAME=your_database_name
     DB_USER=your_database_user
     DB_PASS=your_database_password
     JWT_SECRET=your_jwt_secret
     SMTP_HOST=your_smtp_host
     SMTP_USER=your_smtp_username
     SMTP_PASS=your_smtp_password
     ```

4. Configure your web server:

   - Point your web server to the project's root directory
   - Ensure PHP has write permissions for necessary directories
   - Enable required PHP extensions (mysqli, json, etc.)

5. Access the application:
   - Open your web browser and navigate to the configured URL
   - The system will redirect to the home page

## Sample .env File

Create a `.env` file in the root directory with the following configuration:

```env
# Database Configuration
DB_SERVERNAME=localhost
DB_USERNAME=root
DB_PASSWORD=
DB_NAME=hanzo_cakery

# JWT Configuration
JWT_SECRET=your_secure_jwt_secret_key_here

# Application Configuration
APP_PASSWORD=your_app_specific_password
APP_EMAIL=your_email@gmail.com
APP_NAME=Hanzo Cakery

# reCAPTCHA Configuration
RECAPTCHA_SITE=your_recaptcha_site_key
RECAPTCHA_SECRET=your_recaptcha_secret_key

# Google Configuration
GOOGLE_CLIENT=your_google_client_id
GOOGLE_SECRET=your_google_client_secret
GOOGLE_REDIRECT=http://localhost/hanzo-cakery/auth/google-callback.php
```

Note:

- Replace the placeholder values with your actual configuration
- Keep your JWT_SECRET secure and unique
- For Google integration, obtain credentials from Google Cloud Console
- For reCAPTCHA, get your keys from Google reCAPTCHA admin console

## Directory Structure

- `/admin` - Administrator interface and functionality
- `/auth` - Authentication related files
- `/home` - Customer-facing pages
- `/assets` - Static assets (images, etc.)
- `/includes` - Common PHP includes
- `/styles` - CSS stylesheets
- `/utils` - Utility functions
- `/vendor` - Composer dependencies
