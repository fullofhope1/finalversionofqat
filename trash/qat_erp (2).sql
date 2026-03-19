-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 16, 2026 at 09:16 PM
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
-- Database: `qat_erp`
--

-- --------------------------------------------------------

--
-- Table structure for table `advertisements`
--

CREATE TABLE `advertisements` (
  `id` int(11) NOT NULL,
  `client_name` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `image_url` text DEFAULT NULL,
  `link_url` text DEFAULT NULL,
  `status` enum('Active','Inactive') DEFAULT 'Active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `media_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `total_debt` decimal(10,2) DEFAULT 0.00,
  `debt_limit` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `is_deleted` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `total_debt`, `debt_limit`, `created_at`, `is_deleted`) VALUES
(1, 'ctest', '772166545', 5000.00, NULL, '2026-03-12 14:19:16', 0),
(2, 'ctest2', '0772166545', 5000.00, NULL, '2026-03-12 14:21:36', 0),
(3, 'ltest', '10772166545', 0.00, NULL, '2026-03-12 14:53:55', 0);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `expense_date` date DEFAULT curdate(),
  `description` varchar(255) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `category` varchar(100) NOT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `leftovers`
--

CREATE TABLE `leftovers` (
  `id` int(11) NOT NULL,
  `source_date` date NOT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `qat_type_id` int(11) DEFAULT NULL,
  `unit_type` enum('weight','قبضة','قرطاس') NOT NULL DEFAULT 'weight',
  `weight_kg` decimal(10,2) NOT NULL,
  `quantity_units` int(11) NOT NULL DEFAULT 0,
  `status` enum('Pending','Dropped','Transferred_Next_Day','Sold','Auto_Momsi','Auto_Dropped') DEFAULT 'Pending',
  `decision_date` date DEFAULT NULL,
  `sale_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `leftovers`
--

INSERT INTO `leftovers` (`id`, `source_date`, `purchase_id`, `qat_type_id`, `unit_type`, `weight_kg`, `quantity_units`, `status`, `decision_date`, `sale_date`, `created_at`) VALUES
(1, '2026-03-12', 1, 1, 'weight', 0.25, 0, 'Dropped', '2026-03-12', '2026-03-13', '2026-03-12 14:53:14'),
(2, '2026-03-12', 2, 3, 'قبضة', 0.00, 85, 'Dropped', '2026-03-12', '2026-03-13', '2026-03-12 14:53:14'),
(3, '2026-03-12', 3, 7, 'قرطاس', 0.00, 95, 'Dropped', '2026-03-12', '2026-03-13', '2026-03-12 14:53:14'),
(4, '2026-03-12', 1, 1, 'weight', 0.25, 0, 'Auto_Dropped', '2026-03-12', '2026-03-12', '2026-03-12 15:06:46'),
(5, '2026-03-12', 2, 3, 'قبضة', 0.00, 85, 'Auto_Dropped', '2026-03-12', '2026-03-12', '2026-03-12 15:06:46'),
(6, '2026-03-12', 3, 7, 'قرطاس', 0.00, 95, 'Auto_Dropped', '2026-03-12', '2026-03-12', '2026-03-12 15:06:46'),
(7, '2026-03-13', 4, 1, 'weight', 0.25, 0, 'Auto_Dropped', '2026-03-13', '2026-03-13', '2026-03-12 15:06:46'),
(8, '2026-03-13', 5, 3, 'قبضة', 0.00, 85, 'Auto_Dropped', '2026-03-13', '2026-03-13', '2026-03-12 15:06:46'),
(9, '2026-03-13', 6, 7, 'قرطاس', 0.00, 95, 'Auto_Dropped', '2026-03-13', '2026-03-13', '2026-03-12 15:06:46'),
(10, '2026-03-12', 7, 2, 'weight', 0.25, 0, 'Dropped', '2026-03-12', '2026-03-13', '2026-03-12 15:08:56'),
(11, '2026-03-12', 7, 2, 'weight', 0.25, 0, 'Auto_Dropped', '2026-03-12', '2026-03-12', '2026-03-12 15:09:53'),
(12, '2026-03-13', 8, 2, 'weight', 0.25, 0, 'Auto_Dropped', '2026-03-13', '2026-03-13', '2026-03-12 15:09:53'),
(13, '2026-03-12', 9, 4, 'قبضة', 0.00, 100, 'Dropped', '2026-03-12', '2026-03-13', '2026-03-12 15:09:53'),
(14, '2026-03-12', 9, 4, 'قبضة', 0.00, 100, 'Auto_Dropped', '2026-03-12', '2026-03-12', '2026-03-16 17:19:55'),
(15, '2026-03-13', 10, 4, 'قبضة', 0.00, 76, 'Auto_Dropped', '2026-03-13', '2026-03-13', '2026-03-16 17:19:56');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `payment_date` date DEFAULT curdate(),
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `providers`
--

CREATE TABLE `providers` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `providers`
--

INSERT INTO `providers` (`id`, `name`, `phone`, `created_at`, `created_by`) VALUES
(1, 'Rtest', '772166545', '2026-03-12 14:16:10', 31);

-- --------------------------------------------------------

--
-- Table structure for table `purchases`
--

CREATE TABLE `purchases` (
  `id` int(11) NOT NULL,
  `purchase_date` date NOT NULL,
  `qat_type_id` int(11) DEFAULT NULL,
  `source_weight_grams` decimal(10,2) DEFAULT 0.00,
  `received_weight_grams` decimal(10,2) DEFAULT 0.00,
  `provider_id` int(11) DEFAULT NULL,
  `expected_quantity_kg` decimal(10,2) DEFAULT 0.00,
  `vendor_name` varchar(100) DEFAULT NULL,
  `agreed_price` decimal(10,2) NOT NULL,
  `price_per_kilo` decimal(10,2) DEFAULT 0.00,
  `unit_type` enum('weight','قبضة','قرطاس') NOT NULL DEFAULT 'weight',
  `source_units` int(11) NOT NULL DEFAULT 0,
  `received_units` int(11) DEFAULT 0,
  `price_per_unit` decimal(10,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `net_cost` decimal(10,2) GENERATED ALWAYS AS (`agreed_price` - `discount`) STORED,
  `quantity_kg` decimal(10,2) DEFAULT NULL,
  `status` enum('Fresh','Momsi','Closed') DEFAULT 'Fresh',
  `media_path` varchar(255) DEFAULT NULL,
  `is_received` tinyint(1) DEFAULT 1,
  `received_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  `original_purchase_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `purchases`
--

INSERT INTO `purchases` (`id`, `purchase_date`, `qat_type_id`, `source_weight_grams`, `received_weight_grams`, `provider_id`, `expected_quantity_kg`, `vendor_name`, `agreed_price`, `price_per_kilo`, `unit_type`, `source_units`, `received_units`, `price_per_unit`, `discount`, `quantity_kg`, `status`, `media_path`, `is_received`, `received_at`, `created_at`, `created_by`, `original_purchase_id`) VALUES
(1, '2026-03-12', 1, 500.00, 500.00, 1, 0.00, NULL, 500.00, 1000.00, 'weight', 0, 0, 0.00, 0.00, 0.50, 'Closed', NULL, 1, '2026-03-12 17:18:19', '2026-03-12 14:16:40', 31, NULL),
(2, '2026-03-12', 3, 0.00, 0.00, 1, 0.00, NULL, 100000.00, 0.00, 'قبضة', 100, 100, 1000.00, 0.00, 0.00, 'Closed', NULL, 1, '2026-03-12 17:18:25', '2026-03-12 14:17:25', 31, NULL),
(3, '2026-03-12', 7, 0.00, 0.00, 1, 0.00, NULL, 1000000.00, 0.00, 'قرطاس', 100, 100, 10000.00, 0.00, 0.00, 'Closed', NULL, 1, '2026-03-12 17:18:31', '2026-03-12 14:17:47', 31, NULL),
(4, '2026-03-13', 1, 0.00, 250.00, 1, 0.00, NULL, 0.00, 0.00, 'weight', 0, 0, 0.00, 0.00, 0.25, 'Closed', NULL, 1, '2026-03-13 00:00:01', '2026-03-12 14:53:14', NULL, 1),
(5, '2026-03-13', 3, 0.00, 0.00, 1, 0.00, NULL, 0.00, 0.00, 'قبضة', 0, 85, 0.00, 0.00, 0.00, 'Closed', NULL, 1, '2026-03-13 00:00:01', '2026-03-12 14:53:14', NULL, 2),
(6, '2026-03-13', 7, 0.00, 0.00, 1, 0.00, NULL, 0.00, 0.00, 'قرطاس', 0, 95, 0.00, 0.00, 0.00, 'Closed', NULL, 1, '2026-03-13 00:00:01', '2026-03-12 14:53:14', NULL, 3),
(7, '2026-03-12', 2, 500.00, 500.00, 1, 0.00, NULL, 500.00, 1000.00, 'weight', 0, 0, 0.00, 0.00, 0.50, 'Closed', NULL, 1, '2026-03-12 18:07:36', '2026-03-12 15:07:16', 31, NULL),
(8, '2026-03-13', 2, 0.00, 250.00, 1, 0.00, NULL, 0.00, 0.00, 'weight', 0, 0, 0.00, 0.00, 0.25, 'Closed', NULL, 1, '2026-03-13 00:00:01', '2026-03-12 15:08:56', NULL, 7),
(9, '2026-03-12', 4, 0.00, 0.00, 1, 0.00, NULL, 99999999.99, 0.00, 'قبضة', 100, 100, 1000000.00, 0.00, 0.00, 'Closed', NULL, 1, '2026-03-12 18:09:42', '2026-03-12 15:09:26', 31, NULL),
(10, '2026-03-13', 4, 0.00, 0.00, 1, 0.00, NULL, 0.00, 0.00, 'قبضة', 0, 100, 0.00, 0.00, 0.00, 'Closed', NULL, 1, '2026-03-13 00:00:01', '2026-03-12 15:09:53', NULL, 9);

-- --------------------------------------------------------

--
-- Table structure for table `qat_deposits`
--

CREATE TABLE `qat_deposits` (
  `id` int(11) NOT NULL,
  `deposit_date` date NOT NULL,
  `currency` enum('YER','SAR','USD') NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `recipient` varchar(100) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `qat_types`
--

CREATE TABLE `qat_types` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `media_path` varchar(255) DEFAULT NULL,
  `price_weight` decimal(10,2) DEFAULT 0.00,
  `price_qabdah` decimal(10,2) DEFAULT 0.00,
  `price_qartas` decimal(10,2) DEFAULT 0.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `qat_types`
--

INSERT INTO `qat_types` (`id`, `name`, `description`, `is_deleted`, `media_path`, `price_weight`, `price_qabdah`, `price_qartas`) VALUES
(1, 'جمام نقوة', NULL, 0, NULL, 0.00, 0.00, 0.00),
(2, 'جمام كالف', NULL, 0, 'uploads/1771796307_WIN_20250204_08_35_49_Pro.jpg', 0.00, 0.00, 0.00),
(3, 'جمام سمين', NULL, 0, 'uploads/1772219687_Screenshot_20251209_155907_ .jpg', 0.00, 0.00, 0.00),
(4, 'جمام قصار', NULL, 0, NULL, 0.00, 0.00, 0.00),
(5, 'صدور نقوة', NULL, 0, NULL, 0.00, 0.00, 0.00),
(6, 'صدور عادي', NULL, 0, NULL, 0.00, 0.00, 0.00),
(7, 'قطل', NULL, 0, NULL, 0.00, 0.00, 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `refunds`
--

CREATE TABLE `refunds` (
  `id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `refund_type` enum('Cash','Debt') NOT NULL,
  `reason` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sales`
--

CREATE TABLE `sales` (
  `id` int(11) NOT NULL,
  `sale_date` date DEFAULT curdate(),
  `due_date` date DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `qat_type_id` int(11) DEFAULT NULL,
  `purchase_id` int(11) DEFAULT NULL,
  `leftover_id` int(11) DEFAULT NULL,
  `qat_status` enum('Tari','Momsi','Leftover') DEFAULT 'Tari',
  `weight_grams` decimal(10,2) NOT NULL,
  `weight_kg` decimal(10,3) GENERATED ALWAYS AS (`weight_grams` / 1000) STORED,
  `unit_type` enum('weight','قبضة','قرطاس') NOT NULL DEFAULT 'weight',
  `quantity_units` int(11) NOT NULL DEFAULT 0,
  `price` decimal(10,2) NOT NULL,
  `paid_amount` decimal(10,2) DEFAULT 0.00,
  `discount` decimal(10,2) DEFAULT 0.00,
  `refund_amount` decimal(10,2) DEFAULT 0.00,
  `payment_method` enum('Cash','Debt','Internal Transfer','Kuraimi Deposit','Jayb Deposit') NOT NULL,
  `transfer_sender` varchar(100) DEFAULT NULL,
  `transfer_receiver` varchar(100) DEFAULT NULL,
  `transfer_number` varchar(100) DEFAULT NULL,
  `transfer_company` varchar(100) DEFAULT NULL,
  `is_paid` tinyint(1) DEFAULT 1,
  `debt_type` enum('Daily','Monthly','Yearly','Deferred') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sales`
--

INSERT INTO `sales` (`id`, `sale_date`, `due_date`, `customer_id`, `qat_type_id`, `purchase_id`, `leftover_id`, `qat_status`, `weight_grams`, `unit_type`, `quantity_units`, `price`, `paid_amount`, `discount`, `refund_amount`, `payment_method`, `transfer_sender`, `transfer_receiver`, `transfer_number`, `transfer_company`, `is_paid`, `debt_type`, `notes`, `created_at`) VALUES
(1, '2026-03-12', '2026-03-12', 1, 3, 2, NULL, 'Tari', 0.00, 'قبضة', 5, 10000.00, 0.00, 0.00, 0.00, 'Cash', NULL, NULL, NULL, NULL, 1, NULL, '', '2026-03-12 14:19:23'),
(2, '2026-03-12', '2026-03-16', 2, 3, 2, NULL, 'Tari', 0.00, 'قبضة', 10, 5000.00, 0.00, 0.00, 0.00, 'Debt', NULL, NULL, NULL, NULL, 0, 'Daily', ' [ترحيل آلي من 2026-03-12] [ترحيل آلي من 2026-03-13] [ترحيل آلي من 2026-03-14] [ترحيل آلي من 2026-03-15]', '2026-03-12 14:22:15'),
(3, '2026-03-12', '2026-03-12', 1, 1, 1, NULL, 'Tari', 250.00, 'weight', 0, 3000.00, 0.00, 0.00, 0.00, 'Internal Transfer', 'علي', 'ماجد القادري', '45565445', 'الكريمي', 1, NULL, '', '2026-03-12 14:50:07'),
(4, '2026-03-12', '2026-03-12', 2, 7, 3, NULL, 'Tari', 0.00, 'قرطاس', 5, 5000.00, 0.00, 0.00, 0.00, 'Cash', NULL, NULL, NULL, NULL, 1, NULL, '', '2026-03-12 14:50:37'),
(5, '2026-03-12', '2026-03-16', 1, 2, 7, NULL, 'Tari', 250.00, 'weight', 0, 5000.00, 0.00, 0.00, 0.00, 'Debt', NULL, NULL, NULL, NULL, 0, 'Daily', ' [ترحيل آلي من 2026-03-12] [ترحيل آلي من 2026-03-13] [ترحيل آلي من 2026-03-14] [ترحيل آلي من 2026-03-15]', '2026-03-12 15:07:45'),
(6, '2026-03-12', '2026-03-12', 3, 4, 10, NULL, 'Momsi', 0.00, 'قبضة', 4, 5000.00, 0.00, 0.00, 0.00, 'Cash', NULL, NULL, NULL, NULL, 1, NULL, '', '2026-03-12 15:10:03'),
(7, '2026-03-12', '2026-03-12', 2, 4, 10, NULL, 'Momsi', 0.00, 'قبضة', 20, 20000.00, 0.00, 0.00, 0.00, 'Cash', NULL, NULL, NULL, NULL, 1, NULL, '', '2026-03-12 15:10:40');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

CREATE TABLE `staff` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `role` varchar(50) DEFAULT NULL,
  `daily_salary` decimal(10,2) DEFAULT 0.00,
  `withdrawal_limit` decimal(10,2) DEFAULT NULL,
  `base_salary` decimal(10,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `unknown_transfers`
--

CREATE TABLE `unknown_transfers` (
  `id` int(11) NOT NULL,
  `transfer_date` date NOT NULL,
  `receipt_number` varchar(100) DEFAULT NULL,
  `sender_name` varchar(100) NOT NULL,
  `amount` decimal(10,2) DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `currency` varchar(5) DEFAULT 'YER'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `display_name` varchar(255) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','super_admin','user') NOT NULL,
  `sub_role` varchar(50) DEFAULT 'full',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `display_name`, `phone`, `password`, `role`, `sub_role`, `created_at`) VALUES
(1, 'super admin', 'null', 'null', '123456', 'super_admin', 'full', '2026-01-19 19:20:42'),
(3, 'admin1', 'abdulqawi mohammed', '0772166545', '$2y$10$nXfGuul5Yo08xojUUJvLWOpfz6wf/kIqqe1m0haDVUko68f90jo3e', 'user', 'full', '2026-02-21 20:52:31'),
(4, 'abood', 'عبد القوي', '7721665', '$2y$10$jQXWWPSW7HtQZqVT4Z9dnO6kajO0xLODBOv2LGgJ3i1ZJiWPnWbmy', 'user', 'full', '2026-02-22 21:31:51'),
(5, 'Abdul', NULL, NULL, '$2y$10$QWHZUCDODeXxdsh.ydGcIeSUBatiqlEw8BL37XjKhWcCUpaOtcVz2', 'super_admin', 'reports', '2026-02-23 11:14:39'),
(6, 'Mohammed', NULL, NULL, '$2y$10$8htdjtFEYV9Ryopnbn9L9u5XI1m7MGitzJG8TYUnBWO772166545', 'super_admin', 'full', '2026-02-23 11:14:39'),
(7, 'Abdullah', NULL, NULL, '$2y$10$4.8UPgvvvzK1rPXuBsLSxO07K1d8c9ZFcBFLByMiE2Q/1hZqC9XEy', 'super_admin', 'sales_debts', '2026-02-23 11:14:39'),
(8, 'Aham', NULL, NULL, '$2y$10$LQC/gZsvzoLh.uoygVIod.j4X1gdKNmx9YQghJhyF8K9uLBXc4RlG', 'super_admin', 'receiving', '2026-02-23 11:14:39'),
(9, '202310400240', 'عبد القوي', 'admin', '$2y$10$6y9FA.fHNOOW67yxfDJqrOWqbv5Zsn.N5NwUea386tpm4hddFXs7.', 'user', 'full', '2026-02-27 19:31:06'),
(10, '2023104002', 'عبد القوي', 'admin', '$2y$10$Eo9GytjYblfsobZBHNH8venN/tTOK31WrWPq0I0hraHgYW8QfUgvG', 'user', 'full', '2026-02-27 19:32:52'),
(11, 'ali', 'عبد القوي', 'admin', '$2y$10$KqRzLNxGspTw/63AsyNCl.lOKGGa79gHwPnLp9RSkTeoABAwpC2mO', 'user', 'full', '2026-02-27 19:33:54'),
(12, 'ad', 'عبد القوي', 'test', '$2y$10$MsoGM/z62ATebE2YGKCmg.BRu3QKVRK3/pv9iV6DGgUC4hC0eVhJq', 'user', 'full', '2026-03-02 10:48:29'),
(14, 'four', NULL, NULL, '$2y$10$H/cgwxYUh2P3yOH36aAdH.WzTzVZBawG7RHdjDJ8Hn0rpHaK7xDAC', 'user', 'full', '2026-03-04 14:10:45'),
(15, 'three', NULL, NULL, '$2y$10$H/cgwxYUh2P3yOH36aAdH.WzTzVZBawG7RHdjDJ8Hn0rpHaK7xDAC', 'user', 'full', '2026-03-04 14:10:45'),
(16, 'two', NULL, NULL, '$2y$10$H/cgwxYUh2P3yOH36aAdH.WzTzVZBawG7RHdjDJ8Hn0rpHaK7xDAC', 'user', 'full', '2026-03-04 14:10:45'),
(17, 'one', NULL, NULL, '$2y$10$H/cgwxYUh2P3yOH36aAdH.WzTzVZBawG7RHdjDJ8Hn0rpHaK7xDAC', 'user', 'full', '2026-03-04 14:10:45'),
(19, 'test_sourcing_admin', NULL, NULL, '$2y$10$lzAb7mowXYO64YLBj4Uk9ODIeyR.hHmSByj6jp2k6oKZDQJEZhvUm', 'admin', 'full', '2026-03-07 09:11:03'),
(20, 'test_sales_admin', NULL, NULL, '$2y$10$An09RtZbjIN2Rmhvikbr2u1vCb8ushswWLiYXB2gwhM9DYTqCMljK', 'super_admin', 'full', '2026-03-07 09:11:04'),
(21, 'super', 'عبد القوي', '0772166545', '$2y$10$zWqyjEI/1VOvs88GCR7Q7u0WeSzk733CCzWqSdg1MTBB1VtPp5sue', 'super_admin', 'verifier', '2026-03-07 09:22:29'),
(22, 'sales', 'عبد القوي', '0772166545', '$2y$10$8htdjtFEYV9Ryopnbn9L9u5XI1m7MGitzJG8TYUnBWOuW5OBi5pZS', 'super_admin', 'seller', '2026-03-07 09:25:00'),
(23, 'accountant', 'عبد القوي', '0772166545', '$2y$10$tqEmBeZU.0HPglSELrd2pecXVjZea6/1rwzP7z.YuHDPifSLhojaa', 'super_admin', 'accountant', '2026-03-07 09:32:27'),
(24, 'partner', 'عبد القوي', '0772166545', '$2y$10$fwTIGz3oVCcpWloStSEsxO5R5B6trKGCorx03jnxwhjOcHXDTH.8m', 'super_admin', 'partner', '2026-03-07 09:37:07'),
(25, 'test', 'test', '772166545', '$2y$10$6sBEYOlEmwgO/zH91dfFveoY6O6fjK4xiRM1D1IQdsT7shlZ7r3Ym', 'super_admin', 'partner', '2026-03-07 09:41:51'),
(26, 'moha', 'mohammed', '772166545', '$2y$10$mMPFKu.yGPxa2OADERU1relcz/RXESHbFSuvPvGK2ditEcaDQ.n2e', 'super_admin', 'full', '2026-03-07 13:49:04'),
(27, '', NULL, NULL, '', 'super_admin', 'full', '2026-03-08 12:17:33'),
(31, 'admin', 'admin', NULL, '$2y$10$Q6eryJl/c7NpXLr5dey9j.6s2vCWCw9.5hc6zZZLEV9UMKdWg.1Wa', 'admin', 'full', '2026-03-08 19:21:26'),
(32, 'superadmin', 'superadmin', NULL, '$2y$10$VmbDnpsvfPT5iF2WcVg2jexJn/5Ub7sxKqG70QExxaHXlbNtOJqk2', 'super_admin', 'full', '2026-03-08 19:21:26'),
(33, 'est', 'test', '0772166545', '$2y$10$lE8m2WsTL5lx1Ff/DEJcOuUqvW/8Kl1eKaWET63y4sjWjK6zcn19q', 'super_admin', 'accountant', '2026-03-09 17:08:27');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `advertisements`
--
ALTER TABLE `advertisements`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leftovers`
--
ALTER TABLE `leftovers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qat_type_id` (`qat_type_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `providers`
--
ALTER TABLE `providers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `purchases`
--
ALTER TABLE `purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `qat_type_id` (`qat_type_id`),
  ADD KEY `idx_purchases_date` (`purchase_date`),
  ADD KEY `fk_original_purchase` (`original_purchase_id`);

--
-- Indexes for table `qat_deposits`
--
ALTER TABLE `qat_deposits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `qat_types`
--
ALTER TABLE `qat_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `refunds`
--
ALTER TABLE `refunds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `sales`
--
ALTER TABLE `sales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `qat_type_id` (`qat_type_id`),
  ADD KEY `idx_sales_date` (`sale_date`),
  ADD KEY `fk_sales_leftover` (`leftover_id`);

--
-- Indexes for table `staff`
--
ALTER TABLE `staff`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `unknown_transfers`
--
ALTER TABLE `unknown_transfers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `advertisements`
--
ALTER TABLE `advertisements`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leftovers`
--
ALTER TABLE `leftovers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `providers`
--
ALTER TABLE `providers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `purchases`
--
ALTER TABLE `purchases`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `qat_deposits`
--
ALTER TABLE `qat_deposits`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `qat_types`
--
ALTER TABLE `qat_types`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `refunds`
--
ALTER TABLE `refunds`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sales`
--
ALTER TABLE `sales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `staff`
--
ALTER TABLE `staff`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `unknown_transfers`
--
ALTER TABLE `unknown_transfers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `leftovers`
--
ALTER TABLE `leftovers`
  ADD CONSTRAINT `leftovers_ibfk_1` FOREIGN KEY (`qat_type_id`) REFERENCES `qat_types` (`id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `purchases`
--
ALTER TABLE `purchases`
  ADD CONSTRAINT `fk_original_purchase` FOREIGN KEY (`original_purchase_id`) REFERENCES `purchases` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`qat_type_id`) REFERENCES `qat_types` (`id`);

--
-- Constraints for table `refunds`
--
ALTER TABLE `refunds`
  ADD CONSTRAINT `refunds_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`);

--
-- Constraints for table `sales`
--
ALTER TABLE `sales`
  ADD CONSTRAINT `fk_sales_leftover` FOREIGN KEY (`leftover_id`) REFERENCES `leftovers` (`id`),
  ADD CONSTRAINT `sales_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `sales_ibfk_2` FOREIGN KEY (`qat_type_id`) REFERENCES `qat_types` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
