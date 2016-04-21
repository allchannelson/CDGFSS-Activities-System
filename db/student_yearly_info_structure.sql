-- phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Apr 21, 2016 at 06:28 AM
-- Server version: 10.1.9-MariaDB
-- PHP Version: 7.0.5

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
-- Table structure for table `student_yearly_info`
--

CREATE TABLE `student_yearly_info` (
  `student_index` int(10) UNSIGNED NOT NULL,
  `enrollment_year` int(4) UNSIGNED NOT NULL,
  `form` int(4) NOT NULL,
  `class` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_number` int(2) DEFAULT NULL,
  `house` char(1) COLLATE utf8_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student_yearly_info`
--
ALTER TABLE `student_yearly_info`
  ADD PRIMARY KEY (`student_index`,`enrollment_year`),
  ADD KEY `student_index` (`student_index`);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_yearly_info`
--
ALTER TABLE `student_yearly_info`
  ADD CONSTRAINT `ForeignKeyOnStudent_Index` FOREIGN KEY (`student_index`) REFERENCES `student` (`student_index`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
