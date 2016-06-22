--
-- Database: `activity_prototype`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity`
--

CREATE TABLE IF NOT EXISTS `activity` (
  `activity_index` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `teacher` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `unit` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name_english` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `name_chinese` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `time` time DEFAULT NULL,
  `partner_name_english` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `partner_name_chinese` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `destination` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`activity_index`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `activity_student`
--

CREATE TABLE IF NOT EXISTS `activity_student` (
  `activity_index` int(10) UNSIGNED NOT NULL,
  `student_index` int(10) UNSIGNED NOT NULL,
  `student_enrollment_year` int(4) UNSIGNED NOT NULL,
  PRIMARY KEY (`activity_index`,`student_index`,`student_enrollment_year`),
  KEY `Student_FK` (`student_index`,`student_enrollment_year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE IF NOT EXISTS `student` (
  `student_index` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `student_number` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `name_chinese` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `name_english` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `gender` char(1) COLLATE utf8_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL,
  PRIMARY KEY (`student_index`),
  UNIQUE KEY `student_index` (`student_index`)
) ENGINE=InnoDB AUTO_INCREMENT=903 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_yearly_info`
--

CREATE TABLE IF NOT EXISTS `student_yearly_info` (
  `student_index` int(10) UNSIGNED NOT NULL,
  `enrollment_year` int(4) UNSIGNED NOT NULL,
  `form` int(4) NOT NULL,
  `class` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  `class_number` int(2) DEFAULT NULL,
  `house` char(1) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`student_index`,`enrollment_year`),
  KEY `student_index` (`student_index`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_student`
--
ALTER TABLE `activity_student`
  ADD CONSTRAINT `Activity_FK` FOREIGN KEY (`activity_index`) REFERENCES `activity` (`activity_index`),
  ADD CONSTRAINT `Student_FK` FOREIGN KEY (`student_index`,`student_enrollment_year`) REFERENCES `student_yearly_info` (`student_index`, `enrollment_year`);

--
-- Constraints for table `student_yearly_info`
--
ALTER TABLE `student_yearly_info`
  ADD CONSTRAINT `ForeignKeyOnStudent_Index` FOREIGN KEY (`student_index`) REFERENCES `student` (`student_index`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
