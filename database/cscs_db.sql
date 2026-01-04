-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 15, 2025 at 07:34 AM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cscs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `sugar` varchar(50) DEFAULT NULL,
  `addon` varchar(100) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_list`
--

CREATE TABLE `category_list` (
  `id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category_list`
--

INSERT INTO `category_list` (`id`, `name`, `description`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 'Hot', 'Hot Coffee', 1, 0, '2022-04-22 09:59:46', '2022-04-22 09:59:46'),
(2, 'Cold', 'Cold Coffee', 1, 0, '2022-04-22 10:01:06', '2022-04-22 10:01:06');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `points` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `fullname`, `email`, `password`, `points`, `created_at`) VALUES
(1, 'Mashitah Arifin', 'mashitaharifin@gmail.com', '$2y$10$GNCmd784GamcMNy0nLuHIO55ZAK.Ug14aF5q7VwcRf70Phftcrcay', 66, '2025-06-15 01:00:05'),
(2, 'Offline Customer', 'N/A', 'N/A', 0, '2025-06-15 01:12:27'),
(3, 'Indra Nayl', 'indranayl@gmail.com', '$2y$10$GwOTSTe6VVAjJ6FhL7G/GOj44FjUig1Yhfpq08eGw7WJMxq8FBZRq', 236, '2025-06-15 02:57:42'),
(4, 'Aimi Zulaiqha', 'aimizulaiqha17@gmail.com', '$2y$10$XAltbSaesiAIZHXyXIPmhOO/u5WarUvKRC46trN0XEwDQiOJU8niO', 1696, '2025-06-15 04:01:24'),
(5, 'Natasha Afiqah', 'natashaafiqah28@gmail.com', '$2y$10$F4R.O6nCgD1DhTwGm9jiB.jug9RIJQXRA0yEsZUCC0ujrxDL8q.g2', 2398, '2025-06-15 05:32:02');

-- --------------------------------------------------------

--
-- Table structure for table `loyalty_history`
--

CREATE TABLE `loyalty_history` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) DEFAULT NULL,
  `points_earned` int(11) DEFAULT 0,
  `points_redeemed` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `loyalty_history`
--

INSERT INTO `loyalty_history` (`id`, `customer_id`, `order_id`, `points_earned`, `points_redeemed`, `created_at`) VALUES
(1, 1, 1, 26, 0, '2025-06-15 09:02:57'),
(2, 1, 2, 40, 0, '2025-06-15 10:32:15'),
(3, 3, 5, 236, 0, '2025-06-15 11:00:17'),
(4, 4, 7, 230, 0, '2025-06-15 12:04:02'),
(5, 4, 8, 1466, 0, '2025-06-15 13:18:37'),
(6, 5, 10, 2398, 0, '2025-06-15 13:33:32');

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` varchar(50) DEFAULT 'Preparing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `customer_id`, `customer_name`, `phone`, `payment_method`, `total_price`, `created_at`, `status`) VALUES
(1, 1, 'Mashitah Arifin', '0197242208', 'Online Banking', '26.00', '2025-06-15 01:02:56', 'Completed'),
(2, 1, 'Mashitah Arifin', '0197242208', 'Cash', '40.50', '2025-06-15 02:32:14', 'Completed'),
(3, 2, 'Offline Customer', 'N/A', 'Online banking', '23.00', '2025-06-15 02:56:25', 'Completed'),
(4, 2, 'Offline Customer', 'N/A', 'Online banking', '23.00', '2025-06-15 02:56:25', 'Completed'),
(5, 3, 'Indra Nayl', '0148340754', 'Online Banking', '236.00', '2025-06-15 03:00:17', 'Completed'),
(6, 2, 'Offline Customer', 'N/A', 'Online banking', '66.50', '2025-06-15 03:35:25', 'Ready'),
(7, 4, 'Aimi Zulaiqha', '0177365473', 'Credit Card', '230.00', '2025-06-15 04:04:02', 'Ready'),
(8, 4, 'Aimi Zulaiqha', '0177365473', 'Credit Card', '1466.00', '2025-06-15 05:18:37', 'Ready'),
(9, 2, 'Offline Customer', 'N/A', 'Online banking', '943.00', '2025-06-15 05:28:02', 'Preparing'),
(10, 5, 'Natasha Afiqah', '01121779004', 'Credit Card', '2398.00', '2025-06-15 05:33:32', 'Preparing');

-- --------------------------------------------------------

--
-- Table structure for table `order_items`
--

CREATE TABLE `order_items` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `sugar` varchar(50) DEFAULT NULL,
  `addon` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `price`, `quantity`, `type`, `sugar`, `addon`) VALUES
(1, 1, 9, 'Hot Crème Brûlée Matcha Latte', '13.00', 2, 'Hot', '75', NULL),
(2, 2, 4, 'Iced Matcha Latte', '13.50', 3, 'Cold', '50%', 'Vanilla Syrup, Caramel Drizzle'),
(3, 5, 12, 'Iced Mocha Latte', '12.50', 4, 'Cold', '100%', 'Vanilla Syrup, Caramel Drizzle, Extra Shot'),
(4, 5, 6, 'Hot Matcha Latte', '12.00', 11, 'Hot', '25%', 'Vanilla Syrup'),
(5, 5, 2, 'Hot Latte', '9.00', 6, 'Hot', '50%', ''),
(6, 6, 5, 'Iced Latte', '9.50', 2, 'Iced', '50%', 'Vanilla Syrup'),
(7, 6, 12, 'Iced Mocha Latte', '9.50', 5, 'Iced', '0%', 'Extra Shot'),
(8, 7, 9, 'Hot Crème Brûlée Matcha Latte', '12.00', 3, 'Hot', '50%', 'Caramel Drizzle'),
(9, 7, 12, 'Iced Mocha Latte', '11.50', 6, 'Cold', '25%', 'Vanilla Syrup, Caramel Drizzle'),
(10, 7, 4, 'Iced Matcha Latte', '12.50', 10, 'Cold', '75%', 'Extra Shot'),
(11, 8, 3, 'Hot Cappuccino', '10.00', 50, 'Hot', '25%', 'Extra Shot'),
(12, 8, 9, 'Hot Crème Brûlée Matcha Latte', '12.00', 35, 'Hot', '50%', 'Vanilla Syrup'),
(13, 8, 6, 'Hot Matcha Latte', '12.00', 28, 'Hot', '100%', 'Caramel Drizzle'),
(14, 8, 5, 'Iced Latte', '10.50', 20, 'Cold', '50%', 'Caramel Drizzle'),
(15, 9, 4, 'Iced Matcha Latte', '11.50', 32, 'Iced', '0%', 'Vanilla Syrup, Caramel Drizzle'),
(16, 9, 8, 'Iced Buttercream Latte', '12.50', 46, 'Iced', '75%', 'Extra Shot'),
(17, 10, 4, 'Iced Matcha Latte', '14.50', 55, 'Cold', '75%', 'Vanilla Syrup, Caramel Drizzle, Extra Shot'),
(18, 10, 8, 'Iced Buttercream Latte', '13.50', 67, 'Cold', '25%', 'Extra Shot'),
(19, 10, 13, 'Iced MatchaBerry', '14.50', 48, 'Cold', '50%', 'Vanilla Syrup, Extra Shot');

-- --------------------------------------------------------

--
-- Table structure for table `product_list`
--

CREATE TABLE `product_list` (
  `id` int(11) NOT NULL,
  `category_id` int(30) NOT NULL,
  `name` text NOT NULL,
  `description` text NOT NULL,
  `price` float(15,2) NOT NULL DEFAULT 0.00,
  `photo` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `delete_flag` tinyint(1) NOT NULL DEFAULT 0,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `product_list`
--

INSERT INTO `product_list` (`id`, `category_id`, `name`, `description`, `price`, `photo`, `status`, `delete_flag`, `date_created`, `date_updated`) VALUES
(1, 1, 'Americano', 'Classic black coffee made with espresso and hot water.', 8.00, NULL, 1, 1, '2025-06-05 04:17:30', '2025-06-14 17:19:06'),
(2, 1, 'Hot Latte', 'Espresso with steamed milk and a light layer of foam.', 9.00, 'hot-latte.jpeg', 1, 0, '2025-06-05 04:17:30', '2025-06-14 18:40:40'),
(3, 1, 'Hot Cappuccino', 'Espresso with equal parts of steamed milk and foam.', 9.00, 'hot-cappucino.avif', 1, 0, '2025-06-05 04:17:30', '2025-06-14 18:41:16'),
(4, 2, 'Iced Matcha Latte', 'Japanese green tea blended with chilled milk and a touch of sweetness, served over ice for a refreshing finish.', 11.50, 'iced-matcha-latte.jpg', 1, 0, '2025-06-05 04:17:30', '2025-06-14 18:48:13'),
(5, 2, 'Iced Latte', 'Espresso with chilled milk and a delicate layer of foam, served over ice for a smooth, refreshing finish.', 9.50, 'iced-latte.webp', 1, 0, '2025-06-05 04:17:30', '2025-06-14 18:50:07'),
(6, 1, 'Hot Matcha Latte', 'Japanese green tea with milk and a touch of sweetness.', 11.00, 'hot-matcha.jpeg', 1, 0, '2025-06-05 04:17:30', '2025-06-14 18:40:57'),
(7, 1, 'Hot Mocha Latte', 'Rich espresso blended with steamed milk and velvety chocolate for a warm, indulgent treat.', 9.00, 'hot-mocha-latte.jpeg', 1, 0, '2025-06-05 04:17:30', '2025-06-14 18:53:04'),
(8, 2, 'Iced Buttercream Latte', 'A velvety vanilla latte—creamy espresso swirled with caramelized sweetness for a rich sip.', 12.50, 'buttercream-latte.webp', 1, 0, '2025-06-05 04:17:30', '2025-06-14 18:57:25'),
(9, 1, 'Hot Crème Brûlée Matcha Latte', 'Smooth matcha and steamed milk topped with caramelized sugar for a warm, indulgent treat.', 11.00, 'creme-brulee-matcha-latte.jpeg', 1, 0, '2025-06-05 04:17:30', '2025-06-14 18:57:02'),
(10, 2, 'Iced Americano', 'Classic black coffee made with espresso and chilled water, served over ice.', 8.50, 'iced-americano.jpg', 1, 0, '2025-06-14 17:39:22', '2025-06-14 18:11:22'),
(11, 2, 'Iced Cappucino', 'Espresso with equal parts of chilled milk and airy foam, served over ice for a cool and creamy experience.', 9.50, 'iced-cappucino.webp', 1, 0, '2025-06-14 18:40:25', '2025-06-14 21:02:13'),
(12, 2, 'Iced Mocha Latte', 'Smooth espresso, chilled milk, and chocolate over ice for a refreshing, creamy delight.', 9.50, 'iced-mocha-latte.webp', 1, 0, '2025-06-14 18:54:04', '2025-06-14 21:03:34'),
(13, 2, 'Iced MatchaBerry', 'A refreshing fusion of earthy matcha and sweet strawberry, blended with chilled milk and served over ice for a creamy, vibrant treat.', 12.50, 'iced-matchaberry.webp', 1, 0, '2025-06-14 18:57:36', '2025-06-14 21:03:44');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `firstname` varchar(250) NOT NULL,
  `lastname` varchar(250) NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `avatar` text DEFAULT NULL,
  `last_login` datetime DEFAULT NULL,
  `type` tinyint(1) NOT NULL DEFAULT 0,
  `date_added` datetime NOT NULL DEFAULT current_timestamp(),
  `date_updated` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `firstname`, `lastname`, `username`, `password`, `avatar`, `last_login`, `type`, `date_added`, `date_updated`, `status`) VALUES
(1, 'Izham', 'Aziz', 'admin', '$2y$10$nOUIs5kJ7naTuTFkBy1veuEvS/fp/daZKRfZRUyItYXvQ3VbFQ9Fe', NULL, NULL, 1, '2025-06-05 08:29:58', '2025-06-05 08:30:31', '1'),
(3, 'Faruq', 'Mansor', 'faruqmansor18', '$2y$10$NFIwN.2Qu7ZGvSjIK54hYuNHtFe6NyqLFnMbbDAY5816HpJOXm3Nq', NULL, NULL, 2, '2025-06-06 08:47:30', '2025-06-06 14:47:30', '1'),
(4, 'Radhiah', 'Rad', 'raradcomel', '$2y$10$8Y4FvqTFVlB2Hnxj7PbLHeqLTuZdxawM2x6w3827aAdxST3Xy6Qz6', NULL, NULL, 2, '2025-06-06 08:49:47', '2025-06-08 13:06:04', '1'),
(6, 'Nurina', 'Sabri', 'ninasabri', '$2y$10$Qm6VrqUthvMdGq.9xsDxGO99iqMxCzmBR2TjMEOnkdlRfPVURUH8a', NULL, NULL, 2, '2025-06-15 07:21:02', '2025-06-15 13:21:02', '1');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_list`
--
ALTER TABLE `category_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `loyalty_history`
--
ALTER TABLE `loyalty_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `product_list`
--
ALTER TABLE `product_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `loyalty_history`
--
ALTER TABLE `loyalty_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `product_list`
--
ALTER TABLE `product_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `loyalty_history`
--
ALTER TABLE `loyalty_history`
  ADD CONSTRAINT `loyalty_history_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `loyalty_history_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
