-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2026 at 06:06 AM
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
-- Database: `mentalhealthsystemdatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

CREATE TABLE `appointment` (
  `AppointmentID` int(11) NOT NULL,
  `PatientID` int(11) NOT NULL,
  `TherapistID` int(11) NOT NULL,
  `AppointmentDate` date NOT NULL,
  `AppointmentTime` time NOT NULL,
  `DurationMinutes` int(11) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `NotesForSession` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`AppointmentID`, `PatientID`, `TherapistID`, `AppointmentDate`, `AppointmentTime`, `DurationMinutes`, `Status`, `NotesForSession`) VALUES
(1, 1, 1, '2026-11-10', '10:30:00', 50, 'Booked', NULL),
(2, 1, 1, '2026-11-10', '10:00:00', 50, 'Completed', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `moodtracker`
--

CREATE TABLE `moodtracker` (
  `MoodEntryID` int(11) NOT NULL,
  `PatientID` int(11) NOT NULL,
  `MoodDate` date NOT NULL,
  `MoodScore` int(11) NOT NULL,
  `EnergyLevel` int(11) DEFAULT NULL,
  `SleepHours` decimal(3,1) DEFAULT NULL,
  `TriggerEvents` varchar(200) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `patient`
--

CREATE TABLE `patient` (
  `PatientID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Gender` varchar(10) DEFAULT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `Address` varchar(200) DEFAULT NULL,
  `EmergencyContactName` varchar(100) DEFAULT NULL,
  `EmergencyContactPhone` varchar(15) DEFAULT NULL,
  `AnonymousID` varchar(50) DEFAULT NULL,
  `RegistrationDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `patient`
--

INSERT INTO `patient` (`PatientID`, `FirstName`, `LastName`, `DateOfBirth`, `Gender`, `Email`, `PhoneNumber`, `Address`, `EmergencyContactName`, `EmergencyContactPhone`, `AnonymousID`, `RegistrationDate`) VALUES
(1, 'Amara', 'Smith', '2003-05-12', NULL, 'amara@su.edu', NULL, NULL, NULL, NULL, 'ANON-67664ada-dd2b-11f1-91ef-0a002700000a', '2026-11-10');

--
-- Triggers `patient`
--
DELIMITER $$
CREATE TRIGGER `trg_patient_before_insert` BEFORE INSERT ON `patient` FOR EACH ROW BEGIN
    IF NEW.AnonymousID IS NULL THEN
        SET NEW.AnonymousID = CONCAT('ANON-', UUID());
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `reminder`
--

CREATE TABLE `reminder` (
  `ReminderID` int(11) NOT NULL,
  `AppointmentID` int(11) NOT NULL,
  `RemainderDateTime` datetime NOT NULL,
  `Channel` varchar(10) NOT NULL,
  `SentStatus` varchar(20) NOT NULL,
  `Message` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessionnote`
--

CREATE TABLE `sessionnote` (
  `SessionNoteID` int(11) NOT NULL,
  `AppointmentID` int(11) NOT NULL,
  `TherapistID` int(11) NOT NULL,
  `NoteContent` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL,
  `IsConfidential` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sessionnote`
--

INSERT INTO `sessionnote` (`SessionNoteID`, `AppointmentID`, `TherapistID`, `NoteContent`, `CreatedDate`, `IsConfidential`) VALUES
(1, 2, 1, 'Student reported feeling overwhelmed by upcoming exams. We discussed time-blocking strategies and practiced a grounding exercise. Student will try the 5-4-3-2-1 technique before studying next week.', '2026-11-10 22:23:48', 0);

-- --------------------------------------------------------

--
-- Table structure for table `therapist`
--

CREATE TABLE `therapist` (
  `TherapistID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Specialization` varchar(100) DEFAULT NULL,
  `LicenseNumber` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `HireDate` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `therapist`
--

INSERT INTO `therapist` (`TherapistID`, `FirstName`, `LastName`, `Specialization`, `LicenseNumber`, `Email`, `PhoneNumber`, `HireDate`) VALUES
(1, 'Maya', 'Chen', 'Anxiety & stress', 'SU-COUN-28491', 'm.chen@su.edu', NULL, '2024-01-15');

-- --------------------------------------------------------

--
-- Table structure for table `userlogin`
--

CREATE TABLE `userlogin` (
  `UserID` int(11) NOT NULL,
  `PatientID` int(11) DEFAULT NULL,
  `TherapistID` int(11) DEFAULT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` varchar(20) NOT NULL,
  `LastLogin` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `userlogin`
--

INSERT INTO `userlogin` (`UserID`, `PatientID`, `TherapistID`, `Username`, `PasswordHash`, `Role`, `LastLogin`) VALUES
(2, NULL, NULL, 'admin@su.edu', '$2y$10$SDrGhmhBBO2BiibfY6.8SOK.fS.sJPst/5OwUjbyPz.Gv/AklUcVy', 'Admin', '2026-11-10 21:55:19');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `appointment`
--
ALTER TABLE `appointment`
  ADD PRIMARY KEY (`AppointmentID`),
  ADD KEY `PatientID` (`PatientID`),
  ADD KEY `TherapistID` (`TherapistID`);

--
-- Indexes for table `moodtracker`
--
ALTER TABLE `moodtracker`
  ADD PRIMARY KEY (`MoodEntryID`),
  ADD KEY `PatientID` (`PatientID`);

--
-- Indexes for table `patient`
--
ALTER TABLE `patient`
  ADD PRIMARY KEY (`PatientID`),
  ADD UNIQUE KEY `Email` (`Email`),
  ADD UNIQUE KEY `AnonymousID` (`AnonymousID`);

--
-- Indexes for table `reminder`
--
ALTER TABLE `reminder`
  ADD PRIMARY KEY (`ReminderID`),
  ADD KEY `AppointmentID` (`AppointmentID`);

--
-- Indexes for table `sessionnote`
--
ALTER TABLE `sessionnote`
  ADD PRIMARY KEY (`SessionNoteID`),
  ADD UNIQUE KEY `AppointmentID` (`AppointmentID`),
  ADD KEY `TherapistID` (`TherapistID`);

--
-- Indexes for table `therapist`
--
ALTER TABLE `therapist`
  ADD PRIMARY KEY (`TherapistID`),
  ADD UNIQUE KEY `LicenseNumber` (`LicenseNumber`),
  ADD UNIQUE KEY `Email` (`Email`);

--
-- Indexes for table `userlogin`
--
ALTER TABLE `userlogin`
  ADD PRIMARY KEY (`UserID`),
  ADD UNIQUE KEY `Username` (`Username`),
  ADD UNIQUE KEY `PatientID` (`PatientID`),
  ADD UNIQUE KEY `TherapistID` (`TherapistID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `moodtracker`
--
ALTER TABLE `moodtracker`
  MODIFY `MoodEntryID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `PatientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reminder`
--
ALTER TABLE `reminder`
  MODIFY `ReminderID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `sessionnote`
--
ALTER TABLE `sessionnote`
  MODIFY `SessionNoteID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `therapist`
--
ALTER TABLE `therapist`
  MODIFY `TherapistID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `userlogin`
--
ALTER TABLE `userlogin`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patient` (`PatientID`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`TherapistID`) REFERENCES `therapist` (`TherapistID`) ON DELETE CASCADE;

--
-- Constraints for table `moodtracker`
--
ALTER TABLE `moodtracker`
  ADD CONSTRAINT `moodtracker_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patient` (`PatientID`) ON DELETE CASCADE;

--
-- Constraints for table `reminder`
--
ALTER TABLE `reminder`
  ADD CONSTRAINT `reminder_ibfk_1` FOREIGN KEY (`AppointmentID`) REFERENCES `appointment` (`AppointmentID`) ON DELETE CASCADE;

--
-- Constraints for table `sessionnote`
--
ALTER TABLE `sessionnote`
  ADD CONSTRAINT `sessionnote_ibfk_1` FOREIGN KEY (`AppointmentID`) REFERENCES `appointment` (`AppointmentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `sessionnote_ibfk_2` FOREIGN KEY (`TherapistID`) REFERENCES `therapist` (`TherapistID`) ON DELETE CASCADE;

--
-- Constraints for table `userlogin`
--
ALTER TABLE `userlogin`
  ADD CONSTRAINT `userlogin_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patient` (`PatientID`) ON DELETE CASCADE,
  ADD CONSTRAINT `userlogin_ibfk_2` FOREIGN KEY (`TherapistID`) REFERENCES `therapist` (`TherapistID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
