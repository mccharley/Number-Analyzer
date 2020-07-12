-- phpMyAdmin SQL Dump
-- version 4.9.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 30, 2020 at 12:53 PM
-- Server version: 5.6.47-log
-- PHP Version: 7.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `analyzer_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `log`
--

CREATE TABLE `log` (
  `date` varchar(20) NOT NULL,
  `log_type` varchar(30) NOT NULL,
  `num_records` int(100) NOT NULL COMMENT 'number of records in pitch_list',
  `processed` int(11) NOT NULL COMMENT 'total records processed',
  `success_count` int(6) NOT NULL,
  `failed_count` int(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Holds records on each message push';

-- --------------------------------------------------------

--
-- Table structure for table `mobile_vd_log`
--

CREATE TABLE `mobile_vd_log` (
  `date` varchar(20) NOT NULL,
  `log_type` varchar(100) NOT NULL,
  `num_records` varchar(100) NOT NULL COMMENT 'number of records in pitch_list',
  `initiated` varchar(100) NOT NULL COMMENT 'total records processed',
  `pitch_list_count` varchar(100) NOT NULL COMMENT 'found on pitch list mobiles',
  `bad` varchar(100) NOT NULL COMMENT 'found invalid mobiles',
  `success_count` varchar(100) NOT NULL COMMENT 'successful removal',
  `failed_count` varchar(100) NOT NULL COMMENT 'failed removal'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Holds records on each message push';

-- --------------------------------------------------------

--
-- Table structure for table `num_head_segment`
--

CREATE TABLE `num_head_segment` (
  `header_segment` varchar(5) NOT NULL COMMENT '0x0xx',
  `operator_code` varchar(6) NOT NULL COMMENT 'refrence to operator table'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='table o';

--
-- Dumping data for table `num_head_segment`
--

INSERT INTO `num_head_segment` (`header_segment`, `operator_code`) VALUES
('0803', '62130'),
('0804', '62140'),
('0806', '62130'),
('0703', '62130'),
('0706', '62130'),
('0813', '62130'),
('0816', '62130'),
('0810', '62130'),
('0814', '62130'),
('0903', '62130'),
('0705', '62150'),
('0815', '62150'),
('0805', '62150'),
('0807', '62150'),
('0811', '62150'),
('0905', '62150'),
('0708', '62120'),
('0802', '62120'),
('0808', '62120'),
('0812', '62120'),
('0701', '62120'),
('0902', '62120'),
('0809', '62160'),
('0817', '62160'),
('0818', '62160'),
('0909', '62160'),
('0908', '62160'),
('0704', '62125'),
('07025', '62125'),
('07026', '62125');

-- --------------------------------------------------------

--
-- Table structure for table `operator`
--

CREATE TABLE `operator` (
  `SN` int(5) NOT NULL COMMENT 'serial number',
  `operator_name` varchar(15) NOT NULL COMMENT 'operator name',
  `operator_table_name` varchar(20) NOT NULL COMMENT 'operator table name',
  `operator_code` varchar(6) NOT NULL COMMENT 'op table refrence'
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='operator description table';

--
-- Dumping data for table `operator`
--

INSERT INTO `operator` (`SN`, `operator_name`, `operator_table_name`, `operator_code`) VALUES
(2, 'NTEL', 'pitch_list_ntel', '62140'),
(3, 'MTN', 'pitch_list_mtn', '62130'),
(4, 'ETISALAT', 'pitch_list_etisalat', '62160'),
(5, 'AIRTEL', 'pitch_list_airtel', '62120'),
(6, 'GLO', 'pitch_list_glo', '62150'),
(1, 'VISAFONE', 'pitch_list_visafone', '62125');

-- --------------------------------------------------------

--
-- Table structure for table `pitch_list`
--

CREATE TABLE `pitch_list` (
  `fname` varchar(15) NOT NULL,
  `lname` varchar(15) NOT NULL,
  `email` varchar(35) NOT NULL,
  `mobile` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='mobile contact data';

-- --------------------------------------------------------

--
-- Table structure for table `pitch_list_dirty`
--

CREATE TABLE `pitch_list_dirty` (
  `fname` varchar(15) NOT NULL,
  `lname` varchar(15) NOT NULL,
  `email` varchar(35) NOT NULL,
  `mobile` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='unmatched mobile contact data. update number segment and network operator data to allow for more accurate filtering';

-- --------------------------------------------------------

--
-- Table structure for table `pitch_list_dump`
--

CREATE TABLE `pitch_list_dump` (
  `fname` varchar(15) NOT NULL,
  `lname` varchar(15) NOT NULL,
  `email` varchar(35) NOT NULL,
  `mobile` varchar(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='campainge data to market';



-- --------------------------------------------------------
-- Indexes for table `num_head_segment`
--
ALTER TABLE `num_head_segment`
  ADD PRIMARY KEY (`header_segment`),
  ADD UNIQUE KEY `header_segment` (`header_segment`),
  ADD UNIQUE KEY `header_segment_2` (`header_segment`),
  ADD KEY `operator_code` (`operator_code`),
  ADD KEY `operator_code_2` (`operator_code`);

--
-- Indexes for table `operator`
--
ALTER TABLE `operator`
  ADD PRIMARY KEY (`operator_code`),
  ADD KEY `SN` (`SN`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `operator`
--
ALTER TABLE `operator`
  MODIFY `SN` int(5) NOT NULL AUTO_INCREMENT COMMENT 'serial number', AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
