-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 29, 2025 at 03:17 AM
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
-- Database: `nutnessr`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `item_name` varchar(255) NOT NULL,
  `item_category` varchar(100) NOT NULL,
  `item_price` decimal(12,2) NOT NULL,
  `item_quota` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `user_id`, `item_name`, `item_category`, `item_price`, `item_quota`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 10, 'qabc', 'C', 5000.00, 10, '2025-05-28 09:28:15', '2025-05-28 10:05:10', '2025-05-28 14:43:13'),
(3, 10, 'test item 23', 'B', 4500.00, 100, '2025-05-28 09:43:41', '2025-05-28 10:05:24', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `mitra`
--

CREATE TABLE `mitra` (
  `id_mitra` bigint(20) UNSIGNED NOT NULL,
  `nama_mitra` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `mitra`
--

INSERT INTO `mitra` (`id_mitra`, `nama_mitra`) VALUES
(1, 'Ancika Healthy Gym');

-- --------------------------------------------------------

--
-- Table structure for table `penyewaan`
--

CREATE TABLE `penyewaan` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `tanggal` date NOT NULL,
  `durasi` int(11) NOT NULL,
  `item_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `penyewaan`
--

INSERT INTO `penyewaan` (`id`, `user_id`, `tanggal`, `durasi`, `item_id`, `created_at`, `updated_at`) VALUES
(2, 13, '2025-05-29', 2, 2, '2025-05-29 01:16:12', NULL),
(3, 13, '2025-05-31', 3, 2, '2025-05-29 01:16:31', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `role`, `foto`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'alyaraisaarl', '5051231015@student.its.ac.id', '16d7a4fca7442dda3ad93c9a726597e4', 'admin', '8P97JGEwWofoto_almet_Alya.jpg', '2025-04-23 19:17:53', '2025-04-23 19:17:53', NULL),
(5, 'adminalya', 'adminalya@gmail.com', '0192023a7bbd73250516f069df18b500', 'user', NULL, '2025-04-24 03:19:45', '2025-04-24 03:19:45', NULL),
(7, 'admin', 'admin@gmail.com', '0192023a7bbd73250516f069df18b500', 'admin', NULL, '2025-04-24 03:35:03', '2025-04-24 03:35:03', NULL),
(8, 'alyaarl', 'alyalaksono@gmail.com', 'd592dcf42786e319d4b851f333ee478b', 'user', NULL, '2025-05-07 23:11:43', '2025-05-07 23:11:43', NULL),
(9, 'anchikagym', 'alyalaksono18@gmail.com', '41aa1757c6270efbb82a16f0e3b94779', 'admin_mitra', NULL, '2025-05-28 03:30:40', '2025-05-28 03:30:40', NULL),
(10, 'alya', 'mompa@gmail.com', '25f9e794323b453885f5181f1b624d0b', 'admin_mitra', NULL, '2025-05-28 13:27:03', '2025-05-28 13:27:03', NULL),
(11, 'alya1', 'tole@tole.com', '0cc175b9c0f1b6a831c399e269772661', '', NULL, '2025-05-29 00:07:02', '2025-05-29 00:07:02', NULL),
(13, 'test1', 'tole2@tole.com', '900150983cd24fb0d6963f7d28e17f72', '', NULL, '2025-05-29 00:07:31', '2025-05-29 00:07:31', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`);

--
-- Indexes for table `mitra`
--
ALTER TABLE `mitra`
  ADD PRIMARY KEY (`id_mitra`);

--
-- Indexes for table `penyewaan`
--
ALTER TABLE `penyewaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `penyewaan`
--
ALTER TABLE `penyewaan`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `fk_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `penyewaan`
--
ALTER TABLE `penyewaan`
  ADD CONSTRAINT `penyewaan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `penyewaan_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
