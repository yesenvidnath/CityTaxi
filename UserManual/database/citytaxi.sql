-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 08, 2024 at 03:12 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `citytaxi`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAssignedRides` (IN `driverID` INT)   BEGIN
    SELECT r.Ride_ID, r.Taxi_ID, r.Driver_ID, r.Passenger_ID, r.Type, r.Start_Location,
           r.End_Location, r.Start_time, r.End_time, r.Start_date, r.End_date,
           r.Total_distance, r.Amount, r.Status
    FROM rides r
    WHERE r.Driver_ID = driverID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetAvailableDrivers` ()   BEGIN
    SELECT d.Driver_ID, d.Current_Location, u.First_name, u.Last_name, t.Taxi_type
    FROM drivers d
    JOIN users u ON d.User_ID = u.user_ID
    JOIN drivervehicleassignment dva ON d.Driver_ID = dva.Driver_ID
    JOIN taxis t ON dva.Taxi_ID = t.Taxi_ID
    WHERE d.Availability = 1;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDriverAvailability` (IN `driverID` INT)   BEGIN
    SELECT Availability 
    FROM drivers 
    WHERE Driver_ID = driverID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDriverDetails` (IN `driverID` INT)   BEGIN
    -- Fetch driver details
    SELECT d.Driver_ID, d.User_ID, d.Licence_ID, d.Current_Location, d.Availability,
           u.First_name, u.Last_name, u.Email, u.NIC_No, u.mobile_number, u.Address, u.user_img
    FROM drivers d
    JOIN users u ON d.User_ID = u.user_ID
    WHERE d.Driver_ID = driverID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDriverDetailsByUserID` (IN `userID` INT)   BEGIN
    -- Fetch driver details
    SELECT d.Driver_ID, d.User_ID, d.Current_Location, d.Availability,
           u.First_name, u.Last_name, u.Email, u.NIC_No, u.mobile_number, u.Address, u.user_img
    FROM drivers d
    JOIN users u ON d.User_ID = u.user_ID
    WHERE d.User_ID = userID;

    -- Fetch assigned rides for the driver
    SELECT r.Ride_ID, r.Taxi_ID, r.Start_Location, r.End_Location, r.Status
    FROM rides r
    WHERE r.Driver_ID = (SELECT Driver_ID FROM drivers WHERE User_ID = userID);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetDriverVehicleInfo` (IN `driverId` INT)   BEGIN
    SELECT 
        dva.Assignment_ID, 
        dva.Driver_ID, 
        dva.Taxi_ID, 
        t.Plate_number,    -- Use the correct column name
        t.Taxi_type        -- Use the correct column name
    FROM 
        drivervehicleassignment dva
    JOIN 
        taxis t ON dva.Taxi_ID = t.Taxi_ID
    WHERE 
        dva.Driver_ID = driverId;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetPassengerDetails` (IN `passengerID` INT)   BEGIN
    -- Fetch details from ratings
    SELECT `Rating_ID`, `Rate`, `Comment`, `Driver_ID`, `Passenger_ID` 
    FROM `ratings` 
    WHERE `Passenger_ID` = passengerID;

    -- Fetch details from reservations
    SELECT `Reservation_ID`, `TaxiID`, `Start_Location`, `End_Location`, `Driver_ID`, `Passenger_ID` 
    FROM `reservations` 
    WHERE `Passenger_ID` = passengerID;

    -- Fetch details from rides
    SELECT `Ride_ID`, `Taxi_ID`, `Driver_ID`, `Passenger_ID`, `Type`, `Start_Location`, `End_Location`, `Start_time`, `End_time`, `Start_date`, `End_date`, `Total_distance`, `Amount`, `Status` 
    FROM `rides` 
    WHERE `Passenger_ID` = passengerID;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTaxiRatesByType` ()   BEGIN
    SELECT `Taxi_type`, `Rate_per_Km`
    FROM `taxis`
    GROUP BY `Taxi_type`; -- Ensures unique taxi types
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetTaxiTypes` ()   BEGIN
    SELECT `Taxi_ID`, `Taxi_type`, `Vehicle_Img` 
    FROM `taxis`;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `GetUserByEmail` (IN `userEmail` VARCHAR(255))   BEGIN
    SELECT * FROM Users WHERE Email = userEmail;
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `RegisterUser` (IN `p_first_name` VARCHAR(50), IN `p_last_name` VARCHAR(50), IN `p_email` VARCHAR(100), IN `p_password` VARCHAR(100), IN `p_nic_no` VARCHAR(20), IN `p_address` VARCHAR(255), IN `p_user_img` VARCHAR(255))   BEGIN
    DECLARE user_id INT;

    -- Insert into users table
    INSERT INTO users (user_type, password, Email, First_name, Last_name, NIC_No, Address, user_img)
    VALUES ('Passenger', p_password, p_email, p_first_name, p_last_name, p_nic_no, p_address, p_user_img);

    -- Get the last inserted user ID
    SET user_id = LAST_INSERT_ID();

    -- Insert into passengers table
    INSERT INTO passengers (User_ID)
    VALUES (user_id);
END$$

CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateDriverAvailability` (IN `driverID` INT, IN `availability` INT)   BEGIN
    UPDATE drivers 
    SET Availability = availability 
    WHERE Driver_ID = driverID;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `Admin_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Permission` varchar(255) DEFAULT NULL,
  `Description` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`Admin_ID`, `User_ID`, `Permission`, `Description`) VALUES
(1, 3, 'Full Access', 'Main administrator');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `Driver_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Licence_ID` int(11) DEFAULT NULL,
  `Current_Location` varchar(255) DEFAULT NULL,
  `Availability` tinyint(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`Driver_ID`, `User_ID`, `Licence_ID`, `Current_Location`, `Availability`) VALUES
(1, 2, 1, '6.8780,79.8684', 0),
(10, 4, 2, '6.8772,79.8695', 1),
(11, 5, 3, '7.0913,79.9999', 1),
(12, 6, 4, '7.0920,79.9988', 1),
(13, 7, 5, '6.8760,79.8702', 1),
(14, 8, 6, '6.8790,79.8672', 1),
(15, 9, 7, '7.0930,79.9983', 1),
(16, 10, 8, '7.0925,79.9995', 1),
(17, 11, 9, '7.0917,79.9981', 1);

-- --------------------------------------------------------

--
-- Table structure for table `drivervehicleassignment`
--

CREATE TABLE `drivervehicleassignment` (
  `Assignment_ID` int(11) NOT NULL,
  `Driver_ID` int(11) NOT NULL,
  `Taxi_ID` int(11) NOT NULL,
  `Assignment_Date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivervehicleassignment`
--

INSERT INTO `drivervehicleassignment` (`Assignment_ID`, `Driver_ID`, `Taxi_ID`, `Assignment_Date`) VALUES
(1, 1, 1, '2024-09-21'),
(2, 10, 2, '2024-09-21'),
(3, 11, 3, '2024-09-21'),
(4, 12, 4, '2024-09-21'),
(5, 13, 5, '2024-09-21'),
(6, 14, 6, '2024-09-21'),
(7, 15, 7, '2024-09-21'),
(8, 16, 8, '2024-09-21'),
(9, 17, 9, '2024-09-21');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `Invoice_ID` int(11) NOT NULL,
  `Payment_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`Invoice_ID`, `Payment_ID`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `license`
--

CREATE TABLE `license` (
  `License_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `NIC_Img_Front` varchar(255) DEFAULT NULL,
  `NIC_Img_Back` varchar(255) DEFAULT NULL,
  `Drivers_license_Front_ID` varchar(255) DEFAULT NULL,
  `Drivers_license_Back_ID` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `license`
--

INSERT INTO `license` (`License_ID`, `User_ID`, `NIC_Img_Front`, `NIC_Img_Back`, `Drivers_license_Front_ID`, `Drivers_license_Back_ID`) VALUES
(1, 2, 'jane_nic_front.jpg', 'jane_nic_back.jpg', 'jane_dl_front.jpg', 'jane_dl_back.jpg'),
(2, 4, 'nic_front_4.jpg', 'nic_back_4.jpg', 'license_front_4.jpg', 'license_back_4.jpg'),
(3, 5, 'nic_front_5.jpg', 'nic_back_5.jpg', 'license_front_5.jpg', 'license_back_5.jpg'),
(4, 6, 'nic_front_6.jpg', 'nic_back_6.jpg', 'license_front_6.jpg', 'license_back_6.jpg'),
(5, 7, 'nic_front_7.jpg', 'nic_back_7.jpg', 'license_front_7.jpg', 'license_back_7.jpg'),
(6, 8, 'nic_front_8.jpg', 'nic_back_8.jpg', 'license_front_8.jpg', 'license_back_8.jpg'),
(7, 9, 'nic_front_9.jpg', 'nic_back_9.jpg', 'license_front_9.jpg', 'license_back_9.jpg'),
(8, 10, 'nic_front_10.jpg', 'nic_back_10.jpg', 'license_front_10.jpg', 'license_back_10.jpg'),
(9, 11, 'nic_front_11.jpg', 'nic_back_11.jpg', 'license_front_11.jpg', 'license_back_11.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `passengers`
--

CREATE TABLE `passengers` (
  `Passenger_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `passengers`
--

INSERT INTO `passengers` (`Passenger_ID`, `User_ID`) VALUES
(1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `Payment_ID` int(11) NOT NULL,
  `Payment_date` date DEFAULT NULL,
  `Payment_time` time DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `Driver_ID` int(11) DEFAULT NULL,
  `Taxi_ID` int(11) DEFAULT NULL,
  `Ride_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`Payment_ID`, `Payment_date`, `Payment_time`, `Amount`, `Driver_ID`, `Taxi_ID`, `Ride_ID`) VALUES
(1, '2023-09-10', '10:45:00', 20.00, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE `ratings` (
  `Rating_ID` int(11) NOT NULL,
  `Rate` int(11) DEFAULT NULL CHECK (`Rate` >= 1 and `Rate` <= 5),
  `Comment` varchar(255) DEFAULT NULL,
  `Driver_ID` int(11) DEFAULT NULL,
  `Passenger_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `ratings`
--

INSERT INTO `ratings` (`Rating_ID`, `Rate`, `Comment`, `Driver_ID`, `Passenger_ID`) VALUES
(1, 5, 'Great ride, very comfortable!', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `Reservation_ID` int(11) NOT NULL,
  `TaxiID` int(11) DEFAULT NULL,
  `Start_Location` varchar(255) DEFAULT NULL,
  `End_Location` varchar(255) DEFAULT NULL,
  `Driver_ID` int(11) DEFAULT NULL,
  `Passenger_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`Reservation_ID`, `TaxiID`, `Start_Location`, `End_Location`, `Driver_ID`, `Passenger_ID`) VALUES
(1, 1, '123 Main St, City', '456 Park Ave, City', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `rides`
--

CREATE TABLE `rides` (
  `Ride_ID` int(11) NOT NULL,
  `Taxi_ID` int(11) DEFAULT NULL,
  `Driver_ID` int(11) DEFAULT NULL,
  `Passenger_ID` int(11) DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Start_Location` varchar(255) DEFAULT NULL,
  `End_Location` varchar(255) DEFAULT NULL,
  `Start_time` time DEFAULT NULL,
  `End_time` time DEFAULT NULL,
  `Start_date` date DEFAULT NULL,
  `End_date` date DEFAULT NULL,
  `Total_distance` decimal(10,2) DEFAULT NULL,
  `Amount` decimal(10,2) DEFAULT NULL,
  `Status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rides`
--

INSERT INTO `rides` (`Ride_ID`, `Taxi_ID`, `Driver_ID`, `Passenger_ID`, `Type`, `Start_Location`, `End_Location`, `Start_time`, `End_time`, `Start_date`, `End_date`, `Total_distance`, `Amount`, `Status`) VALUES
(1, 1, 1, 1, 'DayRide', '123 Main St', '456 Park Ave', '10:00:00', '10:30:00', '2023-09-10', '2023-09-10', 15.50, 20.00, 'Accepted');

-- --------------------------------------------------------

--
-- Table structure for table `taxis`
--

CREATE TABLE `taxis` (
  `Taxi_ID` int(11) NOT NULL,
  `Taxi_type` varchar(50) DEFAULT NULL,
  `Vehicle_Owner_ID` int(11) DEFAULT NULL,
  `Plate_number` varchar(20) DEFAULT NULL,
  `Registration_Date` date DEFAULT NULL,
  `RevenueLicence` varchar(50) DEFAULT NULL,
  `Insurance_info` varchar(255) DEFAULT NULL,
  `Revenue_licence_Img` varchar(255) DEFAULT NULL,
  `Insurance_Card_Img` varchar(255) DEFAULT NULL,
  `Vehicle_Img` varchar(255) DEFAULT NULL,
  `Rate_per_Km` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `taxis`
--

INSERT INTO `taxis` (`Taxi_ID`, `Taxi_type`, `Vehicle_Owner_ID`, `Plate_number`, `Registration_Date`, `RevenueLicence`, `Insurance_info`, `Revenue_licence_Img`, `Insurance_Card_Img`, `Vehicle_Img`, `Rate_per_Km`) VALUES
(1, 'Car', 1, 'ABC-1234', '2023-01-01', 'RL123', 'Fully Insured', 'rev_licence_img.jpg', 'insurance_card_img.jpg', 'car_img.jpg', 150.00),
(2, 'Car', 1, 'ABC-1234', '2023-01-15', 'REV123456', 'INS123456', 'rev_img1.jpg', 'ins_img1.jpg', 'car1.png', 150.00),
(3, 'Van', 1, 'DEF-5678', '2023-02-20', 'REV789012', 'INS789012', 'rev_img2.jpg', 'ins_img2.jpg', 'van1.png', 205.00),
(4, 'SUV', 1, 'GHI-9012', '2023-03-10', 'REV345678', 'INS345678', 'rev_img3.jpg', 'ins_img3.jpg', 'suv1.png', 195.00),
(5, 'Car', 1, 'MNO-7890', '2023-05-15', 'REV567890', 'INS567890', 'rev_img5.jpg', 'ins_img5.jpg', 'car2.png', 150.00),
(6, 'Car', 1, 'ABC1234', '2024-01-01', 'RL-123456', 'Insurance123', 'rev_licence_img1.jpg', 'insurance_card_img1.jpg', 'car_img1.jpg', 150.00),
(7, 'Van', 1, 'DEF5678', '2024-01-05', 'RL-234567', 'Insurance234', 'rev_licence_img2.jpg', 'insurance_card_img2.jpg', 'van_img1.jpg', 205.00),
(8, 'SUV', 1, 'GHI9012', '2024-01-10', 'RL-345678', 'Insurance345', 'rev_licence_img3.jpg', 'insurance_card_img3.jpg', 'suv_img1.jpg', 195.00),
(9, 'Van', 1, 'JKL3456', '2024-01-15', 'RL-456789', 'Insurance456', 'rev_licence_img4.jpg', 'insurance_card_img4.jpg', 'motorbike_img1.jpg', 205.00);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_ID` int(11) NOT NULL,
  `user_type` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `Email` varchar(255) NOT NULL,
  `First_name` varchar(50) DEFAULT NULL,
  `Last_name` varchar(50) DEFAULT NULL,
  `NIC_No` varchar(20) DEFAULT NULL,
  `mobile_number` varchar(15) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `user_img` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_ID`, `user_type`, `password`, `Email`, `First_name`, `Last_name`, `NIC_No`, `mobile_number`, `Address`, `user_img`) VALUES
(1, 'Passenger', 'pass123', 'john.doe@example.com', 'John', 'Doe', '982345678V', '0704605516', '123 Main St, City', 'john_img.jpg'),
(2, 'Driver', 'driver123', 'jane.smith@example.com', 'Jane', 'Smith', '983456789V', NULL, '456 Side St, City', 'jane_img.jpg'),
(3, 'Admin', 'admin123', 'admin@example.com', 'Admin', 'User', '999123456V', NULL, 'Admin Address', 'admin_img.jpg'),
(4, 'driver', 'password123', 'driver1@example.com', 'John', 'Doe', '951234567V', '0771234567', 'Colombo', 'john_doe.png'),
(5, 'driver', 'password123', 'driver2@example.com', 'Jane', 'Smith', '961234567V', '0772345678', 'Kandy', 'jane_smith.png'),
(6, 'driver', 'password123', 'driver3@example.com', 'Mark', 'Johnson', '971234567V', '0773456789', 'Galle', 'mark_johnson.png'),
(7, 'driver', 'password123', 'driver4@example.com', 'Sarah', 'Williams', '981234567V', '0774567890', 'Negombo', 'sarah_williams.png'),
(8, 'driver', 'password123', 'driver1@example.com', 'John', 'Doe', '951234567V', '0771234567', 'Colombo', 'john_doe.png'),
(9, 'driver', 'password123', 'driver2@example.com', 'Jane', 'Smith', '961234567V', '0772345678', 'Kandy', 'jane_smith.png'),
(10, 'driver', 'password123', 'driver3@example.com', 'Mark', 'Johnson', '971234567V', '0773456789', 'Galle', 'mark_johnson.png'),
(11, 'driver', 'password123', 'driver4@example.com', 'Sarah', 'Williams', '981234567V', '0774567890', 'Negombo', 'sarah_williams.png');

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_owner`
--

CREATE TABLE `vehicle_owner` (
  `Vehicle_Owner_ID` int(11) NOT NULL,
  `User_ID` int(11) DEFAULT NULL,
  `Driving_Licence_No` varchar(50) DEFAULT NULL,
  `Licence_ID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicle_owner`
--

INSERT INTO `vehicle_owner` (`Vehicle_Owner_ID`, `User_ID`, `Driving_Licence_No`, `Licence_ID`) VALUES
(1, 2, 'D123456789', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`Admin_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`Driver_ID`),
  ADD KEY `User_ID` (`User_ID`),
  ADD KEY `Licence_ID` (`Licence_ID`);

--
-- Indexes for table `drivervehicleassignment`
--
ALTER TABLE `drivervehicleassignment`
  ADD PRIMARY KEY (`Assignment_ID`),
  ADD KEY `Driver_ID` (`Driver_ID`),
  ADD KEY `Taxi_ID` (`Taxi_ID`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`Invoice_ID`),
  ADD KEY `Payment_ID` (`Payment_ID`);

--
-- Indexes for table `license`
--
ALTER TABLE `license`
  ADD PRIMARY KEY (`License_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `passengers`
--
ALTER TABLE `passengers`
  ADD PRIMARY KEY (`Passenger_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`Payment_ID`),
  ADD KEY `Driver_ID` (`Driver_ID`),
  ADD KEY `Taxi_ID` (`Taxi_ID`),
  ADD KEY `Ride_ID` (`Ride_ID`);

--
-- Indexes for table `ratings`
--
ALTER TABLE `ratings`
  ADD PRIMARY KEY (`Rating_ID`),
  ADD KEY `Driver_ID` (`Driver_ID`),
  ADD KEY `Passenger_ID` (`Passenger_ID`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`Reservation_ID`),
  ADD KEY `TaxiID` (`TaxiID`),
  ADD KEY `Driver_ID` (`Driver_ID`),
  ADD KEY `Passenger_ID` (`Passenger_ID`);

--
-- Indexes for table `rides`
--
ALTER TABLE `rides`
  ADD PRIMARY KEY (`Ride_ID`),
  ADD KEY `Taxi_ID` (`Taxi_ID`),
  ADD KEY `Driver_ID` (`Driver_ID`),
  ADD KEY `Passenger_ID` (`Passenger_ID`);

--
-- Indexes for table `taxis`
--
ALTER TABLE `taxis`
  ADD PRIMARY KEY (`Taxi_ID`),
  ADD KEY `Vehicle_Owner_ID` (`Vehicle_Owner_ID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_ID`);

--
-- Indexes for table `vehicle_owner`
--
ALTER TABLE `vehicle_owner`
  ADD PRIMARY KEY (`Vehicle_Owner_ID`),
  ADD KEY `User_ID` (`User_ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `Admin_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `Driver_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `drivervehicleassignment`
--
ALTER TABLE `drivervehicleassignment`
  MODIFY `Assignment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `Invoice_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `license`
--
ALTER TABLE `license`
  MODIFY `License_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `passengers`
--
ALTER TABLE `passengers`
  MODIFY `Passenger_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `Payment_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `ratings`
--
ALTER TABLE `ratings`
  MODIFY `Rating_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `Reservation_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rides`
--
ALTER TABLE `rides`
  MODIFY `Ride_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `taxis`
--
ALTER TABLE `taxis`
  MODIFY `Taxi_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `vehicle_owner`
--
ALTER TABLE `vehicle_owner`
  MODIFY `Vehicle_Owner_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin`
--
ALTER TABLE `admin`
  ADD CONSTRAINT `admin_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`),
  ADD CONSTRAINT `drivers_ibfk_2` FOREIGN KEY (`Licence_ID`) REFERENCES `license` (`License_ID`);

--
-- Constraints for table `drivervehicleassignment`
--
ALTER TABLE `drivervehicleassignment`
  ADD CONSTRAINT `drivervehicleassignment_ibfk_1` FOREIGN KEY (`Driver_ID`) REFERENCES `drivers` (`Driver_ID`),
  ADD CONSTRAINT `drivervehicleassignment_ibfk_2` FOREIGN KEY (`Taxi_ID`) REFERENCES `taxis` (`Taxi_ID`);

--
-- Constraints for table `invoices`
--
ALTER TABLE `invoices`
  ADD CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`Payment_ID`) REFERENCES `payments` (`Payment_ID`);

--
-- Constraints for table `license`
--
ALTER TABLE `license`
  ADD CONSTRAINT `license_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`);

--
-- Constraints for table `passengers`
--
ALTER TABLE `passengers`
  ADD CONSTRAINT `passengers_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`Driver_ID`) REFERENCES `drivers` (`Driver_ID`),
  ADD CONSTRAINT `payments_ibfk_2` FOREIGN KEY (`Taxi_ID`) REFERENCES `taxis` (`Taxi_ID`),
  ADD CONSTRAINT `payments_ibfk_3` FOREIGN KEY (`Ride_ID`) REFERENCES `rides` (`Ride_ID`);

--
-- Constraints for table `ratings`
--
ALTER TABLE `ratings`
  ADD CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`Driver_ID`) REFERENCES `drivers` (`Driver_ID`),
  ADD CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`Passenger_ID`) REFERENCES `passengers` (`Passenger_ID`);

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_ibfk_1` FOREIGN KEY (`TaxiID`) REFERENCES `taxis` (`Taxi_ID`),
  ADD CONSTRAINT `reservations_ibfk_2` FOREIGN KEY (`Driver_ID`) REFERENCES `drivers` (`Driver_ID`),
  ADD CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`Passenger_ID`) REFERENCES `passengers` (`Passenger_ID`);

--
-- Constraints for table `rides`
--
ALTER TABLE `rides`
  ADD CONSTRAINT `rides_ibfk_1` FOREIGN KEY (`Taxi_ID`) REFERENCES `taxis` (`Taxi_ID`),
  ADD CONSTRAINT `rides_ibfk_2` FOREIGN KEY (`Driver_ID`) REFERENCES `drivers` (`Driver_ID`),
  ADD CONSTRAINT `rides_ibfk_3` FOREIGN KEY (`Passenger_ID`) REFERENCES `passengers` (`Passenger_ID`);

--
-- Constraints for table `taxis`
--
ALTER TABLE `taxis`
  ADD CONSTRAINT `taxis_ibfk_1` FOREIGN KEY (`Vehicle_Owner_ID`) REFERENCES `vehicle_owner` (`Vehicle_Owner_ID`);

--
-- Constraints for table `vehicle_owner`
--
ALTER TABLE `vehicle_owner`
  ADD CONSTRAINT `vehicle_owner_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `users` (`user_ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
