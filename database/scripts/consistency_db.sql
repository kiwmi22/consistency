-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1 
-- Generation Time: Apr 27, 2026 at 12:34 PM
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
-- Database: `consistency_db`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_AddHabit` (IN `p_UserID` INT, IN `p_HabitName` VARCHAR(50), IN `p_Description` VARCHAR(255), IN `p_TargetPerWeek` INT, IN `p_CreatedDate` DATE)   BEGIN
    INSERT INTO tblHabits (UserID, HabitName, Description, TargetPerWeek, IsActive, CreatedDate)
    VALUES (p_UserID, p_HabitName, p_Description, p_TargetPerWeek, TRUE, p_CreatedDate);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_DeleteHabit` (IN `p_HabitID` INT)   BEGIN
    UPDATE tblHabits
    SET IsActive = FALSE
    WHERE HabitID = p_HabitID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_FindHabitByName` (IN `p_UserID` INT, IN `p_HabitName` VARCHAR(50))   BEGIN
    SELECT * FROM tblHabits
    WHERE UserID = p_UserID
    AND HabitName LIKE CONCAT('%', p_HabitName, '%')
    AND IsActive = TRUE;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_GetHabitsByUser` (IN `p_UserID` INT)   BEGIN
    SELECT * FROM tblHabits
    WHERE UserID = p_UserID AND IsActive = TRUE
    ORDER BY CreatedDate ASC;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `sp_UpdateHabit` (IN `p_HabitID` INT, IN `p_HabitName` VARCHAR(50), IN `p_Description` VARCHAR(255), IN `p_TargetPerWeek` INT)   BEGIN
    UPDATE tblHabits
    SET HabitName = p_HabitName,
        Description = p_Description,
        TargetPerWeek = p_TargetPerWeek
    WHERE HabitID = p_HabitID;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `tblhabits`
--

CREATE TABLE `tblhabits` (
  `HabitID` int(11) NOT NULL,
  `UserID` int(11) NOT NULL,
  `HabitName` varchar(50) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  `TargetPerWeek` int(11) NOT NULL,
  `IsActive` tinyint(1) DEFAULT 1,
  `CreatedDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblhabits`
--

INSERT INTO `tblhabits` (`HabitID`, `UserID`, `HabitName`, `Description`, `TargetPerWeek`, `IsActive`, `CreatedDate`) VALUES
(1, 1, 'Morning Run Updated', 'Run 10km every morning', 6, 1, '2026-04-21'),
(2, 1, 'Read a Book', 'Read at least 20 pages', 7, 0, '2026-04-21'),
(3, 1, 'Drink Water', 'Drink 2 litres of water daily', 7, 1, '2026-04-22'),
(4, 2, 'Meditate', '10 minutes of meditation', 5, 1, '2026-04-22'),
(5, 2, 'Exercise', '30 minutes gym session', 3, 1, '2026-04-22'),
(6, 1, 'Journal Writing', 'Write 1 page every day', 7, 1, '2026-04-24');

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `UserID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `IsAdmin` tinyint(1) DEFAULT 0,
  `DateCreated` date NOT NULL,
  `IsActive` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`UserID`, `Username`, `Email`, `PasswordHash`, `IsAdmin`, `DateCreated`, `IsActive`) VALUES
(1, 'sashi_test', 'sashi@test.com', 'hashedpassword1', 0, '2026-04-21', 1),
(2, 'abin_test', 'abin@test.com', 'hashedpassword2', 0, '2026-04-21', 1),
(3, 'admin_user', 'admin@test.com', 'hashedpassword3', 1, '2026-04-21', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tblhabits`
--
ALTER TABLE `tblhabits`
  ADD PRIMARY KEY (`HabitID`),
  ADD KEY `fk_habits_user` (`UserID`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tblhabits`
--
ALTER TABLE `tblhabits`
  MODIFY `HabitID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tblhabits`
--
ALTER TABLE `tblhabits`
  ADD CONSTRAINT `fk_habits_user` FOREIGN KEY (`UserID`) REFERENCES `tblusers` (`UserID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
