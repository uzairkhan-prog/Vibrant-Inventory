-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 01, 2025 at 08:56 PM
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
-- Database: `inventory_management_vibrant`
--

-- --------------------------------------------------------

--
-- Table structure for table `assets`
--

CREATE TABLE `assets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `value` decimal(12,2) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `assets`
--

INSERT INTO `assets` (`id`, `title`, `value`, `date`, `created_at`, `updated_at`) VALUES
(4, 'Bill', 30000.00, '2025-07-31', '2025-08-02 04:04:45', '2025-08-02 04:04:45');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Packaging & Sealing', 'Packaging & Sealing', '2025-07-22 01:54:02', '2025-07-22 01:54:02'),
(2, 'Filling Machines', 'Filling Machines', '2025-07-22 01:54:18', '2025-07-22 01:54:18'),
(3, 'Printing Solutions', 'Printing Solutions', '2025-07-22 01:54:42', '2025-07-22 01:54:42'),
(4, 'Processing Machines', 'Processing Machines', '2025-07-22 01:54:56', '2025-07-22 01:54:56'),
(5, 'Spare Parts', 'Spare Parts', '2025-07-22 01:55:07', '2025-07-22 01:55:07');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `balance`, `created_at`, `updated_at`) VALUES
(4, 'Leigh Bernard', NULL, NULL, 'Nihil sit incidunt', 200000.00, '2025-07-31 04:14:04', '2025-10-02 00:18:51'),
(5, 'Uzair Khan', NULL, NULL, 'Karachi.', 100000.00, '2025-10-01 02:37:55', '2025-10-02 00:18:48');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_payments`
--

INSERT INTO `customer_payments` (`id`, `customer_id`, `description`, `payment_type`, `amount`, `created_at`, `updated_at`) VALUES
(2, 4, 'hghh hgj', 'Cash', 60000.00, '2025-07-31 04:14:18', '2025-07-31 04:17:29'),
(3, 4, 'sssss', 'Cash', 70000.00, '2025-08-26 00:20:04', '2025-08-26 00:20:04');

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `expense_name_id` bigint(20) UNSIGNED DEFAULT NULL,
  `payment_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` text DEFAULT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expenses`
--

INSERT INTO `expenses` (`id`, `expense_name_id`, `payment_type_id`, `description`, `amount`, `created_at`, `updated_at`) VALUES
(4, 4, 1, 'Id perspiciatis ut', 67.00, '2025-07-22 01:52:23', '2025-07-22 01:52:23');

-- --------------------------------------------------------

--
-- Table structure for table `expense_names`
--

CREATE TABLE `expense_names` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `expense_names`
--

INSERT INTO `expense_names` (`id`, `name`, `created_at`, `updated_at`) VALUES
(4, 'Bill', '2025-07-22 01:50:57', '2025-07-22 01:50:57');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ledgers`
--

CREATE TABLE `ledgers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('customer','supplier') NOT NULL,
  `balance` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ledgers`
--

INSERT INTO `ledgers` (`id`, `reference_id`, `type`, `balance`, `created_at`, `updated_at`) VALUES
(2, 1, 'supplier', 65000.00, '2025-07-22 03:41:06', '2025-07-22 03:42:23'),
(5, 4, 'customer', 10000.00, '2025-08-08 00:24:37', '2025-08-08 00:24:37'),
(6, 4, 'customer', 500.00, '2025-09-17 02:05:46', '2025-09-17 02:05:46');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(12, '2025_06_19_173628_create_expenses_table', 4),
(17, '2025_06_19_173628_create_expenses-old_table', 7),
(28, '0001_01_01_000000_create_users_table', 8),
(29, '0001_01_01_000001_create_cache_table', 8),
(30, '0001_01_01_000002_create_jobs_table', 8),
(31, '2025_06_19_173624_create_categories_table', 8),
(32, '2025_06_19_173624_create_products_table', 8),
(33, '2025_06_19_173625_create_customers_table', 8),
(34, '2025_06_19_173625_create_suppliers_table', 8),
(35, '2025_06_19_173626_create_purchases_table', 9),
(36, '2025_06_19_173627_create_sales_table', 10),
(37, '2025_06_19_173627_create_sale_items_table', 11),
(38, '2025_06_30_182037_create_sale_returns_table', 12),
(39, '2025_06_19_173629_create_assets_table', 13),
(40, '2025_06_19_173628_create_ledgers_table', 14),
(41, '2025_06_23_174214_add_packing_to_products_table', 15),
(42, '2025_06_25_200435_create_customer_payments_table', 16),
(43, '2025_06_25_191726_create_supplier_payments_table', 17),
(45, '2025_07_17_203809_create_payment_types_table', 18),
(46, '2025_07_21_171754_create_expense_names_table', 19),
(48, '2025_07_25_185214_add_discount_and_tax_to_purchase_items_table', 20),
(49, '2025_07_28_213035_add_discount_and_tax_to_sale_items_table', 21),
(52, '2025_10_01_163118_add_sale_id_to_sale_returns_table', 22);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payment_types`
--

CREATE TABLE `payment_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `payment_types`
--

INSERT INTO `payment_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Cash', '2025-07-22 01:11:58', '2025-07-22 01:11:58');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `packing` int(11) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 0,
  `price_per_unit` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `category_id`, `name`, `packing`, `description`, `quantity`, `price_per_unit`, `created_at`, `updated_at`) VALUES
(1, 1, 'Semi-auto Stretch Wrapping DBC800S', 3, NULL, 18, 35000.00, '2025-07-22 01:58:27', '2025-09-18 03:06:48'),
(2, 2, 'Single Nozel Piston Filling Machine', 6, NULL, 29, 85000.00, '2025-07-22 02:00:01', '2025-09-18 03:48:16'),
(3, 3, 'Pad Printer Machine', 7, NULL, 1, 60000.00, '2025-07-22 02:00:26', '2025-09-18 04:19:00'),
(4, 4, 'Peanut Peeling Machine', 1, NULL, 5, 45000.00, '2025-07-22 02:00:51', '2025-09-18 03:55:11'),
(5, 5, 'Heaters', 8, NULL, 1, 12000.00, '2025-07-22 02:02:39', '2025-10-01 02:40:04');

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `supplier_id`, `total_amount`, `date`, `created_at`, `updated_at`) VALUES
(7, 2, 51915.36, '2025-07-24', '2025-07-26 02:43:04', '2025-07-26 02:50:52'),
(9, 1, 12000.00, '2025-08-06', '2025-08-08 00:28:51', '2025-08-08 00:28:51'),
(10, 2, 130000.00, '2025-08-06', '2025-08-08 00:57:33', '2025-08-08 00:57:33'),
(11, 1, 354.00, '2025-09-17', '2025-09-18 03:00:57', '2025-09-18 03:48:16'),
(12, 1, 3363.00, '2025-09-17', '2025-09-18 03:05:22', '2025-09-18 03:05:22'),
(13, 2, 3540.00, '2025-09-17', '2025-09-18 03:06:13', '2025-09-18 03:06:13'),
(14, 2, 3186.00, '2025-09-17', '2025-09-18 03:06:48', '2025-09-18 03:06:48'),
(16, 2, 1500.00, '2025-09-17', '2025-09-18 03:30:08', '2025-09-18 03:30:08'),
(17, 2, 1000.00, '2025-09-17', '2025-09-18 03:53:20', '2025-09-18 03:53:20'),
(18, 1, 650.00, '2025-09-17', '2025-09-18 03:55:11', '2025-09-18 03:55:11'),
(19, 2, 120000.00, '2025-09-17', '2025-09-18 03:56:15', '2025-09-18 04:19:00'),
(20, 2, 36000.00, '2025-09-17', '2025-09-18 04:21:58', '2025-09-18 04:21:58');

-- --------------------------------------------------------

--
-- Table structure for table `purchase_items`
--

CREATE TABLE `purchase_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `discount` decimal(8,2) NOT NULL DEFAULT 0.00,
  `tax` decimal(8,2) NOT NULL DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `purchase_items`
--

INSERT INTO `purchase_items` (`id`, `purchase_id`, `product_id`, `quantity`, `price`, `created_at`, `updated_at`, `discount`, `tax`) VALUES
(11, 7, 2, 2, 200.00, '2025-07-26 02:50:52', '2025-07-26 02:50:52', 12.00, 18.00),
(12, 7, 5, 3, 500.00, '2025-07-26 02:50:52', '2025-07-26 02:50:52', 0.00, 0.00),
(13, 7, 4, 1, 50000.00, '2025-07-26 02:50:52', '2025-07-26 02:50:52', 0.00, 0.00),
(16, 9, 5, 1, 12000.00, '2025-08-08 00:28:51', '2025-08-08 00:28:51', 0.00, 0.00),
(17, 10, 5, 10, 13000.00, '2025-08-08 00:57:33', '2025-08-08 00:57:33', 0.00, 0.00),
(19, 12, 1, 10, 300.00, '2025-09-18 03:05:22', '2025-09-18 03:05:22', 5.00, 18.00),
(20, 13, 4, 5, 600.00, '2025-09-18 03:06:13', '2025-09-18 03:06:13', 0.00, 18.00),
(21, 14, 1, 3, 900.00, '2025-09-18 03:06:48', '2025-09-18 03:06:48', 0.00, 18.00),
(22, 16, 4, 3, 500.00, '2025-09-18 03:30:08', '2025-09-18 03:30:08', 0.00, 0.00),
(24, 11, 2, 3, 100.00, '2025-09-18 03:48:16', '2025-09-18 03:48:16', 0.00, 18.00),
(25, 17, 5, 5, 200.00, '2025-09-18 03:53:20', '2025-09-18 03:53:20', 0.00, 0.00),
(26, 18, 4, 1, 650.00, '2025-09-18 03:55:11', '2025-09-18 03:55:11', 0.00, 0.00),
(32, 19, 3, 2, 60000.00, '2025-09-18 04:19:00', '2025-09-18 04:19:00', 0.00, 0.00),
(33, 20, 5, 3, 12000.00, '2025-09-18 04:21:58', '2025-09-18 04:21:58', 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `customer_id`, `total_amount`, `date`, `created_at`, `updated_at`) VALUES
(6, 4, 739890.00, '2025-08-07', '2025-08-07 23:52:43', '2025-08-07 23:52:43'),
(16, 4, 14160.00, '2025-08-07', '2025-08-08 00:52:48', '2025-08-08 00:52:48'),
(17, 4, 100.00, '2025-09-16', '2025-09-17 01:23:23', '2025-09-17 01:23:24'),
(18, 4, 100.00, '2025-09-16', '2025-09-17 01:23:24', '2025-09-17 01:23:24'),
(19, 5, 13452.00, '2025-09-30', '2025-10-01 02:40:04', '2025-10-01 02:40:04');

-- --------------------------------------------------------

--
-- Table structure for table `sale_items`
--

CREATE TABLE `sale_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `discount` decimal(8,2) DEFAULT NULL,
  `tax` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sale_items`
--

INSERT INTO `sale_items` (`id`, `sale_id`, `product_id`, `quantity`, `price`, `discount`, `tax`, `created_at`, `updated_at`) VALUES
(17, 6, 5, 9, 12000.00, 0.00, 0.00, '2025-08-07 23:52:43', '2025-08-07 23:52:43'),
(18, 6, 2, 7, 85000.00, 10.00, 18.00, '2025-08-07 23:52:43', '2025-08-07 23:52:43'),
(21, 16, 5, 1, 12000.00, 0.00, 18.00, '2025-08-08 00:52:48', '2025-08-08 00:52:48'),
(22, 17, 3, 1, 100.00, 0.00, 0.00, '2025-09-17 01:23:24', '2025-09-17 01:23:24'),
(23, 18, 3, 1, 100.00, 0.00, 0.00, '2025-09-17 01:23:24', '2025-09-17 01:23:24'),
(24, 19, 5, 1, 12000.00, 5.00, 18.00, '2025-10-01 02:40:04', '2025-10-01 02:40:04');

-- --------------------------------------------------------

--
-- Table structure for table `sale_returns`
--

CREATE TABLE `sale_returns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `packing` varchar(255) DEFAULT NULL,
  `qty_return` int(11) NOT NULL,
  `amount_deducted` decimal(10,2) NOT NULL,
  `total_after_return` decimal(10,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('sjNPaMVvKPPBKSHBZlhse3DqBthmcNS069rConUy', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/140.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaEFpWUpTSVNLeFI5M2tCTzRsSjBqR0xYQ3JWWHBZQUhTTmR6aEpsMCI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjE6e3M6MzoidXJsIjtzOjI5OiJodHRwOi8vMTI3LjAuMC4xOjgwMDAvcmVwb3J0cyI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjI7fQ==', 1759344852);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `balance` decimal(12,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `phone`, `email`, `address`, `balance`, `created_at`, `updated_at`) VALUES
(1, 'Bilal Khan', NULL, NULL, 'Pakistan, Karachi.', 500000.00, '2025-07-22 02:08:43', '2025-07-22 02:08:43'),
(2, 'Tabish Khan', NULL, NULL, 'Pakistan, Karachi.', 100000.00, '2025-07-22 02:09:28', '2025-07-22 02:09:28');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_payments`
--

INSERT INTO `supplier_payments` (`id`, `supplier_id`, `description`, `payment_type`, `amount`, `created_at`, `updated_at`) VALUES
(4, 1, 'thissss', 'Cash', 200000.00, '2025-07-31 02:32:30', '2025-07-31 02:32:30'),
(5, 1, 'Esse assumenda opti', 'Cheque', 8500.00, '2025-07-31 03:42:52', '2025-07-31 03:42:52'),
(6, 1, 'df  dsfd', 'Cash', 6000.00, '2025-07-31 22:25:45', '2025-07-31 22:25:45'),
(7, 2, 'ssss', 'Cash', 50000.00, '2025-08-26 00:30:38', '2025-08-26 00:30:38');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(2, 'Admin User', 'admin@vibrantengineering.pk', '2025-06-20 02:39:09', '$2y$12$dBvjXfpzPTR/YmreMhOAZusn981zVLTIy/XaqK7YOMknuOOBSKazC', 'AHQgTqXT706m6s802yQq2jd6vkG5nYLAkfjWKFZpwTSLWGFEI5TdPQUQ8mwc', '2025-06-20 02:39:09', '2025-06-20 02:39:09');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assets`
--
ALTER TABLE `assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_payments_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expense_names`
--
ALTER TABLE `expense_names`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ledgers`
--
ALTER TABLE `ledgers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `payment_types`
--
ALTER TABLE `payment_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchases_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `purchase_items`
--
ALTER TABLE `purchase_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_customer_id_foreign` (`customer_id`);

--
-- Indexes for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_items_sale_id_foreign` (`sale_id`),
  ADD KEY `sale_items_product_id_foreign` (`product_id`);

--
-- Indexes for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sale_returns_customer_id_foreign` (`customer_id`),
  ADD KEY `sale_returns_product_id_foreign` (`product_id`),
  ADD KEY `sale_returns_sale_id_foreign` (`sale_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_payments_supplier_id_foreign` (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assets`
--
ALTER TABLE `assets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `expense_names`
--
ALTER TABLE `expense_names`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ledgers`
--
ALTER TABLE `ledgers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `payment_types`
--
ALTER TABLE `payment_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `sale_returns`
--
ALTER TABLE `sale_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `purchases_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_items`
--
ALTER TABLE `sale_items`
  ADD CONSTRAINT `sale_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_items_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sale_returns`
--
ALTER TABLE `sale_returns`
  ADD CONSTRAINT `sale_returns_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_returns_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sale_returns_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  ADD CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
