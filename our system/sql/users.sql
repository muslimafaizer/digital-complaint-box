-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 13, 2025 at 05:13 PM
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
-- Database: `dcbs_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('complainer','handler') NOT NULL DEFAULT 'complainer',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `user_type`, `created_at`) VALUES
(1, 'Fazil Muhammad', 'fazil@gmail.com', '$2y$10$Xqgvf4rWA9IcmBveyq26DOXqXXHQscAV8dl9NvC2ZyDOKKCR2CEcq', 'complainer', '2025-08-09 21:39:50'),
(2, 'kusu', 'fazilh@gmail.com', '$2y$10$6n5NmJCv9Qaf/b4wip3V5Ob.ePM2ivblPwxFda0.QVxSX.yJmKMey', 'handler', '2025-08-09 21:57:09'),
(6, 'dada', 'dada@gmail.com', '$2y$10$fcpxNNz5VhSYKGST6/NnruvFgEdeBI.D/02zavyHcXMf8PfjOZDai', 'complainer', '2025-08-10 12:56:03'),
(7, 'dhilip kumar', 'dhilip@gmail.com', '$2y$10$fgI6z96sOunzWDG5tNQgBeKRzHVe9NrGmWCiTfwZbRhwDP29fngHa', 'complainer', '2025-08-11 17:32:40'),
(9, 'canteen dutta', 'dutta@gmail.com', '$2y$10$Xqgvf4rWA9IcmBveyq26DOXqXXHQscAV8dl9NvC2ZyDOKKCR2CEcq', 'handler', '2025-08-11 17:48:26');

--
-- Indexes for dumped tables
--

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
