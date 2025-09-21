-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 21, 2025 at 05:14 PM
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
-- Database: `lab_equipment_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `available_equipment`
--

CREATE TABLE `available_equipment` (
  `Equipment_ID` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Available_Date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `available_equipment`
--

INSERT INTO `available_equipment` (`Equipment_ID`, `Name`, `Type`, `Quantity`, `Available_Date`) VALUES
(1, 'CUp', 'essential', 1, '2025-09-20'),
(2, 'Desktop PCs', 'Computer Science', 9, '2025-09-19'),
(4, 'Soldering Station', 'Electronics', 2, '2025-09-20'),
(5, 'Lathe Machine', 'Mechanical', 2, '2025-09-21'),
(6, 'Projector', 'General', 3, '2025-09-21'),
(7, '3D Printer', 'Fabrication', 1, '2025-09-22');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `available_equipment`
--
ALTER TABLE `available_equipment`
  ADD PRIMARY KEY (`Equipment_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `available_equipment`
--
ALTER TABLE `available_equipment`
  MODIFY `Equipment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
