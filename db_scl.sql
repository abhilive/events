-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 28, 2016 at 07:30 PM
-- Server version: 10.1.14-MariaDB-1~trusty
-- PHP Version: 7.0.6-13+donate.sury.org~trusty+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_scl`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_user`
--

CREATE TABLE `admin_user` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `admin_user`
--

INSERT INTO `admin_user` (`id`, `username`, `password`) VALUES
(1, 'admin', 'password');

-- --------------------------------------------------------

--
-- Table structure for table `group_type`
--

CREATE TABLE `group_type` (
  `id` int(11) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `group_type`
--

INSERT INTO `group_type` (`id`, `name`) VALUES
(1, 'Regional'),
(2, 'Group'),
(3, 'Individual');

-- --------------------------------------------------------

--
-- Table structure for table `location`
--

CREATE TABLE `location` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `location`
--

INSERT INTO `location` (`id`, `name`) VALUES
(1, 'Nagpur'),
(2, 'Mohali'),
(3, 'Dehradun');

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `id` int(11) NOT NULL,
  `name` varchar(300) NOT NULL,
  `email` varchar(200) NOT NULL,
  `group_id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `status_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`id`, `name`, `email`, `group_id`, `location_id`, `status_id`) VALUES
(1, 'Samrat & Group', '', 1, 1, 0),
(2, 'Gurpreet Singh & Team', '', 1, 2, 0),
(3, 'Devika & Bhavna', '', 1, 1, 0),
(4, 'Vishal & Team', '', 1, 2, 0),
(5, 'Surbhi & Group', '', 1, 1, 0),
(8, 'Rubal, Mrunal, Vivek', 'hr@sdn.net', 2, 1, 0),
(9, 'Alok Dubey, Tejaswini, Sujit, Akbar', 'hr@sdn.net', 2, 1, 0),
(10, 'Arpit, Ashutosh, Sapna, Manisha, Punnet', 'hr@sdn.net', 2, 1, 0),
(11, 'Yash Raj, Piyush C, Shubham B', 'hr@sdn.net', 2, 1, 0),
(12, 'BD Boys', 'hr@sdn.net', 2, 1, 0),
(13, 'Abhishek &amp; Group', 'hr@sdn.net', 2, 1, 0),
(14, 'Rishabh Bhardwaj, Sahil Salgotra', 'hr@sdm.net', 2, 2, 0),
(15, 'Gaurav Bajaj, Ravnit Suri', 'hr@sdm.net', 2, 2, 0),
(16, 'Alpna Gargi, Mamta', 'hr@sdm.com', 2, 2, 0),
(17, 'Neha Sharma, Anjali Sharma', 'hr@sdm.net', 2, 2, 0),
(18, 'Ankush Manocha, Shubham Chauhan', 'hr@sdm.net', 2, 2, 0),
(19, 'Gagandeep Singh, Parvati, Amit Gupta, Monika, Tarun Sahani, Ajay Grover', 'hr@sdm.net', 2, 2, 0),
(20, 'Raj Kumar, Susheel Rawat', 'hr@sdm.net', 2, 2, 0),
(21, 'Aditya, Lokesh', 'hr@sdn.net', 1, 1, 0),
(22, 'Inderjit & Team', 'hr@sdm.net', 1, 2, 0),
(23, 'Jagdeep Singh &amp; Team', 'hr@sdm.net', 1, 2, 0),
(24, 'Hemant Khandait', 'hr@sdm.net', 3, 1, 0),
(25, 'Bhavna Batra', 'hr@sdm.net', 3, 2, 0),
(26, 'Geetika Jagota', 'hr@sdm.net', 3, 2, 0),
(27, 'K.Santosh', 'hr@sdm.net', 3, 2, 0),
(28, 'Davinder Pal (MS)', 'hr@sdm.net', 3, 2, 0),
(29, 'Tarun', 'hr@sdm.net', 3, 2, 0),
(30, 'Amit Kumar', 'hr@sdm.net', 3, 2, 0),
(31, 'Lav Singh', 'hr@sdm.net', 3, 2, 0),
(32, 'Akshay Mahajan', 'hr@sdn.net', 3, 1, 0),
(33, 'Kapil Sharma', 'hr@sdn.net', 3, 1, 0),
(34, 'Kartik Bhave', 'hr@sdn.net', 3, 1, 0),
(35, 'Pawan Sharma', 'hr@sdn.net', 3, 1, 0),
(36, 'Ankur Arora', 'hr@sdn.net', 3, 1, 0),
(37, 'Muneer', 'hr@sdn.net', 3, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `statuses`
--

CREATE TABLE `statuses` (
  `id` int(11) NOT NULL,
  `title` varchar(80) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `statuses`
--

INSERT INTO `statuses` (`id`, `title`) VALUES
(0, 'Garbage Entry'),
(1, 'Disqualified'),
(2, 'Confirmed for Branch Semi Finals'),
(3, 'Confirmed for Branch Finals'),
(4, 'Confirmed for Finals'),
(5, 'Wildcard Entry');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_user`
--
ALTER TABLE `admin_user`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `group_type`
--
ALTER TABLE `group_type`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `location`
--
ALTER TABLE `location`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `group` (`group_id`),
  ADD KEY `location` (`location_id`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `statuses`
--
ALTER TABLE `statuses`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_user`
--
ALTER TABLE `admin_user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `group_type`
--
ALTER TABLE `group_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `location`
--
ALTER TABLE `location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`group_id`) REFERENCES `group_type` (`id`),
  ADD CONSTRAINT `participants_ibfk_2` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`),
  ADD CONSTRAINT `participants_ibfk_3` FOREIGN KEY (`status_id`) REFERENCES `statuses` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
