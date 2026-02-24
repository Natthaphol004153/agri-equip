-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2026 at 05:30 AM
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
-- Database: `agri_equip_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_number` varchar(255) NOT NULL,
  `customer_id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `assigned_staff_id` bigint(20) UNSIGNED DEFAULT NULL,
  `scheduled_start` datetime NOT NULL,
  `scheduled_end` datetime NOT NULL,
  `actual_start` datetime DEFAULT NULL,
  `actual_end` datetime DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `status` enum('scheduled','in_progress','completed_pending_approval','completed','cancelled','paused') NOT NULL DEFAULT 'scheduled',
  `total_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deposit_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_status` enum('pending','deposit_paid','paid','partial') DEFAULT 'pending',
  `payment_method` enum('transfer','cash') DEFAULT NULL,
  `payment_trans_ref` varchar(100) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `job_number`, `customer_id`, `equipment_id`, `assigned_staff_id`, `scheduled_start`, `scheduled_end`, `actual_start`, `actual_end`, `image_path`, `note`, `status`, `total_price`, `deposit_amount`, `payment_status`, `payment_method`, `payment_trans_ref`, `payment_proof`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'JOB-251219-6473', 5, 5, 2, '2025-12-19 08:00:00', '2025-12-19 16:00:00', '2025-12-19 08:00:00', '2025-12-19 16:00:00', NULL, 'Auto Generated Job', 'completed', 12000.00, 3600.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(2, 'JOB-251220-9426', 5, 1, 5, '2025-12-20 13:00:00', '2025-12-20 20:00:00', '2025-12-20 13:00:00', '2025-12-20 20:00:00', NULL, 'Auto Generated Job', 'completed', 3500.00, 1050.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(3, 'JOB-251220-1306', 2, 1, 5, '2025-12-20 10:00:00', '2025-12-20 16:00:00', '2025-12-20 10:00:00', '2025-12-20 16:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(4, 'JOB-251220-4113', 2, 2, 3, '2025-12-20 09:00:00', '2025-12-20 16:00:00', '2025-12-20 09:00:00', '2025-12-20 16:00:00', NULL, 'Auto Generated Job', 'completed', 3500.00, 1050.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(5, 'JOB-251221-4927', 7, 3, 3, '2025-12-21 10:00:00', '2025-12-21 15:00:00', '2025-12-21 10:00:00', '2025-12-21 15:00:00', NULL, 'Auto Generated Job', 'completed', 6000.00, 1800.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(6, 'JOB-251221-8441', 4, 5, 5, '2025-12-21 12:00:00', '2025-12-21 20:00:00', '2025-12-21 12:00:00', '2025-12-21 20:00:00', NULL, 'Auto Generated Job', 'completed', 12000.00, 3600.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(7, 'JOB-251222-8285', 7, 2, 5, '2025-12-22 14:00:00', '2025-12-22 20:00:00', '2025-12-22 14:00:00', '2025-12-22 20:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(8, 'JOB-251222-5148', 6, 1, 4, '2025-12-22 09:00:00', '2025-12-22 15:00:00', '2025-12-22 09:00:00', '2025-12-22 15:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(9, 'JOB-251223-2379', 5, 5, 4, '2025-12-23 08:00:00', '2025-12-23 13:00:00', '2025-12-23 08:00:00', '2025-12-23 13:00:00', NULL, 'Auto Generated Job', 'completed', 7500.00, 2250.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(10, 'JOB-251224-4079', 7, 3, 6, '2025-12-24 13:00:00', '2025-12-24 17:00:00', '2025-12-24 13:00:00', '2025-12-24 17:00:00', NULL, 'Auto Generated Job', 'completed', 4800.00, 1440.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(11, 'JOB-251225-4124', 3, 1, 5, '2025-12-25 14:00:00', '2025-12-25 20:00:00', '2025-12-25 14:00:00', '2025-12-25 20:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(12, 'JOB-251227-4385', 4, 1, 2, '2025-12-27 10:00:00', '2025-12-27 16:00:00', '2025-12-27 10:00:00', '2025-12-27 16:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(13, 'JOB-251227-3371', 6, 2, 4, '2025-12-27 11:00:00', '2025-12-27 15:00:00', '2025-12-27 11:00:00', '2025-12-27 15:00:00', NULL, 'Auto Generated Job', 'completed', 2000.00, 600.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(14, 'JOB-251227-7161', 2, 2, 3, '2025-12-27 11:00:00', '2025-12-27 14:00:00', '2025-12-27 11:00:00', '2025-12-27 14:00:00', NULL, 'Auto Generated Job', 'completed', 1500.00, 450.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(15, 'JOB-251228-8258', 5, 1, 4, '2025-12-28 10:00:00', '2025-12-28 13:00:00', '2025-12-28 10:00:00', '2025-12-28 13:00:00', NULL, 'Auto Generated Job', 'completed', 1500.00, 450.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(16, 'JOB-251228-9757', 1, 5, 4, '2025-12-28 13:00:00', '2025-12-28 19:00:00', '2025-12-28 13:00:00', '2025-12-28 19:00:00', NULL, 'Auto Generated Job', 'completed', 9000.00, 2700.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(17, 'JOB-251231-3899', 1, 5, 4, '2025-12-31 11:00:00', '2025-12-31 18:00:00', '2025-12-31 11:00:00', '2025-12-31 18:00:00', NULL, 'Auto Generated Job', 'completed', 10500.00, 3150.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(18, 'JOB-251231-9232', 4, 3, 5, '2025-12-31 12:00:00', '2025-12-31 17:00:00', '2025-12-31 12:00:00', '2025-12-31 17:00:00', NULL, 'Auto Generated Job', 'completed', 6000.00, 1800.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(19, 'JOB-251231-1676', 1, 3, 3, '2025-12-31 08:00:00', '2025-12-31 14:00:00', '2025-12-31 08:00:00', '2025-12-31 14:00:00', NULL, 'Auto Generated Job', 'completed', 7200.00, 2160.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(20, 'JOB-260102-9027', 2, 3, 2, '2026-01-02 13:00:00', '2026-01-02 16:00:00', '2026-01-02 13:00:00', '2026-01-02 16:00:00', NULL, 'Auto Generated Job', 'completed', 3600.00, 1080.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(21, 'JOB-260104-4903', 2, 3, 6, '2026-01-04 10:00:00', '2026-01-04 14:00:00', '2026-01-04 10:00:00', '2026-01-04 14:00:00', NULL, 'Auto Generated Job', 'completed', 4800.00, 1440.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(22, 'JOB-260104-4291', 5, 2, 3, '2026-01-04 08:00:00', '2026-01-04 15:00:00', '2026-01-04 08:00:00', '2026-01-04 15:00:00', NULL, 'Auto Generated Job', 'completed', 3500.00, 1050.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(23, 'JOB-260104-2913', 1, 1, 6, '2026-01-04 14:00:00', '2026-01-04 17:00:00', '2026-01-04 14:00:00', '2026-01-04 17:00:00', NULL, 'Auto Generated Job', 'completed', 1500.00, 450.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(24, 'JOB-260106-4085', 2, 5, 5, '2026-01-06 14:00:00', '2026-01-06 19:00:00', '2026-01-06 14:00:00', '2026-01-06 19:00:00', NULL, 'Auto Generated Job', 'completed', 7500.00, 2250.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(25, 'JOB-260106-4736', 2, 2, 2, '2026-01-06 09:00:00', '2026-01-06 16:00:00', '2026-01-06 09:00:00', '2026-01-06 16:00:00', NULL, 'Auto Generated Job', 'completed', 3500.00, 1050.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(26, 'JOB-260106-7677', 1, 1, 3, '2026-01-06 14:00:00', '2026-01-06 20:00:00', '2026-01-06 14:00:00', '2026-01-06 20:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(27, 'JOB-260107-9191', 2, 2, 3, '2026-01-07 09:00:00', '2026-01-07 17:00:00', '2026-01-07 09:00:00', '2026-01-07 17:00:00', NULL, 'Auto Generated Job', 'completed', 4000.00, 1200.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(28, 'JOB-260107-1631', 3, 1, 6, '2026-01-07 13:00:00', '2026-01-07 17:00:00', '2026-01-07 13:00:00', '2026-01-07 17:00:00', NULL, 'Auto Generated Job', 'completed', 2000.00, 600.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(29, 'JOB-260109-3841', 5, 2, 6, '2026-01-09 13:00:00', '2026-01-09 16:00:00', '2026-01-09 13:00:00', '2026-01-09 16:00:00', NULL, 'Auto Generated Job', 'completed', 1500.00, 450.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(30, 'JOB-260111-9545', 3, 4, 3, '2026-01-11 08:00:00', '2026-01-11 13:00:00', '2026-01-11 08:00:00', '2026-01-11 13:00:00', NULL, 'Auto Generated Job', 'completed', 4000.00, 1200.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(31, 'JOB-260112-5718', 2, 1, 6, '2026-01-12 08:00:00', '2026-01-12 13:00:00', '2026-01-12 08:00:00', '2026-01-12 13:00:00', NULL, 'Auto Generated Job', 'completed', 2500.00, 750.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(32, 'JOB-260113-1272', 4, 4, 6, '2026-01-13 11:00:00', '2026-01-13 16:00:00', '2026-01-13 11:00:00', '2026-01-13 16:00:00', NULL, 'Auto Generated Job', 'completed', 4000.00, 1200.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(33, 'JOB-260113-7682', 6, 1, 4, '2026-01-13 10:00:00', '2026-01-13 15:00:00', '2026-01-13 10:00:00', '2026-01-13 15:00:00', NULL, 'Auto Generated Job', 'completed', 2500.00, 750.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(34, 'JOB-260113-4916', 6, 2, 5, '2026-01-13 11:00:00', '2026-01-13 15:00:00', '2026-01-13 11:00:00', '2026-01-13 15:00:00', NULL, 'Auto Generated Job', 'completed', 2000.00, 600.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(35, 'JOB-260115-9209', 5, 4, 2, '2026-01-15 12:00:00', '2026-01-15 15:00:00', '2026-01-15 12:00:00', '2026-01-15 15:00:00', NULL, 'Auto Generated Job', 'completed', 2400.00, 720.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(36, 'JOB-260116-1218', 6, 3, 2, '2026-01-16 11:00:00', '2026-01-16 17:00:00', '2026-01-16 11:00:00', '2026-01-16 17:00:00', NULL, 'Auto Generated Job', 'completed', 7200.00, 2160.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(37, 'JOB-260118-9137', 4, 2, 4, '2026-01-18 14:00:00', '2026-01-18 22:00:00', '2026-01-18 14:00:00', '2026-01-18 22:00:00', NULL, 'Auto Generated Job', 'completed', 4000.00, 1200.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(38, 'JOB-260120-8431', 7, 3, 3, '2026-01-20 12:00:00', '2026-01-20 17:00:00', '2026-01-20 12:00:00', '2026-01-20 17:00:00', NULL, 'Auto Generated Job', 'completed', 6000.00, 1800.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(39, 'JOB-260120-5548', 6, 3, 2, '2026-01-20 13:00:00', '2026-01-20 16:00:00', '2026-01-20 13:00:00', '2026-01-20 16:00:00', NULL, 'Auto Generated Job', 'completed', 3600.00, 1080.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(40, 'JOB-260122-8782', 5, 1, 6, '2026-01-22 10:00:00', '2026-01-22 13:00:00', '2026-01-22 10:00:00', '2026-01-22 13:00:00', NULL, 'Auto Generated Job', 'completed', 1500.00, 450.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(41, 'JOB-260122-6745', 5, 4, 2, '2026-01-22 14:00:00', '2026-01-22 18:00:00', '2026-01-22 14:00:00', '2026-01-22 18:00:00', NULL, 'Auto Generated Job', 'completed', 3200.00, 960.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(42, 'JOB-260122-1213', 2, 5, 4, '2026-01-22 11:00:00', '2026-01-22 15:00:00', '2026-01-22 11:00:00', '2026-01-22 15:00:00', NULL, 'Auto Generated Job', 'completed', 6000.00, 1800.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(43, 'JOB-260124-3082', 2, 2, 4, '2026-01-24 14:00:00', '2026-01-24 17:00:00', '2026-01-24 14:00:00', '2026-01-24 17:00:00', NULL, 'Auto Generated Job', 'completed', 1500.00, 450.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(44, 'JOB-260125-6037', 1, 1, 2, '2026-01-25 09:00:00', '2026-01-25 17:00:00', '2026-01-25 09:00:00', '2026-01-25 17:00:00', NULL, 'Auto Generated Job', 'completed', 4000.00, 1200.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(45, 'JOB-260125-8362', 7, 1, 2, '2026-01-25 08:00:00', '2026-01-25 14:00:00', '2026-01-25 08:00:00', '2026-01-25 14:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(46, 'JOB-260126-8000', 1, 2, 6, '2026-01-26 12:00:00', '2026-01-26 18:00:00', '2026-01-26 12:00:00', '2026-01-26 18:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(47, 'JOB-260126-6604', 2, 3, 5, '2026-01-26 12:00:00', '2026-01-26 17:00:00', '2026-01-26 12:00:00', '2026-01-26 17:00:00', NULL, 'Auto Generated Job', 'completed', 6000.00, 1800.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(48, 'JOB-260126-5178', 4, 5, 4, '2026-01-26 11:00:00', '2026-01-26 15:00:00', '2026-01-26 11:00:00', '2026-01-26 15:00:00', NULL, 'Auto Generated Job', 'completed', 6000.00, 1800.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(49, 'JOB-260127-9598', 3, 3, 4, '2026-01-27 10:00:00', '2026-01-27 14:00:00', '2026-01-27 10:00:00', '2026-01-27 14:00:00', NULL, 'Auto Generated Job', 'completed', 4800.00, 1440.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(50, 'JOB-260127-3792', 3, 5, 4, '2026-01-27 10:00:00', '2026-01-27 18:00:00', '2026-01-27 10:00:00', '2026-01-27 18:00:00', NULL, 'Auto Generated Job', 'completed', 12000.00, 3600.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(51, 'JOB-260127-3399', 5, 3, 4, '2026-01-27 13:00:00', '2026-01-27 20:00:00', '2026-01-27 13:00:00', '2026-01-27 20:00:00', NULL, 'Auto Generated Job', 'completed', 8400.00, 2520.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(52, 'JOB-260128-1962', 2, 1, 3, '2026-01-28 09:00:00', '2026-01-28 14:00:00', '2026-01-28 09:00:00', '2026-01-28 14:00:00', NULL, 'Auto Generated Job', 'completed', 2500.00, 750.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(53, 'JOB-260130-5969', 7, 1, 3, '2026-01-30 10:00:00', '2026-01-30 13:00:00', '2026-01-30 10:00:00', '2026-01-30 13:00:00', NULL, 'Auto Generated Job', 'completed', 1500.00, 450.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(54, 'JOB-260130-5921', 2, 3, 2, '2026-01-30 10:00:00', '2026-01-30 16:00:00', '2026-01-30 10:00:00', '2026-01-30 16:00:00', NULL, 'Auto Generated Job', 'completed', 7200.00, 2160.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(55, 'JOB-260131-4185', 7, 4, 2, '2026-01-31 14:00:00', '2026-01-31 20:00:00', '2026-01-31 14:00:00', '2026-01-31 20:00:00', NULL, 'Auto Generated Job', 'completed', 4800.00, 1440.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(56, 'JOB-260131-9909', 4, 1, 4, '2026-01-31 10:00:00', '2026-01-31 16:00:00', '2026-01-31 10:00:00', '2026-01-31 16:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(57, 'JOB-260201-1603', 1, 2, 6, '2026-02-01 09:00:00', '2026-02-01 17:00:00', '2026-02-01 09:00:00', '2026-02-01 17:00:00', NULL, 'Auto Generated Job', 'completed', 4000.00, 1200.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(58, 'JOB-260201-9169', 4, 1, 3, '2026-02-01 13:00:00', '2026-02-01 17:00:00', '2026-02-01 13:00:00', '2026-02-01 17:00:00', NULL, 'Auto Generated Job', 'completed', 2000.00, 600.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(59, 'JOB-260201-5975', 6, 5, 6, '2026-02-01 11:00:00', '2026-02-01 15:00:00', '2026-02-01 11:00:00', '2026-02-01 15:00:00', NULL, 'Auto Generated Job', 'completed', 6000.00, 1800.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(60, 'JOB-260204-1216', 4, 5, 4, '2026-02-04 11:00:00', '2026-02-04 18:00:00', '2026-02-04 11:00:00', '2026-02-04 18:00:00', NULL, 'Auto Generated Job', 'completed', 10500.00, 3150.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(61, 'JOB-260206-5818', 7, 2, 6, '2026-02-06 14:00:00', '2026-02-06 21:00:00', '2026-02-06 14:00:00', '2026-02-06 21:00:00', NULL, 'Auto Generated Job', 'completed', 3500.00, 1050.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(62, 'JOB-260206-6646', 4, 1, 2, '2026-02-06 08:00:00', '2026-02-06 14:00:00', '2026-02-06 08:00:00', '2026-02-06 14:00:00', NULL, 'Auto Generated Job', 'completed', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(63, 'JOB-260207-7363', 5, 1, 5, '2026-02-07 08:00:00', '2026-02-07 16:00:00', '2026-02-07 08:00:00', '2026-02-07 16:00:00', NULL, 'Auto Generated Job', 'completed', 4000.00, 1200.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(64, 'JOB-260207-2697', 6, 3, 6, '2026-02-07 11:00:00', '2026-02-07 14:00:00', '2026-02-07 11:00:00', '2026-02-07 14:00:00', NULL, 'Auto Generated Job', 'completed', 3600.00, 1080.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(65, 'JOB-260207-7519', 5, 3, 6, '2026-02-07 08:00:00', '2026-02-07 12:00:00', '2026-02-07 08:00:00', '2026-02-07 12:00:00', NULL, 'Auto Generated Job', 'completed', 4800.00, 1440.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(66, 'JOB-260209-2224', 2, 4, 4, '2026-02-09 12:00:00', '2026-02-09 19:00:00', '2026-02-09 12:00:00', '2026-02-09 19:00:00', NULL, 'Auto Generated Job', 'completed', 5600.00, 1680.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(67, 'JOB-260209-2256', 1, 2, 3, '2026-02-09 10:00:00', '2026-02-09 17:00:00', '2026-02-09 10:00:00', '2026-02-09 17:00:00', NULL, 'Auto Generated Job', 'completed', 3500.00, 1050.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(68, 'JOB-260210-4956', 4, 1, 6, '2026-02-10 13:00:00', '2026-02-10 16:00:00', '2026-02-10 13:00:00', '2026-02-10 16:00:00', NULL, 'Auto Generated Job', 'completed', 1500.00, 450.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(69, 'JOB-260213-4189', 3, 1, 3, '2026-02-13 10:00:00', '2026-02-13 15:00:00', '2026-02-13 10:00:00', '2026-02-13 15:00:00', NULL, 'Auto Generated Job', 'completed', 2500.00, 750.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(70, 'JOB-260216-8747', 2, 4, 6, '2026-02-16 10:00:00', '2026-02-16 17:00:00', '2026-02-16 10:00:00', '2026-02-16 17:00:00', NULL, 'Auto Generated Job', 'completed', 5600.00, 1680.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(71, 'JOB-260216-9897', 3, 5, 3, '2026-02-16 13:00:00', '2026-02-16 18:00:00', '2026-02-16 13:00:00', '2026-02-16 18:00:00', NULL, 'Auto Generated Job', 'completed', 7500.00, 2250.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(72, 'JOB-260216-1775', 4, 4, 6, '2026-02-16 14:00:00', '2026-02-16 20:00:00', '2026-02-16 14:00:00', '2026-02-16 20:00:00', NULL, 'Auto Generated Job', 'completed', 4800.00, 1440.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(73, 'JOB-260217-2941', 7, 1, 6, '2026-02-17 12:00:00', '2026-02-17 20:00:00', '2026-02-17 12:00:00', '2026-02-17 20:00:00', NULL, 'Auto Generated Job', 'completed', 4000.00, 1200.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(74, 'JOB-260219-7160', 6, 5, 2, '2026-02-19 09:00:00', '2026-02-19 15:00:00', '2026-02-17 14:12:09', '2026-02-17 14:18:01', 'job_evidence/LqIHSIiVzq9ezeUiYhga8J5OppLt5IOFqpwxfoFN.png', 'งานจบแล้ว', 'completed_pending_approval', 9000.00, 2700.00, 'paid', NULL, 'BYPASS-1771337881', 'payments/ljeMbTYDlCCCClgo7QHL1Swy7DpuYDjcyyN87SrN.jpg', '2026-02-16 21:02:33', '2026-02-17 07:18:01', NULL),
(75, 'JOB-260219-3359', 1, 4, 3, '2026-02-19 12:00:00', '2026-02-19 18:00:00', '2026-02-18 01:27:19', NULL, NULL, 'Auto Generated Job', 'in_progress', 4800.00, 1440.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-17 18:27:19', NULL),
(76, 'JOB-260220-2673', 3, 5, 4, '2026-02-20 10:00:00', '2026-02-20 14:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 6000.00, 1800.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(77, 'JOB-260221-5365', 1, 4, 6, '2026-02-21 11:00:00', '2026-02-21 17:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 4800.00, 1440.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(78, 'JOB-260221-3389', 4, 3, 5, '2026-02-21 11:00:00', '2026-02-21 14:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 3600.00, 1080.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(79, 'JOB-260222-9583', 4, 4, 5, '2026-02-22 08:00:00', '2026-02-22 11:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 2400.00, 720.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(80, 'JOB-260222-3523', 6, 2, 4, '2026-02-22 11:00:00', '2026-02-22 19:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 4000.00, 1200.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(81, 'JOB-260222-4143', 7, 2, 5, '2026-02-22 10:00:00', '2026-02-22 15:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 2500.00, 750.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(82, 'JOB-260223-7641', 7, 2, 6, '2026-02-23 11:00:00', '2026-02-23 17:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 3000.00, 900.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(83, 'JOB-260223-9429', 3, 4, 6, '2026-02-23 10:00:00', '2026-02-23 16:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 4800.00, 1440.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(84, 'JOB-260224-5966', 4, 2, 2, '2026-02-24 12:00:00', '2026-02-24 19:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 3500.00, 1050.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(85, 'JOB-260224-9300', 4, 3, 5, '2026-02-24 11:00:00', '2026-02-24 19:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 9600.00, 2880.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(86, 'JOB-260224-8808', 3, 4, 4, '2026-02-24 10:00:00', '2026-02-24 14:00:00', NULL, NULL, NULL, 'Auto Generated Job', 'scheduled', 3200.00, 960.00, 'pending', NULL, NULL, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(87, 'JOB-20260217-001', 8, 1, 2, '2026-02-17 18:36:00', '2026-02-18 17:36:00', '2026-02-17 10:37:23', '2026-02-17 10:44:10', 'job_evidence/YHRa4kvflMQCCGiaSl1ZwJJrqc2lv2L2FoK2bf0U.png', 'asdasd', 'completed_pending_approval', 2.00, 1.00, 'paid', NULL, 'BYPASS-1771325049', 'payments/JkJIsmKygYYQmQ3aoxDRLJUXLQeJbl1Q9coG8aDw.png', '2026-02-17 03:36:44', '2026-02-17 03:44:10', NULL),
(88, 'JOB-20260218-001', 9, 1, 2, '2026-02-19 00:22:00', '2026-02-19 01:16:00', NULL, NULL, NULL, NULL, 'cancelled', 5.00, 2.00, 'deposit_paid', 'transfer', NULL, NULL, '2026-02-17 17:18:48', '2026-02-17 17:24:42', NULL),
(89, 'JOB-20260218-002', 8, 4, 3, '2026-02-18 00:25:00', '2026-02-18 02:22:00', NULL, NULL, NULL, NULL, 'cancelled', 10.00, 5.00, 'deposit_paid', 'transfer', NULL, NULL, '2026-02-17 17:22:48', '2026-02-17 17:24:44', NULL),
(90, 'JOB-20260218-003', 9, 5, 2, '2026-02-19 00:26:00', '2026-02-19 00:29:00', '2026-02-18 00:46:13', '2026-02-18 00:54:04', 'job_evidence/9OtlRcgFlpIjPQvt4wnD5jtscjjz3kpmxXiHi9ie.jpg', 'sdasd', 'completed_pending_approval', 5.00, 3.00, 'paid', 'cash', NULL, NULL, '2026-02-17 17:27:09', '2026-02-17 17:54:04', NULL),
(91, 'JOB-20260218-004', 9, 1, 2, '2026-02-20 00:39:00', '2026-02-21 00:39:00', NULL, NULL, NULL, 'เยี่ยมมาก', 'completed', 100.00, 95.00, 'paid', 'cash', NULL, NULL, '2026-02-17 17:39:16', '2026-02-17 17:53:36', NULL),
(92, 'JOB-20260218-005', 9, 3, 3, '2026-02-22 01:25:00', '2026-02-23 01:25:00', '2026-02-18 01:27:14', '2026-02-18 01:27:36', 'job_evidence/CH70fKAYXlgQj5pIQRniwhO6H9ToEN8JWOFhatHK.png', 'asdas', 'completed', 500.00, 450.00, 'paid', 'cash', NULL, NULL, '2026-02-17 18:25:55', '2026-02-17 18:28:09', NULL);

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
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `customer_code` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `customer_type` enum('individual','farm','company') NOT NULL DEFAULT 'individual',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`id`, `customer_code`, `name`, `phone`, `address`, `latitude`, `longitude`, `customer_type`, `notes`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CUS-001', 'กำนันแม้น', '081-111-1111', '12/3 หมู่ 1 ต.บ้านนา อ.เมือง จ.ขอนแก่น', NULL, NULL, 'individual', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(2, 'CUS-002', 'เจ๊แต๋ว สวนผลไม้', '089-222-2222', 'สวนป้าแต๋ว ระยอง ฮิ', NULL, NULL, 'farm', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(3, 'CUS-003', 'บจก. เกษตรรุ่งเรือง', '02-333-4444', '88 นิคมอุตสาหกรรมนวนคร', NULL, NULL, 'company', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(4, 'CUS-004', 'ลุงมี นาข้าว', '085-555-5555', 'ทุ่งกุลาร้องไห้ จ.ร้อยเอ็ด', NULL, NULL, 'individual', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(5, 'CUS-005', 'ไร่อ้อย สุขใจ', '090-666-6666', 'อ.ท่าม่วง จ.กาญจนบุรี', NULL, NULL, 'farm', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(6, 'CUS-006', 'ผู้ใหญ่บ้าน สมยศ', '087-777-8888', 'หมู่ 5 ต.หนองหญ้าไซ', NULL, NULL, 'individual', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(7, 'CUS-007', 'สหกรณ์โคนม วังน้ำเขียว', '044-999-9999', 'อ.วังน้ำเขียว จ.นครราชสีมา', NULL, NULL, 'company', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(8, 'CUS-008', 'ณัฏฐพล ฟาร์ม', '0643086816', 'sdf', NULL, NULL, 'individual', NULL, '2026-02-17 03:31:26', '2026-02-17 03:31:26', NULL),
(9, 'CUS-009', 'นายเต้ยอิอิ', '0123456789', 'asdasd', NULL, NULL, 'individual', NULL, '2026-02-17 07:04:16', '2026-02-17 07:04:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_code` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('drone','tractor','harvester','sprayer','excavator','other') NOT NULL,
  `registration_number` varchar(255) DEFAULT NULL,
  `current_hours` decimal(10,2) NOT NULL DEFAULT 0.00,
  `maintenance_hour_threshold` decimal(8,2) NOT NULL,
  `hourly_rate` decimal(10,2) DEFAULT NULL,
  `current_status` enum('available','booked','in_use','maintenance','breakdown') NOT NULL DEFAULT 'available',
  `image_path` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `equipment_code`, `name`, `type`, `registration_number`, `current_hours`, `maintenance_hour_threshold`, `hourly_rate`, `current_status`, `image_path`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'TR-001', 'รถไถ Kubota L5018 (คันที่ 1)', 'tractor', NULL, 342.00, 500.00, 500.00, 'available', 'storage/equipments/ACoeaY65XyqS9jP4OEKx6VX8ZkVnMpZYzEwr6hmq.jpg', '2026-02-16 21:02:33', '2026-02-17 06:48:29', NULL),
(2, 'TR-002', 'รถไถ Kubota L5018 (คันที่ 2)', 'tractor', NULL, 452.00, 500.00, 500.00, 'available', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(3, 'HV-001', 'รถเกี่ยวข้าว Yanmar YH850', 'harvester', NULL, 128.00, 300.00, 1200.00, 'available', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(4, 'DR-001', 'โดรนพ่นยา DJI Agras T30', 'drone', NULL, 93.00, 100.00, 800.00, 'available', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(5, 'EX-001', 'รถขุดเล็ก (Backhoe) PC30', 'excavator', NULL, 408.00, 600.00, 1500.00, 'available', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL);

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
-- Table structure for table `fuel_logs`
--

CREATE TABLE `fuel_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `fuel_source` enum('external','internal') NOT NULL DEFAULT 'external',
  `fuel_tank_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `liters` decimal(8,2) DEFAULT NULL,
  `mileage` decimal(10,2) DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `refill_date` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fuel_logs`
--

INSERT INTO `fuel_logs` (`id`, `equipment_id`, `user_id`, `fuel_source`, `fuel_tank_id`, `amount`, `liters`, `mileage`, `image_path`, `note`, `refill_date`, `created_at`, `updated_at`) VALUES
(1, 1, 6, 'external', NULL, 877.50, 27.00, NULL, NULL, 'เติมปั๊ม ปตท. (Seeder)', '2026-02-04 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(2, 4, 5, 'internal', 1, 1175.57, 39.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2025-12-25 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(3, 5, 2, 'external', NULL, 975.00, 30.00, NULL, NULL, 'เติมปั๊ม ปตท. (Seeder)', '2026-01-29 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(4, 4, 4, 'internal', 1, 934.43, 31.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2026-01-24 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(5, 1, 6, 'internal', 1, 994.71, 33.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2026-01-07 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(6, 3, 2, 'internal', 1, 1296.14, 43.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2026-01-22 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(7, 2, 4, 'external', NULL, 942.50, 29.00, NULL, NULL, 'เติมปั๊ม ปตท. (Seeder)', '2026-01-11 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(8, 2, 3, 'internal', 1, 1235.86, 41.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2025-12-25 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(9, 2, 6, 'internal', 1, 1356.43, 45.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2026-02-14 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(10, 1, 5, 'external', NULL, 1332.50, 41.00, NULL, NULL, 'เติมปั๊ม ปตท. (Seeder)', '2026-01-12 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(11, 5, 4, 'internal', 1, 813.86, 27.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2026-02-09 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(12, 1, 6, 'internal', 1, 1024.86, 34.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2026-02-15 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(13, 5, 4, 'external', NULL, 1235.00, 38.00, NULL, NULL, 'เติมปั๊ม ปตท. (Seeder)', '2026-01-19 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(14, 4, 4, 'external', NULL, 910.00, 28.00, NULL, NULL, 'เติมปั๊ม ปตท. (Seeder)', '2026-01-27 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(15, 2, 5, 'external', NULL, 1105.00, 34.00, NULL, NULL, 'เติมปั๊ม ปตท. (Seeder)', '2026-01-21 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(16, 3, 6, 'internal', 1, 1296.14, 43.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2026-01-29 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(17, 5, 4, 'external', NULL, 1202.50, 37.00, NULL, NULL, 'เติมปั๊ม ปตท. (Seeder)', '2025-12-30 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(18, 5, 5, 'internal', 1, 1597.57, 53.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2025-12-29 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(19, 4, 2, 'internal', 1, 1296.14, 43.00, NULL, NULL, 'เติมจากถังบริษัท (Seeder)', '2025-12-29 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(20, 2, 4, 'external', NULL, 1885.00, 58.00, NULL, NULL, 'เติมปั๊ม ปตท. (Seeder)', '2026-01-02 04:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33');

-- --------------------------------------------------------

--
-- Table structure for table `fuel_purchases`
--

CREATE TABLE `fuel_purchases` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fuel_tank_id` bigint(20) UNSIGNED NOT NULL,
  `liters` decimal(10,2) NOT NULL,
  `price_per_liter` decimal(10,2) NOT NULL,
  `total_cost` decimal(12,2) NOT NULL,
  `purchase_date` date NOT NULL,
  `supplier` varchar(255) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fuel_purchases`
--

INSERT INTO `fuel_purchases` (`id`, `fuel_tank_id`, `liters`, `price_per_liter`, `total_cost`, `purchase_date`, `supplier`, `note`, `created_at`, `updated_at`) VALUES
(1, 1, 2000.00, 29.50, 59000.00, '2025-12-17', 'Seeder Oil Supply', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(2, 1, 1500.00, 31.00, 46500.00, '2026-01-17', 'Seeder Oil Supply', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(3, 2, 1000.00, 30.00, 30000.00, '2026-01-27', 'Seeder Oil Supply', NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33');

-- --------------------------------------------------------

--
-- Table structure for table `fuel_tanks`
--

CREATE TABLE `fuel_tanks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `capacity` decimal(10,2) NOT NULL,
  `current_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `average_price` decimal(10,4) NOT NULL DEFAULT 0.0000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fuel_tanks`
--

INSERT INTO `fuel_tanks` (`id`, `name`, `capacity`, `current_balance`, `average_price`, `created_at`, `updated_at`) VALUES
(1, 'ถังใหญ่ (ดีเซล B7)', 5000.00, 3068.00, 30.1429, '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(2, 'ถังสำรอง (หลังอู่)', 2000.00, 1000.00, 30.0000, '2026-02-16 21:02:33', '2026-02-16 21:02:33');

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
-- Table structure for table `leaves`
--

CREATE TABLE `leaves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `start_date` datetime NOT NULL,
  `end_date` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_logs`
--

CREATE TABLE `maintenance_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `equipment_id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED DEFAULT NULL,
  `maintenance_type` varchar(255) NOT NULL DEFAULT 'corrective',
  `description` text NOT NULL,
  `status` enum('pending','in_progress','completed') NOT NULL DEFAULT 'pending',
  `total_cost` decimal(10,2) NOT NULL DEFAULT 0.00,
  `image_url` varchar(255) DEFAULT NULL,
  `service_provider` varchar(255) DEFAULT NULL,
  `reset_counter` tinyint(1) NOT NULL DEFAULT 0,
  `maintenance_date` datetime DEFAULT NULL,
  `completion_date` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_logs`
--

INSERT INTO `maintenance_logs` (`id`, `equipment_id`, `booking_id`, `maintenance_type`, `description`, `status`, `total_cost`, `image_url`, `service_provider`, `reset_counter`, `maintenance_date`, `completion_date`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 'corrective', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 1701.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2025-12-13 04:02:33', '2025-12-13 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(2, 1, NULL, 'preventive', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 783.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2026-01-10 04:02:33', '2026-01-10 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(3, 2, NULL, 'preventive', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 4996.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2026-01-09 04:02:33', '2026-01-09 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(4, 2, NULL, 'corrective', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 2877.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2026-01-14 04:02:33', '2026-01-14 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(5, 2, NULL, 'preventive', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 843.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2025-11-26 04:02:33', '2025-11-26 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(6, 3, NULL, 'preventive', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 1403.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2026-02-02 04:02:33', '2026-02-02 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(7, 3, NULL, 'corrective', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 4941.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2026-02-06 04:02:33', '2026-02-06 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(8, 3, NULL, 'preventive', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 4216.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2026-02-02 04:02:33', '2026-02-02 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(9, 4, NULL, 'corrective', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 4466.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2025-12-23 04:02:33', '2025-12-23 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(10, 4, NULL, 'preventive', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 1918.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2025-11-18 04:02:33', '2025-11-18 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33'),
(11, 5, NULL, 'corrective', 'เช็คระยะตามกำหนด / เปลี่ยนถ่ายน้ำมันเครื่อง', 'completed', 4062.00, NULL, 'ช่างศูนย์บริการ (Seeder)', 0, '2026-01-11 04:02:33', '2026-01-11 08:02:33', '2026-02-16 21:02:33', '2026-02-16 21:02:33');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_12_27_093601_create_equipment_table', 1),
(5, '2025_12_27_095632_create_personal_access_tokens_table', 1),
(6, '2025_12_27_100057_create_customers_table', 1),
(7, '2025_12_27_102010_create_bookings_table', 1),
(8, '2025_12_27_102903_create_maintenance_logs_table', 1),
(9, '2025_12_27_104537_create_task_activities_table', 1),
(10, '2025_12_28_192531_create_leaves_table', 1),
(11, '2025_12_30_092005_add_payment_details_to_bookings_table', 1),
(12, '2025_12_30_100307_add_image_and_note_to_bookings_table', 1),
(13, '2025_12_31_115716_create_fuel_logs_table', 1),
(14, '2026_01_01_131321_modify_payment_status_in_bookings_table', 1),
(15, '2026_01_01_155723_add_payment_trans_ref_to_bookings_table', 1),
(16, '2026_01_02_132415_create_fuel_tanks_table', 1),
(17, '2026_01_02_132601_create_fuel_purchases_table', 1),
(18, '2026_01_02_132631_update_fuel_logs_table', 1),
(19, '2026_01_02_140439_create_settings_table', 1),
(20, '2026_02_18_001000_add_payment_method_to_bookings_table', 2);

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` text NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'general',
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `task_activities`
--

CREATE TABLE `task_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `booking_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_type` enum('check_in','photo_uploaded','issue_reported','finished') NOT NULL,
  `description` text DEFAULT NULL,
  `image_paths` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`image_paths`)),
  `location_lat` decimal(10,8) DEFAULT NULL,
  `location_lng` decimal(11,8) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `role` enum('admin','staff') NOT NULL DEFAULT 'staff',
  `phone` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `username`, `email`, `password`, `pin`, `role`, `phone`, `is_active`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Administrator (Main)', 'admin', 'admin@agritech.com', '$2y$12$g5F/dqJg4LEVdMOazbDPhe6lFq.YvE24cSOMqFTk3k1qeV0I.FPI6', NULL, 'admin', NULL, 1, NULL, '2026-02-16 21:02:31', '2026-02-16 21:02:31', NULL),
(2, 'ช่างสมชาย (Senior)', 'somchai', 'somchai@agritech.com', '$2y$12$jkbMSWNQK7qxJ.MpUp8eEOctOBSnESLk1RX03iCw18D3pEwdH08eK', '$2y$12$M/3coubNfYPRp3SLHHHbvuZXa6H/mijmnF1SJpGEhs6B7HAYoeTrC', 'staff', NULL, 1, NULL, '2026-02-16 21:02:31', '2026-02-16 21:02:31', NULL),
(3, 'ช่างวิชัย (Junior)', 'wichai', 'wichai@agritech.com', '$2y$12$Gy9ndD4A4Gr.5AoNj68Ab.HCNNl2vitDM3Dzd9V/V5ABPRtjjUmi.', '$2y$12$/48fNhyt7X7KyluMnWiOPe5gOzWDVZu.aV89cLmRmzC1eXfs0i9h.', 'staff', NULL, 1, NULL, '2026-02-16 21:02:32', '2026-02-16 21:02:32', NULL),
(4, 'คนขับยอดชาย', 'yodchai', 'yodchai@agritech.com', '$2y$12$XpYeRSg33sKWUHtp2h2ppeXB3.oOLfOkDCFwl1g0N/mYKU.3iNWR6', '$2y$12$w8vrZzbo4Fwilp9XtYQ.O.aaKNxBABV8QwvrljvxFTCRBwcBxSpSm', 'staff', NULL, 1, NULL, '2026-02-16 21:02:32', '2026-02-16 21:02:32', NULL),
(5, 'คนขับสมศักดิ์', 'somsak', 'somsak@agritech.com', '$2y$12$JuhcVg15r4tJVxWFF09gaOB3jlloQma.OHvR6/GSeAZfZzy8aQaoC', '$2y$12$X0VvFO9xzHGe2oIPLFoZwuWWxg1Sxc/qV579gQNvMwbEf/ux9APUu', 'staff', NULL, 1, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL),
(6, 'ธุรการสาวสวย', 'admin_asst', 'admin_asst@agritech.com', '$2y$12$gjLDbh7nb0EqGhvQXynMP.jAh4P7oc4DvvtLGpN.r3wPhd0sEAQZC', '$2y$12$HfpjqbwvsYxPCGrTzjVvoO3mibO1pg/31d1a.LmdOi0wkXQqqLeVe', 'staff', NULL, 1, NULL, '2026-02-16 21:02:33', '2026-02-16 21:02:33', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bookings_job_number_unique` (`job_number`),
  ADD KEY `bookings_customer_id_foreign` (`customer_id`),
  ADD KEY `bookings_equipment_id_foreign` (`equipment_id`),
  ADD KEY `bookings_assigned_staff_id_foreign` (`assigned_staff_id`);

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
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `customers_customer_code_unique` (`customer_code`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipment_equipment_code_unique` (`equipment_code`),
  ADD UNIQUE KEY `equipment_registration_number_unique` (`registration_number`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fuel_logs`
--
ALTER TABLE `fuel_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fuel_logs_equipment_id_foreign` (`equipment_id`),
  ADD KEY `fuel_logs_user_id_foreign` (`user_id`),
  ADD KEY `fuel_logs_fuel_tank_id_foreign` (`fuel_tank_id`);

--
-- Indexes for table `fuel_purchases`
--
ALTER TABLE `fuel_purchases`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fuel_purchases_fuel_tank_id_foreign` (`fuel_tank_id`);

--
-- Indexes for table `fuel_tanks`
--
ALTER TABLE `fuel_tanks`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `leaves`
--
ALTER TABLE `leaves`
  ADD PRIMARY KEY (`id`),
  ADD KEY `leaves_user_id_foreign` (`user_id`);

--
-- Indexes for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `maintenance_logs_equipment_id_foreign` (`equipment_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`),
  ADD KEY `personal_access_tokens_expires_at_index` (`expires_at`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `settings_key_unique` (`key`);

--
-- Indexes for table `task_activities`
--
ALTER TABLE `task_activities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `task_activities_booking_id_foreign` (`booking_id`),
  ADD KEY `task_activities_user_id_foreign` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_username_unique` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=93;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_logs`
--
ALTER TABLE `fuel_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `fuel_purchases`
--
ALTER TABLE `fuel_purchases`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `fuel_tanks`
--
ALTER TABLE `fuel_tanks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `leaves`
--
ALTER TABLE `leaves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `task_activities`
--
ALTER TABLE `task_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_assigned_staff_id_foreign` FOREIGN KEY (`assigned_staff_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`),
  ADD CONSTRAINT `bookings_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`);

--
-- Constraints for table `fuel_logs`
--
ALTER TABLE `fuel_logs`
  ADD CONSTRAINT `fuel_logs_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fuel_logs_fuel_tank_id_foreign` FOREIGN KEY (`fuel_tank_id`) REFERENCES `fuel_tanks` (`id`),
  ADD CONSTRAINT `fuel_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `fuel_purchases`
--
ALTER TABLE `fuel_purchases`
  ADD CONSTRAINT `fuel_purchases_fuel_tank_id_foreign` FOREIGN KEY (`fuel_tank_id`) REFERENCES `fuel_tanks` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `leaves`
--
ALTER TABLE `leaves`
  ADD CONSTRAINT `leaves_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `maintenance_logs`
--
ALTER TABLE `maintenance_logs`
  ADD CONSTRAINT `maintenance_logs_equipment_id_foreign` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `task_activities`
--
ALTER TABLE `task_activities`
  ADD CONSTRAINT `task_activities_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `task_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
