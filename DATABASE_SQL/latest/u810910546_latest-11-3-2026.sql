-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 10, 2026 at 08:15 PM
-- Server version: 11.8.3-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u810910546_latest`
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
(3, 'Printers', NULL, '2025-12-18 17:22:35', '2026-01-03 09:34:35'),
(4, 'Band sealer', NULL, '2025-12-19 13:47:35', '2025-12-19 13:47:35'),
(5, 'Induction Sealers', NULL, '2025-12-19 13:49:39', '2025-12-19 13:49:39'),
(6, 'Cup Sealer', NULL, '2025-12-19 13:49:59', '2025-12-19 13:49:59'),
(7, 'Can Seamer', NULL, '2025-12-19 13:50:26', '2025-12-19 13:50:26'),
(8, 'Pedal Sealers', NULL, '2025-12-19 13:51:12', '2025-12-30 13:39:33'),
(9, 'L bar Sealers & Shrink Tunnel', NULL, '2025-12-19 13:51:40', '2025-12-19 13:51:40'),
(12, 'Labelling Machine', NULL, '2025-12-19 13:52:52', '2025-12-19 13:52:52'),
(13, 'Band Sealers (Spare Parts)', NULL, '2025-12-19 13:53:19', '2025-12-19 13:53:19'),
(14, 'Spare Parts', NULL, '2025-12-19 13:53:49', '2025-12-19 13:53:49'),
(15, 'Vacuum Sealers', NULL, '2025-12-19 13:54:08', '2025-12-19 13:54:08'),
(17, 'Conveyor Belt', NULL, '2025-12-19 13:54:54', '2025-12-19 13:54:54'),
(18, 'Carton Packaging', NULL, '2025-12-19 13:55:16', '2025-12-30 13:53:38'),
(19, 'Wrapping Machines', NULL, '2025-12-30 14:34:52', '2026-01-03 09:33:02'),
(20, 'Filling Machine', NULL, '2026-01-06 19:30:45', '2026-01-06 19:30:45');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
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

INSERT INTO `customers` (`id`, `name`, `company_name`, `phone`, `email`, `address`, `balance`, `created_at`, `updated_at`) VALUES
(2, 'Jibran Satti', 'Hub Pak Salt Refinery', '03452079654', 'admin@vibrantengineering.pk', 'Karachi', 613600.00, '2026-02-14 15:16:05', '2026-02-14 15:16:39'),
(3, 'Ghulam Nabi', 'Production House Pvt Ltd', '03323702898', 'admin@vibrantengineering.pk', 'Karachi', 850001.20, '2026-02-14 15:17:27', '2026-02-21 11:00:40'),
(4, 'Ifthikar Ahmed', 'Fauji Meat Limited', '0331 6232794', 'admin@vibrantengineering.pk', 'Karachi', 0.00, '2026-02-14 15:18:13', '2026-02-14 15:18:13'),
(5, 'Mahmood', 'ITT Foods Pvt Ltd', '0320 2646386', 'admin@vibrantengineering.pk', 'Karachi', 0.00, '2026-02-14 15:18:57', '2026-02-14 15:18:57'),
(6, 'Counter Sale', 'Counter Sale', '03352385773', 'info@vibrantengineering.pk', 'Karachi', 2073000.08, '2026-02-17 09:14:03', '2026-03-10 11:50:12'),
(7, 'Daniyal Hussain', 'Envertron', '03341344302', 'daniyal.hussain@envertron.com', 'Karachi', 90000.00, '2026-02-17 09:17:09', '2026-02-17 09:17:09'),
(8, 'Adnan', 'Maroof Bakers', '03129211448', 'admin@vibrantengineering.pk', 'Karachi', 1600.00, '2026-02-18 10:18:11', '2026-02-21 10:57:37'),
(9, 'Israr Sheikh', 'Karachi Nimco', '03406698433', 'admin@vibrantengineering.pk', 'Karachi', 4500.00, '2026-02-18 10:27:07', '2026-02-18 10:27:07'),
(10, 'Moin Hassan', 'OLX COSTUMER', '03163855471', 'admin@vibrantengineering.pk', 'Lahore', 190000.00, '2026-02-18 10:49:44', '2026-02-18 10:49:44'),
(11, 'Abdul Basit Khan', 'OLX COSTUMER', '03007403444', 'admin@vibrantengineering.pk', 'Queeta', 275000.00, '2026-02-18 10:52:37', '2026-02-27 14:14:45'),
(12, 'Muhammad Abdullah', 'Inter Mark Product Line PVT.LTD', '03000652850', 'admin@vibrantengineering.pk', 'Karachi', 41300.00, '2026-02-18 11:08:20', '2026-02-20 11:36:16'),
(13, 'Huzaifa Ahmed', 'Spore and Move', '03390063223', 'admin@vibrantengineering.pk', 'Karachi', 46000.00, '2026-02-18 13:30:36', '2026-02-18 13:30:36'),
(14, 'Hamza', 'Ishaq Engineering', '03002182209', 'admin@vibrantengineering.pk', 'Karachi', 126600.00, '2026-02-18 13:36:34', '2026-03-06 11:05:10'),
(15, 'Asif', 'Kababjees', '03352385773', 'info@vibrantengineering.pk', 'Karachi', 1103000.00, '2026-02-20 12:14:05', '2026-02-20 12:14:05'),
(16, 'Farhan', 'Kaysons', '0345-3672116', 'info@vibrantengineering.pk', 'Karachi', 370000.00, '2026-02-21 07:36:09', '2026-02-21 07:39:36'),
(17, 'Mr.Atif', 'Z&Z foods', '0321-8250105', 'info@vibrantengineering.pk', 'Karachi', 150000.00, '2026-02-23 07:36:52', '2026-02-23 07:37:09'),
(18, 'Ahsan', 'chase up', '03452567460', 'info@vibrantengineering.pk', 'Karachi', 132900.00, '2026-03-03 11:38:11', '2026-03-03 11:38:32'),
(19, 'Waseem', 'MY Engineering', '03352385773', 'info@vibrantengineering.pk', 'Karachi', 170000.00, '2026-03-06 11:57:29', '2026-03-06 12:03:29'),
(20, 'Shoaib', 'Packing Manufacture Company', '03131008100', 'info@vibrantengineering.pk', 'Karachi', 112000.00, '2026-03-09 11:52:15', '2026-03-09 11:53:09'),
(21, 'Orient', 'Orient Oil\'s PVT LTD', '03352385773', 'info@vibrantengineering.pk', 'Karachi', 220660.00, '2026-03-10 09:37:59', '2026-03-10 09:46:13');

-- --------------------------------------------------------

--
-- Table structure for table `customer_payments`
--

CREATE TABLE `customer_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `sale_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customer_payments`
--

INSERT INTO `customer_payments` (`id`, `customer_id`, `sale_id`, `description`, `payment_type`, `amount`, `created_at`, `updated_at`) VALUES
(1, 6, 20, 'Advance received against Sale Invoice #20', 'Cash', 30000.00, '2026-02-18 18:45:14', '2026-02-18 18:45:14'),
(3, 6, 18, 'Advance received against Sale Invoice #18', 'Cash', 47000.00, '2026-02-18 20:25:00', '2026-02-18 20:25:00'),
(4, 6, 13, 'Advance received against Sale Invoice #13', 'Cash', 3400.00, '2026-02-18 20:27:18', '2026-02-18 20:27:18'),
(5, 6, 10, 'Advance received against Sale Invoice #10', 'Cash', 1800.00, '2026-02-18 20:27:47', '2026-02-18 20:27:47'),
(9, 2, 6, 'Cheque deposited in Vibrant Engineering Meezan', 'Cheque', 613600.00, '2026-02-18 21:20:22', '2026-02-18 21:20:22'),
(10, 7, 8, NULL, 'Bank Transfer', 90000.00, '2026-02-19 18:46:30', '2026-02-19 18:46:30'),
(11, 9, 12, NULL, 'Bank Transfer', 4500.00, '2026-02-19 18:46:48', '2026-02-19 18:46:48'),
(12, 10, 7, NULL, 'Bank Transfer', 190000.00, '2026-02-19 18:47:06', '2026-02-19 18:47:06'),
(13, 11, 14, NULL, 'Bank Transfer', 110000.00, '2026-02-19 18:47:25', '2026-02-19 18:47:25'),
(14, 13, 17, NULL, 'Bank Transfer', 46000.00, '2026-02-19 18:47:40', '2026-02-19 18:47:40'),
(15, 14, 19, NULL, 'Bank Transfer', 72600.00, '2026-02-19 18:47:54', '2026-02-19 18:47:54'),
(16, 6, 16, 'Advance received against Sale Invoice #16', 'Cash', 600.00, '2026-02-20 10:40:08', '2026-02-20 10:40:08'),
(17, 12, 15, 'Advance received against Sale Invoice #15', 'Cash', 41300.00, '2026-02-20 11:36:16', '2026-02-20 11:36:16'),
(18, 6, 21, 'Advance received against Sale Invoice #21', 'Cash', 2400.00, '2026-02-20 12:07:08', '2026-02-20 12:07:08'),
(20, 16, 23, 'Advance received against Sale Invoice #23', 'Cash', 370000.00, '2026-02-21 07:39:36', '2026-02-21 07:39:36'),
(21, 6, 25, 'Advance received against Sale Invoice #25', 'Cash', 46000.00, '2026-02-21 10:37:58', '2026-02-21 10:37:58'),
(22, 6, 24, 'Advance received against Sale Invoice #24', 'Cash', 48500.00, '2026-02-21 10:38:28', '2026-02-21 10:38:28'),
(23, 8, 11, 'Advance received against Sale Invoice #11', 'Cash', 1600.00, '2026-02-21 10:57:37', '2026-02-21 10:57:37'),
(24, 3, 9, 'Advance received against Sale Invoice #9', 'Cash', 800000.00, '2026-02-21 11:00:40', '2026-02-21 11:00:40'),
(26, 17, 26, 'Advance received against Sale Invoice #26', 'Cash', 150000.00, '2026-02-23 07:37:09', '2026-02-23 07:37:09'),
(27, 6, 27, 'Advance received against Sale Invoice #27', 'Cash', 60000.00, '2026-02-23 10:34:35', '2026-02-23 10:34:35'),
(28, 6, 28, 'Sale Return - Reduced Rs 25000.00 from Invoice #28 for 1 unit(s) of FRE-500', 'Cash', 0.00, '2026-02-23 10:35:05', '2026-03-03 18:16:29'),
(30, 6, 22, 'Advance received against Sale Invoice #22', 'Cash', 15000.00, '2026-02-23 12:02:11', '2026-02-23 12:02:11'),
(31, 6, 30, 'Advance received against Sale Invoice #30', 'Cash', 7500.00, '2026-02-23 20:55:52', '2026-02-23 20:55:52'),
(32, 6, 31, 'Advance received against Sale Invoice #31', 'Cash', 72000.00, '2026-02-24 11:03:43', '2026-02-24 11:03:43'),
(34, 11, 34, 'Advance received against Sale Invoice #34', 'Cash', 165000.00, '2026-02-27 14:14:45', '2026-02-27 14:14:45'),
(35, 3, 9, NULL, 'Bank Transfer', 50001.20, '2026-02-28 01:56:34', '2026-03-07 01:00:48'),
(36, 6, 35, 'Advance received against Sale Invoice #35', 'Cash', 30000.00, '2026-02-28 12:19:41', '2026-02-28 12:19:41'),
(39, 6, 38, 'Advance received against Sale Invoice #38', 'Cash', 22000.00, '2026-03-03 09:34:19', '2026-03-03 09:34:19'),
(40, 18, 33, 'Advance received against Sale Invoice #33', 'Cash', 132900.00, '2026-03-03 11:38:32', '2026-03-03 11:38:32'),
(41, 14, 37, 'Advance received against Sale Invoice #37', 'Cash', 36000.00, '2026-03-03 11:47:39', '2026-03-03 11:47:39'),
(43, 6, 40, 'Advance received against Sale Invoice #40', 'Cash', 8500.00, '2026-03-03 12:25:41', '2026-03-03 12:25:41'),
(44, 6, 41, 'Advance received against Sale Invoice #41', 'Cash', 2200.00, '2026-03-04 10:48:38', '2026-03-04 10:48:38'),
(45, 6, 42, 'Advance received against Sale Invoice #42', 'Cash', 30000.00, '2026-03-06 07:57:02', '2026-03-06 07:57:02'),
(46, 6, 39, 'Advance received against Sale Invoice #39', 'Cash', 55000.00, '2026-03-06 07:58:55', '2026-03-06 07:58:55'),
(47, 6, 43, 'Advance received against Sale Invoice #43', 'Cash', 7000.00, '2026-03-06 10:35:58', '2026-03-06 10:35:58'),
(48, 14, 44, 'Advance received against Sale Invoice #44', 'Cash', 18000.00, '2026-03-06 11:05:10', '2026-03-06 11:05:10'),
(49, 6, 45, 'Advance received against Sale Invoice #45', 'Cash', 4000.00, '2026-03-06 11:07:47', '2026-03-06 11:07:47'),
(51, 6, 46, 'Advance received against Sale Invoice #46', 'Cash', 22000.00, '2026-03-06 12:30:23', '2026-03-06 12:30:23'),
(52, 6, 47, 'Advance received against Sale Invoice #47', 'Cash', 1507100.00, '2026-03-07 01:08:00', '2026-03-07 01:08:00'),
(53, 6, 48, 'Advance received against Sale Invoice #48', 'Cash', 1000.00, '2026-03-07 09:51:43', '2026-03-07 09:51:43'),
(54, 6, 49, 'Advance received against Sale Invoice #49', 'Cash', 3000.00, '2026-03-07 12:03:26', '2026-03-07 12:03:26'),
(55, 6, 50, 'Advance received against Sale Invoice #50', 'Cash', 3500.00, '2026-03-07 12:15:24', '2026-03-07 12:15:24'),
(56, 6, 51, 'Advance received against Sale Invoice #51', 'Cash', 1500.00, '2026-03-09 10:15:11', '2026-03-09 10:15:11'),
(57, 19, 36, NULL, 'Cash', 170000.00, '2026-03-09 10:28:30', '2026-03-09 10:28:30'),
(58, 20, 52, 'Advance received against Sale Invoice #52', 'Cash', 112000.00, '2026-03-09 11:53:09', '2026-03-09 11:53:09'),
(61, 6, 53, 'Advance received against Sale Invoice #53', 'Cash', 33000.00, '2026-03-10 11:50:12', '2026-03-10 11:50:12');

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
(7, 2, 1, 'Roti', 100.00, '2026-02-18 12:28:59', '2026-02-18 12:28:59'),
(8, 3, 1, 'paid to Noman', 100.00, '2026-02-18 12:36:29', '2026-02-21 07:34:12'),
(9, 5, 1, 'Paid to bykea rider for deliver cans in office (kababjees asif bagla)', 480.00, '2026-02-18 13:43:02', '2026-02-18 13:43:02'),
(10, 6, 3, 'paid to ware house basement', 4000.00, '2026-02-18 13:47:09', '2026-02-18 13:47:09'),
(11, 5, 1, 'paid good\'s bilti charges', 1300.00, '2026-02-18 13:48:31', '2026-02-18 13:48:31'),
(12, 3, 1, 'Mr.akif paid to noman', 100.00, '2026-02-18 13:49:30', '2026-02-18 13:49:30'),
(13, 2, 1, 'Roti', 120.00, '2026-02-18 13:50:12', '2026-02-18 13:50:21'),
(15, 3, 1, 'paid to Noman for deliver Can Seamer (Korangi)', 500.00, '2026-02-18 13:55:04', '2026-02-18 13:55:04'),
(16, 1, 1, 'paid to auto charges (Induction Sealer , Asfar)', 800.00, '2026-02-18 13:56:14', '2026-02-18 13:56:14'),
(17, 10, 1, 'paid to auto charges for deliver goods (Abdul Basit Khan, fr-900ms HQ, Granule Filler 100GM', 800.00, '2026-02-18 13:58:48', '2026-03-07 09:07:28'),
(18, 8, 1, 'yellow can seamer Dye 7000\r\nBlk can Seamer Dye 4000', 11000.00, '2026-02-20 11:48:33', '2026-02-23 11:30:28'),
(19, 8, 3, 'paid to Electricity bill', 10062.00, '2026-02-20 11:53:32', '2026-02-20 11:53:32'),
(20, 9, 3, 'paid to sales Tax', 21298.00, '2026-02-20 11:55:11', '2026-02-20 11:55:11'),
(21, 4, 1, 'paid to noman to make a liquid machine nozels (made by tanveer bhai)', 500.00, '2026-02-20 12:27:44', '2026-02-20 12:27:44'),
(22, 10, 1, 'paid to Mr.Noman for TCS (teflone tape , multan)', 640.00, '2026-02-21 10:41:22', '2026-02-21 10:41:22'),
(23, 1, 1, 'paid to Yango Driver to deliver pet cans in office (Athar Bhai)', 670.00, '2026-02-21 10:43:11', '2026-02-21 10:43:11'),
(24, 11, 1, 'paid to noman for tcs 1000 and this Remaining amount has been less on machine repairing service', 360.00, '2026-02-23 08:24:31', '2026-02-23 08:26:37'),
(25, 1, 1, 'paid to Noman for deliver goods in bilti station (Auto Charges)', 600.00, '2026-02-23 10:37:58', '2026-02-23 10:37:58'),
(26, 12, 1, 'paid for sale discount Againts inv # 23', 200.00, '2026-02-23 10:41:24', '2026-02-23 10:41:24'),
(27, 1, 1, 'paid to suzuki rent for deliver machines \r\n(shrink Tunnel and L-bar sealer)', 2000.00, '2026-02-23 10:42:39', '2026-02-23 10:42:39'),
(28, 13, 3, 'basement Warehouse rent nazimabad no 2', 23000.00, '2026-02-23 11:58:54', '2026-02-23 11:59:13'),
(29, 3, 1, 'Paid to noman for fuel (office to cooperative & cooperative to office)', 300.00, '2026-02-26 11:04:48', '2026-02-26 11:04:48'),
(30, 14, 1, 'paid to Mr noman', 300.00, '2026-02-27 06:55:21', '2026-02-27 06:55:21'),
(31, 1, 1, 'paid to mr.Luqman for auto charges to deliver goods in bilti station', 400.00, '2026-02-27 06:56:26', '2026-02-27 06:56:26'),
(32, 15, 1, '3000 each person (Noman,Luqman,Hamza)', 9000.00, '2026-02-27 07:05:21', '2026-02-27 07:05:21'),
(33, 3, 1, 'paid to noman for office bike fuel', 200.00, '2026-02-27 12:23:16', '2026-02-27 12:23:16'),
(34, 10, 1, 'paid to norullah for receive goods in shalimar bilti station', 500.00, '2026-02-27 12:24:43', '2026-02-27 12:24:43'),
(35, 1, 1, 'paid to norullah for deliver bilti goods \r\n(shalimar bilti station to passport office bilti station)', 1200.00, '2026-02-28 12:23:26', '2026-02-28 12:23:26'),
(36, 3, 1, 'paid to luqman for office bike', 100.00, '2026-03-06 09:20:25', '2026-03-06 09:20:25'),
(37, 1, 1, 'paid to auto driver to pickup goods from the bilti station and bring it to the office', 1200.00, '2026-03-06 09:24:04', '2026-03-06 09:24:04'),
(39, 8, 1, 'paid to mr.noman for purchase pad printer ink and chemical', 2000.00, '2026-03-06 12:25:40', '2026-03-06 12:25:40'),
(40, 13, 1, 'paid to rent exp for nazimabad no 1 Ware house', 22500.00, '2026-03-06 12:48:56', '2026-03-07 07:07:45'),
(42, 4, 1, 'paid to macine pc repairing and casting', 1100.00, '2026-03-07 09:35:59', '2026-03-07 09:35:59'),
(43, 8, 1, 'Car punctured', 300.00, '2026-03-07 09:37:54', '2026-03-07 09:37:54'),
(44, 16, 1, 'complementary againts inv # 48', 1000.00, '2026-03-07 09:54:20', '2026-03-07 09:55:07'),
(45, 17, 1, 'paid to akif bhai for office bike tyre purchase  2200 and fiting charges 150', 2350.00, '2026-03-07 12:08:41', '2026-03-07 12:08:41'),
(46, 16, 1, 'complementary againts inv # 50', 3500.00, '2026-03-07 12:17:58', '2026-03-07 12:17:58'),
(47, 3, 1, 'office bike fuel', 100.00, '2026-03-09 07:47:52', '2026-03-09 07:47:52'),
(48, 3, 1, 'office bike fuel', 150.00, '2026-03-09 07:48:29', '2026-03-09 07:48:29'),
(49, 10, 1, 'paid to noman for deliver goods by tcs', 500.00, '2026-03-09 07:49:32', '2026-03-09 07:49:32'),
(50, 1, 1, 'paid to noman for delivery pet cans', 450.00, '2026-03-09 07:50:18', '2026-03-09 07:50:18'),
(51, 10, 1, 'paid to noman for receive good by shalimar bilti station', 850.00, '2026-03-09 11:32:10', '2026-03-10 11:40:04'),
(52, 3, 1, 'for office bike', 100.00, '2026-03-10 10:19:15', '2026-03-10 10:19:15'),
(53, 6, 1, 'paid to ahsan for warehouse basement Maintenance', 4000.00, '2026-03-10 11:02:48', '2026-03-10 11:02:48'),
(57, 3, 1, 'paid fuel exp for hyderi bilti purpose', 150.00, '2026-03-10 12:04:55', '2026-03-10 12:04:55');

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
(1, 'Transport Expenses', '2025-12-19 13:20:05', '2026-02-21 10:42:12'),
(2, 'Lunch Expense', '2025-12-19 13:20:32', '2025-12-19 13:20:32'),
(3, 'Fuel Expense', '2025-12-19 13:20:54', '2025-12-19 13:20:54'),
(4, 'Repair & Maintenance', '2025-12-19 13:28:10', '2025-12-19 13:28:10'),
(5, 'OFFICE EXPENSE', '2026-02-18 13:41:28', '2026-02-18 13:41:28'),
(6, 'Maintenance Expense', '2026-02-18 13:45:18', '2026-02-18 13:45:18'),
(7, 'Can Seamer Dye', '2026-02-20 11:47:42', '2026-02-20 11:47:42'),
(8, 'Shop Exp', '2026-02-20 11:50:08', '2026-02-20 11:50:08'),
(9, 'Monthly Sale Tax', '2026-02-20 11:54:27', '2026-02-20 11:54:27'),
(10, 'Courier expense', '2026-02-21 10:40:04', '2026-02-21 10:40:04'),
(11, 'Machine Repairing', '2026-02-23 08:05:06', '2026-02-23 08:05:06'),
(12, 'Sale Discount', '2026-02-23 10:40:08', '2026-02-23 10:40:18'),
(13, 'Rent Exp', '2026-02-23 11:57:16', '2026-02-23 11:57:16'),
(14, 'Advance Salary', '2026-02-27 06:54:42', '2026-02-27 06:54:42'),
(15, 'Ramadan Grocery bonus 2026', '2026-02-27 07:00:04', '2026-02-27 07:00:04'),
(16, 'Discount', '2026-03-07 09:53:15', '2026-03-07 12:13:53'),
(17, 'Bike Exp', '2026-03-07 12:06:00', '2026-03-07 12:06:00'),
(18, 'Salary Exp', '2026-03-10 11:13:33', '2026-03-10 11:13:33');

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
(52, '2025_10_01_163118_add_sale_id_to_sale_returns_table', 22),
(53, '2025_11_24_194917_add_company_name_to_customers_table', 23),
(54, '2025_11_24_203830_add_company_name_to_suppliers_table', 24),
(55, '2026_02_18_182647_add_sale_id_to_customer_payments_table', 25),
(56, '2026_02_20_223436_add_purchase_id_to_supplier_payments_table', 26),
(57, '2026_03_10_174354_add_role_to_users_table', 27);

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
(1, 'Cash', '2025-12-19 13:22:06', '2025-12-19 13:22:06'),
(3, 'Fuzail Qureshi - Meezan', '2026-02-17 09:51:20', '2026-02-17 09:51:20'),
(4, 'Vibrant Enterprises', '2026-02-17 09:51:53', '2026-02-17 09:51:53'),
(5, 'Vibrant Engineering - Meezan', '2026-02-17 09:52:10', '2026-02-17 09:52:10');

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
(1, 4, 'FR-900 MS Metal Gear', 1, NULL, 42, 28200.00, '2026-01-06 19:30:45', '2026-03-06 07:58:55'),
(2, 4, 'FR-900 MS Plastic Gear', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 14:19:53'),
(3, 4, 'FR-900 SS Metal Gear', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 14:20:16'),
(4, 4, 'FR-900 MS Metal Gear without switch', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 14:20:28'),
(5, 4, 'FR-900M Mini Band Sealer', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 14:20:40'),
(6, 4, 'FR-900 SS HQ Dingye', 1, NULL, 23, 41100.00, '2026-01-06 19:30:45', '2026-02-14 14:24:38'),
(7, 4, 'FR-900 MS HQ Dingye', 1, NULL, 9, 37000.00, '2026-01-06 19:30:45', '2026-02-27 14:14:45'),
(8, 4, 'FR-1100V Dingye', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:24:33'),
(9, 4, 'FR-1100C Dingye', 1, NULL, 2, 76000.00, '2026-01-06 19:30:45', '2026-02-14 14:29:14'),
(10, 4, 'QLF-1680', 1, NULL, 1, 314300.00, '2026-01-06 19:30:45', '2026-02-18 10:56:48'),
(11, 7, 'RC-12K Digital Can Seamer', 1, NULL, 2, 108000.00, '2026-01-06 19:30:45', '2026-03-06 12:03:29'),
(12, 7, 'TDFJ-160 Yellow Can Seamer', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:22:10'),
(13, 18, 'KZ-900 Dingye', 1, NULL, 6, 105850.00, '2026-01-06 19:30:45', '2026-03-10 09:46:13'),
(14, 18, 'ST-900', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:20:21'),
(16, 18, 'FXJ-6050 Case Sealer', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:19:18'),
(17, 18, 'FXJ-4030 Case Sealer', 1, NULL, 1, 180000.00, '2026-01-06 19:30:45', '2026-02-12 13:03:56'),
(18, 18, 'DBC-800S Box Wrapping Machine', 1, NULL, 1, 192000.00, '2026-01-06 19:30:45', '2026-02-12 11:43:00'),
(19, 9, 'BS-4020 Roller Conveyor', 1, NULL, 4, 81000.00, '2026-01-06 19:30:45', '2026-02-21 07:39:36'),
(20, 9, 'BS-4020 Mesh Conveyor', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:17:03'),
(21, 9, 'BS-4020L', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:16:25'),
(22, 9, 'BSS-1538B Label Shrinker', 1, NULL, 2, 264100.00, '2026-01-06 19:30:45', '2026-02-12 11:38:38'),
(23, 9, 'BS-4525 Jet Tunnel', 1, NULL, 1, 123500.00, '2026-01-06 19:30:45', '2026-02-12 13:20:25'),
(24, 9, 'BS-4535 Shrink Tunnel', 1, NULL, 1, 111500.00, '2026-01-06 19:30:45', '2026-02-12 13:00:14'),
(25, 9, 'BSP-5040 PE Tunnel', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:15:22'),
(26, 9, 'BS-2615', 1, NULL, 1, 62000.00, '2026-01-06 19:30:45', '2026-02-12 14:00:42'),
(28, 9, 'BS-4525A', 1, NULL, 2, 100000.00, '2026-01-06 19:30:45', '2026-02-12 12:56:43'),
(30, 9, 'DFM-5540 2 in 1', 1, NULL, 1, 244850.00, '2026-01-06 19:30:45', '2026-02-14 14:27:34'),
(31, 19, 'HW-450 Hand Wrapper', 1, NULL, 8, 11450.00, '2026-01-06 19:30:45', '2026-02-14 14:40:41'),
(32, 19, 'HW-550 Hand Wrapper', 1, NULL, 2, 17200.00, '2026-01-06 19:30:45', '2026-02-12 14:07:08'),
(33, 19, 'ACW-88 Perfume Wrapper', 1, NULL, 1, 185000.00, '2026-01-06 19:30:45', '2026-02-14 14:38:59'),
(34, 6, 'WY-806B Verly', 1, NULL, 14, 13500.00, '2026-01-06 19:30:45', '2026-03-06 11:05:10'),
(35, 6, 'ZYF-07 Fully Auto', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:09:34'),
(36, 6, 'DY-70A 4 Cup Sealer', 1, NULL, 1, 128000.00, '2026-01-06 19:30:45', '2026-02-14 14:37:56'),
(37, 6, 'DY-2014A Semi Auto Tray Sealer', 1, NULL, 1, 111500.00, '2026-01-06 19:30:45', '2026-02-12 12:14:44'),
(38, 6, 'QD-2014P Fully Auto Tray Sealer', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:08:18'),
(39, 5, 'DGYF-S500A', 1, NULL, 24, 21400.00, '2026-01-06 19:30:45', '2026-03-10 11:50:12'),
(40, 5, 'DGYF-S500B', 1, NULL, 4, 26200.00, '2026-01-06 19:30:45', '2026-02-21 11:00:40'),
(41, 5, 'DGYF-S500C', 1, NULL, 0, 26152.00, '2026-01-06 19:30:45', '2026-02-21 11:00:40'),
(42, 5, 'DGYF-S500D', 1, NULL, 7, 26000.00, '2026-01-06 19:30:45', '2026-02-12 14:07:32'),
(43, 5, 'DGYF-S400A', 1, NULL, 1, 11500.00, '2026-01-06 19:30:45', '2026-02-12 13:13:19'),
(44, 5, 'DGYF-S400C', 1, NULL, 2, 15800.00, '2026-01-06 19:30:45', '2026-02-12 13:12:27'),
(45, 5, 'LGYF-2000AX', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:05:35'),
(46, 5, 'LGYF-2000BX', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:05:17'),
(47, 5, 'LGYF-1500A', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:04:56'),
(48, 5, 'LGYF-2100B', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:04:41'),
(49, 5, 'LGYF-1900A', 1, NULL, 1, 160000.00, '2026-01-06 19:30:45', '2026-02-12 13:01:29'),
(50, 5, 'LGYF-1900B', 1, NULL, 1, 183500.00, '2026-01-06 19:30:45', '2026-02-12 13:02:34'),
(51, 15, 'DZ-500', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:03:22'),
(52, 15, 'DZ-400', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:03:02'),
(53, 15, 'DZ-320', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:02:46'),
(54, 15, 'DZ-260C', 1, NULL, 2, 50000.00, '2026-01-06 19:30:45', '2026-02-12 14:05:07'),
(55, 15, 'DZ-260 Digital', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 07:01:52'),
(56, 15, 'LF-1080B', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-14 08:04:49'),
(57, 15, 'DZ-5002S Double Chamber', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 11:18:32'),
(59, 3, 'TIJ DC Jet (1INCH)', 1, NULL, 4, 52000.00, '2026-01-06 19:30:45', '2026-02-12 14:22:03'),
(61, 3, 'Assembly Printer DC Jet', 1, NULL, 1, 105000.00, '2026-01-06 19:30:45', '2026-02-12 14:36:16'),
(62, 3, 'TDY-380C Pad Printer', 1, NULL, 1, 42000.00, '2026-01-06 19:30:45', '2026-02-24 11:03:43'),
(63, 3, 'DY-8B', 1, NULL, 21, 13500.00, '2026-01-06 19:30:45', '2026-02-12 13:14:46'),
(64, 3, 'MY-380', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:13:43'),
(66, 3, 'Date Coder DS-1188', 1, NULL, 0, 50000.00, '2026-01-06 19:30:45', '2026-03-09 11:53:09'),
(67, 12, 'MT-50 Round Bottle labeler', 1, NULL, 1, 72000.00, '2026-01-06 19:30:45', '2026-03-09 11:19:09'),
(68, 12, 'MT-60 Flat Bottle Labeler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:11:18'),
(69, 20, 'SN-1000 Paste Filler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:10:34'),
(71, 20, 'SN-300 Paste Filler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:07:33'),
(72, 20, 'SN-100 Paste Filler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:06:55'),
(73, 20, 'DN-1000 Paste Filler', 1, NULL, 1, 178000.00, '2026-01-06 19:30:45', '2026-02-12 14:01:10'),
(74, 20, 'DN-500 Paste Filler', 1, NULL, 1, 171000.00, '2026-01-06 19:30:45', '2026-02-12 13:05:21'),
(75, 20, 'DN-300 Paste Filler', 1, NULL, 1, 145074.00, '2026-01-06 19:30:45', '2026-02-12 12:53:42'),
(76, 20, 'DN-100 Paste Filler', 1, NULL, 1, 143000.00, '2026-01-06 19:30:45', '2026-02-12 12:53:18'),
(77, 20, 'FZ-100 Powder Filler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:05:49'),
(78, 20, 'FZ-1000 Powder Filler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:05:24'),
(79, 20, 'FZ-500 Powder Filler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:04:55'),
(80, 20, 'A03 Manual Filler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:04:28'),
(81, 20, 'A02 PNEUMATIC Filler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:04:05'),
(82, 20, 'GFK-160 Liquid Pump Filler', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:03:05'),
(83, 17, 'SS China Canveyor', 1, NULL, 1, 67000.00, '2026-01-06 19:30:45', '2026-02-12 12:50:23'),
(84, 17, 'Feeder Conveyor', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-12 15:03:36'),
(85, 8, 'FRE-400', 1, NULL, 1, 12000.00, '2026-01-06 19:30:45', '2026-02-12 14:04:10'),
(86, 8, 'FRE-500', 1, NULL, 5, 13000.00, '2026-01-06 19:30:45', '2026-03-10 16:13:23'),
(87, 8, 'FRE-600', 1, NULL, 0, 14500.00, '2026-01-06 19:30:45', '2026-03-06 12:30:23'),
(88, 8, 'PFS-350 HQ 14inch', 1, NULL, 0, 38000.00, '2026-01-06 19:30:45', '2026-03-03 11:38:32'),
(89, 8, 'PFS-450 HQ 18 INCH', 1, NULL, 0, 39000.00, '2026-01-06 19:30:45', '2026-03-03 11:38:32'),
(90, 8, 'PFS-600 HQ (24 INCH)', 1, NULL, 1, 46000.00, '2026-01-06 19:30:45', '2026-02-13 07:39:34'),
(92, 8, 'Unique Heat Sealer', 1, NULL, 1, 16000.00, '2026-01-06 19:30:45', '2026-02-12 14:40:05'),
(93, 8, 'Hand Sealer 8mm 8inch', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 06:57:59'),
(94, 8, 'Hand Sealer 8mm 12inch', 1, NULL, 0, 0.00, '2026-01-06 19:30:45', '2026-02-13 06:57:41'),
(97, 20, 'SN-FILLER 500ML', 1, NULL, 3, 75000.00, '2026-02-12 12:03:06', '2026-02-12 12:04:10'),
(98, 8, 'PFS-650 (26INCH)', 1, NULL, 3, 50000.00, '2026-02-12 14:03:45', '2026-02-12 14:03:45'),
(99, 14, 'PET CANS', 1, NULL, 3000, 37.00, '2026-02-12 14:09:35', '2026-02-12 14:13:23'),
(100, 4, 'BAG CLOSER', 1, NULL, 2, 9500.00, '2026-02-12 14:11:46', '2026-02-12 14:11:46'),
(101, 9, 'BS-3020 SHRINK TUNNEL', 1, NULL, 1, 73000.00, '2026-02-12 14:14:28', '2026-02-12 14:14:28'),
(102, 7, 'RETORT CAN MACHINE', 1, NULL, 1, 250000.00, '2026-02-12 14:17:37', '2026-02-12 14:17:37'),
(103, 3, 'TIJ PRINTER ZK-1680', 1, NULL, 4, 35000.00, '2026-02-12 14:22:39', '2026-02-13 11:15:50'),
(104, 20, 'GRANUILE FILLER 500G', 1, NULL, 1, 72000.00, '2026-02-12 14:41:18', '2026-02-12 14:41:18'),
(105, 20, 'GRANUILE FILLER 1000G', 1, NULL, 1, 100000.00, '2026-02-12 14:41:43', '2026-02-12 14:41:43'),
(106, 3, 'TIJ DC JET PRINTER', 1, NULL, 3, 40000.00, '2026-02-12 14:42:36', '2026-03-06 11:46:43'),
(107, 3, 'BARCODE PRINTER', 1, NULL, 1, 12500.00, '2026-02-12 14:43:35', '2026-02-12 14:43:35'),
(108, 8, 'PFS-800', 1, NULL, 1, 72000.00, '2026-02-12 14:47:46', '2026-02-18 10:55:42'),
(109, 9, 'FQL-450', 1, NULL, 0, 161500.00, '2026-02-12 15:01:46', '2026-02-21 07:39:36'),
(110, 13, 'FR-900 TEETH BELT', 1, NULL, 177, 50.00, '2026-02-13 14:11:11', '2026-03-04 10:48:38'),
(111, 13, 'TEFLON BELT 750MM NORMAL', 1, NULL, 1764, 47.00, '2026-02-13 14:17:32', '2026-03-04 10:48:38'),
(112, 13, 'TEFLON BELT 750MM HQ', 1, NULL, 881, 86.00, '2026-02-13 14:18:38', '2026-02-18 18:45:59'),
(113, 13, 'TEFLON BELT 770MM NORMAL', 1, NULL, 226, 72.00, '2026-02-13 14:21:26', '2026-02-20 10:40:08'),
(114, 13, 'FR-900 NORMAL BUTTON', 1, NULL, 97, 300.00, '2026-02-13 14:22:37', '2026-02-20 12:07:08'),
(115, 13, 'FR-900 HQ BUTTONS', 1, NULL, 26, 340.00, '2026-02-13 14:23:54', '2026-02-13 14:23:54'),
(116, 13, 'FR-900 RUBBER ROLLER', 1, NULL, 25, 346.00, '2026-02-13 14:24:48', '2026-03-09 10:15:11'),
(117, 13, 'FR-900 HEATER', 1, NULL, 35, 290.00, '2026-02-13 14:25:15', '2026-02-23 20:55:52'),
(118, 13, 'FR-900 HQ BLOWER', 1, NULL, 2, 1650.00, '2026-02-13 14:25:54', '2026-02-13 14:25:54'),
(119, 13, 'FR-900 CONVEYOR GUIDE ROLLER', 1, NULL, 20, 1250.00, '2026-02-13 14:26:33', '2026-02-13 14:26:33'),
(120, 13, 'FR-900 RELAY', 1, NULL, 37, 375.00, '2026-02-13 14:27:18', '2026-02-13 14:27:18'),
(121, 14, 'TEFLON TAPE', 1, NULL, 0, 1450.00, '2026-02-13 14:28:14', '2026-02-20 12:09:42'),
(122, 14, 'BS-4020 HEATER', 1, NULL, 5, 2000.00, '2026-02-13 14:28:46', '2026-02-13 14:28:46'),
(123, 14, '32X36 INK ROLLER FINERAY', 1, NULL, 348, 170.00, '2026-02-13 14:29:42', '2026-03-07 12:03:26'),
(124, 14, '36X40 INK ROLLER FINERAY', 1, NULL, 156, 200.00, '2026-02-13 14:30:25', '2026-03-06 11:07:47'),
(125, 13, 'FR-900 MOTOR', 1, NULL, 14, 4490.00, '2026-02-13 14:31:25', '2026-02-13 14:31:25'),
(126, 14, 'PRINTER CONECTOR WIRE', 1, NULL, 2, 1500.00, '2026-02-13 14:34:59', '2026-02-13 14:34:59'),
(127, 13, 'FR-900 SEALING BELT ROLLER', 1, NULL, 25, 375.00, '2026-02-13 14:36:36', '2026-02-13 14:36:36'),
(128, 13, 'FR-900 SMALL GUIDE ROLLER', 1, NULL, 20, 332.00, '2026-02-13 14:37:29', '2026-02-13 14:37:29'),
(129, 14, 'TRAY SEALER PENCIL HEATER', 1, NULL, 5, 2500.00, '2026-02-13 14:39:11', '2026-02-13 14:39:11'),
(130, 13, 'FR-900 SPEED CONECTOR', 1, NULL, 8, 100.00, '2026-02-13 14:39:42', '2026-02-13 14:39:42'),
(131, 13, 'FR-900 HEAT BLOCKER', 1, NULL, 14, 1700.00, '2026-02-13 14:40:27', '2026-02-13 14:59:25'),
(132, 14, 'SENSOR LIQUID FILLING', 1, NULL, 10, 1500.00, '2026-02-13 14:41:11', '2026-02-13 14:41:11'),
(133, 14, 'PRINTER PAD 2INCH', 1, NULL, 4, 3500.00, '2026-02-13 14:42:08', '2026-03-07 12:15:24'),
(134, 14, 'PRINTER PAD 1/5INCH', 1, NULL, 4, 2400.00, '2026-02-13 14:42:47', '2026-02-13 14:42:47'),
(135, 14, 'PRINTER PAD 1 INCH', 1, NULL, 2, 1000.00, '2026-02-13 14:44:01', '2026-02-13 14:44:01'),
(136, 14, 'PAD PRINTER SPEED CONECTOR', 1, NULL, 7, 100.00, '2026-02-13 14:44:35', '2026-02-13 14:44:35'),
(137, 13, 'FR-900 GARARI', 1, NULL, 5, 2700.00, '2026-02-13 14:45:08', '2026-02-13 14:45:08'),
(138, 14, 'PRINTER SENSOR DC JET', 1, NULL, 9, 2500.00, '2026-02-13 14:45:44', '2026-02-13 14:45:44'),
(139, 13, 'FR-900 FANS', 1, NULL, 5, 300.00, '2026-02-13 14:46:17', '2026-02-13 14:46:17'),
(140, 14, 'INDUCTION SEALER BUTTON', 1, NULL, 29, 100.00, '2026-02-13 14:47:05', '2026-02-13 14:47:05'),
(141, 14, 'DATE CODER CONTROL CARD', 1, NULL, 3, 2700.00, '2026-02-13 14:47:57', '2026-02-13 14:47:57'),
(142, 14, 'DIGIT COMB DK-1100A', 1, NULL, 8, 3000.00, '2026-02-13 14:48:38', '2026-02-13 14:48:38'),
(143, 14, 'BS-4020 SHRINK TUNNEL RELAY', 1, NULL, 4, 1500.00, '2026-02-13 14:49:15', '2026-02-13 14:49:15'),
(144, 14, 'MANUAL CUP SEALER HEATER', 1, NULL, 10, 1850.00, '2026-02-13 14:50:02', '2026-02-13 14:50:02'),
(145, 14, 'MANUAL CUP SEALER CUTTER', 1, NULL, 10, 1850.00, '2026-02-13 14:50:37', '2026-02-13 14:50:37'),
(146, 13, 'TEMPRATURE CONTROLLER', 1, NULL, 7, 1300.00, '2026-02-13 14:51:09', '2026-02-23 20:55:52'),
(147, 13, 'FR-900 HEAVY DUTY MOTOR', 1, NULL, 1, 7500.00, '2026-02-13 14:51:49', '2026-02-13 14:51:49'),
(148, 14, 'L-BAR SEALER ELEMENT', 1, NULL, 48, 1000.00, '2026-02-13 14:52:27', '2026-03-07 09:51:43'),
(149, 14, 'L-BAR SEALER SILICON STRAP', 1, NULL, 21, 1000.00, '2026-02-13 14:53:17', '2026-03-06 10:35:58'),
(150, 14, 'AIR COMPRESOR 50LTR', 1, NULL, 1, 29000.00, '2026-02-13 14:54:01', '2026-02-13 14:54:01'),
(151, 4, 'Vertical Stand HQ', 1, NULL, 14, 5000.00, '2026-02-14 14:14:16', '2026-03-03 12:25:41'),
(152, 14, 'BLACK INK RIBBON 35MM', 1, NULL, 95, 232.00, '2026-02-14 14:47:43', '2026-02-18 20:27:47'),
(153, 13, 'FR-1100V TEFLON BELT 1210*15', 1, NULL, 50, 213.00, '2026-02-14 14:49:31', '2026-02-18 18:45:45'),
(154, 13, 'FR-1100V HEATER', 1, NULL, 10, 1660.00, '2026-02-14 14:50:25', '2026-02-28 17:46:02'),
(155, 13, 'QLF-1680 HEATER', 1, NULL, 10, 1220.00, '2026-02-14 14:51:18', '2026-02-28 17:45:55'),
(156, 20, 'GRANULE FILLER 100gm', 1, NULL, 0, 35000.00, '2026-02-18 10:12:07', '2026-03-09 12:08:00'),
(157, 14, 'Repair & Maintenance', 1, NULL, 100, 0.00, '2026-02-20 12:16:04', '2026-02-24 11:19:40'),
(159, 8, 'Impulse Foot Pedal Sealer 18inch', 1, NULL, 0, 23000.00, '2026-02-27 06:51:35', '2026-03-06 07:57:02'),
(160, 14, 'February Sale 2026 1-13th', 1, NULL, 0, 1140969.00, '2026-03-07 01:06:59', '2026-03-07 01:08:00'),
(161, 3, 'TIJ DC Jet Printer Ink Cartridge', 1, NULL, 2, 13000.00, '2026-03-09 10:32:36', '2026-03-09 10:33:17');

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
(5, 1, 23000.00, '2026-02-28', '2026-02-28 17:52:07', '2026-03-03 16:30:02'),
(6, 2, 80000.00, '2026-03-06', '2026-03-06 11:45:58', '2026-03-06 11:45:58'),
(7, 1, 14500.00, '2026-03-06', '2026-03-06 12:29:43', '2026-03-06 12:29:43'),
(8, 2, 172000.00, '2026-03-07', '2026-03-07 11:21:34', '2026-03-09 11:19:09'),
(9, 7, 26000.00, '2026-03-09', '2026-03-09 10:33:17', '2026-03-09 10:33:17');

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
(7, 5, 159, 1, 23000.00, '2026-03-03 16:30:02', '2026-03-03 16:30:02', 0.00, 0.00),
(8, 6, 106, 2, 40000.00, '2026-03-06 11:45:58', '2026-03-06 11:45:58', 0.00, 0.00),
(9, 7, 87, 1, 14500.00, '2026-03-06 12:29:43', '2026-03-06 12:29:43', 0.00, 0.00),
(13, 9, 161, 2, 13000.00, '2026-03-09 10:33:17', '2026-03-09 10:33:17', 0.00, 0.00),
(14, 8, 66, 2, 50000.00, '2026-03-09 11:19:09', '2026-03-09 11:19:09', 0.00, 0.00),
(15, 8, 67, 1, 72000.00, '2026-03-09 11:19:09', '2026-03-09 11:19:09', 0.00, 0.00);

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
(6, 2, 613600.00, '2026-02-14', '2026-02-17 01:55:18', '2026-02-18 10:56:48'),
(7, 10, 190000.00, '2026-02-17', '2026-02-17 09:14:45', '2026-02-18 10:55:52'),
(8, 7, 90000.00, '2026-02-17', '2026-02-17 09:17:44', '2026-02-18 10:55:42'),
(9, 3, 850001.20, '2026-02-17', '2026-02-17 09:40:35', '2026-02-21 11:00:40'),
(10, 6, 1800.00, '2026-02-14', '2026-02-18 10:14:54', '2026-02-18 20:27:47'),
(11, 8, 1600.00, '2026-02-16', '2026-02-18 10:19:19', '2026-02-21 10:57:37'),
(12, 9, 4500.00, '2026-02-17', '2026-02-18 10:27:57', '2026-02-18 10:28:10'),
(13, 6, 3400.00, '2026-02-16', '2026-02-18 10:30:27', '2026-02-18 20:27:18'),
(14, 11, 110000.00, '2026-02-17', '2026-02-18 10:54:32', '2026-02-18 10:55:31'),
(15, 12, 41300.00, '2026-02-18', '2026-02-18 11:09:41', '2026-02-20 11:36:16'),
(16, 6, 600.00, '2026-02-18', '2026-02-18 13:26:35', '2026-02-20 10:40:08'),
(17, 13, 46000.00, '2026-02-18', '2026-02-18 13:31:19', '2026-02-18 13:31:19'),
(18, 6, 47000.00, '2026-02-18', '2026-02-18 13:32:14', '2026-02-18 20:25:00'),
(19, 14, 72600.00, '2026-02-18', '2026-02-18 13:38:17', '2026-02-18 13:38:17'),
(20, 6, 30000.00, '2026-02-18', '2026-02-18 18:45:14', '2026-02-18 18:45:14'),
(21, 6, 2400.00, '2026-02-20', '2026-02-20 12:07:08', '2026-02-20 12:07:08'),
(22, 6, 46000.00, '2026-02-20', '2026-02-20 12:25:06', '2026-02-23 12:02:11'),
(23, 16, 370000.00, '2026-02-21', '2026-02-21 07:39:11', '2026-02-21 07:39:36'),
(24, 6, 48500.00, '2026-02-21', '2026-02-21 10:35:48', '2026-02-21 10:38:28'),
(25, 6, 46000.00, '2026-02-21', '2026-02-21 10:36:40', '2026-02-21 10:37:58'),
(26, 17, 150000.00, '2026-02-21', '2026-02-21 12:26:10', '2026-02-23 07:37:09'),
(27, 6, 60000.00, '2026-02-23', '2026-02-23 10:34:11', '2026-02-23 10:34:35'),
(28, 6, 0.00, '2026-02-23', '2026-02-23 10:35:05', '2026-03-03 18:16:29'),
(30, 6, 7500.00, '2026-02-23', '2026-02-23 11:49:39', '2026-02-23 20:55:52'),
(31, 6, 72000.00, '2026-02-24', '2026-02-24 11:03:43', '2026-02-24 11:03:43'),
(33, 18, 132900.00, '2026-02-27', '2026-02-27 12:05:57', '2026-03-03 11:38:32'),
(34, 11, 165000.00, '2026-02-27', '2026-02-27 14:14:45', '2026-02-27 14:14:45'),
(35, 6, 30000.00, '2026-02-28', '2026-02-28 12:19:41', '2026-02-28 12:19:41'),
(36, 19, 170000.00, '2026-03-02', '2026-03-03 09:25:28', '2026-03-06 12:03:29'),
(37, 14, 36000.00, '2026-03-03', '2026-03-03 09:27:59', '2026-03-03 11:47:39'),
(38, 6, 22000.00, '2026-03-03', '2026-03-03 09:34:19', '2026-03-03 09:34:19'),
(39, 6, 55000.00, '2026-03-03', '2026-03-03 12:25:03', '2026-03-06 07:58:55'),
(40, 6, 8500.00, '2026-03-03', '2026-03-03 12:25:41', '2026-03-03 12:25:41'),
(41, 6, 2200.00, '2026-03-04', '2026-03-04 10:48:38', '2026-03-04 10:48:38'),
(42, 6, 30000.00, '2026-02-25', '2026-03-06 07:57:02', '2026-03-06 07:57:02'),
(43, 6, 7000.00, '2026-03-06', '2026-03-06 10:35:58', '2026-03-06 10:35:58'),
(44, 14, 18000.00, '2026-03-06', '2026-03-06 11:05:10', '2026-03-06 11:05:10'),
(45, 6, 4000.08, '2026-03-06', '2026-03-06 11:07:47', '2026-03-06 11:07:47'),
(46, 6, 22000.00, '2026-03-06', '2026-03-06 12:30:23', '2026-03-06 12:30:23'),
(47, 6, 1507100.00, '2026-02-13', '2026-03-07 01:08:00', '2026-03-07 01:08:00'),
(48, 6, 1000.00, '2026-03-07', '2026-03-07 09:51:43', '2026-03-07 09:51:43'),
(49, 6, 3000.00, '2026-03-07', '2026-03-07 12:03:26', '2026-03-07 12:03:26'),
(50, 6, 3500.00, '2026-03-07', '2026-03-07 12:15:24', '2026-03-07 12:15:24'),
(51, 6, 1500.00, '2026-03-09', '2026-03-09 10:15:11', '2026-03-09 10:15:11'),
(52, 20, 112000.00, '2026-03-09', '2026-03-09 11:53:09', '2026-03-09 11:53:09'),
(53, 6, 33000.00, '2026-03-09', '2026-03-09 12:26:09', '2026-03-10 11:50:12'),
(54, 21, 220660.00, '2026-03-11', '2026-03-10 09:46:13', '2026-03-10 09:46:13');

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
(14, 12, 152, 5, 900.00, 0.00, 0.00, '2026-02-18 10:28:10', '2026-02-18 10:28:10'),
(22, 14, 7, 1, 55000.00, 0.00, 0.00, '2026-02-18 10:55:31', '2026-02-18 10:55:31'),
(23, 14, 156, 1, 55000.00, 0.00, 0.00, '2026-02-18 10:55:31', '2026-02-18 10:55:31'),
(24, 8, 108, 1, 90000.00, 0.00, 0.00, '2026-02-18 10:55:42', '2026-02-18 10:55:42'),
(25, 7, 11, 1, 190000.00, 0.00, 0.00, '2026-02-18 10:55:52', '2026-02-18 10:55:52'),
(29, 6, 10, 1, 613600.00, 0.00, 0.00, '2026-02-18 10:56:48', '2026-02-18 10:56:48'),
(32, 17, 1, 1, 46000.00, 0.00, 0.00, '2026-02-18 13:31:19', '2026-02-18 13:31:19'),
(34, 19, 110, 60, 130.00, 0.00, 0.00, '2026-02-18 13:38:17', '2026-02-18 13:38:17'),
(35, 19, 117, 60, 1000.00, 0.00, 0.00, '2026-02-18 13:38:17', '2026-02-18 13:38:17'),
(36, 19, 111, 60, 80.00, 0.00, 0.00, '2026-02-18 13:38:17', '2026-02-18 13:38:17'),
(37, 20, 121, 10, 3000.00, 0.00, 0.00, '2026-02-18 18:45:14', '2026-02-18 18:45:14'),
(39, 18, 1, 1, 47000.00, 0.00, 0.00, '2026-02-18 20:25:00', '2026-02-18 20:25:00'),
(40, 13, 111, 4, 250.00, 0.00, 0.00, '2026-02-18 20:27:18', '2026-02-18 20:27:18'),
(41, 13, 110, 2, 300.00, 0.00, 0.00, '2026-02-18 20:27:18', '2026-02-18 20:27:18'),
(42, 13, 152, 2, 900.00, 0.00, 0.00, '2026-02-18 20:27:18', '2026-02-18 20:27:18'),
(43, 10, 152, 2, 900.00, 0.00, 0.00, '2026-02-18 20:27:47', '2026-02-18 20:27:47'),
(46, 16, 113, 2, 300.00, 0.00, 0.00, '2026-02-20 10:40:08', '2026-02-20 10:40:08'),
(47, 15, 39, 1, 35000.00, 0.00, 18.00, '2026-02-20 11:36:16', '2026-02-20 11:36:16'),
(48, 21, 114, 2, 650.00, 0.00, 0.00, '2026-02-20 12:07:08', '2026-02-20 12:07:08'),
(49, 21, 110, 2, 300.00, 0.00, 0.00, '2026-02-20 12:07:08', '2026-02-20 12:07:08'),
(50, 21, 111, 2, 250.00, 0.00, 0.00, '2026-02-20 12:07:08', '2026-02-20 12:07:08'),
(54, 23, 19, 1, 130000.00, 0.00, 0.00, '2026-02-21 07:39:36', '2026-02-21 07:39:36'),
(55, 23, 109, 1, 240000.00, 0.00, 0.00, '2026-02-21 07:39:36', '2026-02-21 07:39:36'),
(58, 25, 1, 1, 46000.00, 0.00, 0.00, '2026-02-21 10:37:58', '2026-02-21 10:37:58'),
(59, 24, 106, 1, 48500.00, 0.00, 0.00, '2026-02-21 10:38:28', '2026-02-21 10:38:28'),
(60, 11, 111, 8, 200.00, 0.00, 0.00, '2026-02-21 10:57:37', '2026-02-21 10:57:37'),
(61, 9, 41, 15, 36017.00, 0.00, 18.00, '2026-02-21 11:00:40', '2026-02-21 11:00:40'),
(62, 9, 40, 5, 36017.00, 0.00, 18.00, '2026-02-21 11:00:40', '2026-02-21 11:00:40'),
(65, 26, 13, 1, 150000.00, 0.00, 0.00, '2026-02-23 07:37:09', '2026-02-23 07:37:09'),
(67, 27, 7, 1, 60000.00, 0.00, 0.00, '2026-02-23 10:34:35', '2026-02-23 10:34:35'),
(68, 28, 86, 0, 25000.00, 0.00, 0.00, '2026-02-23 10:35:05', '2026-03-03 18:16:29'),
(75, 22, 1, 1, 46000.00, 0.00, 0.00, '2026-02-23 12:02:11', '2026-02-23 12:02:11'),
(76, 30, 146, 1, 4000.00, 0.00, 0.00, '2026-02-23 20:55:52', '2026-02-23 20:55:52'),
(77, 30, 117, 2, 1500.00, 0.00, 0.00, '2026-02-23 20:55:52', '2026-02-23 20:55:52'),
(78, 30, 111, 2, 250.00, 0.00, 0.00, '2026-02-23 20:55:52', '2026-02-23 20:55:52'),
(79, 31, 62, 1, 72000.00, 0.00, 0.00, '2026-02-24 11:03:43', '2026-02-24 11:03:43'),
(86, 34, 7, 1, 55000.00, 0.00, 0.00, '2026-02-27 14:14:45', '2026-02-27 14:14:45'),
(87, 34, 156, 2, 55000.00, 0.00, 0.00, '2026-02-27 14:14:45', '2026-02-27 14:14:45'),
(88, 35, 39, 1, 30000.00, 0.00, 0.00, '2026-02-28 12:19:41', '2026-02-28 12:19:41'),
(91, 38, 86, 1, 22000.00, 0.00, 0.00, '2026-03-03 09:34:19', '2026-03-03 09:34:19'),
(92, 33, 88, 2, 44300.00, 0.00, 0.00, '2026-03-03 11:38:32', '2026-03-03 11:38:32'),
(93, 33, 89, 1, 44300.00, 0.00, 0.00, '2026-03-03 11:38:32', '2026-03-03 11:38:32'),
(94, 37, 34, 2, 18000.00, 0.00, 0.00, '2026-03-03 11:47:39', '2026-03-03 11:47:39'),
(96, 40, 151, 1, 8500.00, 0.00, 0.00, '2026-03-03 12:25:41', '2026-03-03 12:25:41'),
(97, 41, 110, 4, 275.00, 0.00, 0.00, '2026-03-04 10:48:38', '2026-03-04 10:48:38'),
(98, 41, 111, 4, 275.00, 0.00, 0.00, '2026-03-04 10:48:38', '2026-03-04 10:48:38'),
(99, 42, 159, 1, 30000.00, 0.00, 0.00, '2026-03-06 07:57:02', '2026-03-06 07:57:02'),
(100, 39, 1, 1, 55000.00, 0.00, 0.00, '2026-03-06 07:58:55', '2026-03-06 07:58:55'),
(101, 43, 148, 1, 3000.00, 0.00, 0.00, '2026-03-06 10:35:58', '2026-03-06 10:35:58'),
(102, 43, 149, 2, 2000.00, 0.00, 0.00, '2026-03-06 10:35:58', '2026-03-06 10:35:58'),
(103, 44, 34, 1, 18000.00, 0.00, 0.00, '2026-03-06 11:05:10', '2026-03-06 11:05:10'),
(104, 45, 124, 12, 333.34, 0.00, 0.00, '2026-03-06 11:07:47', '2026-03-06 11:07:47'),
(106, 36, 11, 1, 170000.00, 0.00, 0.00, '2026-03-06 12:03:29', '2026-03-06 12:03:29'),
(107, 46, 87, 1, 22000.00, 0.00, 0.00, '2026-03-06 12:30:23', '2026-03-06 12:30:23'),
(108, 47, 160, 1, 1507100.00, 0.00, 0.00, '2026-03-07 01:08:00', '2026-03-07 01:08:00'),
(109, 48, 148, 1, 1000.00, 0.00, 0.00, '2026-03-07 09:51:43', '2026-03-07 09:51:43'),
(110, 49, 123, 12, 250.00, 0.00, 0.00, '2026-03-07 12:03:26', '2026-03-07 12:03:26'),
(111, 50, 133, 1, 3500.00, 0.00, 0.00, '2026-03-07 12:15:24', '2026-03-07 12:15:24'),
(112, 51, 116, 1, 1500.00, 0.00, 0.00, '2026-03-09 10:15:11', '2026-03-09 10:15:11'),
(113, 52, 66, 2, 56000.00, 0.00, 0.00, '2026-03-09 11:53:09', '2026-03-09 11:53:09'),
(115, 54, 39, 1, 32000.00, 0.00, 18.00, '2026-03-10 09:46:13', '2026-03-10 09:46:13'),
(116, 54, 13, 1, 155000.00, 0.00, 18.00, '2026-03-10 09:46:13', '2026-03-10 09:46:13'),
(118, 53, 39, 1, 33000.00, 0.00, 0.00, '2026-03-10 11:50:12', '2026-03-10 11:50:12');

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

--
-- Dumping data for table `sale_returns`
--

INSERT INTO `sale_returns` (`id`, `sale_id`, `customer_id`, `product_id`, `packing`, `qty_return`, `amount_deducted`, `total_after_return`, `created_at`, `updated_at`) VALUES
(5, 28, 6, 86, '1', 1, 25000.00, 622700.00, '2026-03-03 07:48:29', '2026-03-03 18:16:29');

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
('6BD2IhWobLIFE2RrT2Ua08fgJx1BOXrIt2q8YMvV', NULL, '103.221.247.69', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiQmNWNVR0TW5vTExRdG8wemVjYjJ5S2lRUVMxOVlydmpiNkJkOVRoZSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NToiaHR0cHM6Ly92aWJyYW50ZW5naW5lZXJpbmdwb3J0YWwuY29tL3Byb2R1Y3RzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vdmlicmFudGVuZ2luZWVyaW5ncG9ydGFsLmNvbS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773158846),
('6PVDH94TaisQHRg4rQneBNtSU4XEH3uAqCAz1qZv', NULL, '103.163.238.68', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoienluSHJobWZDdUpNYzBiWENHbXV6dmFXSFhzOGNRM2piZWRQTWFGcSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NjoiaHR0cHM6Ly92aWJyYW50ZW5naW5lZXJpbmdwb3J0YWwuY29tL3B1cmNoYXNlcyI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQyOiJodHRwczovL3ZpYnJhbnRlbmdpbmVlcmluZ3BvcnRhbC5jb20vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773160538),
('7cmpN4XEBWKfxonJbWNj4vZ6DtykIs8XCax8CHOy', NULL, '58.27.130.5', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiT2VCYjJIek52MWs1aUJVUnBBaE1MMWs2dkxvS3FSb2RJOVhOTHhISiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1NDoiaHR0cHM6Ly92aWJyYW50ZW5naW5lZXJpbmdwb3J0YWwuY29tL3N1cHBsaWVycy9kZXRhaWxzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vdmlicmFudGVuZ2luZWVyaW5ncG9ydGFsLmNvbS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773159291),
('8OSaJVqwNtYJa8lTmsDrkcwxz5ef93AXfjNc89zJ', 4, '2407:aa80:15:fe76:844c:b95f:6d1a:2028', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiNUMyazhTM1JneXVmd3RtbmhUeThEYndqMktaWUwwa3V6OGp0VHEzZSI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ1OiJodHRwczovL3ZpYnJhbnRlbmdpbmVlcmluZ3BvcnRhbC5jb20vcHJvZHVjdHMiO3M6NToicm91dGUiO3M6MTQ6InByb2R1Y3RzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NDt9', 1773173689),
('8w8brb8UfPfe7uYMWHutZzz3yZYxRanv8docVvaj', NULL, '103.163.238.71', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNTFDZWhmSWxjT2xIemRQVlFXdk52czlRclRTYlR5RENHOVFkWmVjSiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo1MjoiaHR0cHM6Ly92aWJyYW50ZW5naW5lZXJpbmdwb3J0YWwuY29tL2V4cGVuc2VzL2NyZWF0ZSI7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQyOiJodHRwczovL3ZpYnJhbnRlbmdpbmVlcmluZ3BvcnRhbC5jb20vbG9naW4iO3M6NToicm91dGUiO3M6NToibG9naW4iO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19', 1773158893),
('hX1Mv9ZVia4dV8CHpSZocmldmMdhZAgXZ135Kwzz', NULL, '119.153.111.197', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiTk90WEFuQWJSQXhoUXVGeldqdnJIaERrYjZTSG9uTnJGRHJzSDV1bCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NToiaHR0cHM6Ly92aWJyYW50ZW5naW5lZXJpbmdwb3J0YWwuY29tL2V4cGVuc2VzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vdmlicmFudGVuZ2luZWVyaW5ncG9ydGFsLmNvbS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773158887),
('mLnHBNzFpaVqN8rLsbFQONzMgxcGIfP0us6DGR5u', NULL, '157.20.147.37', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoia3FPYmRRTWNvS3VGeHdZZEpHNHV0WW5ka2F1cGpFV0llQVFuMnV3cCI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0MjoiaHR0cHM6Ly92aWJyYW50ZW5naW5lZXJpbmdwb3J0YWwuY29tL3NhbGVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vdmlicmFudGVuZ2luZWVyaW5ncG9ydGFsLmNvbS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773159225),
('tuiOJ4sDCDosut8qjwsnJ5f6UZ2PDY5GVTa4qBbw', 2, '2407:aa80:15:fe76:844c:b95f:6d1a:2028', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoidzhoQ0hmR25pUDVhakVhbVJBZXF1ZGdGWHFabWVoTVlJSnJvUFVLdiI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQyOiJodHRwczovL3ZpYnJhbnRlbmdpbmVlcmluZ3BvcnRhbC5jb20vdXNlcnMiO3M6NToicm91dGUiO3M6MTE6InVzZXJzLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1773173694),
('Yel80Fz9pTTy8Shjv1GSLdIc9NusZgatTEe2Xokz', NULL, '182.176.130.6', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiNnRxZ1NFajVieDU1d3U4S29VMUxNTXcwd2Foelo1RERzbWlUZE1NVSI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0ODoiaHR0cHM6Ly92aWJyYW50ZW5naW5lZXJpbmdwb3J0YWwuY29tL2N1c3RvbWVycy82Ijt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NDI6Imh0dHBzOi8vdmlicmFudGVuZ2luZWVyaW5ncG9ydGFsLmNvbS9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1773159232),
('ZjFyRr8kPLlhlAC7qGpRXwA48OFDwZ4gcXfJhwbd', 2, '2407:aa80:15:fe76:844c:b95f:6d1a:2028', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'YTo1OntzOjY6Il90b2tlbiI7czo0MDoiaFh6cUx1a1owRUw5QnMySFV6ZWQzZEJQT2xEY2xtUTN6ZUc2WFBnZyI7czozOiJ1cmwiO2E6MDp7fXM6OToiX3ByZXZpb3VzIjthOjI6e3M6MzoidXJsIjtzOjQ5OiJodHRwczovL3ZpYnJhbnRlbmdpbmVlcmluZ3BvcnRhbC5jb20vc2FsZS1yZXR1cm5zIjtzOjU6InJvdXRlIjtzOjE4OiJzYWxlLXJldHVybnMuaW5kZXgiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO30=', 1773160780),
('Ztb3OElW8ofecPaANueKrTpa6cL4yXGU8Deo87rF', NULL, '182.176.130.7', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiaWVxS3lwb1liWnBOeVVCZmJERE5FWnhLNVdLcWVrZ2lRMzN6NEE1ViI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czo0NDoiaHR0cHM6Ly92aWJyYW50ZW5naW5lZXJpbmdwb3J0YWwuY29tL3JlcG9ydHMiO31zOjk6Il9wcmV2aW91cyI7YToyOntzOjM6InVybCI7czo0MjoiaHR0cHM6Ly92aWJyYW50ZW5naW5lZXJpbmdwb3J0YWwuY29tL2xvZ2luIjtzOjU6InJvdXRlIjtzOjU6ImxvZ2luIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1773159212);

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
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

INSERT INTO `suppliers` (`id`, `name`, `company_name`, `phone`, `email`, `address`, `balance`, `created_at`, `updated_at`) VALUES
(1, 'Bolton Market', 'Ref by Fuzail', '+92 335 2385773', 'info@vibrantengineering.pk', 'Karachi', 14500.00, '2026-02-26 11:47:02', '2026-03-06 12:29:43'),
(2, 'Akram', 'Qasim Traders', '03352385773', 'info@vibrantengineering.pk', 'Hyderabad', 115000.00, '2026-03-06 11:39:12', '2026-03-09 12:07:19'),
(3, 'Noman', 'sealer house', '03352385773', 'info@vibrantengineering.pk', 'lahore', 0.00, '2026-03-06 11:58:33', '2026-03-06 12:01:43'),
(4, 'abubakar', 'Haq bahoo', '03352385773', 'info@vibrantengineering.pk', 'Lahore', 0.00, '2026-03-06 11:59:18', '2026-03-06 11:59:18'),
(5, 'naeem babar', 'world international', '03352385773', 'info@vibrantengineering.pk', 'Lahore', 0.00, '2026-03-06 12:00:03', '2026-03-06 12:00:03'),
(6, 'abdullah', 'abdullah International', '03352385773', 'info@vibrantengineering.pk', 'Lahore', 0.00, '2026-03-06 12:01:01', '2026-03-06 12:01:01'),
(7, 'Waseem', 'MY Engineering', '03352385773', 'info@vibrantengineering.pk', 'karachi', 26000.00, '2026-03-09 10:30:22', '2026-03-09 10:33:17');

-- --------------------------------------------------------

--
-- Table structure for table `supplier_payments`
--

CREATE TABLE `supplier_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `supplier_id` bigint(20) UNSIGNED NOT NULL,
  `purchase_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `payment_type` varchar(255) NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `supplier_payments`
--

INSERT INTO `supplier_payments` (`id`, `supplier_id`, `purchase_id`, `description`, `payment_type`, `amount`, `created_at`, `updated_at`) VALUES
(7, 1, 5, 'Advance paid against Purchase Invoice #5', 'Cash', 23000.00, '2026-03-03 16:30:02', '2026-03-03 16:30:02'),
(8, 2, 6, 'Payment paid against Purchase #6', 'Cash', 80000.00, '2026-03-06 11:45:58', '2026-03-06 11:45:58'),
(9, 1, 7, 'Payment paid against Purchase #7', 'Cash', 14500.00, '2026-03-06 12:29:43', '2026-03-06 12:29:43'),
(11, 7, 9, 'Payment paid against Purchase #9', 'Cash', 26000.00, '2026-03-09 10:33:17', '2026-03-09 10:33:17'),
(12, 2, 8, 'Advance paid against Purchase Invoice #8', 'Cash', 172000.00, '2026-03-09 11:19:09', '2026-03-09 11:19:09');

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
  `updated_at` timestamp NULL DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'admin'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`, `role`) VALUES
(2, 'Admin', 'info@vibrantengineering.pk', NULL, '$2y$12$P9MfWtTnCBcw8sl/lP8Z.O1HBPwriOOP1d8rD8vxHzr1FYP3S.WUm', '7ReapFdlWy7kT6dBqhQAOVD12HSx87ZFSdlu9Wjbp1DUVL5ZLaDLEF7DkXDv', '2025-06-20 02:39:09', '2026-02-16 18:11:34', 'admin'),
(4, 'Accountant', 'accountant@vibrantengineering.pk', NULL, '$2y$12$XgePrnAO3jizqcbJVjrJCOqVv0F94RX5O0fQL2DQm6u4R5OptsGR2', NULL, '2026-03-10 20:13:28', '2026-03-10 20:13:28', 'editor');

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
  ADD KEY `customer_payments_customer_id_foreign` (`customer_id`),
  ADD KEY `customer_payments_sale_id_foreign` (`sale_id`);

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
  ADD KEY `supplier_payments_supplier_id_foreign` (`supplier_id`),
  ADD KEY `supplier_payments_purchase_id_foreign` (`purchase_id`);

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `customer_payments`
--
ALTER TABLE `customer_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `expense_names`
--
ALTER TABLE `expense_names`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `payment_types`
--
ALTER TABLE `payment_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=162;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `purchase_items`
--
ALTER TABLE `purchase_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=55;

--
-- AUTO_INCREMENT for table `sale_items`
--
ALTER TABLE `sale_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=119;

--
-- AUTO_INCREMENT for table `sale_returns`
--
ALTER TABLE `sale_returns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `supplier_payments`
--
ALTER TABLE `supplier_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `customer_payments`
--
ALTER TABLE `customer_payments`
  ADD CONSTRAINT `customer_payments_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `customer_payments_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE SET NULL;

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
  ADD CONSTRAINT `supplier_payments_purchase_id_foreign` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `supplier_payments_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
