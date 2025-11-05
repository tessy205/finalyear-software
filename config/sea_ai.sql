-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Nov 05, 2025 at 07:18 AM
-- Server version: 10.4.21-MariaDB
-- PHP Version: 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sea_ai`
--

-- --------------------------------------------------------

--
-- Table structure for table `access_codes`
--

CREATE TABLE `access_codes` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `code` char(4) NOT NULL,
  `is_used` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `access_codes`
--

INSERT INTO `access_codes` (`id`, `email`, `code`, `is_used`, `created_at`) VALUES
(1, 'testmail@gmail.com', '0789', 1, '2025-11-04 16:58:03'),
(4, '123@mail.com', '0299', 1, '2025-11-05 04:36:53');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `email`, `password`, `created_at`) VALUES
(1, 'admin@example.com', '$2y$10$gneRicsQyEs4XX4iPLPvoOFW5WsXY5glWHGm1hU3KQecGRJhDeXjq', '2025-11-04 16:55:07');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

CREATE TABLE `courses` (
  `id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `course_code` varchar(100) NOT NULL,
  `level` varchar(50) NOT NULL,
  `info` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `courses`
--

INSERT INTO `courses` (`id`, `lecturer_id`, `title`, `course_code`, `level`, `info`, `created_at`, `updated_at`) VALUES
(1, 1, 'aaaa bbb', '123', '100', 'cool', '2025-11-04 17:07:13', '2025-11-04 17:16:16'),
(2, 1, 'Logic and Philosophy', 'ABC1231', '100', 'the course is about ..', '2025-11-04 17:08:00', '2025-11-04 17:11:02'),
(3, 2, 'Cloud Computing', 'CMP112', '100', 'about cloud computing for students', '2025-11-05 04:51:07', '2025-11-05 05:13:58'),
(4, 2, 'khjghgchhkjbvh', 'oiyugkiughjb', '300', 'khjgvnbkjbvaa', '2025-11-05 04:51:51', '2025-11-05 05:12:39');

-- --------------------------------------------------------

--
-- Table structure for table `lecturers`
--

CREATE TABLE `lecturers` (
  `id` int(11) NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `is_blocked` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `lecturers`
--

INSERT INTO `lecturers` (`id`, `name`, `email`, `password`, `is_blocked`, `created_at`) VALUES
(1, 'Test', 'testmail@gmail.com', '$2y$10$tEY4rNvwlFqWoMgxrKaoiO4AeRgfy2Xtex.yJ1zJOaCq1JhyBkRZ.', 0, '2025-11-04 17:05:58'),
(2, 'John', '123@mail.com', '$2y$10$uZzt8FCJAuBSsUSd5jzXvOJv5xzNzsDPhDHj3STi1rGviv.CC1qdq', 0, '2025-11-05 04:37:25');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `course_id` int(11) NOT NULL,
  `lecturer_id` int(11) NOT NULL,
  `type` enum('mcq','theory') NOT NULL,
  `question_text` text NOT NULL,
  `options` text DEFAULT NULL,
  `answer` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `course_id`, `lecturer_id`, `type`, `question_text`, `options`, `answer`, `created_at`) VALUES
(1, 3, 2, 'theory', 'aa', NULL, '', '2025-11-05 04:58:04'),
(2, 3, 2, 'mcq', 'Which of the following is a key characteristic of cloud computing that enables users to access computing resources on demand, paying only for what they use?', '{\"a\":\"Resource Pooling\",\"b\":\"Broad Network Access\",\"c\":\"Measured Service\",\"d\":\"Rapid Elasticity\"}', 'c', '2025-11-05 05:24:03'),
(3, 3, 2, 'mcq', 'Which cloud deployment model offers the greatest level of customization and control over infrastructure, while also requiring the most significant operational responsibilities from the user?', '{\"a\":\"Public Cloud\",\"b\":\"Private Cloud\",\"c\":\"Hybrid Cloud\",\"d\":\"Community Cloud\"}', 'b', '2025-11-05 05:24:03'),
(5, 3, 2, 'mcq', 'In the context of cloud security, what is the primary purpose of a Security Assertion Markup Language (SAML) assertion?', '{\"a\":\"To encrypt data transmitted between the client and the cloud server.\",\"b\":\"To provide authorization credentials for accessing cloud resources.\",\"c\":\"To verify the integrity of data stored in the cloud.\",\"d\":\"To exchange authentication and authorization data between an identity provider and a service provider.\"}', 'a', '2025-11-05 05:26:25');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `access_codes`
--
ALTER TABLE `access_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `email` (`email`),
  ADD KEY `code` (`code`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `courses`
--
ALTER TABLE `courses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `lecturer_id` (`lecturer_id`);

--
-- Indexes for table `lecturers`
--
ALTER TABLE `lecturers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `course_id` (`course_id`),
  ADD KEY `lecturer_id` (`lecturer_id`),
  ADD KEY `type` (`type`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `access_codes`
--
ALTER TABLE `access_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `courses`
--
ALTER TABLE `courses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `lecturers`
--
ALTER TABLE `lecturers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `courses`
--
ALTER TABLE `courses`
  ADD CONSTRAINT `courses_ibfk_1` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`lecturer_id`) REFERENCES `lecturers` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
