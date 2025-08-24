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
-- Table structure for table `complaints`
--

CREATE TABLE `complaints` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `handler_id` int(10) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `photo_path` varchar(255) DEFAULT NULL,
  `video_path` varchar(255) DEFAULT NULL,
  `audio_path` varchar(255) DEFAULT NULL,
  `status` enum('Pending','In Progress','Solved') DEFAULT 'Pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `complaints`
--

INSERT INTO `complaints` (`id`, `user_id`, `handler_id`, `title`, `description`, `photo_path`, `video_path`, `audio_path`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'asdxascZXCsczXasc', 'sCZCsvasczcxZx', NULL, NULL, 'uploads/1754806742_pour-hot-water-in-cup-104070.mp3', 'In Progress', '2025-08-10 06:19:02', '2025-08-10 07:13:36'),
(2, 6, 2, 'zXcSVSff', 'azXZXadfa', 'uploads/1754810845_Screenshot 2025-08-02 205536.png', NULL, NULL, 'Pending', '2025-08-10 07:27:25', '2025-08-10 07:27:25'),
(3, 6, 2, 'fafsadagfasdas', 'dasfasssssssssssssssssssssssssssss', NULL, 'uploads/1754810896_Particles, Light, Beautiful Wallpaper. Free Stock Video.mp4', NULL, 'In Progress', '2025-08-10 07:28:16', '2025-08-10 07:29:21'),
(4, 7, 2, 'fan problem', 'room number 322 our fan not working my hostel is pandula', 'uploads/1754913938_logo.png', NULL, NULL, 'In Progress', '2025-08-11 12:05:38', '2025-08-11 12:07:45'),
(5, 1, 9, 'dzgvfzxv', 'svzxfcaszsxcasfzC', 'uploads/1755097511_1.png', NULL, NULL, 'In Progress', '2025-08-13 15:05:11', '2025-08-13 15:11:13');

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
-- Indexes for table `complaints`
--
ALTER TABLE `complaints`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `handler_id` (`handler_id`);

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
-- AUTO_INCREMENT for table `complaints`
--
ALTER TABLE `complaints`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `complaints`
--
ALTER TABLE `complaints`
  ADD CONSTRAINT `complaints_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `complaints_ibfk_2` FOREIGN KEY (`handler_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
