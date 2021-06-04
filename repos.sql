-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 04, 2021 at 04:17 PM
-- Server version: 5.7.34-0ubuntu0.18.04.1
-- PHP Version: 7.4.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `repositorios_ampliffy`
--

-- --------------------------------------------------------

--
-- Table structure for table `repos`
--

CREATE TABLE `repos` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `parents` json NOT NULL,
  `children` json NOT NULL,
  `last_update` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `repos`
--

INSERT INTO `repos` (`id`, `name`, `parents`, `children`, `last_update`) VALUES
(1, 'ampliffy/lib-2', '[\"ampliffy/proyecto-1\", \"ampliffy/proyecto-2\"]', '[\"ampliffy/proyecto-4\"]', '2021-06-04 16:14:48');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `repos`
--
ALTER TABLE `repos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`),
  ADD KEY `name_2` (`name`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `repos`
--
ALTER TABLE `repos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
