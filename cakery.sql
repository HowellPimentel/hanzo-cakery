-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 16, 2025 at 09:34 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cakery`
--

-- --------------------------------------------------------

--
-- Table structure for table `cakes`
--

CREATE TABLE `cakes` (
  `id` int(11) NOT NULL,
  `cake_name` varchar(255) DEFAULT NULL,
  `cake_description` varchar(255) DEFAULT NULL,
  `cake_price` decimal(10,2) DEFAULT NULL,
  `cake_type` enum('Cake in a Tub','Bento','Cake') DEFAULT NULL,
  `image_path` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cakes`
--

INSERT INTO `cakes` (`id`, `cake_name`, `cake_description`, `cake_price`, `cake_type`, `image_path`) VALUES
(1, 'Ube Cake in a tub', 'Soft and flavorful purple yam cake, topped with ube halaya and cream.', 69.00, 'Cake in a Tub', 'ubetub.png'),
(2, 'Brigadeiro cake in a tub', 'A Brazilian-inspired chocolate cake made with sweetened condensed milk and cocoa, topped with chocolate sprinkles.', 69.00, 'Cake in a Tub', 'brigadeirotub.png'),
(3, 'Manga Graham Cake in a tub', 'Layers of ripe mangoes, graham crackers, and cream—refreshing and light.', 69.00, 'Cake in a Tub', '6826e7cc3005d.png'),
(4, 'Manga Graham Bento', 'Layers of ripe mangoes, graham crackers, and cream—refreshing and light.', 230.00, 'Bento', 'mangobento.png'),
(6, 'Ube Bento', 'Soft and flavorful purple yam cake, topped with ube halaya and cream.', 230.00, 'Bento', '6826e7e742851.jpg'),
(7, '4 in 1 Cake', 'A delightful combination of four flavors in one cake—perfect for sharing and satisfying everyone’s cravings.', 610.00, 'Cake', '4in1.png'),
(8, 'Red Velvet', 'Moist red velvet cake with a hint of cocoa, layered in rich cream cheese frosting — a classic treat for any occasion.', 360.00, 'Cake', '6826e814a9167.png'),
(9, 'Moist Chocolate', 'Deep, rich, and moist chocolate cake that melts in your mouth.', 360.00, 'Cake', 'chocomoist.png');

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `cake_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `cake_id` int(11) DEFAULT NULL,
  `payment_id` int(11) DEFAULT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `order_date` datetime DEFAULT current_timestamp(),
  `status` enum('pending','done','received') DEFAULT 'pending',
  `quantity` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment`
--

CREATE TABLE `payment` (
  `id` int(11) NOT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `account_name` varchar(100) DEFAULT NULL,
  `account_number` varchar(15) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payment`
--

INSERT INTO `payment` (`id`, `payment_method`, `account_name`, `account_number`, `amount`) VALUES
(45, 'GCash', 'Jeff Ivan B Mayor', '099140923571', 670.00),
(46, 'GCash', 'Jeff Ivan B Mayor', '09910923571', 670.00),
(47, 'GCash', 'Jeff Ivan B Mayor', '09910923571', 129.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(6) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `lastname` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('user','admin') DEFAULT 'user',
  `address` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `email`, `phone_number`, `password`, `role`, `address`, `created_at`) VALUES
(7, 'Master', 'Admin', 'admin', 'admin123@security.ph', '09123456789', '$2y$10$ffIylgl.6Esa717g.Y8RMu/Sm.RCUx5MMeyJTewYcLR28TZ1UEeJa', 'admin', 'Malaybalay City', '2025-05-16 15:30:39'),
(8, 'Howell Griff', 'Pimentel', 'adashino', 'pimentelhowell992@gmail.com', '09987654321', '$2y$10$Pn4UVehh9o3ijB9pekytEuX8nVwWn5sBWdlTRuYbKYmGnZPwuWMaG', 'user', 'Landing', '2025-05-16 15:31:37');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cakes`
--
ALTER TABLE `cakes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `cake_name` (`cake_name`);

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cake_id` (`cake_id`),
  ADD KEY `fk_cart_user` (`user_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cake_id` (`cake_id`),
  ADD KEY `payment_id` (`payment_id`),
  ADD KEY `orders_ibfk_1` (`user_id`);

--
-- Indexes for table `payment`
--
ALTER TABLE `payment`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cakes`
--
ALTER TABLE `cakes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payment`
--
ALTER TABLE `payment`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_ibfk_1` FOREIGN KEY (`cake_id`) REFERENCES `cakes` (`id`),
  ADD CONSTRAINT `fk_cart_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`cake_id`) REFERENCES `cakes` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`payment_id`) REFERENCES `payment` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
