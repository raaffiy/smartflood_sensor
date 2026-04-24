-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 24, 2026 at 03:49 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `smartflood_sensor`
--

-- --------------------------------------------------------

--
-- Table structure for table `monitoring`
--

CREATE TABLE `monitoring` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `lokasi_sungai` varchar(150) NOT NULL,
  `waktu_pengukuran` datetime NOT NULL,
  `tinggi_air` decimal(6,2) NOT NULL,
  `status_banjir` enum('aman','waspada','bahaya') NOT NULL,
  `deskripsi` text DEFAULT NULL,
  `foto_bukti` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `monitoring`
--

INSERT INTO `monitoring` (`id`, `user_id`, `lokasi_sungai`, `waktu_pengukuran`, `tinggi_air`, `status_banjir`, `deskripsi`, `foto_bukti`, `created_at`) VALUES
(1, 1, 'Sungai Citarum', '2026-04-24 19:36:00', 70.00, 'waspada', 'sample sample sample sample', 'foto_69eb641a3ee13.jpeg', '2026-04-24 12:37:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `email`, `password`, `created_at`) VALUES
(1, 'rafyy', 'raafimaulana43@gmail.com', '$2y$10$bn.uPRAqLZkgsxGYWktlvOHG7dGL0q35jVJVPpWGyYyOUPV8PRl2u', '2026-04-24 12:36:10');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `monitoring`
--
ALTER TABLE `monitoring`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

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
-- AUTO_INCREMENT for table `monitoring`
--
ALTER TABLE `monitoring`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `monitoring`
--
ALTER TABLE `monitoring`
  ADD CONSTRAINT `monitoring_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
