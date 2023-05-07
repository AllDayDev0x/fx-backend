-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Dec 28, 2022 at 03:21 PM
-- Server version: 8.0.31-0ubuntu0.20.04.2
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `fx_albert`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `about` text COLLATE utf8mb4_unicode_ci,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/placeholder.jpeg',
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `gender` enum('male','female','others','rather-not-select') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'male',
  `status` tinyint NOT NULL DEFAULT '1',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `unique_id`, `name`, `email`, `password`, `about`, `picture`, `timezone`, `gender`, `status`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'admin-demo', 'Admin', 'demo@demo.com', '$2y$10$CJ1N5I7qLlSqtoYLiRib9OJ594tzRAMkNG16U20ufoh6on.o087au', 'About', 'https://freak.fan/placeholder.jpeg', 'Asia/Kolkata', 'male', 1, NULL, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(2, 'admin-demo', 'Test', 'test@demo.com', '$2y$10$EvywOybu8SRlvIG4o4v5JO0Tqa.3tOE4LxVpisB5fU89gZysdKuYm', 'About', 'https://freak.fan/placeholder.jpeg', 'Asia/Kolkata', 'male', 1, NULL, '2022-12-28 04:19:06', '2022-12-28 04:19:06');

-- --------------------------------------------------------

--
-- Table structure for table `audio_call_payments`
--

CREATE TABLE `audio_call_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1568705608',
  `model_id` int NOT NULL,
  `user_id` int NOT NULL,
  `audio_call_request_id` int NOT NULL,
  `user_card_id` int NOT NULL DEFAULT '0',
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `paid_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_date` datetime DEFAULT NULL,
  `is_failed` tinyint NOT NULL DEFAULT '0',
  `failed_reason` tinyint NOT NULL DEFAULT '0',
  `admin_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `user_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00',
  `total_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audio_call_requests`
--

CREATE TABLE `audio_call_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1957480160',
  `user_id` int NOT NULL,
  `model_id` int NOT NULL,
  `agora_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10eb71e39',
  `virtual_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10eb71e3d',
  `call_status` int NOT NULL DEFAULT '0',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audio_chat_messages`
--

CREATE TABLE `audio_chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1645152620',
  `audio_call_request_id` int NOT NULL,
  `model_id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bell_notifications`
--

CREATE TABLE `bell_notifications` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '662533830',
  `from_user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `image` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `post_id` int NOT NULL DEFAULT '0',
  `post_comment_id` int NOT NULL DEFAULT '0',
  `action_url` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '/home',
  `notification_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'follow',
  `is_read` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `block_users`
--

CREATE TABLE `block_users` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `block_by` int NOT NULL,
  `blocked_to` int NOT NULL,
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1074231444',
  `user_id` int NOT NULL,
  `order_id` int NOT NULL,
  `user_product_id` int NOT NULL,
  `quantity` double(8,2) NOT NULL DEFAULT '0.00',
  `per_quantity_price` double(8,2) NOT NULL DEFAULT '0.00',
  `sub_total` double(8,2) NOT NULL DEFAULT '0.00',
  `tax_price` double(8,2) NOT NULL DEFAULT '0.00',
  `delivery_price` double(8,2) NOT NULL DEFAULT '0.00',
  `total` double(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '665958664',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/cat-placeholder.jpeg',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category_details`
--

CREATE TABLE `category_details` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1309043170',
  `user_id` int NOT NULL DEFAULT '0',
  `post_id` int NOT NULL DEFAULT '0',
  `category_id` int NOT NULL DEFAULT '0',
  `type` enum('profile','post') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'profile',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_assets`
--

CREATE TABLE `chat_assets` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '518876842',
  `from_user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `chat_message_id` int NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `is_paid` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `blur_file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_asset_payments`
--

CREATE TABLE `chat_asset_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1869399733',
  `from_user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `chat_message_id` int NOT NULL,
  `user_card_id` int NOT NULL DEFAULT '0',
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_date` datetime DEFAULT NULL,
  `is_failed` tinyint NOT NULL DEFAULT '0',
  `failed_reason` tinyint NOT NULL DEFAULT '0',
  `admin_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `user_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_messages`
--

CREATE TABLE `chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1830904976',
  `from_user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_file_uploaded` int NOT NULL DEFAULT '0',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `is_paid` tinyint NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `chat_users`
--

CREATE TABLE `chat_users` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1810421687',
  `from_user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coinpayment_transactions`
--

CREATE TABLE `coinpayment_transactions` (
  `id` int UNSIGNED NOT NULL,
  `uuid` char(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `txn_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buyer_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `buyer_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `currency_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `time_expires` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amount_total_fiat` decimal(10,2) DEFAULT NULL,
  `amount` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `amountf` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `coin` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `confirms_needed` int DEFAULT NULL,
  `payment_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `qrcode_url` text COLLATE utf8mb4_unicode_ci,
  `received` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `receivedf` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `recv_confirms` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_text` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status_url` text COLLATE utf8mb4_unicode_ci,
  `timeout` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `checkout_url` longtext COLLATE utf8mb4_unicode_ci,
  `redirect_url` longtext COLLATE utf8mb4_unicode_ci,
  `cancel_url` longtext COLLATE utf8mb4_unicode_ci,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `coinpayment_transaction_items`
--

CREATE TABLE `coinpayment_transaction_items` (
  `id` int UNSIGNED NOT NULL,
  `coinpayment_transaction_id` int NOT NULL,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `qty` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `currency_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `state` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `currencies`
--

CREATE TABLE `currencies` (
  `id` bigint UNSIGNED NOT NULL,
  `currency_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `currency_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `currencies`
--

INSERT INTO `currencies` (`id`, `currency_name`, `currency_code`, `currency`, `created_at`, `updated_at`) VALUES
(1, 'United Arab Emirates Dirham', 'AED', 'د.إ', NULL, NULL),
(2, 'Afghanistani Afghani', 'AFN', '؋', NULL, NULL),
(3, 'Albanian Lek', 'ALL', 'Lek', NULL, NULL),
(4, 'Armenian Dram', 'AMD', 'դր', NULL, NULL),
(5, 'Netherlands Antillean Guilder', 'ANG', 'NAf', NULL, NULL),
(6, 'Angolan Kwanza', 'AOA', 'Kz', NULL, NULL),
(7, 'Argentine Peso', 'ARS', '$', NULL, NULL),
(8, 'Australian Dollar', 'AUD', '$', NULL, NULL),
(9, 'Aruban Florin', 'AWG', 'ƒ', NULL, NULL),
(10, 'Azerbaijani Manat', 'AZN', '₼', NULL, NULL),
(11, 'Bosnia-Herzegovina Convertible Mark', 'BAM', 'KM', NULL, NULL),
(12, 'Barbados Dollar', 'BBD', '$', NULL, NULL),
(13, 'Bangladeshi Taka', 'BDT', '৳', NULL, NULL),
(14, 'Bulgarian Lev', 'BGN', 'лв', NULL, NULL),
(15, 'Burundian Franc', 'BIF', 'FBu', NULL, NULL),
(16, 'Bermuda Dollar', 'BMD', '$', NULL, NULL),
(17, 'Brunei Dollar', 'BND', '$', NULL, NULL),
(18, 'Bolivian Boliviano', 'BOB', '$b', NULL, NULL),
(19, 'Brazilian Real', 'BRL', 'R$', NULL, NULL),
(20, 'Bahamian Dollar', 'BSD', '$', NULL, NULL),
(21, 'Botswana Pula', 'BWP', 'P', NULL, NULL),
(22, 'Belize Dollar', 'BZD', 'BZ$', NULL, NULL),
(23, 'Canadian Dollar', 'CAD', '$', NULL, NULL),
(24, 'Congolese franc', 'CDF', 'FC', NULL, NULL),
(25, 'Swiss Franc', 'CHF', 'CHF', NULL, NULL),
(26, 'Chilean Peso', 'CLP', '$', NULL, NULL),
(27, 'Chinese Yuan Renminbi', 'CNY', '¥', NULL, NULL),
(28, 'Colombian Peso', 'COP', '$', NULL, NULL),
(29, 'Cuban Convertible Peso', 'CUC', '$', NULL, NULL),
(30, 'Cape Verde Escudo', 'CVE', '$', NULL, NULL),
(31, 'Czech Koruna', 'CZK', 'Kč', NULL, NULL),
(32, 'Djiboutian Franc', 'DJF', 'Fdj', NULL, NULL),
(33, 'Danish Krone', 'DKK', 'kr.', NULL, NULL),
(34, 'Dominican Peso', 'DOP', 'RD$', NULL, NULL),
(35, 'Algerian Dinar', 'DZD', 'دج', NULL, NULL),
(36, 'Egyptian Pound', 'EGP', '£', NULL, NULL),
(37, 'Ethiopian Birr', 'ETB', 'ብር', NULL, NULL),
(38, 'European Euro', 'EUR', '€', NULL, NULL),
(39, 'Falkland Islands Pound', 'FKP', '£', NULL, NULL),
(40, 'Fiji Dollar', 'FJD', '$', NULL, NULL),
(41, 'United Kingdom Pound Sterling', 'GBP', '£', NULL, NULL),
(42, 'Georgian Lari', 'GEL', 'ლ', NULL, NULL),
(43, 'Gibraltar Pound', 'GIP', '£', NULL, NULL),
(44, 'Gambian Dalasi', 'GMD', 'D', NULL, NULL),
(45, 'Guinean Franc', 'GNF', 'FG', NULL, NULL),
(46, 'Guatemalan Quetzal', 'GTQ', 'Q', NULL, NULL),
(47, 'Guyanese Dollar', 'GYD', '$', NULL, NULL),
(48, 'Hong Kong Dollar', 'HKD', 'HK$', NULL, NULL),
(49, 'Honduran Lempira', 'HNL', 'L', NULL, NULL),
(50, 'Croatian Kuna', 'HRK', 'kn', NULL, NULL),
(51, 'Haitian Gourde', 'HTG', 'G', NULL, NULL),
(52, 'Hungarian Forint', 'HUF', 'Ft', NULL, NULL),
(53, 'Indonesian Rupiah', 'IDR', 'Rp', NULL, NULL),
(54, 'Israeli New Sheqel', 'ILS', '₪', NULL, NULL),
(55, 'Indian Rupee', 'INR', '₹', NULL, NULL),
(56, 'Icelandic Krona', 'ISK', 'kr', NULL, NULL),
(57, 'Jamaican Dollar', 'JMD', 'J$', NULL, NULL),
(58, 'Japanese Yen', 'JPY', '¥', NULL, NULL),
(59, 'Kenyan Shilling', 'KES', 'KSh,', NULL, NULL),
(60, 'Kyrgyzstani Som', 'KGS', 'лв', NULL, NULL),
(61, 'Cambodian Riel', 'KHR', '៛', NULL, NULL),
(62, 'Comorian Franc', 'KMF', 'CF', NULL, NULL),
(63, 'Korean Won', 'KRW', '₩', NULL, NULL),
(64, 'Cayman Islands Dollar', 'KYD', '$', NULL, NULL),
(65, 'Kazakhstani Tenge', 'KZT', 'лв', NULL, NULL),
(66, 'Lao Kip', 'LAK', '₭', NULL, NULL),
(67, 'Lebanese Pound', 'LBP', '£', NULL, NULL),
(68, 'Sri Lankan Rupee', 'LKR', '₨', NULL, NULL),
(69, 'Liberian Dollar', 'LRD', '$', NULL, NULL),
(70, 'Lesotho Loti', 'LSL', 'L', NULL, NULL),
(71, 'Moroccan Dirham', 'MAD', 'DH', NULL, NULL),
(72, 'Moldovan Leu', 'MDL', 'L', NULL, NULL),
(73, 'Malagasy Ariary', 'MGA', 'Ar', NULL, NULL),
(74, 'Macedonian Denar', 'MKD', 'ден', NULL, NULL),
(75, 'Myanmar Kyat', 'MMK', 'K', NULL, NULL),
(76, 'Mongolian Tugrik', 'MNT', '₮', NULL, NULL),
(77, 'Macanese Pataca', 'MOP', 'MOP$', NULL, NULL),
(78, 'Mauritanian Ouguiya', 'MRO', 'UM', NULL, NULL),
(79, 'Mauritian Rupee', 'MUR', '₨', NULL, NULL),
(80, 'Maldives Rufiyaa', 'MVR', 'Rf', NULL, NULL),
(81, 'Malawian Kwacha', 'MWK', 'MK', NULL, NULL),
(82, 'Mexican Peso', 'MXN', '$', NULL, NULL),
(83, 'Malaysian Ringgit', 'MYR', 'RM', NULL, NULL),
(84, 'Mozambican Metical', 'MZN', 'MT', NULL, NULL),
(85, 'Namibian Dollar', 'NAD', '$', NULL, NULL),
(86, 'Nigerian Naira', 'NGN', '₦', NULL, NULL),
(87, 'Nicaraguan Córdoba', 'NIO', 'C$', NULL, NULL),
(88, 'Norwegian Krone', 'NOK', 'kr', NULL, NULL),
(89, 'Nepalese Rupee', 'NPR', '₨', NULL, NULL),
(90, 'New Zealand Dollar', 'NZD', '$', NULL, NULL),
(91, 'Panamanian Balboa', 'PAB', 'B/.', NULL, NULL),
(92, 'Peruvian Nuevo Sol', 'PEN', 'S/.', NULL, NULL),
(93, 'Papua New Guinea Kina', 'PGK', 'K', NULL, NULL),
(94, 'Philippine Peso', 'PHP', '₱', NULL, NULL),
(95, 'Pakistan Rupee', 'PKR', '₨', NULL, NULL),
(96, 'Polish Zloty', 'PLN', 'zł', NULL, NULL),
(97, 'Paraguay Guarani', 'PYG', 'Gs', NULL, NULL),
(98, 'Qatari Riyal', 'QAR', '﷼', NULL, NULL),
(99, 'Romanian Leu', 'RON', 'lei', NULL, NULL),
(100, 'Serbian Dinar', 'RSD', 'Дин.', NULL, NULL),
(101, 'Russian Ruble', 'RUB', '₽', NULL, NULL),
(102, 'Rwandan Franc', 'RWF', 'FRw', NULL, NULL),
(103, 'Saudi Arabian Riyal', 'SAR', '﷼', NULL, NULL),
(104, 'Solomon Islands Dollar', 'SBD', '$', NULL, NULL),
(105, 'Seychelles Rupee', 'SCR', '₨', NULL, NULL),
(106, 'Swedish Krona', 'SEK', 'kr', NULL, NULL),
(107, 'Singapore Dollar', 'SGD', '$', NULL, NULL),
(108, 'Saint Helena Pound', 'SHP', '£', NULL, NULL),
(109, 'Sierra Leonean Leone', 'SLL', 'Le', NULL, NULL),
(110, 'Somali Shilling', 'SOS', 'S', NULL, NULL),
(111, 'Suriname Dollar', 'SRD', '$', NULL, NULL),
(112, 'Sao Tome Dobra', 'STD', 'Db', NULL, NULL),
(113, 'Swazi Lilangeni', 'SZL', 'E', NULL, NULL),
(114, 'Thai Baht', 'THB', '฿', NULL, NULL),
(115, 'Tajikistan Somoni', 'TJS', 'ЅM', NULL, NULL),
(116, 'Tongan Pa Anga', 'TOP', 'T$', NULL, NULL),
(117, 'Turkish New Lira', 'TRY', '₺', NULL, NULL),
(118, 'Trinidad and Tobago Dollar', 'TTD', 'TT$', NULL, NULL),
(119, 'New Taiwan Dollar', 'TWD', 'NT$', NULL, NULL),
(120, 'Tanzanian Shilling', 'TZS', 'TSh', NULL, NULL),
(121, 'Ukrainian Hryvnia', 'UAH', '₴', NULL, NULL),
(122, 'Ugandan Shilling', 'UGX', 'USh', NULL, NULL),
(123, 'United States Dollar', 'USD', '$', NULL, NULL),
(124, 'Uruguayan peso', 'UYU', '$U', NULL, NULL),
(125, 'Uzbekistani Som', 'UZS', 'лв', NULL, NULL),
(126, 'Viet Nam Dong', 'VND', '₫', NULL, NULL),
(127, 'Vanuatu vatu', 'VUV', 'VT', NULL, NULL),
(128, 'Samoan Tala', 'WST', 'WS$', NULL, NULL),
(129, 'Central African CFA', 'XAF', 'FCFA', NULL, NULL),
(130, 'East Caribbean Dollar', 'XCD', '$', NULL, NULL),
(131, 'West African CFA', 'XOF', 'CFA', NULL, NULL),
(132, 'CFP franc', 'XPF', '₣', NULL, NULL),
(133, 'Yemeni Rial', 'YER', '﷼', NULL, NULL),
(134, 'South African Rand', 'ZAR', 'R', NULL, NULL),
(135, 'Zambian Kwacha', 'ZMW', 'ZK', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `delivery_addresses`
--

CREATE TABLE `delivery_addresses` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '552425912',
  `user_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `pincode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `state` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `landmark` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_default` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `documents`
--

CREATE TABLE `documents` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1902438665',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'jpg',
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/document.jpg',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_required` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faqs`
--

CREATE TABLE `faqs` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '106473068',
  `question` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fav_users`
--

CREATE TABLE `fav_users` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1652022756',
  `user_id` int NOT NULL,
  `fav_user_id` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int NOT NULL COMMENT 'login user id - content_creators,users',
  `follower_id` int NOT NULL COMMENT 'fallowers id of content_creators',
  `status` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hashtags`
--

CREATE TABLE `hashtags` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '477031071',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `count` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_videos`
--

CREATE TABLE `live_videos` (
  `id` int UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '240382621',
  `user_id` int NOT NULL,
  `agora_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10e995841',
  `virtual_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10e995853',
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'public' COMMENT 'Public, Private',
  `broadcast_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'broadcast',
  `payment_status` int NOT NULL DEFAULT '0' COMMENT '0 - No, 1 - Yes',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `browser_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Store Streamer Browser Name',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `is_streaming` int NOT NULL DEFAULT '0',
  `snapshot` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/images/live-streaming.jpeg',
  `video_url` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `viewer_cnt` int NOT NULL DEFAULT '0',
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `no_of_minutes` int NOT NULL DEFAULT '0',
  `port_no` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_video_chat_messages`
--

CREATE TABLE `live_video_chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1470318175',
  `from_user_id` int NOT NULL,
  `live_video_id` int NOT NULL DEFAULT '0',
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `live_video_payments`
--

CREATE TABLE `live_video_payments` (
  `id` int UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1296512895',
  `live_video_id` int NOT NULL,
  `user_id` int NOT NULL,
  `live_video_viewer_id` int NOT NULL,
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `live_video_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `admin_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `user_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `status` tinyint NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2019_01_26_221915_create_coinpayment_transactions_table', 1),
(3, '2019_08_19_000000_create_failed_jobs_table', 1),
(4, '2020_08_18_122654_create_jobs_table', 1),
(5, '2020_11_30_030150_create_coinpayment_transaction_items_table', 1),
(6, '2021_09_15_151905_auth_related_migrations', 1),
(7, '2021_09_15_170052_add_lookup_related_migrations', 1),
(8, '2021_09_15_170958_add_post_related_migrations', 1),
(9, '2021_09_15_172623_add_products_related_migrations', 1),
(10, '2021_09_15_173759_add_streaming_related_migrations', 1),
(11, '2021_09_30_062250_add_v3_related_migrations', 1),
(12, '2021_11_23_102450_add_message_to_call_requests_table', 1),
(13, '2021_12_16_064400_add_browser_type_to_user_login_sessions_table', 1),
(14, '2022_01_07_104511_add_is_featured_to_stories_table', 1),
(15, '2022_01_17_073618_add_token_related_fileds_to_payment_tables', 1),
(16, '2022_01_25_055812_add_token_related_fields_to_user_login_sessions', 1),
(17, '2022_02_02_073521_add_v6_related_migrations', 1),
(18, '2022_04_09_110622_add_v6_additional_fields', 1),
(19, '2022_05_09_125925_add_laravel8_changes', 1),
(20, '2022_05_14_051518_add_call_charge_token_related_migrations', 1),
(21, '2022_05_26_105728_create_product_categories_table', 1),
(22, '2022_06_27_110854_add_tips_type_field_to_user_tips_table', 1),
(23, '2022_07_19_102008_create_currencies_table', 1),
(24, '2022_07_25_131007_add_device_unique_id_field_to_user_login_sessions_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '866066238',
  `user_id` int NOT NULL,
  `delivery_address_id` int NOT NULL,
  `total_products` int NOT NULL DEFAULT '0',
  `sub_total` double(8,2) NOT NULL DEFAULT '0.00',
  `tax_price` double(8,2) NOT NULL DEFAULT '0.00',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `total` double(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_payments`
--

CREATE TABLE `order_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1874200255',
  `user_id` int NOT NULL,
  `order_id` int NOT NULL,
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `delivery_price` double(8,2) NOT NULL DEFAULT '0.00',
  `sub_total` double(8,2) NOT NULL DEFAULT '0.00',
  `tax_price` double(8,2) NOT NULL DEFAULT '0.00',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `total` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_date` datetime DEFAULT NULL,
  `is_failed` tinyint NOT NULL DEFAULT '0',
  `failed_reason` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `order_products`
--

CREATE TABLE `order_products` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '181430835',
  `user_id` int NOT NULL,
  `order_id` int NOT NULL,
  `user_product_id` int NOT NULL,
  `quantity` double(8,2) NOT NULL DEFAULT '0.00',
  `per_quantity_price` double(8,2) NOT NULL DEFAULT '0.00',
  `sub_total` double(8,2) NOT NULL DEFAULT '0.00',
  `tax_price` double(8,2) NOT NULL DEFAULT '0.00',
  `delivery_price` double(8,2) NOT NULL DEFAULT '0.00',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `total` double(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `page_counters`
--

CREATE TABLE `page_counters` (
  `id` bigint UNSIGNED NOT NULL,
  `page` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `count` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '805974099',
  `user_id` int NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci,
  `publish_time` datetime DEFAULT NULL,
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `is_paid_post` tinyint NOT NULL DEFAULT '0',
  `is_published` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_albums`
--

CREATE TABLE `post_albums` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1725812553',
  `user_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_album_files`
--

CREATE TABLE `post_album_files` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '921970356',
  `post_id` int NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_bookmarks`
--

CREATE TABLE `post_bookmarks` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '811074959',
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_comments`
--

CREATE TABLE `post_comments` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '851948743',
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_comment_likes`
--

CREATE TABLE `post_comment_likes` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1208391406',
  `user_id` int NOT NULL,
  `post_comment_id` int NOT NULL DEFAULT '0',
  `post_comment_reply_id` int NOT NULL DEFAULT '0',
  `post_user_id` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_comment_replies`
--

CREATE TABLE `post_comment_replies` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1622247631',
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `post_comment_id` int NOT NULL,
  `reply` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_files`
--

CREATE TABLE `post_files` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '797268211',
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blur_file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `preview_file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `video_preview_file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_hashtags`
--

CREATE TABLE `post_hashtags` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '15498421',
  `user_id` int NOT NULL DEFAULT '0',
  `post_id` int NOT NULL DEFAULT '0',
  `hashtag_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_likes`
--

CREATE TABLE `post_likes` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1441889467',
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `post_user_id` int NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_payments`
--

CREATE TABLE `post_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1307499933',
  `user_id` int NOT NULL,
  `post_id` int NOT NULL,
  `user_card_id` int NOT NULL DEFAULT '0',
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `promo_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `promo_code_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `is_promo_code_applied` tinyint NOT NULL DEFAULT '0',
  `promo_code_reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_date` datetime DEFAULT NULL,
  `is_failed` tinyint NOT NULL DEFAULT '0',
  `failed_reason` tinyint NOT NULL DEFAULT '0',
  `admin_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `user_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `trans_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_categories`
--

CREATE TABLE `product_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1658568929',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/cat-placeholder.jpeg',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_inventories`
--

CREATE TABLE `product_inventories` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1944197119',
  `user_product_id` int NOT NULL,
  `total_quantity` double(8,2) NOT NULL DEFAULT '0.00',
  `remaining_quantity` double(8,2) NOT NULL DEFAULT '0.00',
  `onhold_quantity` double(8,2) NOT NULL DEFAULT '0.00',
  `used_quantity` double(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `product_sub_categories`
--

CREATE TABLE `product_sub_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '2112462916',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `product_category_id` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/cat-placeholder.jpeg',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `promo_codes`
--

CREATE TABLE `promo_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10e128db0',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `promo_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '0',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `start_date` datetime DEFAULT NULL,
  `expiry_date` datetime DEFAULT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `no_of_users_limit` smallint DEFAULT NULL,
  `per_users_limit` tinyint DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `referral_codes`
--

CREATE TABLE `referral_codes` (
  `id` int UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '327228743',
  `user_id` int NOT NULL,
  `referral_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `total_referrals` int NOT NULL DEFAULT '0',
  `referral_earnings` double(8,2) NOT NULL DEFAULT '0.00' COMMENT 'Using the current user code, if someone joined means the current user will get this earnings',
  `referee_earnings` double(8,2) NOT NULL DEFAULT '0.00' COMMENT 'if the current user joined using someother user referral code means the current user will get some earnings',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_posts`
--

CREATE TABLE `report_posts` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '216048930',
  `post_id` int NOT NULL,
  `block_by` int NOT NULL,
  `report_reason_id` int NOT NULL DEFAULT '0',
  `reason` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `report_reasons`
--

CREATE TABLE `report_reasons` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1577093972',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int UNSIGNED NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `key`, `value`, `status`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Freak.Fan', 1, NULL, NULL),
(2, 'frontend_url', '', 1, NULL, NULL),
(3, 'tag_name', '', 1, NULL, NULL),
(4, 'site_logo', '/logo.png', 1, NULL, NULL),
(5, 'site_icon', '/favicon.png', 1, NULL, NULL),
(6, 'version', 'v1.0.0', 1, NULL, NULL),
(7, 'default_lang', 'en', 1, NULL, NULL),
(8, 'currency', '$', 1, NULL, NULL),
(9, 'currency_code', 'usd', 1, NULL, NULL),
(10, 'tax_percentage', '10', 1, NULL, NULL),
(11, 'admin_take_count', '12', 1, NULL, NULL),
(12, 'is_demo_control_enabled', '0', 1, NULL, NULL),
(13, 'is_account_email_verification', '0', 1, NULL, NULL),
(14, 'is_email_notification', '1', 1, NULL, NULL),
(15, 'is_email_configured', '1', 1, NULL, NULL),
(16, 'is_push_notification', '1', 1, NULL, NULL),
(17, 'chat_socket_url', '', 1, NULL, NULL),
(18, 'MAILGUN_PUBLIC_KEY', '', 1, NULL, NULL),
(19, 'MAILGUN_PRIVATE_KEY', '', 1, NULL, NULL),
(20, 'stripe_publishable_key', 'pk_test_uDYrTXzzAuGRwDYtu7dkhaF3', 1, NULL, NULL),
(21, 'stripe_secret_key', 'sk_test_lRUbYflDyRP3L2UbnsehTUHW', 1, NULL, NULL),
(22, 'stripe_mode', 'sandbox', 1, NULL, NULL),
(23, 'token_expiry_hour', '10000000', 1, NULL, NULL),
(24, 'copyright_content', 'Copyrights 2021. All rights reserved.', 1, NULL, NULL),
(25, 'contact_email', '', 1, NULL, NULL),
(26, 'contact_address', '', 1, NULL, NULL),
(27, 'contact_mobile', '', 1, NULL, NULL),
(28, 'google_analytics', '', 1, NULL, NULL),
(29, 'header_scripts', '', 1, NULL, NULL),
(30, 'body_scripts', '', 1, NULL, NULL),
(31, 'appstore_user', '', 1, NULL, NULL),
(32, 'playstore_user', '', 1, NULL, NULL),
(33, 'playstore_stardom', '', 1, NULL, NULL),
(34, 'facebook_link', '', 1, NULL, NULL),
(35, 'linkedin_link', '', 1, NULL, NULL),
(36, 'twitter_link', '', 1, NULL, NULL),
(37, 'pinterest_link', '', 1, NULL, NULL),
(38, 'instagram_link', '', 1, NULL, NULL),
(39, 'demo_admin_email', '', 1, NULL, NULL),
(40, 'demo_admin_password', '', 1, NULL, NULL),
(41, 'demo_user_email', '', 1, NULL, NULL),
(42, 'demo_user_password', '', 1, NULL, NULL),
(43, 'meta_title', '', 1, NULL, NULL),
(44, 'meta_description', '', 1, NULL, NULL),
(45, 'meta_author', '', 1, NULL, NULL),
(46, 'meta_keywords', '', 1, NULL, NULL),
(47, 'user_fcm_sender_id', '865212328189', 1, NULL, NULL),
(48, 'user_fcm_server_key', '', 1, NULL, NULL),
(49, 'demo_support_member_email', '', 1, NULL, NULL),
(50, 'demo_support_member_password', '', 1, NULL, NULL),
(51, 'admin_commission', '20', 1, NULL, NULL),
(52, 'post_video_placeholder', 'https://freak.fan/images/post_video_placeholder.jpg', 1, NULL, NULL),
(53, 'MAILGUN_PUBLIC_KEY', '', 1, NULL, NULL),
(54, 'is_verified_badge_enabled', '0', 1, NULL, NULL),
(55, 's3_bucket', '0', 1, NULL, NULL),
(56, 'is_user_active_status', '1', 1, NULL, NULL),
(57, 'frontend_no_data_image', 'https://freak.fan/images/no-data-found.svg', 1, NULL, NULL),
(58, 'is_mailgun_email_validate', '0', 1, NULL, NULL),
(59, 'user_online_status_limit', '0', 1, NULL, NULL),
(60, 'FB_CLIENT_ID', '', 1, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(61, 'FB_CLIENT_SECRET', '', 1, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(62, 'FB_CALL_BACK', '', 1, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(63, 'TWITTER_CLIENT_ID', '', 1, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(64, 'TWITTER_CLIENT_SECRET', '', 1, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(65, 'TWITTER_CALL_BACK', '', 1, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(66, 'GOOGLE_CLIENT_ID', '', 1, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(67, 'GOOGLE_CLIENT_SECRET', '', 1, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(68, 'GOOGLE_CALL_BACK', '', 1, '2022-12-28 04:19:06', '2022-12-28 04:19:06'),
(69, 'BN_USER_FOLLOWINGS', 'fans', 1, NULL, NULL),
(70, 'BN_USER_COMMENT', 'post/', 1, NULL, NULL),
(71, 'BN_USER_LIKE', 'post/', 1, NULL, NULL),
(72, 'BN_USER_TIPS', 'payments', 1, NULL, NULL),
(73, 'BN_CHAT_MESSAGE', 'inbox', 1, NULL, NULL),
(74, 'BN_LIVE_VIDEO', 'live-videos', 1, NULL, NULL),
(75, 'BN_USER_VIDEO_CALL', 'video-calls-history', 1, NULL, NULL),
(76, 'BN_USER_AUDIO_CALL', 'audio-calls-history', 1, NULL, NULL),
(77, 'tips_admin_commission', '10', 1, NULL, NULL),
(78, 'subscription_admin_commission', '10', 1, NULL, NULL),
(79, 'is_notification_count_enabled', '0', 1, NULL, NULL),
(80, 'notification_time', '', 1, NULL, NULL),
(81, 'is_paypal_enabled', '1', 1, NULL, NULL),
(82, 'PAYPAL_ID', 'AaXkweZD5g9s0X3BsO0Y4Q-kNzbmLZaog0mbmVGrTT5IX0O73LoLVcHp17e6pkG7Vm04JEUuG6up30LD', 1, NULL, NULL),
(83, 'PAYPAL_SECRET', '', 1, NULL, NULL),
(84, 'PAYPAL_MODE', 'sandbox', 1, NULL, NULL),
(85, 'is_user_allowed_verified_badge', '0', 1, NULL, NULL),
(86, 'verified_badge_file', 'https://freak.fan/images/verified.svg', 1, NULL, NULL),
(87, 'verified_badge_text', 'Verified', 1, NULL, NULL),
(88, 'is_multilanguage_enabled', '1', 1, NULL, NULL),
(89, 'is_welcome_steps', '1', 1, NULL, NULL),
(90, 'video_call_admin_commission', '10', 1, NULL, NULL),
(91, 'video_call_start_plus_minus', '10', 1, NULL, NULL),
(92, 'is_one_to_one_call_enabled', '1', 1, NULL, NULL),
(93, 'is_one_to_many_call_enabled', '1', 1, NULL, NULL),
(94, 'audio_call_admin_commission', '10', 1, NULL, NULL),
(95, 'audio_call_start_plus_minus', '10', 1, NULL, NULL),
(96, 'is_watermark_logo_enabled', '0', 1, NULL, NULL),
(97, 'watermark_logo', 'https://freak.fan/images/watermark.png', 1, NULL, NULL),
(98, 'watermark_position', 'top-left', 1, NULL, NULL),
(99, 'live_streaming_placeholder_img', 'https://freak.fan/images/livestreaming_placeholder.jpg', 1, NULL, NULL),
(100, 'live_streaming_admin_commission', '0', 1, NULL, NULL),
(101, 'agora_app_id', '', 1, NULL, NULL),
(102, 'agora_certificate_id', '', 1, NULL, NULL),
(103, 'is_agora_configured', '0', 1, NULL, NULL),
(104, 'snapchat_link', '', 1, NULL, NULL),
(105, 'youtube_link', '', 1, NULL, NULL),
(106, 'google_plus_link', '', 1, NULL, NULL),
(107, 'is_coinpayment_enabled', '0', 1, NULL, NULL),
(108, 'referral_earnings', '10', 1, NULL, NULL),
(109, 'referrer_earnings', '10', 1, NULL, NULL),
(110, 'is_referral_enabled', '1', 1, NULL, NULL),
(111, 'is_chat_asset_enabled', '1', 1, NULL, NULL),
(112, 'profile_placeholder', 'https://freak.fan/placeholder.jpeg', 1, NULL, NULL),
(113, 'cover_placeholder', 'https://freak.fan/cover.jpg', 1, NULL, NULL),
(114, 'post_image_placeholder', 'https://freak.fan/images/post_image_placeholder.jpg', 1, NULL, NULL),
(115, 'video_call_placeholder', 'https://freak.fan/images/video_call_placeholder.jpg', 1, NULL, NULL),
(116, 'audio_call_placeholder', 'https://freak.fan/images/audio_call_placeholder.jpg', 1, NULL, NULL),
(117, 'ppv_image_placeholder', 'https://freak.fan/images/ppv_image_placeholder.jpg', 1, NULL, NULL),
(118, 'ppv_audio_placeholder', 'https://freak.fan/images/ppv_audio_placeholder.jpg', 1, NULL, NULL),
(119, 'ppv_video_placeholder', 'https://freak.fan/images/ppv_video_placeholder.jpg', 1, NULL, NULL),
(120, 'is_stripe_enabled', '1', 1, NULL, NULL),
(121, 'is_ads_enabled', '0', 1, NULL, NULL),
(122, 'header_ad', '', 1, NULL, NULL),
(123, 'footer_ad', '', 1, NULL, NULL),
(124, 'sidebar_ad', '', 1, NULL, NULL),
(125, 'is_ccbill_enabled', '0', 1, NULL, NULL),
(126, 'ccbill_url', '', 1, NULL, NULL),
(127, 'ccbill_account_number', '', 1, NULL, NULL),
(128, 'ccbill_sub_account_number', '', 1, NULL, NULL),
(129, 'flex_form_id', '', 1, NULL, NULL),
(130, 'salt_key', '', 1, NULL, NULL),
(131, 'buy_single_user_products', '0', 1, NULL, NULL),
(132, 'NOCAPTCHA_SECRET_KEY', '', 1, NULL, NULL),
(133, 'NOCAPTCHA_SITE_KEY', '', 1, NULL, NULL),
(134, 'is_captcha_enabled', '1', 1, NULL, NULL),
(135, 'is_only_wallet_payment', '1', 1, NULL, NULL),
(136, 'is_wallet_payment_enabled', '1', 1, NULL, NULL),
(137, 'token_symbol', 'Token', 1, NULL, NULL),
(138, 'token_amount', '1', 1, NULL, NULL),
(139, 'tip_min_token', '1', 1, NULL, NULL),
(140, 'tip_max_token', '10000', 1, NULL, NULL),
(141, 'symbol_position', 'suffix', 1, NULL, NULL),
(142, 'min_token_call_charge', '10', 1, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `static_pages`
--

CREATE TABLE `static_pages` (
  `id` int UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10e1aa769',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('about','privacy','terms','refund','cancellation','faq','help','contact','others') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'others',
  `section_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `static_pages`
--

INSERT INTO `static_pages` (`id`, `unique_id`, `title`, `description`, `type`, `section_type`, `status`, `created_at`, `updated_at`) VALUES
(1, 'about', 'about', 'about', 'about', NULL, 1, '2022-12-28 04:19:07', '2022-12-28 04:19:07'),
(2, 'contact', 'contact', 'contact', 'contact', NULL, 1, '2022-12-28 04:19:07', '2022-12-28 04:19:07'),
(3, 'privacy', 'privacy', 'privacy', 'privacy', NULL, 1, '2022-12-28 04:19:07', '2022-12-28 04:19:07'),
(4, 'terms', 'terms', 'terms', 'terms', NULL, 1, '2022-12-28 04:19:07', '2022-12-28 04:19:07'),
(5, 'help', 'help', 'help', 'help', NULL, 1, '2022-12-28 04:19:07', '2022-12-28 04:19:07');

-- --------------------------------------------------------

--
-- Table structure for table `stories`
--

CREATE TABLE `stories` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '262045651',
  `user_id` int NOT NULL DEFAULT '0',
  `content` text COLLATE utf8mb4_unicode_ci,
  `publish_time` datetime DEFAULT NULL,
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `is_paid_story` tinyint NOT NULL DEFAULT '0',
  `is_published` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `story_files`
--

CREATE TABLE `story_files` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1468520685',
  `user_id` int NOT NULL DEFAULT '0',
  `story_id` int NOT NULL DEFAULT '0',
  `file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/placeholder.jpeg',
  `blur_file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/placeholder.jpeg',
  `file_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'image',
  `preview_file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/placeholder.jpeg',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1330537937',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `plan` int NOT NULL DEFAULT '1',
  `plan_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'months',
  `is_free` tinyint NOT NULL DEFAULT '0',
  `is_popular` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subscription_payments`
--

CREATE TABLE `subscription_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10dd0abb7',
  `subscription_id` int NOT NULL,
  `user_id` int NOT NULL,
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `is_current_subscription` int NOT NULL DEFAULT '0',
  `expiry_date` datetime DEFAULT NULL,
  `paid_date` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `is_cancelled` tinyint NOT NULL DEFAULT '0',
  `cancel_reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan` int NOT NULL DEFAULT '1',
  `plan_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'months',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sub_categories`
--

CREATE TABLE `sub_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '756146488',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/cat-placeholder.jpeg',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_chats`
--

CREATE TABLE `support_chats` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '209496281',
  `user_id` int NOT NULL,
  `support_member_id` int NOT NULL DEFAULT '0',
  `support_ticket_id` int NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'su, us',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_members`
--

CREATE TABLE `support_members` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '954630614',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `middle_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `about` text COLLATE utf8mb4_unicode_ci,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/placeholder.jpeg',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_expiry` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('web','android','ios') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `is_email_verified` int NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `support_tickets`
--

CREATE TABLE `support_tickets` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1583045604',
  `user_id` int NOT NULL,
  `support_member_id` int NOT NULL DEFAULT '0',
  `subject` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '141905139',
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `middle_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `about` text COLLATE utf8mb4_unicode_ci,
  `gender` enum('male','female','others','rather-not-select') COLLATE utf8mb4_unicode_ci DEFAULT 'rather-not-select',
  `cover` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/cover.jpg',
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/placeholder.jpeg',
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `website` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `amazon_wishlist` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `user_type` tinyint NOT NULL DEFAULT '0',
  `user_account_type` tinyint NOT NULL DEFAULT '0',
  `is_document_verified` tinyint NOT NULL DEFAULT '0',
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_expiry` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `device_token` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `device_type` enum('web','android','ios') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `login_by` enum('manual','facebook','google','instagram','apple','linkedin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual',
  `social_unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `registration_steps` tinyint NOT NULL DEFAULT '0',
  `is_push_notification` tinyint NOT NULL DEFAULT '1',
  `is_email_notification` tinyint NOT NULL DEFAULT '1',
  `user_card_id` int NOT NULL DEFAULT '0',
  `is_email_verified` tinyint NOT NULL DEFAULT '0',
  `verification_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `verification_code_expiry` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `is_online_status` tinyint NOT NULL DEFAULT '1',
  `default_payment_method` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'WALLET',
  `status` tinyint NOT NULL DEFAULT '1',
  `one_time_subscription` tinyint NOT NULL DEFAULT '0' COMMENT '0 - Not Subscribed , 1 - Subscribed',
  `amount_paid` double(8,2) NOT NULL DEFAULT '0.00',
  `expiry_date` datetime DEFAULT NULL,
  `no_of_days` tinyint NOT NULL DEFAULT '0',
  `video_call_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `video_call_token` double(8,2) NOT NULL DEFAULT '0.00',
  `audio_call_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `audio_call_token` double(8,2) NOT NULL DEFAULT '0.00',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `eyes_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `height` int NOT NULL DEFAULT '0' COMMENT 'Height in cm',
  `weight` int NOT NULL DEFAULT '0' COMMENT 'Weight in pounds',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `is_two_step_auth_enabled` tinyint NOT NULL DEFAULT '0',
  `is_content_creator` tinyint NOT NULL DEFAULT '1',
  `content_creator_step` tinyint NOT NULL DEFAULT '0',
  `is_verified_badge` tinyint NOT NULL DEFAULT '0',
  `instagram_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `snapchat_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `facebook_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `twitter_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `linkedin_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `pinterest_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `youtube_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `twitch_link` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `timezone` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ios_theme` tinyint NOT NULL DEFAULT '0',
  `latitude` double(15,8) NOT NULL DEFAULT '0.00000000',
  `longitude` double(15,8) NOT NULL DEFAULT '0.00000000',
  `featured_story` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `unique_id`, `name`, `first_name`, `middle_name`, `last_name`, `username`, `email`, `about`, `gender`, `cover`, `picture`, `password`, `mobile`, `address`, `website`, `amazon_wishlist`, `user_type`, `user_account_type`, `is_document_verified`, `payment_mode`, `token`, `token_expiry`, `device_token`, `device_type`, `login_by`, `social_unique_id`, `registration_steps`, `is_push_notification`, `is_email_notification`, `user_card_id`, `is_email_verified`, `verification_code`, `verification_code_expiry`, `email_verified_at`, `is_online_status`, `default_payment_method`, `status`, `one_time_subscription`, `amount_paid`, `expiry_date`, `no_of_days`, `video_call_amount`, `video_call_token`, `audio_call_amount`, `audio_call_token`, `remember_token`, `eyes_color`, `height`, `weight`, `created_at`, `updated_at`, `is_two_step_auth_enabled`, `is_content_creator`, `content_creator_step`, `is_verified_badge`, `instagram_link`, `snapchat_link`, `facebook_link`, `twitter_link`, `linkedin_link`, `pinterest_link`, `youtube_link`, `twitch_link`, `timezone`, `ios_theme`, `latitude`, `longitude`, `featured_story`) VALUES
(1, 'user-demo', 'User', 'User', '', 'Demo', 'user-demo', 'demo@demo.com', NULL, 'rather-not-select', 'https://freak.fan/cover.jpg', 'https://freak.fan/placeholder.jpeg', '$2y$10$nW1rwD6xagNqIOxCgpdq.O8z/IRD/tNA0.W5NYp/fpAuWFBO6o9DS', '9836367763', '', '', '', 0, 0, 0, 'CARD', '2y1043t0qHZr956vKbBcdAZnugdZOmkeNTBQtCOGAKIzYPyQzO9NtNMy', '37672220947', NULL, 'web', 'manual', '', 0, 1, 1, 0, 1, '', '', NULL, 1, 'WALLET', 1, 0, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 0, 0, '2022-12-28 04:19:07', '2022-12-28 04:19:07', 0, 1, 0, 0, '', '', '', '', '', '', '', '', '', 0, 0.00000000, 0.00000000, ''),
(2, 'user-test', 'Test', 'User', '', 'Test', 'user-test', 'test@demo.com', NULL, 'rather-not-select', 'https://freak.fan/cover.jpg', 'https://freak.fan/placeholder.jpeg', '$2y$10$HZqQFDYbEKuZ7/m0Q0t6nOFpiuiOF/8.XXHND.rFt4i..ZcJfvPWi', '9836367763', '', '', '', 0, 0, 0, 'CARD', '2y104O212CfZupNQ8zXBYfo3eoNxICiX4HSdr5N1K5vuNZZJmQ0S', '37672220947', NULL, 'web', 'manual', '', 0, 1, 1, 0, 1, '', '', NULL, 1, 'WALLET', 1, 0, 0.00, NULL, 0, 0.00, 0.00, 0.00, 0.00, NULL, NULL, 0, 0, '2022-12-28 04:19:07', '2022-12-28 04:19:07', 0, 1, 0, 0, '', '', '', '', '', '', '', '', '', 0, 0.00000000, 0.00000000, '');

-- --------------------------------------------------------

--
-- Table structure for table `user_billing_accounts`
--

CREATE TABLE `user_billing_accounts` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int NOT NULL,
  `nickname` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `bank_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `account_holder_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ifsc_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `swift_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bank_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'savings',
  `is_default` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `iban_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `route_number` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_cards`
--

CREATE TABLE `user_cards` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10dd4157c',
  `user_id` int NOT NULL,
  `card_holder_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `card_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_four` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `card_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_documents`
--

CREATE TABLE `user_documents` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1737333424',
  `user_id` int NOT NULL,
  `document_id` int NOT NULL,
  `document_file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `document_file_front` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `document_file_back` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_verified` tinyint NOT NULL DEFAULT '0' COMMENT '0 - pending, 1 - approved, 2 - declined',
  `uploaded_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user' COMMENT 'user | admin',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_login_sessions`
--

CREATE TABLE `user_login_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1724567871',
  `user_id` int NOT NULL DEFAULT '0',
  `device_type` enum('web','android','ios') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `device_token` text COLLATE utf8mb4_unicode_ci,
  `device_model` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `device_unique_id` text COLLATE utf8mb4_unicode_ci,
  `browser_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_current_session` tinyint NOT NULL DEFAULT '1',
  `last_session` datetime NOT NULL,
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token_expiry` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_products`
--

CREATE TABLE `user_products` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '432924689',
  `user_id` int NOT NULL,
  `product_category_id` int NOT NULL,
  `product_sub_category_id` int NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'https://freak.fan/product-placeholder.jpeg',
  `quantity` double(8,2) NOT NULL DEFAULT '0.00',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `price` double(8,2) NOT NULL DEFAULT '0.00',
  `delivery_price` double(8,2) NOT NULL DEFAULT '0.00',
  `is_outofstock` tinyint NOT NULL DEFAULT '1',
  `is_visible` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_product_pictures`
--

CREATE TABLE `user_product_pictures` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1981269335',
  `user_product_id` int NOT NULL,
  `picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_promo_codes`
--

CREATE TABLE `user_promo_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` int NOT NULL DEFAULT '0',
  `promo_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_times_used` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_referrals`
--

CREATE TABLE `user_referrals` (
  `id` int UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1555976800',
  `user_id` int NOT NULL,
  `parent_user_id` int NOT NULL,
  `referral_code_id` int NOT NULL,
  `referral_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `device_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'web',
  `status` int NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_subscriptions`
--

CREATE TABLE `user_subscriptions` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '263866009',
  `user_id` int NOT NULL,
  `monthly_amount` double(8,2) NOT NULL DEFAULT '1.00',
  `yearly_amount` double(8,2) NOT NULL DEFAULT '10.00',
  `monthly_token` double(8,2) NOT NULL DEFAULT '0.00',
  `yearly_token` double(8,2) NOT NULL DEFAULT '0.00',
  `vod_amount` double(8,2) NOT NULL DEFAULT '1.00',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_subscription_payments`
--

CREATE TABLE `user_subscription_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10de7d354',
  `user_subscription_id` int NOT NULL,
  `from_user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `promo_code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `promo_code_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `is_promo_code_applied` tinyint NOT NULL DEFAULT '0',
  `promo_code_reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `is_current_subscription` int NOT NULL DEFAULT '0',
  `expiry_date` datetime DEFAULT NULL,
  `paid_date` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `is_cancelled` tinyint NOT NULL DEFAULT '0',
  `cancel_reason` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan` int NOT NULL DEFAULT '1',
  `plan_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'months',
  `admin_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `user_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `trans_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_tips`
--

CREATE TABLE `user_tips` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1260711763',
  `user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `post_id` int NOT NULL DEFAULT '0',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `tips_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'profile',
  `message` text COLLATE utf8mb4_unicode_ci,
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_card_id` int NOT NULL DEFAULT '0',
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `admin_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `user_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_date` datetime DEFAULT NULL,
  `is_failed` tinyint NOT NULL DEFAULT '0',
  `failed_reason` tinyint NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `user_wallet_payment_id` int NOT NULL DEFAULT '0',
  `trans_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_wallets`
--

CREATE TABLE `user_wallets` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '151178907',
  `user_id` int NOT NULL,
  `total` double(8,2) NOT NULL DEFAULT '0.00',
  `onhold` double(8,2) NOT NULL DEFAULT '0.00',
  `used` double(8,2) NOT NULL DEFAULT '0.00',
  `remaining` double(8,2) NOT NULL DEFAULT '0.00',
  `referral_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `referral_token` double(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_wallet_payments`
--

CREATE TABLE `user_wallet_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '991951507',
  `user_id` int NOT NULL,
  `to_user_id` int NOT NULL DEFAULT '0',
  `received_from_user_id` int NOT NULL DEFAULT '0',
  `generated_invoice_id` int NOT NULL DEFAULT '0',
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'add' COMMENT 'add, paid, credit',
  `amount_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'add' COMMENT 'add, minus',
  `usage_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `requested_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `admin_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `user_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `paid_date` datetime DEFAULT NULL,
  `message` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_cancelled` int NOT NULL DEFAULT '0',
  `cancelled_reason` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `updated_by` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user' COMMENT 'admin, user',
  `bank_statement_picture` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_admin_approved` tinyint NOT NULL DEFAULT '0',
  `user_billing_account_id` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_withdrawals`
--

CREATE TABLE `user_withdrawals` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1353215518',
  `user_id` int NOT NULL,
  `user_wallet_payment_id` int NOT NULL DEFAULT '0',
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'OFFLINE',
  `requested_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `requested_token` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `cancel_reason` text COLLATE utf8mb4_unicode_ci,
  `user_billing_account_id` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '0' COMMENT '0 - pending, 1 - paid, 2 - rejected',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vc_chat_messages`
--

CREATE TABLE `vc_chat_messages` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1232329640',
  `video_call_request_id` int NOT NULL,
  `model_id` int NOT NULL,
  `user_id` int NOT NULL,
  `message` text COLLATE utf8mb4_unicode_ci,
  `status` tinyint NOT NULL DEFAULT '0',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `verification_codes`
--

CREATE TABLE `verification_codes` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '374045978',
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `username` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_call_payments`
--

CREATE TABLE `video_call_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `model_id` int NOT NULL,
  `user_id` int NOT NULL,
  `video_call_request_id` int NOT NULL,
  `user_card_id` int NOT NULL DEFAULT '0',
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `currency` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '$',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `paid_date` datetime DEFAULT NULL,
  `is_failed` tinyint NOT NULL DEFAULT '0',
  `failed_reason` tinyint NOT NULL DEFAULT '0',
  `admin_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `user_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `status` tinyint NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00',
  `total_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `video_call_requests`
--

CREATE TABLE `video_call_requests` (
  `id` int UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1186975422',
  `user_id` int NOT NULL,
  `model_id` int NOT NULL,
  `call_status` int NOT NULL DEFAULT '0',
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `message` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `agora_token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10ed30a70',
  `virtual_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10ed30a77',
  `total_time` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `viewers`
--

CREATE TABLE `viewers` (
  `id` int UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1117648652',
  `live_video_id` int NOT NULL,
  `user_id` int NOT NULL,
  `count` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vod_categories`
--

CREATE TABLE `vod_categories` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1561572425',
  `vod_video_id` int NOT NULL DEFAULT '0',
  `category_id` int NOT NULL DEFAULT '0',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vod_payments`
--

CREATE TABLE `vod_payments` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '63ac10eaf3cd9',
  `from_user_id` int NOT NULL,
  `to_user_id` int NOT NULL,
  `payment_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `payment_mode` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CARD',
  `expiry_date` datetime DEFAULT NULL,
  `paid_date` datetime DEFAULT NULL,
  `status` tinyint NOT NULL DEFAULT '1',
  `admin_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `user_amount` double(8,2) NOT NULL DEFAULT '0.00',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `admin_token` double(8,2) NOT NULL DEFAULT '0.00',
  `user_token` double(8,2) NOT NULL DEFAULT '0.00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vod_videos`
--

CREATE TABLE `vod_videos` (
  `id` bigint UNSIGNED NOT NULL,
  `unique_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '287671624',
  `user_id` int NOT NULL DEFAULT '0',
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` text COLLATE utf8mb4_unicode_ci,
  `publish_time` datetime DEFAULT NULL,
  `token` double(8,2) NOT NULL DEFAULT '0.00',
  `amount` double(8,2) NOT NULL DEFAULT '0.00',
  `preview_file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `blur_file` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `is_paid_vod` tinyint NOT NULL DEFAULT '0',
  `is_published` tinyint NOT NULL DEFAULT '1',
  `status` tinyint NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `admins_email_unique` (`email`);

--
-- Indexes for table `audio_call_payments`
--
ALTER TABLE `audio_call_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audio_call_requests`
--
ALTER TABLE `audio_call_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `audio_chat_messages`
--
ALTER TABLE `audio_chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bell_notifications`
--
ALTER TABLE `bell_notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `block_users`
--
ALTER TABLE `block_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `category_details`
--
ALTER TABLE `category_details`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_assets`
--
ALTER TABLE `chat_assets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_asset_payments`
--
ALTER TABLE `chat_asset_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_messages`
--
ALTER TABLE `chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `chat_users`
--
ALTER TABLE `chat_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `coinpayment_transactions`
--
ALTER TABLE `coinpayment_transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `coinpayment_transactions_uuid_unique` (`uuid`),
  ADD UNIQUE KEY `coinpayment_transactions_txn_id_unique` (`txn_id`),
  ADD UNIQUE KEY `coinpayment_transactions_order_id_unique` (`order_id`);

--
-- Indexes for table `coinpayment_transaction_items`
--
ALTER TABLE `coinpayment_transaction_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `currencies`
--
ALTER TABLE `currencies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `delivery_addresses`
--
ALTER TABLE `delivery_addresses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `documents`
--
ALTER TABLE `documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `faqs`
--
ALTER TABLE `faqs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fav_users`
--
ALTER TABLE `fav_users`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hashtags`
--
ALTER TABLE `hashtags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `live_videos`
--
ALTER TABLE `live_videos`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `live_video_chat_messages`
--
ALTER TABLE `live_video_chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `live_video_payments`
--
ALTER TABLE `live_video_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_payments`
--
ALTER TABLE `order_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `order_products`
--
ALTER TABLE `order_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `page_counters`
--
ALTER TABLE `page_counters`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_albums`
--
ALTER TABLE `post_albums`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_album_files`
--
ALTER TABLE `post_album_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_bookmarks`
--
ALTER TABLE `post_bookmarks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_comments`
--
ALTER TABLE `post_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_comment_likes`
--
ALTER TABLE `post_comment_likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_comment_replies`
--
ALTER TABLE `post_comment_replies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_files`
--
ALTER TABLE `post_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_hashtags`
--
ALTER TABLE `post_hashtags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `post_payments`
--
ALTER TABLE `post_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_categories`
--
ALTER TABLE `product_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_inventories`
--
ALTER TABLE `product_inventories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `product_sub_categories`
--
ALTER TABLE `product_sub_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `promo_codes`
--
ALTER TABLE `promo_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `promo_codes_promo_code_unique` (`promo_code`);

--
-- Indexes for table `referral_codes`
--
ALTER TABLE `referral_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report_posts`
--
ALTER TABLE `report_posts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `report_reasons`
--
ALTER TABLE `report_reasons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `static_pages`
--
ALTER TABLE `static_pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `static_pages_title_unique` (`title`);

--
-- Indexes for table `stories`
--
ALTER TABLE `stories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `story_files`
--
ALTER TABLE `story_files`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sub_categories`
--
ALTER TABLE `sub_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_chats`
--
ALTER TABLE `support_chats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `support_members`
--
ALTER TABLE `support_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `support_members_email_unique` (`email`),
  ADD UNIQUE KEY `support_members_username_unique` (`username`);

--
-- Indexes for table `support_tickets`
--
ALTER TABLE `support_tickets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_billing_accounts`
--
ALTER TABLE `user_billing_accounts`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_billing_accounts_unique_id_unique` (`unique_id`);

--
-- Indexes for table `user_cards`
--
ALTER TABLE `user_cards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_documents`
--
ALTER TABLE `user_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_login_sessions`
--
ALTER TABLE `user_login_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_products`
--
ALTER TABLE `user_products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_product_pictures`
--
ALTER TABLE `user_product_pictures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_promo_codes`
--
ALTER TABLE `user_promo_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_referrals`
--
ALTER TABLE `user_referrals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_subscription_payments`
--
ALTER TABLE `user_subscription_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_tips`
--
ALTER TABLE `user_tips`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_wallets`
--
ALTER TABLE `user_wallets`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_wallet_payments`
--
ALTER TABLE `user_wallet_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_withdrawals`
--
ALTER TABLE `user_withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vc_chat_messages`
--
ALTER TABLE `vc_chat_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `verification_codes`
--
ALTER TABLE `verification_codes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_call_payments`
--
ALTER TABLE `video_call_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `video_call_requests`
--
ALTER TABLE `video_call_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `viewers`
--
ALTER TABLE `viewers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vod_categories`
--
ALTER TABLE `vod_categories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vod_payments`
--
ALTER TABLE `vod_payments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vod_videos`
--
ALTER TABLE `vod_videos`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `audio_call_payments`
--
ALTER TABLE `audio_call_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audio_call_requests`
--
ALTER TABLE `audio_call_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audio_chat_messages`
--
ALTER TABLE `audio_chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bell_notifications`
--
ALTER TABLE `bell_notifications`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `block_users`
--
ALTER TABLE `block_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `category_details`
--
ALTER TABLE `category_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_assets`
--
ALTER TABLE `chat_assets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_asset_payments`
--
ALTER TABLE `chat_asset_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_messages`
--
ALTER TABLE `chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `chat_users`
--
ALTER TABLE `chat_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coinpayment_transactions`
--
ALTER TABLE `coinpayment_transactions`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `coinpayment_transaction_items`
--
ALTER TABLE `coinpayment_transaction_items`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `currencies`
--
ALTER TABLE `currencies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=136;

--
-- AUTO_INCREMENT for table `delivery_addresses`
--
ALTER TABLE `delivery_addresses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `documents`
--
ALTER TABLE `documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faqs`
--
ALTER TABLE `faqs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fav_users`
--
ALTER TABLE `fav_users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hashtags`
--
ALTER TABLE `hashtags`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_videos`
--
ALTER TABLE `live_videos`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_video_chat_messages`
--
ALTER TABLE `live_video_chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `live_video_payments`
--
ALTER TABLE `live_video_payments`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_payments`
--
ALTER TABLE `order_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `order_products`
--
ALTER TABLE `order_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `page_counters`
--
ALTER TABLE `page_counters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_albums`
--
ALTER TABLE `post_albums`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_album_files`
--
ALTER TABLE `post_album_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_bookmarks`
--
ALTER TABLE `post_bookmarks`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_comments`
--
ALTER TABLE `post_comments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_comment_likes`
--
ALTER TABLE `post_comment_likes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_comment_replies`
--
ALTER TABLE `post_comment_replies`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_files`
--
ALTER TABLE `post_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_hashtags`
--
ALTER TABLE `post_hashtags`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `post_payments`
--
ALTER TABLE `post_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_categories`
--
ALTER TABLE `product_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_inventories`
--
ALTER TABLE `product_inventories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `product_sub_categories`
--
ALTER TABLE `product_sub_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `promo_codes`
--
ALTER TABLE `promo_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `referral_codes`
--
ALTER TABLE `referral_codes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_posts`
--
ALTER TABLE `report_posts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `report_reasons`
--
ALTER TABLE `report_reasons`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=143;

--
-- AUTO_INCREMENT for table `static_pages`
--
ALTER TABLE `static_pages`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `stories`
--
ALTER TABLE `stories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `story_files`
--
ALTER TABLE `story_files`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subscription_payments`
--
ALTER TABLE `subscription_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sub_categories`
--
ALTER TABLE `sub_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_chats`
--
ALTER TABLE `support_chats`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_members`
--
ALTER TABLE `support_members`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `support_tickets`
--
ALTER TABLE `support_tickets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `user_billing_accounts`
--
ALTER TABLE `user_billing_accounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_cards`
--
ALTER TABLE `user_cards`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_documents`
--
ALTER TABLE `user_documents`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_login_sessions`
--
ALTER TABLE `user_login_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_products`
--
ALTER TABLE `user_products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_product_pictures`
--
ALTER TABLE `user_product_pictures`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_promo_codes`
--
ALTER TABLE `user_promo_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_referrals`
--
ALTER TABLE `user_referrals`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_subscriptions`
--
ALTER TABLE `user_subscriptions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_subscription_payments`
--
ALTER TABLE `user_subscription_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_tips`
--
ALTER TABLE `user_tips`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_wallets`
--
ALTER TABLE `user_wallets`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_wallet_payments`
--
ALTER TABLE `user_wallet_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_withdrawals`
--
ALTER TABLE `user_withdrawals`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vc_chat_messages`
--
ALTER TABLE `vc_chat_messages`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `verification_codes`
--
ALTER TABLE `verification_codes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_call_payments`
--
ALTER TABLE `video_call_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `video_call_requests`
--
ALTER TABLE `video_call_requests`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `viewers`
--
ALTER TABLE `viewers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vod_categories`
--
ALTER TABLE `vod_categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vod_payments`
--
ALTER TABLE `vod_payments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vod_videos`
--
ALTER TABLE `vod_videos`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
