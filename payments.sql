-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Sep 14, 2024 at 08:39 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `paypal`
--

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` bigint UNSIGNED NOT NULL,
  `invoice_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `capture_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `quantity` int NOT NULL,
  `capture_amount` double NOT NULL,
  `currency` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payer_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `platform_fees` double NOT NULL,
  `payment_method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `seller_receivable_final_amount` double NOT NULL,
  `refund_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`id`, `invoice_id`, `capture_id`, `product_name`, `quantity`, `capture_amount`, `currency`, `payer_name`, `payer_email`, `payment_status`, `platform_fees`, `payment_method`, `seller_receivable_final_amount`, `refund_id`, `created_at`, `updated_at`) VALUES
(1, '0FG22269H3027863U', '7N407234TJ652541W', 'iPhone', 1, 5, 'USD', 'Ram Buyer', 'sb-3jq5z32704261@personal.example.com', 'COMPLETED', 0.59, 'PayPal', 4.41, NULL, '2024-09-14 01:54:51', '2024-09-14 01:54:51'),
(2, '5BK67489VE384691K', '4EH513769G607972D', 'Nokia', 1, 7, 'USD', 'Ram Buyer', 'sb-3jq5z32704261@personal.example.com', 'REFUNDED', 0.67, 'PayPal', 6.33, '2FC71169W98194603', '2024-09-14 01:56:05', '2024-09-14 01:56:52'),
(3, '7HW176561M9284116', '7MB63571A1638604D', 'Pencil', 1, 9, 'USD', 'Ram Buyer', 'sb-3jq5z32704261@personal.example.com', 'COMPLETED', 0.77, 'PayPal', 8.23, NULL, '2024-09-14 03:04:10', '2024-09-14 03:04:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
