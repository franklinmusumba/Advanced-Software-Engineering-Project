<<<<<<< HEAD
-- phpMyAdmin SQL Dump (Complete with Tables, Constraints & Triggers)
-- Database: `mentalhealthsystemdatabase`
=======
-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 11, 2026 at 06:06 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12
>>>>>>> 15e2dc3fc90b0750c7fda323f3c3f7e4f485f421

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- ==========================================
-- 1. MAIN TABLES
-- ==========================================

CREATE TABLE `appointments` (
  `AppointmentID` int(11) NOT NULL AUTO_INCREMENT,
  `PatientID` int(11) NOT NULL,
  `TherapistID` int(11) NOT NULL,
  `AppointmentDate` date NOT NULL,
  `AppointmentTime` time NOT NULL,
  `DurationMinutes` int(11) NOT NULL,
  `Status` varchar(20) NOT NULL,
  `NotesForSession` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`AppointmentID`),
  KEY `PatientID` (`PatientID`),
  KEY `TherapistID` (`TherapistID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
CREATE TABLE `moodtrackers` (
  `MoodEntryID` int(11) NOT NULL AUTO_INCREMENT,
=======
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
>>>>>>> 15e2dc3fc90b0750c7fda323f3c3f7e4f485f421
  `PatientID` int(11) NOT NULL,
  `MoodDate` date NOT NULL,
  `MoodScore` int(11) NOT NULL,
  `EnergyLevel` int(11) DEFAULT NULL,
  `SleepHours` decimal(3,1) DEFAULT NULL,
  `TriggerEvents` varchar(200) DEFAULT NULL,
  `Notes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`MoodEntryID`),
  KEY `PatientID` (`PatientID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `patients` (
  `PatientID` int(11) NOT NULL AUTO_INCREMENT,
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
  `RegistrationDate` date NOT NULL,
  PRIMARY KEY (`PatientID`),
  UNIQUE KEY `Email` (`Email`),
  UNIQUE KEY `AnonymousID` (`AnonymousID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
CREATE TABLE `reminders` (
  `ReminderID` int(11) NOT NULL AUTO_INCREMENT,
=======
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
>>>>>>> 15e2dc3fc90b0750c7fda323f3c3f7e4f485f421
  `AppointmentID` int(11) NOT NULL,
  `RemainderDateTime` datetime NOT NULL,
  `Channel` varchar(10) NOT NULL,
  `SentStatus` varchar(20) NOT NULL,
  `Message` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ReminderID`),
  KEY `AppointmentID` (`AppointmentID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `session_notes` (
  `SessionNoteID` int(11) NOT NULL AUTO_INCREMENT,
  `AppointmentID` int(11) NOT NULL,
  `TherapistID` int(11) NOT NULL,
  `NoteContent` varchar(255) NOT NULL,
  `CreatedDate` datetime NOT NULL,
  `IsConfidential` tinyint(1) NOT NULL,
  PRIMARY KEY (`SessionNoteID`),
  UNIQUE KEY `AppointmentID` (`AppointmentID`),
  KEY `TherapistID` (`TherapistID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
CREATE TABLE `therapists` (
  `TherapistID` int(11) NOT NULL AUTO_INCREMENT,
=======
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
>>>>>>> 15e2dc3fc90b0750c7fda323f3c3f7e4f485f421
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `Specialization` varchar(100) DEFAULT NULL,
  `LicenseNumber` varchar(50) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `PhoneNumber` varchar(15) DEFAULT NULL,
  `HireDate` date NOT NULL,
  PRIMARY KEY (`TherapistID`),
  UNIQUE KEY `LicenseNumber` (`LicenseNumber`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
CREATE TABLE `user_logins` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
=======
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
>>>>>>> 15e2dc3fc90b0750c7fda323f3c3f7e4f485f421
  `PatientID` int(11) DEFAULT NULL,
  `TherapistID` int(11) DEFAULT NULL,
  `Username` varchar(50) NOT NULL,
  `PasswordHash` varchar(255) NOT NULL,
  `Role` varchar(20) NOT NULL,
  `LastLogin` datetime DEFAULT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `Username` (`Username`),
  UNIQUE KEY `PatientID` (`PatientID`),
  UNIQUE KEY `TherapistID` (`TherapistID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

<<<<<<< HEAD
-- ==========================================
-- 2. AUDIT & STATS TABLES (Needed for Triggers)
-- ==========================================
=======
--
-- Dumping data for table `userlogin`
--

INSERT INTO `userlogin` (`UserID`, `PatientID`, `TherapistID`, `Username`, `PasswordHash`, `Role`, `LastLogin`) VALUES
(2, NULL, NULL, 'admin@su.edu', '$2y$10$SDrGhmhBBO2BiibfY6.8SOK.fS.sJPst/5OwUjbyPz.Gv/AklUcVy', 'Admin', '2026-11-10 21:55:19');

--
-- Indexes for dumped tables
--
>>>>>>> 15e2dc3fc90b0750c7fda323f3c3f7e4f485f421

CREATE TABLE `SessionNoteAudit` (
  `AuditID` int(11) NOT NULL AUTO_INCREMENT,
  `SessionNoteID` int(11) DEFAULT NULL,
  `TherapistID` int(11) DEFAULT NULL,
  `ActionTaken` varchar(50) DEFAULT NULL,
  `AuditTimestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`AuditID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `PatientArchive` (
  `ArchiveID` int(11) NOT NULL AUTO_INCREMENT,
  `PatientID` int(11) DEFAULT NULL,
  `FirstName` varchar(50) DEFAULT NULL,
  `LastName` varchar(50) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `DeletedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ArchiveID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `CounsellorStats` (
  `TherapistID` int(11) NOT NULL,
  `TotalAppointments` int(11) DEFAULT 0,
  PRIMARY KEY (`TherapistID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE `AppointmentStatusAudit` (
  `AuditID` int(11) NOT NULL AUTO_INCREMENT,
  `AppointmentID` int(11) DEFAULT NULL,
  `OldStatus` varchar(20) DEFAULT NULL,
  `NewStatus` varchar(20) DEFAULT NULL,
  `ChangedAt` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`AuditID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ==========================================
-- 3. FOREIGN KEY CONSTRAINTS
-- ==========================================

ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`TherapistID`) REFERENCES `therapists` (`TherapistID`) ON DELETE CASCADE;

ALTER TABLE `moodtrackers`
  ADD CONSTRAINT `moodtrackers_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE;

ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`AppointmentID`) REFERENCES `appointments` (`AppointmentID`) ON DELETE CASCADE;

<<<<<<< HEAD
ALTER TABLE `session_notes`
  ADD CONSTRAINT `session_notes_ibfk_1` FOREIGN KEY (`AppointmentID`) REFERENCES `appointments` (`AppointmentID`) ON DELETE CASCADE,
  ADD CONSTRAINT `session_notes_ibfk_2` FOREIGN KEY (`TherapistID`) REFERENCES `therapists` (`TherapistID`) ON DELETE CASCADE;
=======
--
-- AUTO_INCREMENT for table `appointment`
--
ALTER TABLE `appointment`
  MODIFY `AppointmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
>>>>>>> 15e2dc3fc90b0750c7fda323f3c3f7e4f485f421

ALTER TABLE `user_logins`
  ADD CONSTRAINT `user_logins_ibfk_1` FOREIGN KEY (`PatientID`) REFERENCES `patients` (`PatientID`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_logins_ibfk_2` FOREIGN KEY (`TherapistID`) REFERENCES `therapists` (`TherapistID`) ON DELETE CASCADE;

<<<<<<< HEAD
-- ==========================================
-- 4. TRIGGERS
-- ==========================================
=======
--
-- AUTO_INCREMENT for table `patient`
--
ALTER TABLE `patient`
  MODIFY `PatientID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
>>>>>>> 15e2dc3fc90b0750c7fda323f3c3f7e4f485f421

DELIMITER $$

<<<<<<< HEAD
-- Trigger 1: Auto-generate AnonymousID
CREATE TRIGGER `trg_patient_before_insert` 
BEFORE INSERT ON `patients` 
FOR EACH ROW 
BEGIN
    IF NEW.AnonymousID IS NULL THEN
        SET NEW.AnonymousID = CONCAT('ANON-', UUID());
    END IF;
END$$

-- Trigger 2: Audit Session Notes
CREATE TRIGGER `trg_sessionnote_after_insert`
AFTER INSERT ON `session_notes`
FOR EACH ROW
BEGIN
    INSERT INTO `SessionNoteAudit` (SessionNoteID, TherapistID, ActionTaken)
    VALUES (NEW.SessionNoteID, NEW.TherapistID, 'New Session Note Added');
END$$

-- Trigger 3: Archive Patients before delete
CREATE TRIGGER `trg_patient_before_delete`
BEFORE DELETE ON `patients`
FOR EACH ROW
BEGIN
    INSERT INTO `PatientArchive` (PatientID, FirstName, LastName, Email)
    VALUES (OLD.PatientID, OLD.FirstName, OLD.LastName, OLD.Email);
END$$
=======
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
>>>>>>> 15e2dc3fc90b0750c7fda323f3c3f7e4f485f421

-- Trigger 4: Update Stats after Appointment Delete
CREATE TRIGGER `trg_appointment_after_delete`
AFTER DELETE ON `appointments`
FOR EACH ROW
BEGIN
    UPDATE `CounsellorStats` 
    SET TotalAppointments = TotalAppointments - 1 
    WHERE TherapistID = OLD.TherapistID;
END$$

-- Trigger 5: Audit Appointment Status Updates
CREATE TRIGGER `trg_appointment_after_update`
AFTER UPDATE ON `appointments`
FOR EACH ROW
BEGIN
    IF NEW.Status != OLD.Status THEN
        INSERT INTO `AppointmentStatusAudit` (AppointmentID, OldStatus, NewStatus)
        VALUES (NEW.AppointmentID, OLD.Status, NEW.Status);
    END IF;
END$$

DELIMITER ;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;