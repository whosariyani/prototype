-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 01, 2026 at 03:41 PM
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
-- Database: `prototype_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_lrn` varchar(100) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_lrn`, `timestamp`) VALUES
(1, '136500000000', '2026-02-12 01:13:05'),
(2, '136500000000', '2026-02-12 01:13:06'),
(3, '136500000000', '2026-02-12 01:13:56'),
(4, '136500000001', '2026-02-12 01:23:04'),
(5, '136500000000', '2026-02-25 17:26:26'),
(6, '136500000000', '2026-02-26 02:27:52'),
(7, '136500000000', '2026-02-26 02:27:52'),
(8, '136500000000', '2026-02-26 02:27:57'),
(9, '136500000000', '2026-02-26 02:27:58'),
(10, '136500000002', '2026-02-26 08:04:59'),
(11, '136500000002', '2026-02-26 08:05:00'),
(12, '136500000002', '2026-02-26 08:05:00'),
(13, '136500000002', '2026-02-26 08:05:01'),
(14, '136500000001', '2026-02-26 08:05:24'),
(15, '136500000001', '2026-02-26 08:05:24'),
(16, '136500000001', '2026-02-26 08:05:25'),
(17, '136500000002', '2026-02-26 09:12:13'),
(18, '136500000001', '2026-02-27 05:08:35'),
(19, '136500000001', '2026-02-27 05:09:57'),
(20, '136500000000', '2026-02-27 08:47:37'),
(21, '136500000002', '2026-02-27 09:01:49');

-- --------------------------------------------------------

--
-- Table structure for table `employee_id_list`
--

CREATE TABLE `employee_id_list` (
  `id` int(11) NOT NULL,
  `employee_id` varchar(16) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_id_list`
--

INSERT INTO `employee_id_list` (`id`, `employee_id`) VALUES
(1, '136600000000'),
(2, '136600000001'),
(3, '136600000002');

-- --------------------------------------------------------

--
-- Table structure for table `employee_info`
--

CREATE TABLE `employee_info` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `employee_type` varchar(30) NOT NULL,
  `employee_id` varchar(30) NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `address` text NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `account_password` varchar(100) NOT NULL,
  `qr_code_data` longtext DEFAULT NULL,
  `qr_code_url` longtext DEFAULT NULL,
  `qr_code_generated_at` timestamp NULL DEFAULT NULL,
  `is_registered` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `employee_info`
--

INSERT INTO `employee_info` (`id`, `first_name`, `middle_name`, `last_name`, `employee_type`, `employee_id`, `birthdate`, `age`, `sex`, `address`, `contact_number`, `profile_picture`, `account_password`, `qr_code_data`, `qr_code_url`, `qr_code_generated_at`, `is_registered`) VALUES
(1, 'Teacher', '', 'Perez', 'teacher', '136600000000', '0000-00-00', 0, '', '', '', NULL, '123', NULL, NULL, NULL, 0),
(2, 'Arianney Mae', 'Facunla', 'Facunla', 'it_Support', '136600000001', '0000-00-00', 0, '', '', '', NULL, 'yaniwow', NULL, NULL, NULL, 0),
(3, 'Guard', '', 'Sarge', 'security_personnel', '136600000002', '0000-00-00', 0, '', '', '', NULL, '123', NULL, NULL, NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `saved_qr_codes`
--

CREATE TABLE `saved_qr_codes` (
  `id` int(11) NOT NULL,
  `user_id` varchar(50) NOT NULL,
  `user_type` enum('student','employee') NOT NULL,
  `lrn_or_employee_number` varchar(50) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `qr_data` text NOT NULL,
  `qr_image_url` longtext NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_info`
--

CREATE TABLE `student_info` (
  `id` int(11) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `lrn` varchar(12) NOT NULL,
  `grade_level` int(11) NOT NULL,
  `section` varchar(30) NOT NULL,
  `birthdate` date NOT NULL,
  `age` int(11) NOT NULL,
  `sex` varchar(10) NOT NULL,
  `student_address` text NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `parent_guardian` varchar(100) NOT NULL,
  `parent_guardian_contact` varchar(15) NOT NULL,
  `relationship` varchar(30) DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `account_password` varchar(100) NOT NULL,
  `qr_code_url` longtext DEFAULT NULL,
  `qr_code_generated_at` timestamp NULL DEFAULT NULL,
  `is_registered` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_info`
--

INSERT INTO `student_info` (`id`, `first_name`, `middle_name`, `last_name`, `lrn`, `grade_level`, `section`, `birthdate`, `age`, `sex`, `student_address`, `contact_number`, `email_address`, `parent_guardian`, `parent_guardian_contact`, `relationship`, `profile_picture`, `account_password`, `qr_code_url`, `qr_code_generated_at`, `is_registered`) VALUES
(1, 'Erwin', '', 'Regicide', '136500000000', 12, 'Rossum', '1891-11-15', 134, 'male', 'Saxony-Anhalt', '09999999999', 'e.regicide@outlook.com', 'Mutter', '09999999998', 'parent', 'uploads/136500000000_erwin-regicide.jpg', '123', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADIAQMAAACXljzdAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAA+UlEQVRYheWXuxGEMAxElyFwSAmUcqVxpbkUl0DogDmdPrbhbqABSQkjPycsqw/AY2xkUZA+wKukln/8kWwvXJYKUEY6NE8eycpvzEQ14KNj4nyPSYY67gkgRyUm6bUgF7jqb6rECen9bakT3Xc+9+QS6gPchQtiGuhDphnVWbwhLcAb4Q+Mmei9GuFEHLAjErGq192FBcprFTvksx84IiQaMNTa5pjlgjk+DNFpJqET3dS5zDk/ZMTCjtcbYvwdCES2ps2Y6H6Jurvt5EODnz8wN2Ql28nPTt41iEf6EPdPxAPc30KSVgtjmtka55CM/lanPw3CkMf4Ah8qsDPBoLA3AAAAAElFTkSuQmCC', '2026-02-25 18:25:08', 1),
(12, 'Friedrich Wilhelm', '', 'von Hohenzollern', '136500000001', 12, 'Rossum', '1859-01-27', 167, 'male', 'Der Kronprinzenpalais, Berlin', '09992314567', 'wilhelm.hohenzollern@hotmail.com', 'Victoria Adelaide Mary Louisa', '09281738213', 'parent', 'uploads/136500000001_Frederick_wilhelm.jpg', '123', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADIAQMAAACXljzdAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAA+ElEQVRYheWXvQ3EIAyFX5SCkhEYJaPlRmMURqCkiOLDNj93UrKAcYPMl4Yn+9kBXuMkjQR3A0dyLb/tkagPTr4AFOEuyZ1FEuqLKxEN6tW11TyvSYY65gnAV2lN0nuBP6hd/9AlRkj3N182enY+8+QnpA7wFCaIaiAHTzMqO9cGW4A1wgm/OChJYEIZS5F6d4lI4vGhcDlEHW22yCnrCn1C1rmNnZhwxa9DpNFbu3d1dJoZIyM8TQ8/MrAQOVs+p5lZEkUB3cmHBn9/YGZIoLaTiwbs5F2D9cicZuZJDfY3LEhaL/i2hu9322fMkeFvTYNZB8uQ1/gCCLeGqLQJArYAAAAASUVORK5CYII=', '2026-02-26 07:31:05', 1),
(13, 'Edmund', '', 'Blackadder', '136500000002', 12, 'Rossum', '1887-09-25', 138, '0', 'Manchester, British Empire', '09992314567', 'blackadder.iv@hotmail.com', 'Jane Smith', '09178213781', 'parent', 'uploads/136500000002_Captain_blackadder.jpg', '123', 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAMgAAADIAQMAAACXljzdAAAABlBMVEX///8AAABVwtN+AAAACXBIWXMAAA7EAAAOxAGVKw4bAAAA80lEQVRYhe2XwRGEIAxFv+OBoyVYiqXtlkYplOCRg7PZJAiDO9rAZ3PACc8Lf5IfAB7jJSUSwgfYUjjzDx+J5cBpyYBEhMPzwEhWPbES10C3jknzfUzS1KEngG2lMUntBftBu/6mS0hI9bclT3LvfPSkC68D3AUFKRr4x6aZ5NlqwyyAjWgiXuQlSTAiOwYjZu7iicQ1WznEcpFhIz/lP4sRYCDSPN7Wqs51mpGQFot2ffXwbe9VoSfF+XyaobxLaEmrg25ib5cXGA1Z5byT+w3FnLxq8CfUxPxNnfw9HDl7wbteY65Tno70/uYa3DofNXmML3pSIdTEuCyXAAAAAElFTkSuQmCC', '2026-02-26 08:01:18', 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_lrn_list`
--

CREATE TABLE `student_lrn_list` (
  `id` int(11) NOT NULL,
  `LRN` varchar(12) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_lrn_list`
--

INSERT INTO `student_lrn_list` (`id`, `LRN`) VALUES
(1, '136500000000'),
(2, '136500000001'),
(3, '136500000002'),
(4, '136500000003'),
(5, '136500000004');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_id_list`
--
ALTER TABLE `employee_id_list`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_info`
--
ALTER TABLE `employee_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_number` (`employee_id`),
  ADD KEY `employee_number_2` (`employee_id`);

--
-- Indexes for table `saved_qr_codes`
--
ALTER TABLE `saved_qr_codes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_qr` (`user_id`,`user_type`),
  ADD KEY `idx_user_type` (`user_id`,`user_type`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `student_info`
--
ALTER TABLE `student_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lrn` (`lrn`),
  ADD KEY `lrn_2` (`lrn`);

--
-- Indexes for table `student_lrn_list`
--
ALTER TABLE `student_lrn_list`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `employee_id_list`
--
ALTER TABLE `employee_id_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `employee_info`
--
ALTER TABLE `employee_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `saved_qr_codes`
--
ALTER TABLE `saved_qr_codes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_info`
--
ALTER TABLE `student_info`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `student_lrn_list`
--
ALTER TABLE `student_lrn_list`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
