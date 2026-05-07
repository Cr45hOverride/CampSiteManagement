-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 07, 2026 at 06:09 AM
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
-- Database: `sia_campsite`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `booking_name` varchar(100) NOT NULL,
  `booking_contact` varchar(20) NOT NULL,
  `check_in` date NOT NULL,
  `check_out` date NOT NULL,
  `booking_remark` text DEFAULT NULL,
  `admin_remark` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_amount` decimal(10,2) DEFAULT 50.00,
  `payment_status` enum('unpaid','partial','paid') DEFAULT 'unpaid',
  `payment_reference` varchar(100) DEFAULT NULL,
  `payment_proof` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `site_id`, `booking_name`, `booking_contact`, `check_in`, `check_out`, `booking_remark`, `admin_remark`, `created_at`, `payment_amount`, `payment_status`, `payment_reference`, `payment_proof`) VALUES
(1, 1, 'a', '013430231', '2026-05-10', '2026-05-14', 'asd', NULL, '2026-05-06 03:08:50', 50.00, 'paid', NULL, NULL),
(2, 1, 'dd', '999', '2026-06-10', '2026-06-13', 'asd', NULL, '2026-05-06 03:09:39', 50.00, 'paid', NULL, NULL),
(3, 3, 'ccc', '1212', '2026-05-14', '2026-05-16', '', NULL, '2026-05-06 03:42:08', 50.00, 'unpaid', NULL, NULL),
(4, 10, 'zzz', '234234', '2026-05-12', '2026-05-13', '', NULL, '2026-05-06 03:42:37', 0.00, 'paid', NULL, NULL),
(5, 20, 'oooo', '2344', '2026-05-12', '2026-05-13', 'dfg', NULL, '2026-05-06 03:44:52', 0.00, 'paid', '123456gtfgtr', 'proof_5_1778124959.pdf'),
(6, 7, 'kkk', '3452', '2026-05-13', '2026-05-15', '', NULL, '2026-05-06 06:37:06', 50.00, 'unpaid', 'asdasdasd', 'proof_6_1778125023.pdf');

-- --------------------------------------------------------

--
-- Table structure for table `sites`
--

CREATE TABLE `sites` (
  `id` int(11) NOT NULL,
  `tapak_name` varchar(50) NOT NULL,
  `status` enum('active','maintenance') DEFAULT 'active',
  `remark` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sites`
--

INSERT INTO `sites` (`id`, `tapak_name`, `status`, `remark`) VALUES
(1, 'Tapak 1', 'active', NULL),
(2, 'Tapak 2', 'active', ''),
(3, 'Tapak 3', 'active', NULL),
(4, 'Tapak 4', 'active', NULL),
(5, 'Tapak 5', 'active', NULL),
(6, 'Tapak 6', 'active', NULL),
(7, 'Tapak 7', 'active', NULL),
(8, 'Tapak 8', 'active', NULL),
(9, 'Tapak 9', 'active', NULL),
(10, 'Tapak 10', 'active', NULL),
(11, 'Tapak 11', 'active', NULL),
(12, 'Tapak 12', 'active', NULL),
(13, 'Tapak 13', 'active', NULL),
(14, 'Tapak 14', 'active', NULL),
(15, 'Tapak 15', 'active', NULL),
(16, 'Tapak 16', 'active', NULL),
(17, 'Tapak 17', 'active', NULL),
(18, 'Tapak 18', 'active', NULL),
(19, 'Tapak 19', 'active', NULL),
(20, 'Tapak 20', 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('admin','staff') DEFAULT 'staff',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `role`, `created_at`) VALUES
(1, 'azian@kamalharmoni.com', '$2y$10$jZFYJYw52nlQZbOe/jxSfeRdD1mxuqbTBlYTvLdH8/sFvTBPC.vY.', 'admin', '2026-05-06 04:24:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `site_id` (`site_id`);

--
-- Indexes for table `sites`
--
ALTER TABLE `sites`
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
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `sites`
--
ALTER TABLE `sites`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`site_id`) REFERENCES `sites` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
