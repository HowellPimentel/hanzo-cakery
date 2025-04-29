# Hanzo's Cakery - eCommerce App

Welcome to **Hanzo's Cakery**, an online platform where you can browse, order, and customize cakes from the finest bakery in town! This app allows users to place cake orders, explore a variety of baked goods, and enjoy a seamless shopping experience. 

## Technology Stack

- **PHP**: Server-side scripting language to manage business logic and data processing.
- **MySQL**: Database to store user information, orders, products, and more.
- **HTML, CSS, JavaScript**: Front-end technologies to ensure a smooth and interactive user experience.
- **AJAX**: For smooth asynchronous interactions between the client and server.
- **Apache**: Web server for hosting the application.

## Features

- **Product Catalog**: View a wide range of cakes and baked goods.
- **Customizable Orders**: Personalize your cake orders with different sizes, flavors, and toppings.
- **Shopping Cart**: Add items to your cart, edit quantities, or remove items before checkout.
- **Secure Checkout**: Payment integration for safe online transactions.
- **Order History**: Track your past orders and easily reorder your favorites.
- **Admin Dashboard**: For managing products, orders, and customer information.
  
## Installation

### Prerequisites

- PHP (>= 7.4)
- MySQL or MariaDB
- Apache server (or any compatible server)
- Composer (for dependency management)
  
### Steps to Set Up Locally

1. Clone the repository:

    ```bash
    git clone https://github.com/yourusername/hanzos-cakery.git
    cd hanzos-cakery
    ```

2. Install dependencies using Composer:

    ```bash
    composer install
    ```

3. Set up the MySQL database by creating a new database, for example:

    ```sql
    CREATE DATABASE hanzos_cakery;
    ```

4. Import the provided `database.sql` file to set up the initial tables:

    ```bash
    mysql -u username -p hanzos_cakery < database.sql
    ```

5. Configure your database settings in `config.php`:

    ```php
    define('DB_SERVER', 'localhost');
    define('DB_USERNAME', 'root');
    define('DB_PASSWORD', 'your_password');
    define('DB_NAME', 'hanzos_cakery');
    ```

6. Start the Apache server and navigate to `http://localhost` to access the app.

## Usage

- **For Customers**: Browse products, add items to your cart, customize cakes, and complete your order.
- **For Admin**: Log in to the admin panel to manage products, view customer orders, and manage stock.

## Contributing

1. Fork the repository.
2. Create your feature branch (`git checkout -b feature/feature-name`).
3. Commit your changes (`git commit -am 'Add new feature'`).
4. Push to the branch (`git push origin feature/feature-name`).
5. Open a pull request.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## Contact

For any inquiries or issues, please contact us at [support@hanzoscakery.com](mailto:support@hanzoscakery.com).
