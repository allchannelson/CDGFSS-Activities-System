-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 11, 2016 at 11:54 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `activity_prototype`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_student`
--

CREATE TABLE `activity_student` (
  `activity_index` int(10) UNSIGNED NOT NULL,
  `student_index` int(10) UNSIGNED NOT NULL,
  `student_enrollment_year` int(4) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_student`
--
ALTER TABLE `activity_student`
  ADD PRIMARY KEY (`activity_index`,`student_index`,`student_enrollment_year`),
  ADD KEY `Student_FK` (`student_index`,`student_enrollment_year`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_student`
--
ALTER TABLE `activity_student`
  ADD CONSTRAINT `Activity_FK` FOREIGN KEY (`activity_index`) REFERENCES `activity` (`activity_index`),
  ADD CONSTRAINT `Student_FK` FOREIGN KEY (`student_index`,`student_enrollment_year`) REFERENCES `student_yearly_info` (`student_index`, `enrollment_year`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
