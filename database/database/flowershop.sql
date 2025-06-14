-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 14, 2025 at 02:03 AM
-- Server version: 8.0.30
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `flowershop`
--

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `image_url`, `created_at`, `updated_at`) VALUES
(1, 'Hoa tặng mẹ', 'categories/oRA3Jeux5XRMTxpyTmSS75saghNTfpePTURSnzMW.jpg', '2025-06-13 08:30:41', '2025-06-13 08:30:41');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `value` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `flowers`
--

CREATE TABLE `flowers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `color` varchar(255) DEFAULT NULL,
  `flower_type_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `flowers`
--

INSERT INTO `flowers` (`id`, `name`, `price`, `color`, `flower_type_id`, `created_at`, `updated_at`) VALUES
(1, 'Hoa cúc', '15000.00', 'vàng', 1, '2025-06-13 08:31:08', '2025-06-13 08:31:08'),
(2, 'Hoa hồng', '10000.00', 'đỏ', 1, '2025-06-13 08:31:25', '2025-06-13 08:31:25');

-- --------------------------------------------------------

--
-- Table structure for table `flower_types`
--

CREATE TABLE `flower_types` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `flower_types`
--

INSERT INTO `flower_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Hoa ngày Tết', '2025-06-13 08:30:52', '2025-06-13 08:30:52');

-- --------------------------------------------------------

--
-- Table structure for table `import_receipts`
--

CREATE TABLE `import_receipts` (
  `id` bigint UNSIGNED NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `import_date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `import_receipts`
--

INSERT INTO `import_receipts` (`id`, `note`, `import_date`, `total_price`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'Nhập lô hoa hồng', '2025-06-13', '1600000.00', NULL, '2025-06-13 08:31:38', '2025-06-13 08:31:38'),
(2, 'Nhập lô hoa hồng', '2025-06-13', '16000000.00', NULL, '2025-06-13 09:00:45', '2025-06-13 09:00:45'),
(3, 'Nhập lô hoa hồng', '2025-06-09', '18000000.00', NULL, '2025-06-13 10:00:08', '2025-06-13 10:00:08');

-- --------------------------------------------------------

--
-- Table structure for table `import_receipt_details`
--

CREATE TABLE `import_receipt_details` (
  `id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `import_price` decimal(10,2) NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `import_date` date DEFAULT NULL,
  `import_receipt_id` bigint UNSIGNED NOT NULL,
  `flower_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `import_receipt_details`
--

INSERT INTO `import_receipt_details` (`id`, `quantity`, `import_price`, `subtotal`, `import_date`, `import_receipt_id`, `flower_id`, `created_at`, `updated_at`) VALUES
(1, 0, '9000.00', '900000.00', '2025-06-13', 1, 1, '2025-06-13 08:31:38', '2025-06-13 09:03:03'),
(2, 0, '7000.00', '700000.00', '2025-06-13', 1, 2, '2025-06-13 08:31:38', '2025-06-13 09:00:50'),
(3, 880, '9000.00', '9000000.00', '2025-06-13', 2, 1, '2025-06-13 09:00:45', '2025-06-13 09:35:01'),
(4, 770, '7000.00', '7000000.00', '2025-06-13', 2, 2, '2025-06-13 09:00:45', '2025-06-13 09:35:01'),
(5, 1000, '9000.00', '9000000.00', '2025-06-09', 3, 1, '2025-06-13 10:00:08', '2025-06-13 10:00:08'),
(6, 1000, '9000.00', '9000000.00', '2025-06-09', 3, 2, '2025-06-13 10:00:08', '2025-06-13 10:00:08');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_06_07_160015_create_categories_table', 1),
(5, '2025_06_07_160016_create_discounts_table', 1),
(6, '2025_06_07_160016_create_orders_table', 1),
(7, '2025_06_07_160016_create_products_table', 1),
(8, '2025_06_07_160017_create_flower_types_table', 1),
(9, '2025_06_07_160017_create_flowers_table', 1),
(10, '2025_06_07_160017_create_import_receipts_table', 1),
(11, '2025_06_07_160017_create_order_details_table', 1),
(12, '2025_06_07_160018_create_import_receipt_details_table', 1),
(13, '2025_06_07_160018_create_recipes_table', 1),
(14, '2025_06_08_054402_create_personal_access_tokens_table', 1),
(15, '2025_06_11_024332_add_name_to_discounts_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `note` varchar(255) DEFAULT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` varchar(255) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `buy_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `payment_method` varchar(255) NOT NULL,
  `discount_id` bigint UNSIGNED DEFAULT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `name`, `email`, `phone`, `address`, `note`, `total_price`, `status`, `discount_amount`, `buy_at`, `payment_method`, `discount_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 'vinh', 'vinhngo@gmail.com', '0123456789', 'sg', 'nhanh', '292500.00', 'đang xử lý', '0.00', '2025-06-13 15:33:30', 'cod', NULL, 1, '2025-06-13 08:33:30', '2025-06-13 08:33:30'),
(2, 'vinh', 'vinhngo@gmail.com', '0123456789', 'sg', 'nhanh', '292500.00', 'đang xử lý', '0.00', '2025-06-13 15:33:38', 'cod', NULL, 1, '2025-06-13 08:33:38', '2025-06-13 08:33:38'),
(3, 'vinh', 'vinhngo@gmail.com', '0123456789', 'sg', 'nhanh', '292500.00', 'đang xử lý', '0.00', '2025-06-13 15:33:56', 'cod', NULL, 1, '2025-06-13 08:33:56', '2025-06-13 08:33:56'),
(4, 'vinh', 'vinhngo@gmail.com', '0123456789', 'sg', 'nhanh', '292500.00', 'đang xử lý', '0.00', '2025-06-13 15:33:56', 'cod', NULL, 1, '2025-06-13 08:33:56', '2025-06-13 08:33:56'),
(5, 'vinh', 'vinhngo@gmail.com', '0123456789', 'sg', 'nhanh', '292500.00', 'đang xử lý', '0.00', '2025-06-13 15:33:57', 'cod', NULL, 1, '2025-06-13 08:33:57', '2025-06-13 08:33:57'),
(6, 'vinh', 'vinhngo@gmail.com', '0123456789', 'sg', 'nhanh', '292500.00', 'đang xử lý', '0.00', '2025-06-13 15:35:31', 'cod', NULL, 1, '2025-06-13 08:35:31', '2025-06-13 08:35:31'),
(7, 'vinh', 'ngovinh0808@gmail.com', '0123456789', 'sg', 'nhanh1111', '585000.00', 'đang xử lý', '0.00', '2025-06-13 16:00:50', 'cod', NULL, 1, '2025-06-13 09:00:50', '2025-06-13 09:00:50'),
(8, 'Nguyen Van A', 'a@example.com', '0123456789', '123 Nguyen Trai', 'Giao trong giờ hành chính', '585000.00', 'đang xử lý', '0.00', '2025-06-13 16:03:03', 'cod', NULL, 1, '2025-06-13 09:03:03', '2025-06-13 09:03:03'),
(9, 'vinh', 'ngovinh0808@gmail.com', '0123456789', 'sg', 'nhanh1111', '585000.00', 'đang xử lý', '0.00', '2025-06-13 16:03:52', 'cod', NULL, 1, '2025-06-13 09:03:52', '2025-06-13 09:03:52'),
(10, 'vinh1', 'ngovinh0808@gmail.com', '0123456789', 'sg', 'ok', '292500.00', 'đang xử lý', '0.00', '2025-06-13 16:07:07', 'cod', NULL, 1, '2025-06-13 09:07:07', '2025-06-13 09:07:07'),
(11, 'vinh', 'ngovinh0808@gmail.com', '0123456789', 'sg', 'sg', '2632500.00', 'đang xử lý', '0.00', '2025-06-13 16:35:01', 'cod', NULL, 1, '2025-06-13 09:35:01', '2025-06-13 09:35:01');

-- --------------------------------------------------------

--
-- Table structure for table `order_details`
--

CREATE TABLE `order_details` (
  `id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `subtotal` decimal(10,2) NOT NULL,
  `order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `order_details`
--

INSERT INTO `order_details` (`id`, `quantity`, `subtotal`, `order_id`, `product_id`, `created_at`, `updated_at`) VALUES
(1, 1, '292500.00', 1, 1, '2025-06-13 08:33:30', '2025-06-13 08:33:30'),
(2, 1, '292500.00', 2, 1, '2025-06-13 08:33:38', '2025-06-13 08:33:38'),
(3, 1, '292500.00', 3, 1, '2025-06-13 08:33:56', '2025-06-13 08:33:56'),
(4, 1, '292500.00', 4, 1, '2025-06-13 08:33:56', '2025-06-13 08:33:56'),
(5, 1, '292500.00', 5, 1, '2025-06-13 08:33:57', '2025-06-13 08:33:57'),
(6, 1, '292500.00', 6, 1, '2025-06-13 08:35:31', '2025-06-13 08:35:31'),
(7, 1, '292500.00', 7, 1, '2025-06-13 09:00:50', '2025-06-13 09:00:50'),
(8, 1, '292500.00', 7, 3, '2025-06-13 09:00:50', '2025-06-13 09:00:50'),
(9, 2, '585000.00', 8, 1, '2025-06-13 09:03:03', '2025-06-13 09:03:03'),
(10, 1, '292500.00', 9, 1, '2025-06-13 09:03:52', '2025-06-13 09:03:52'),
(11, 1, '292500.00', 9, 3, '2025-06-13 09:03:52', '2025-06-13 09:03:52'),
(12, 1, '292500.00', 10, 3, '2025-06-13 09:07:07', '2025-06-13 09:07:07'),
(13, 1, '292500.00', 11, 1, '2025-06-13 09:35:01', '2025-06-13 09:35:01'),
(14, 1, '292500.00', 11, 2, '2025-06-13 09:35:01', '2025-06-13 09:35:01'),
(15, 1, '292500.00', 11, 3, '2025-06-13 09:35:01', '2025-06-13 09:35:01'),
(16, 6, '1755000.00', 11, 4, '2025-06-13 09:35:01', '2025-06-13 09:35:01');

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `size` varchar(255) DEFAULT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `description`, `price`, `image`, `status`, `size`, `category_id`, `created_at`, `updated_at`) VALUES
(1, 'Bó hoa cưới hồng đỏ', 'Bó hoa cưới được kết từ 10 bông hồng đỏ và 5 hoa baby', '270000.00', 'products/7OU75hAGKNAxi67zq6BgiQQQjp3o38gp34KLXF51.jpg', 1, 'Lớn', 1, '2025-06-13 08:32:20', '2025-06-13 09:57:18'),
(2, 'Hoa Món Quà Hạnh Phúc', 'Mỗi ngày đều là một ngày đặc biệt để bạn thể hiện sự quan tâm, chăm sóc đến những người bạn yêu thương không chỉ bằng những lời chúc ấm áp mà còn bằng những đóa hoa khoe sắc rạng ngời.', '202500.00', 'products/aMfHEUJM4vkgyFZ9TqtrSJL7CiIbvjtZqJvGQGQ3.jpg', 1, 'Lớn', 1, '2025-06-13 08:32:21', '2025-06-13 10:02:26'),
(3, 'Hoa Yêu Thương Rực Rỡ', 'Mỗi ngày đều là một ngày đặc biệt để bạn thể hiện sự quan tâm, chăm sóc đến những người bạn yêu thương không chỉ bằng những lời chúc ấm áp mà còn bằng những đóa hoa khoe sắc rạng ngời. Hãy để những bông hoa rực rỡ nhất của Dalat Hasfarm được đồng hành cùng bạn trong từng khoảnh khắc đặc biệt, thú vị và đáng quý. Từng loại hoa mang những ý nghĩa đặc biệt, Dalat Hasfarm gửi bạn yêu hoa nhiều lựa chọn Hoa Tặng tuyệt vời cùng những đóa hoa thơm xinh, rực rỡ để bạn dễ dàng chọn được món quà phù hợp nhất dành tặng những người trân quý. Những bông hoa tươi thắm từ lâu đã được xem là một vị “sứ giả tinh thần” giúp gửi trao những cảm xúc yêu thương. Những cánh hoa rạng rỡ tươi xinh và ẩn chứa nhiều ý nghĩa sẽ là lựa chọn hoàn hảo nhất để gửi trao những thông điệp hạnh phúc.', '229500.00', 'products/bCm1dIWHpNE3aYLnNOyQDXAxoIRBvzzH88tgSKvn.jpg', 1, 'Lớn', 1, '2025-06-13 08:32:22', '2025-06-13 10:03:24'),
(4, 'Hoa Lavender Hạnh Phúc', 'Mỗi ngày đều là một ngày đặc biệt để bạn thể hiện sự quan tâm, chăm sóc đến những người bạn yêu thương không chỉ bằng những lời chúc ấm áp mà còn bằng những đóa hoa khoe sắc rạng ngời. Hãy để những bông hoa rực rỡ nhất của Dalat Hasfarm được đồng hành cùng bạn trong từng khoảnh khắc đặc biệt, thú vị và đáng quý. Từng loại hoa sẽ mang những ý nghĩa đặc biệt khác nhau, BST Hoa Chậu Thiết Kế với nhiều loại hoa độc đáo, ưu điểm nổi bật là độ bền cao, dễ chăm sóc, những sản phẩm hoa trong chậu được các bạn florist của Dalat Hasfarm thiết kế xinh xắn là một món quà hoàn hảo nhất dành tặng người đặc biệt. Hoa tươi không đơn thuần chỉ là một món quà tặng mà từ lâu còn được xem là một vị “sứ giả tinh thần” giúp gửi trao những cảm xúc yêu thương. Những cánh hoa rạng rỡ tươi xinh và ẩn chứa nhiều ý nghĩa được chăm chút tỉ mỉ bởi Dalat Hasfarm sẽ là lựa chọn hoàn hảo nhất để gửi trao những thông điệp hạnh phúc.', '390000.00', 'products/YCvRSmq2dfwHnuX007VillWKmsocyioafWgIAcTh.png', 1, 'Lớn', 1, '2025-06-13 08:32:23', '2025-06-13 10:13:52');

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `flower_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `quantity`, `product_id`, `flower_id`, `created_at`, `updated_at`) VALUES
(9, 20, 1, 1, '2025-06-13 09:57:18', '2025-06-13 09:57:18'),
(10, 15, 2, 1, '2025-06-13 10:02:26', '2025-06-13 10:02:26'),
(11, 17, 3, 1, '2025-06-13 10:03:24', '2025-06-13 10:03:24'),
(12, 25, 4, 1, '2025-06-13 10:13:52', '2025-06-13 10:13:52'),
(13, 5, 4, 2, '2025-06-13 10:13:52', '2025-06-13 10:13:52');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('KY6rkSuER53GCIKxZNLaC0xGJKwc24tCWQbjeyJj', NULL, '127.0.0.1', 'PostmanRuntime/7.44.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoidmJSbTBmWllkSGJoNHhiWHVYYlZoaERKRmgzY1ZsbnEyRHdKMGpYVyI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=', 1749834329);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `avatar_url` varchar(255) NOT NULL DEFAULT 'http://127.0.0.1:8000/avatar/avatar.jpg',
  `address` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT '1',
  `role` enum('admin','user') NOT NULL DEFAULT 'user',
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `avatar_url`, `address`, `phone`, `status`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'vinh', 'ngovinh0808@gmail.com', NULL, '$2y$12$llPaaMgCsvA0Vo.gcAjp4eX0VhemhgeyAfE61yXLq6QjrCnHgYhUe', 'http://127.0.0.1:8000/avatar/avatar.jpg', NULL, '0123456789', 1, 'user', NULL, '2025-06-13 08:33:07', '2025-06-13 08:33:07');

--
-- Indexes for dumped tables
--

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
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `flowers`
--
ALTER TABLE `flowers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `flowers_flower_type_id_foreign` (`flower_type_id`);

--
-- Indexes for table `flower_types`
--
ALTER TABLE `flower_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_receipts_user_id_foreign` (`user_id`);

--
-- Indexes for table `import_receipt_details`
--
ALTER TABLE `import_receipt_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `import_receipt_details_import_receipt_id_foreign` (`import_receipt_id`),
  ADD KEY `import_receipt_details_flower_id_foreign` (`flower_id`);

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
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_discount_id_foreign` (`discount_id`),
  ADD KEY `orders_user_id_foreign` (`user_id`);

--
-- Indexes for table `order_details`
--
ALTER TABLE `order_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_details_order_id_foreign` (`order_id`),
  ADD KEY `order_details_product_id_foreign` (`product_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recipes_product_id_foreign` (`product_id`),
  ADD KEY `recipes_flower_id_foreign` (`flower_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

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
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `flowers`
--
ALTER TABLE `flowers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `flower_types`
--
ALTER TABLE `flower_types`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `import_receipts`
--
ALTER TABLE `import_receipts`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `import_receipt_details`
--
ALTER TABLE `import_receipt_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `order_details`
--
ALTER TABLE `order_details`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `flowers`
--
ALTER TABLE `flowers`
  ADD CONSTRAINT `flowers_flower_type_id_foreign` FOREIGN KEY (`flower_type_id`) REFERENCES `flower_types` (`id`);

--
-- Constraints for table `import_receipts`
--
ALTER TABLE `import_receipts`
  ADD CONSTRAINT `import_receipts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `import_receipt_details`
--
ALTER TABLE `import_receipt_details`
  ADD CONSTRAINT `import_receipt_details_flower_id_foreign` FOREIGN KEY (`flower_id`) REFERENCES `flowers` (`id`),
  ADD CONSTRAINT `import_receipt_details_import_receipt_id_foreign` FOREIGN KEY (`import_receipt_id`) REFERENCES `import_receipts` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_discount_id_foreign` FOREIGN KEY (`discount_id`) REFERENCES `discounts` (`id`),
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_details`
--
ALTER TABLE `order_details`
  ADD CONSTRAINT `order_details_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `order_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_flower_id_foreign` FOREIGN KEY (`flower_id`) REFERENCES `flowers` (`id`),
  ADD CONSTRAINT `recipes_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
