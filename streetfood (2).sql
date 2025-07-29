-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 27, 2025 at 08:07 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `streetfood`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart_items`
--

CREATE TABLE `cart_items` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_locations`
--

CREATE TABLE `live_locations` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order`
--

CREATE TABLE `order` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `name` varchar(20) DEFAULT NULL,
  `email` varchar(20) DEFAULT NULL,
  `phno` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order`
--

INSERT INTO `order` (`id`, `product_name`, `quantity`, `location`, `created_at`, `name`, `email`, `phno`) VALUES
(4, 'Rice', 22, '16.660192,74.198796', '2025-07-26 08:17:01', 'kavita', 'kk@gmail.com', '987654321'),
(5, 'Chutney', 4, '16.660192,74.198796', '2025-07-26 10:51:56', 'Swarup', 'kk@gmail.com', '657894533'),
(6, 'water', 4, '16.660192,74.198796', '2025-07-26 12:01:31', 'Aish', 'aish@gmail.com', '989796564'),
(7, 'panipuri', 5, '16.660192,74.198796', '2025-07-26 12:42:48', 'parth', 'parth@gmail.com', '876543223');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `delivery_latitude` varchar(50) DEFAULT NULL,
  `delivery_longitude` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `supplier_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'Pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `user_id`, `vendor_id`, `total_amount`, `delivery_latitude`, `delivery_longitude`, `created_at`, `supplier_id`, `status`) VALUES
(1, 12, 13, 480.00, '16.660192', '74.198796', '2025-07-27 12:46:49', NULL, 'Pending'),
(2, 12, 13, 480.00, '16.660192', '74.198796', '2025-07-27 12:46:52', NULL, 'Pending'),
(3, 12, 13, 130.00, '16.660126499999997', '74.199077', '2025-07-27 12:51:16', NULL, 'Pending'),
(4, 12, 13, 80.00, '16.660126499999997', '74.199077', '2025-07-27 13:44:48', NULL, 'Pending'),
(5, 12, 13, 550.00, '16.660192', '74.198796', '2025-07-27 16:53:51', NULL, 'Pending'),
(6, 12, 13, 50.00, '16.660126499999997', '74.199077', '2025-07-27 17:17:34', NULL, 'Pending');

-- --------------------------------------------------------

--
-- Table structure for table `ordersdetail`
--

CREATE TABLE `ordersdetail` (
  `order_id` int(11) NOT NULL,
  `vendor_name` varchar(100) DEFAULT NULL,
  `vendor_phno` varchar(15) DEFAULT NULL,
  `product_details` text DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `payment_status` varchar(50) DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ordersdetail`
--

INSERT INTO `ordersdetail` (`order_id`, `vendor_name`, `vendor_phno`, `product_details`, `latitude`, `longitude`, `price`, `vendor_id`, `payment_status`, `created_at`) VALUES
(1, 'm_satyam', '9898766543', 'bread x 1, Tamrind x 1', 16.660192, 74.198796, 100.00, 12, 'Pending', '2025-07-27 15:17:18');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `quantity`, `price`) VALUES
(3, 3, 6, 2, 50.00),
(4, 3, 7, 1, 30.00),
(5, 4, 7, 1, 30.00),
(6, 4, 6, 1, 50.00),
(7, 5, 13, 1, 500.00),
(8, 5, 10, 1, 50.00),
(9, 6, 8, 1, 50.00);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `transaction_id` varchar(255) DEFAULT NULL,
  `order_id` varchar(255) DEFAULT NULL,
  `customer_name` varchar(100) DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `method` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `date` datetime DEFAULT NULL,
  `supplier_username` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `transaction_id`, `order_id`, `customer_name`, `amount`, `method`, `status`, `date`, `supplier_username`) VALUES
(1, 'pay_Qy5ER4SaEDOhYE', 'order_Qy5EARTHcU7753', 'ABC Restaurant', 494.00, 'Card', 'Success', '2025-07-27 22:27:55', ''),
(2, 'pay_Qy5pNim3TIzMah', 'order_Qy5pEN4Xmdtqrq', 'ABC Restaurant', 494.00, 'Card', 'Success', '2025-07-27 23:02:50', ''),
(3, 'pay_LMnOPq12345678', 'order_XYZ98765', 'ABC Restaurant', 500.00, 'Card', 'Success', '2025-07-27 10:15:00', 'Kavita'),
(4, 'pay_QyAfQWqpStrLtS', 'order_QyAezuShliSEAW', 'ABC Restaurant', 50.00, 'Card', 'Success', '2025-07-27 19:17:36', '');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `name` varchar(20) DEFAULT NULL,
  `category` varchar(20) DEFAULT NULL,
  `price` double DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `product_image` varchar(50) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `supplier_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `name`, `category`, `price`, `unit`, `product_image`, `description`, `vendor_id`, `supplier_id`) VALUES
(6, 'Banana', 'fruit', 50, '1 dozen', 'banana.jpg', 'This is a fresh fruit', 13, NULL),
(7, 'curry Leaves', 'vegetable', 30, '1 kg', 'curry.jpg', 'this are curry leaves', 13, NULL),
(8, 'Tamrind', 'fruit', 50, '1 kg', 'tamrind.jpg', 'This is a bitter tamrind', 13, NULL),
(10, 'bread', 'diary', 50, '1 packet', 'bread.jpg', 'this is a bread', 13, NULL),
(13, 'mango', 'fruits', 500, 'kg', 'mango.jpg', 'this is mango', 13, NULL),
(14, 'mango', 'fruits', 500, 'kg', 'mango.jpg', 'mango', 13, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `raw_materials`
--

CREATE TABLE `raw_materials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT NULL,
  `unit` varchar(20) DEFAULT NULL,
  `estimated_cost` decimal(10,2) DEFAULT NULL,
  `priority` enum('low','medium','high') DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `raw_materials`
--

INSERT INTO `raw_materials` (`id`, `user_id`, `name`, `quantity`, `unit`, `estimated_cost`, `priority`, `created_at`) VALUES
(1, 12, 'Curry Leaves', 20, '0', 50.00, 'low', '2025-07-26 16:51:18'),
(2, 12, 'Honey', 2, '0', 100.00, 'medium', '2025-07-26 16:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `supplierprofile`
--

CREATE TABLE `supplierprofile` (
  `id` int(11) NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(50) NOT NULL,
  `category` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) NOT NULL,
  `city` varchar(100) NOT NULL,
  `state` varchar(100) NOT NULL,
  `zip_code` varchar(20) NOT NULL,
  `country` varchar(100) NOT NULL,
  `location_notes` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `instagram` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `supplierprofile`
--

INSERT INTO `supplierprofile` (`id`, `business_name`, `owner_name`, `email`, `phone`, `category`, `description`, `address`, `city`, `state`, `zip_code`, `country`, `location_notes`, `website`, `facebook`, `instagram`, `twitter`, `profile_image`, `created_at`, `updated_at`) VALUES
(1, '', '', 'rohan@freshfarm.com', '9822496626', 'fresh-produce', 'We are a family-owned business specializing in fresh fruits sourced directly from local farms. We pride ourselves on quality, sustainability, and supporting our local community.', '123 Market Street', 'Sangli', 'Maharashtra', '', 'US', '', 'https://freshfarmproduceco.com', '', '', '', '', '2025-07-27 19:24:21', '2025-07-27 19:24:21');

-- --------------------------------------------------------

--
-- Table structure for table `testimonials`
--

CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `testimonials`
--

INSERT INTO `testimonials` (`id`, `user_id`, `comment`, `created_at`) VALUES
(1, 12, 'This page is very helpful for us to buy some raw materials for our shop that\'s good idea', '2025-07-26 18:08:57'),
(10, 13, 'Yeah! It\'s good to order raw materials', '2025-07-26 18:38:13');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `contact` varchar(20) DEFAULT NULL,
  `otp` varchar(10) DEFAULT NULL,
  `otp_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `role`, `contact`, `otp`, `otp_expiry`) VALUES
(7, 'yogita jadhav', 'yogita', 'yogita@gmail.com', '$2y$10$0EJA19Dm5RiOppCW458PEeK0RoqaHJAh/0ovmXinHIx79Fpebe3li', 'supplier', '9420320904', NULL, NULL),
(8, 'kavita suresh kharade', 'kavita', 'kavitakharade22@gmail.com', '$2y$10$aRp0N3TlLO2HXJqDuh3b3.8qyeKBaU106V0AyO1z7GlKHLbiQo.Be', 'supplier', '9822496626', NULL, NULL),
(9, 'Vaibhavi Vijay Lohar', 'Vaibhavi_Lohar', 'vaibhavilohar84@gmail.com', '$2y$10$6J98JzoDsnmeb0PUo75p/ucRrdoECq0bbKR/tDLgsL3Qzf/gkANWG', 'vendor', '9822496626', NULL, NULL),
(12, 'Satyam Mali', 'm_satyam', 'malisatyam86@gmail.com', '$2y$10$GFFPzYMHshf5tcmS4uIGoOIXwylXAapGqjDQqO8DCDeRyRcCTA8Om', 'vendor', '9898766543', NULL, NULL),
(13, 'Guru Mane', 'g_mane', 'gurumane@gmail.com', '$2y$10$CyTtBofA5wlkhsBFi17nNuR43pMIYcYWuQ0aNTFuEt9BuQkBu6rSO', 'supplier', '9987645368', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vendor`
--

CREATE TABLE `vendor` (
  `vendor_id` int(11) NOT NULL,
  `name` varchar(30) DEFAULT NULL,
  `email` varchar(30) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vendor_location`
--

CREATE TABLE `vendor_location` (
  `id` int(11) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `latitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_location`
--

INSERT INTO `vendor_location` (`id`, `vendor_id`, `latitude`, `longitude`, `updated_at`) VALUES
(1, 1, 16.660192, 74.198796, '2025-07-26 12:02:11');

-- --------------------------------------------------------

--
-- Table structure for table `vendor_profiles`
--

CREATE TABLE `vendor_profiles` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `business_name` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `zip_code` varchar(20) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `location_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendor_profiles`
--

INSERT INTO `vendor_profiles` (`id`, `user_id`, `business_name`, `category`, `description`, `address`, `city`, `state`, `zip_code`, `country`, `location_notes`, `created_at`, `updated_at`) VALUES
(1, 12, 'Kolhapuri Panipuri', 'organic-vegetables', 'We are a family-owned business specializing in fresh, organic vegetables sourced directly from local farms. We pride ourselves on quality, sustainability, and supporting our local community.', 'dakhancha Raja Galli', 'Kolhapur', 'Maharashtra', NULL, 'India', 'dakhancha Raja galli jivba nana jadhav park', '2025-07-26 16:05:34', '2025-07-26 21:42:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `live_locations`
--
ALTER TABLE `live_locations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order`
--
ALTER TABLE `order`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `fk_supplier` (`supplier_id`);

--
-- Indexes for table `ordersdetail`
--
ALTER TABLE `ordersdetail`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `vendor_id` (`vendor_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `supplierprofile`
--
ALTER TABLE `supplierprofile`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username_2` (`username`);

--
-- Indexes for table `vendor`
--
ALTER TABLE `vendor`
  ADD PRIMARY KEY (`vendor_id`);

--
-- Indexes for table `vendor_location`
--
ALTER TABLE `vendor_location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_locations`
--
ALTER TABLE `live_locations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order`
--
ALTER TABLE `order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `ordersdetail`
--
ALTER TABLE `ordersdetail`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `raw_materials`
--
ALTER TABLE `raw_materials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `supplierprofile`
--
ALTER TABLE `supplierprofile`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `testimonials`
--
ALTER TABLE `testimonials`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `vendor`
--
ALTER TABLE `vendor`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vendor_location`
--
ALTER TABLE `vendor_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `live_locations`
--
ALTER TABLE `live_locations`
  ADD CONSTRAINT `live_locations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_supplier` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `ordersdetail`
--
ALTER TABLE `ordersdetail`
  ADD CONSTRAINT `ordersdetail_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `products_ibfk_2` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `raw_materials`
--
ALTER TABLE `raw_materials`
  ADD CONSTRAINT `raw_materials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `testimonials`
--
ALTER TABLE `testimonials`
  ADD CONSTRAINT `testimonials_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `vendor_profiles`
--
ALTER TABLE `vendor_profiles`
  ADD CONSTRAINT `vendor_profiles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
