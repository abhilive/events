-- phpMyAdmin SQL Dump
-- version 4.6.0
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jul 01, 2016 at 06:55 PM
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
(1, 'Samrat & Group', '', 1, 1, 3),
(2, 'Gurpreet Singh & Team', '', 1, 2, 3),
(3, 'Devika & Bhavna', '', 1, 1, 3),
(4, 'Vishal & Team', '', 1, 2, 3),
(5, 'Surbhi & Group', '', 1, 1, 3),
(8, 'Rubal, Mrunal, Vivek', 'hr@sdn.net', 2, 1, 3),
(9, 'Alok Dubey, Tejaswini, Sujit, Akbar', 'hr@sdn.net', 2, 1, 3),
(10, 'Arpit, Ashutosh, Sapna, Manisha, Punnet', 'hr@sdn.net', 2, 1, 3),
(11, 'Yash Raj, Piyush C, Shubham B', 'hr@sdn.net', 2, 1, 3),
(12, 'BD Boys', 'hr@sdn.net', 2, 1, 3),
(13, 'Abhishek &amp; Group', 'hr@sdn.net', 2, 1, 3),
(14, 'Rishabh Bhardwaj, Sahil Salgotra', 'hr@sdm.net', 2, 2, 3),
(15, 'Gaurav Bajaj, Ravnit Suri', 'hr@sdm.net', 2, 2, 3),
(16, 'Alpna Gargi, Mamta', 'hr@sdm.com', 2, 2, 3),
(17, 'Neha Sharma, Anjali Sharma', 'hr@sdm.net', 2, 2, 3),
(18, 'Ankush Manocha, Shubham Chauhan', 'hr@sdm.net', 2, 2, 3),
(19, 'Gagandeep Singh, Parvati, Amit Gupta, Monika, Tarun Sahani, Ajay Grover', 'hr@sdm.net', 2, 2, 3),
(20, 'Raj Kumar, Susheel Rawat', 'hr@sdm.net', 2, 2, 3),
(21, 'Aditya, Lokesh', 'hr@sdn.net', 1, 1, 3),
(22, 'Inderjit & Team', 'hr@sdm.net', 1, 2, 3),
(23, 'Jagdeep Singh &amp; Team', 'hr@sdm.net', 1, 2, 3),
(24, 'Hemant Khandait', 'hr@sdm.net', 3, 1, 3),
(25, 'Bhavna Batra', 'hr@sdm.net', 3, 2, 3),
(26, 'Geetika Jagota', 'hr@sdm.net', 3, 2, 3),
(27, 'K.Santosh', 'hr@sdm.net', 3, 2, 3),
(28, 'Davinder Pal (MS)', 'hr@sdm.net', 3, 2, 3),
(29, 'Tarun', 'hr@sdm.net', 3, 2, 3),
(30, 'Amit Kumar', 'hr@sdm.net', 3, 2, 3),
(31, 'Lav Singh', 'hr@sdm.net', 3, 2, 3),
(32, 'Akshay Mahajan', 'hr@sdn.net', 3, 1, 3),
(33, 'Kapil Sharma', 'hr@sdn.net', 3, 1, 3),
(34, 'Kartik Bhave', 'hr@sdn.net', 3, 1, 3),
(35, 'Pawan Sharma', 'hr@sdn.net', 3, 1, 3),
(36, 'Ankur Arora', 'hr@sdn.net', 3, 1, 3),
(37, 'Muneer', 'hr@sdn.net', 3, 1, 3),
(38, 'Varun Yadav', 'vy@smartdatainc.net', 2, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `photos`
--

CREATE TABLE `photos` (
  `id` int(11) NOT NULL,
  `under` int(11) NOT NULL,
  `full` text NOT NULL,
  `thumbnail` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `photos`
--

INSERT INTO `photos` (`id`, `under`, `full`, `thumbnail`) VALUES
(5, 1, 'IMG_6413.JPG', 'IMG_6413.JPG'),
(6, 1, 'IMG_6519.JPG', 'IMG_6519.JPG'),
(7, 1, 'IMG_6476.JPG', 'IMG_6476.JPG'),
(8, 1, 'IMG_6510.JPG', 'IMG_6510.JPG'),
(9, 1, 'IMG_6503.JPG', 'IMG_6503.JPG'),
(10, 1, 'IMG_6603.JPG', 'IMG_6603.JPG'),
(11, 1, 'IMG_6591.JPG', 'IMG_6591.JPG'),
(12, 1, 'IMG_6581.JPG', 'IMG_6581.JPG'),
(13, 1, 'IMG_6380.JPG', 'IMG_6380.JPG'),
(14, 1, 'IMG_6540.JPG', 'IMG_6540.JPG'),
(15, 1, 'IMG_6458.JPG', 'IMG_6458.JPG'),
(16, 1, 'IMG_6537.JPG', 'IMG_6537.JPG'),
(17, 1, 'IMG_6401.JPG', 'IMG_6401.JPG'),
(18, 1, 'IMG_6520.JPG', 'IMG_6520.JPG'),
(19, 1, 'IMG_6383.JPG', 'IMG_6383.JPG'),
(20, 1, 'IMG_6463.JPG', 'IMG_6463.JPG'),
(21, 1, 'IMG_6405.JPG', 'IMG_6405.JPG'),
(22, 1, 'IMG_6571.JPG', 'IMG_6571.JPG'),
(23, 1, 'IMG_6580.JPG', 'IMG_6580.JPG'),
(24, 1, 'IMG_6429.JPG', 'IMG_6429.JPG'),
(25, 1, 'IMG_6505.JPG', 'IMG_6505.JPG'),
(26, 1, 'IMG_6511.JPG', 'IMG_6511.JPG'),
(27, 1, 'IMG_6499.JPG', 'IMG_6499.JPG'),
(28, 1, 'IMG_6445.JPG', 'IMG_6445.JPG'),
(29, 1, 'IMG_6403.JPG', 'IMG_6403.JPG'),
(30, 1, 'IMG_6462.JPG', 'IMG_6462.JPG'),
(31, 1, 'IMG_6391.JPG', 'IMG_6391.JPG'),
(32, 1, 'IMG_6509.JPG', 'IMG_6509.JPG'),
(33, 1, 'IMG_6587.JPG', 'IMG_6587.JPG'),
(34, 1, 'IMG_6382.JPG', 'IMG_6382.JPG'),
(35, 1, 'IMG_6426.JPG', 'IMG_6426.JPG'),
(36, 1, 'IMG_6460.JPG', 'IMG_6460.JPG'),
(37, 1, 'IMG_6474.JPG', 'IMG_6474.JPG'),
(38, 1, 'IMG_6508.JPG', 'IMG_6508.JPG'),
(39, 1, 'IMG_6577.JPG', 'IMG_6577.JPG'),
(40, 1, 'IMG_6544.JPG', 'IMG_6544.JPG'),
(41, 1, 'IMG_6384.JPG', 'IMG_6384.JPG'),
(42, 1, 'IMG_6575.JPG', 'IMG_6575.JPG'),
(43, 1, 'IMG_6490.JPG', 'IMG_6490.JPG'),
(44, 1, 'IMG_6419.JPG', 'IMG_6419.JPG'),
(45, 1, 'IMG_6485.JPG', 'IMG_6485.JPG'),
(46, 1, 'IMG_6567.JPG', 'IMG_6567.JPG'),
(47, 1, 'IMG_6464.JPG', 'IMG_6464.JPG'),
(48, 1, 'IMG_6539.JPG', 'IMG_6539.JPG'),
(49, 1, 'IMG_6514.JPG', 'IMG_6514.JPG'),
(50, 1, 'IMG_6390.JPG', 'IMG_6390.JPG'),
(51, 1, 'IMG_6561.JPG', 'IMG_6561.JPG'),
(52, 1, 'IMG_6443.JPG', 'IMG_6443.JPG'),
(53, 1, 'IMG_6422.JPG', 'IMG_6422.JPG'),
(54, 1, 'IMG_6564.JPG', 'IMG_6564.JPG'),
(55, 1, 'IMG_6444.JPG', 'IMG_6444.JPG'),
(56, 1, 'IMG_6416.JPG', 'IMG_6416.JPG'),
(57, 1, 'IMG_6550.JPG', 'IMG_6550.JPG'),
(58, 1, 'IMG_6477.JPG', 'IMG_6477.JPG'),
(59, 1, 'IMG_6582.JPG', 'IMG_6582.JPG'),
(60, 1, 'IMG_6558.JPG', 'IMG_6558.JPG'),
(61, 1, 'IMG_6526.JPG', 'IMG_6526.JPG'),
(62, 1, 'IMG_6493.JPG', 'IMG_6493.JPG'),
(63, 1, 'IMG_6478.JPG', 'IMG_6478.JPG'),
(64, 1, 'IMG_6487.JPG', 'IMG_6487.JPG'),
(65, 1, 'IMG_6486.JPG', 'IMG_6486.JPG'),
(66, 1, 'IMG_6388.JPG', 'IMG_6388.JPG'),
(67, 1, 'IMG_6409.JPG', 'IMG_6409.JPG'),
(68, 1, 'IMG_6369.JPG', 'IMG_6369.JPG'),
(69, 1, 'IMG_6569.JPG', 'IMG_6569.JPG'),
(70, 1, 'IMG_6483.JPG', 'IMG_6483.JPG'),
(71, 1, 'IMG_6592.JPG', 'IMG_6592.JPG'),
(72, 1, 'IMG_6570.JPG', 'IMG_6570.JPG'),
(73, 1, 'IMG_6354.JPG', 'IMG_6354.JPG'),
(74, 1, 'IMG_6410.JPG', 'IMG_6410.JPG'),
(75, 1, 'IMG_6436.JPG', 'IMG_6436.JPG'),
(76, 1, 'IMG_6461.JPG', 'IMG_6461.JPG'),
(77, 1, 'IMG_6579.JPG', 'IMG_6579.JPG'),
(78, 1, 'IMG_6594.JPG', 'IMG_6594.JPG'),
(79, 1, 'IMG_6432.JPG', 'IMG_6432.JPG'),
(80, 1, 'IMG_6482.JPG', 'IMG_6482.JPG'),
(81, 1, 'IMG_6387.JPG', 'IMG_6387.JPG'),
(82, 1, 'IMG_6420.JPG', 'IMG_6420.JPG'),
(83, 1, 'IMG_6424.JPG', 'IMG_6424.JPG'),
(84, 1, 'IMG_6601.JPG', 'IMG_6601.JPG'),
(85, 1, 'IMG_6374.JPG', 'IMG_6374.JPG'),
(86, 1, 'IMG_6496.JPG', 'IMG_6496.JPG'),
(87, 1, 'IMG_6521.JPG', 'IMG_6521.JPG'),
(88, 1, 'IMG_6584.JPG', 'IMG_6584.JPG'),
(89, 1, 'IMG_6375.JPG', 'IMG_6375.JPG'),
(90, 1, 'IMG_6563.JPG', 'IMG_6563.JPG'),
(91, 1, 'IMG_6449.JPG', 'IMG_6449.JPG');

-- --------------------------------------------------------

--
-- Table structure for table `photosnvideos`
--

CREATE TABLE `photosnvideos` (
  `id` int(11) NOT NULL,
  `location_id` int(11) NOT NULL,
  `type` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `photosnvideos`
--

INSERT INTO `photosnvideos` (`id`, `location_id`, `type`) VALUES
(1, 1, 'semifinals');

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
(2, 'Wildcard Entry'),
(3, 'Confirmed for Branch Semi Finals'),
(4, 'Confirmed for Finals'),
(5, 'Confirmed for Branch Finals');

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
-- Indexes for table `photos`
--
ALTER TABLE `photos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `under` (`under`);

--
-- Indexes for table `photosnvideos`
--
ALTER TABLE `photosnvideos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `location_id` (`location_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;
--
-- AUTO_INCREMENT for table `photos`
--
ALTER TABLE `photos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=92;
--
-- AUTO_INCREMENT for table `photosnvideos`
--
ALTER TABLE `photosnvideos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `statuses`
--
ALTER TABLE `statuses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
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

--
-- Constraints for table `photosnvideos`
--
ALTER TABLE `photosnvideos`
  ADD CONSTRAINT `fk_photos_location` FOREIGN KEY (`location_id`) REFERENCES `location` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
