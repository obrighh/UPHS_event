-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 05, 2026 at 03:00 PM
-- Server version: 8.0.40
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `uphs_events`
--

-- --------------------------------------------------------

--
-- Table structure for table `accounts`
--

CREATE TABLE `accounts` (
  `user_id` int NOT NULL,
  `ut_id` int NOT NULL,
  `username` varchar(199) NOT NULL,
  `password` varchar(199) NOT NULL,
  `email` varchar(199) DEFAULT NULL,
  `f_name` varchar(199) NOT NULL,
  `m_name` varchar(199) NOT NULL,
  `l_name` varchar(199) NOT NULL,
  `sch_id` int DEFAULT NULL,
  `date_created` date DEFAULT NULL,
  `user_status` int NOT NULL DEFAULT '1',
  `profile_picture` varchar(199) DEFAULT NULL
);

--
-- Dumping data for table `accounts`
--

INSERT INTO `accounts` (`user_id`, `ut_id`, `username`, `password`, `email`, `f_name`, `m_name`, `l_name`, `sch_id`, `date_created`, `user_status`, `profile_picture`) VALUES
(1, 1, 'username', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'email@gmail.com', 'admin', '', '', 123456789, '2025-11-02', 1, NULL),
(3, 2, 'Matthew', '$2y$10$ioi/bg7sKJBaoCKqCvC.0OfzQ83U6mq/7QeoaB31/gPxbUwgt3gE6', 'matthewgoloyugonls@gmail.com', 'Matthew', 'Sauro', 'Goloyugo', 230100775, '2025-11-03', 1, NULL),
(5, 3, 'organization', '$2y$10$7scXw5/gZiPyPhMSAFnmxeOdvtfXsqxMSBVPjksMz3mGgZOBJRg9e', 'email@gmail.com', 'Fname', 'Mname', 'Lname', 987654321, '2025-11-04', 1, NULL),
(6, 3, 'lebronjames', '$2y$10$JQSHr7/nn0PCg10k9bbEseLuYht/eKINJiMlBhAY2wtrZQmcwvwVm', 'lbj@gmail.com', 'Lebron', 'James', '', NULL, NULL, 0, NULL),
(8, 3, 'lebronjames', '$2y$10$DexVlLUfXi//q1FREckoguorGAj9XJdUxgFBHu22ExO0MopHm2GLC', 'lbj@gmail.com', 'Lebron', 'James', '', 230100667, '2026-04-05', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `announcement`
--

CREATE TABLE `announcement` (
  `a_id` int NOT NULL,
  `org_id` int DEFAULT NULL,
  `u_id` int DEFAULT NULL,
  `title` varchar(199) DEFAULT NULL,
  `description` varchar(999) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `time` time DEFAULT NULL,
  `a_status` int DEFAULT '1',
  `announcement_date_posted` date DEFAULT NULL
);

--
-- Dumping data for table `announcement`
--

INSERT INTO `announcement` (`a_id`, `org_id`, `u_id`, `title`, `description`, `date`, `time`, `a_status`, `announcement_date_posted`) VALUES
(1, 3, 5, 'New Socks!!', 'asdasdasd', '2025-11-20', '14:41:00', 1, '2025-11-22'),
(2, 2, 5, 'Testing', 'testingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtesting', '2025-11-20', '16:21:00', 0, '2025-10-22'),
(3, 1, 5, 'Testing 2', 'testingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtestingtesting', '2025-11-20', '16:35:00', 0, '2025-09-22'),
(4, NULL, 3, 'New Announcement!', 'nyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenyenye', '2025-11-23', '16:59:00', 0, NULL),
(5, NULL, 3, 'New Announcement!', 'nyenyenye', '2025-11-23', '17:04:00', 1, '2025-09-22');

-- --------------------------------------------------------

--
-- Table structure for table `contact`
--

CREATE TABLE `contact` (
  `c_id` int NOT NULL,
  `name` varchar(200) DEFAULT NULL,
  `email` varchar(200) DEFAULT NULL,
  `message` text
);

--
-- Dumping data for table `contact`
--

INSERT INTO `contact` (`c_id`, `name`, `email`, `message`) VALUES
(1, 'Jessica', 'jessica@gmail.com', 'Hi');

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `event_id` int NOT NULL,
  `org_id` int DEFAULT NULL,
  `u_id` int DEFAULT NULL,
  `event_name` varchar(199) DEFAULT NULL,
  `activity` varchar(499) NOT NULL,
  `date_start` date DEFAULT NULL,
  `date_end` date DEFAULT NULL,
  `time_start` time DEFAULT NULL,
  `time_end` time DEFAULT NULL,
  `venue` varchar(199) DEFAULT NULL,
  `event_image` varchar(199) DEFAULT NULL,
  `event_status` int NOT NULL,
  `events_date_posted` date DEFAULT NULL,
  `decline_reason` text DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0
);

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`event_id`, `org_id`, `u_id`, `event_name`, `activity`, `date_start`, `date_end`, `time_start`, `time_end`, `venue`, `event_image`, `event_status`, `events_date_posted`) VALUES
(2, 1, 5, 'Christmas Tree Lighting', 'asdasdasdasd\r\n\r\n\r\nasdasd', '2026-03-31', '2026-03-31', '16:29:00', '23:30:00', 'S301', '', 1, '2025-08-22'),
(3, 2, 5, 'New Year', 'WAAAAAAAAAAAAAAHHHHHHHHHHHH', '2026-04-15', '2027-01-17', '18:00:00', '20:59:00', 'sa labas', '', 1, '2025-12-22'),
(4, NULL, 5, 'Random Event', 'Random event lang ngani, Random event lang ngani, Random event lang ngani, Random event lang ngani, Random event lang ngani, Random event lang ngani, Random event lang ngani, Random event lang ngani, Random event lang ngani, Random event lang ngani, ', '2025-11-23', '2025-11-23', '13:57:00', '13:57:00', 'Room 301, Main Building', NULL, 1, '2025-11-23'),
(5, NULL, 1, 'Sample Event', 'ASasldkjhaslkdjhaskld\r\n\r\nasldjhaksjdhlaksjdhkalsjd\r\n\r\nlkasjdhlaksjdhlaksjdhlkjasdh', '2026-02-07', '2026-02-07', '08:29:00', '21:30:00', 'Carmona, Cavite', 'event_1770469257_6987378969864.jpg', 1, NULL),
(6, NULL, 1, 'Sample Event', 'sample ngani', '2026-03-30', '2026-03-31', '22:23:00', '23:23:00', 'Room 301, IT building', 'event_1774362259_69c29e93d37b6.jpg', 1, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `org_id` int NOT NULL,
  `org_name` varchar(199) NOT NULL,
  `org_logo` varchar(199) DEFAULT NULL
);

--
-- Dumping data for table `organizations`
--

INSERT INTO `organizations` (`org_id`, `org_name`, `org_logo`) VALUES
(1, 'Organization Sample 1', ''),
(2, 'Organization Sample 2', ''),
(3, 'Organization Sample 3', ''),
(4, 'Organization Sample 4', ''),
(5, 'Organization Sample 5', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_type`
--

CREATE TABLE `user_type` (
  `ut_id` int NOT NULL,
  `categories` varchar(199) NOT NULL
);

--
-- Dumping data for table `user_type`
--

INSERT INTO `user_type` (`ut_id`, `categories`) VALUES
(1, 'Admin'),
(2, 'Dean'),
(3, 'Organization Member'),
(4, 'Student');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accounts`
--
ALTER TABLE `accounts`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `sch_id` (`sch_id`),
  ADD KEY `fk_ut_id` (`ut_id`);

--
-- Indexes for table `announcement`
--
ALTER TABLE `announcement`
  ADD PRIMARY KEY (`a_id`);

--
-- Indexes for table `contact`
--
ALTER TABLE `contact`
  ADD PRIMARY KEY (`c_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`org_id`);

--
-- Indexes for table `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`ut_id`),
  ADD KEY `fk_user_id` (`categories`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accounts`
--
ALTER TABLE `accounts`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `announcement`
--
ALTER TABLE `announcement`
  MODIFY `a_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `contact`
--
ALTER TABLE `contact`
  MODIFY `c_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `event_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `org_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `accounts`
--
ALTER TABLE `accounts`
  ADD CONSTRAINT `fk_ut_id` FOREIGN KEY (`ut_id`) REFERENCES `user_type` (`ut_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
