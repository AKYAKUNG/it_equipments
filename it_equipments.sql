-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 23, 2026 at 04:32 PM
-- Server version: 8.0.17
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `it_equipments`
--

-- --------------------------------------------------------

--
-- Table structure for table `budgets`
--

CREATE TABLE `budgets` (
  `BudgetID` int(11) NOT NULL,
  `BudgetName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `budgets`
--

INSERT INTO `budgets` (`BudgetID`, `BudgetName`) VALUES
(101, 'งบประมาณแผ่นดิน'),
(102, 'งบรายได้'),
(103, 'งบประมาณแผ่นดิน'),
(104, 'งบรายได้'),
(105, 'งบประมาณแผ่นดิน'),
(106, 'งบรายได้'),
(107, 'งบประมาณแผ่นดิน'),
(108, 'งบรายได้'),
(109, 'งบประมาณแผ่นดิน'),
(110, 'งบประมาณแผ่นดิน');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `DepartmentID` int(11) NOT NULL,
  `DepartmentName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`DepartmentID`, `DepartmentName`) VALUES
(101, 'ฝ่ายเทคโนโลยีสารสนเทศ'),
(102, 'ฝ่ายบริหารทรัพยากรบุคคล'),
(103, 'ฝ่ายบัญชีและการเงิน'),
(104, 'ฝ่ายการตลาดและประชาสัมพันธ์'),
(105, 'ฝ่ายสำนักงานกลาง'),
(106, 'ฝ่ายการบริการลูกค้า'),
(107, 'ฝ่ายบริหารทั่วไป'),
(108, 'ฝ่ายจัดซื้อและพัสดุ'),
(109, 'ฝ่ายวิจัยและพัฒนา'),
(110, 'ฝ่ายกฎหมายและนิติกรรม');

-- --------------------------------------------------------

--
-- Table structure for table `device_types`
--

CREATE TABLE `device_types` (
  `TypeID` int(11) NOT NULL,
  `CategoryName` varchar(100) NOT NULL,
  `TypeName` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `device_types`
--

INSERT INTO `device_types` (`TypeID`, `CategoryName`, `TypeName`) VALUES
(101, 'ครุภัณฑ์คอมพิวเตอร์', 'Rack Server'),
(102, 'ครุภัณฑ์คอมพิวเตอร์', 'Notebook'),
(103, 'ครุภัณฑ์คอมพิวเตอร์', 'PC Desktop'),
(104, 'ครุภัณฑ์คอมพิวเตอร์', 'Notebook'),
(105, 'ครุภัณฑ์สื่อสาร/เครือข่าย', 'L2 Switch 24-Port'),
(106, 'ครุภัณฑ์สำนักงาน', 'Laser Printer Multi-function'),
(107, 'ครุภัณฑ์คอมพิวเตอร์', 'All-in-One PC'),
(108, 'ครุภัณฑ์โสตทัศนูปกรณ์', 'Projector'),
(109, 'ครุภัณฑ์คอมพิวเตอร์', 'PC Desktop'),
(110, 'ครุภัณฑ์คอมพิวเตอร์', 'UPS (เครื่องสำรองไฟ)');

-- --------------------------------------------------------

--
-- Table structure for table `it_equipments`
--

CREATE TABLE `it_equipments` (
  `EquipmentID` int(11) NOT NULL,
  `SerialNumber` varchar(100) DEFAULT NULL,
  `TypeID` int(11) DEFAULT NULL,
  `LocationID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  `BudgetID` int(11) DEFAULT NULL,
  `ReceiveYear` int(11) DEFAULT NULL,
  `CPU_Model` varchar(100) DEFAULT NULL,
  `RAM_GB` int(11) DEFAULT NULL,
  `Storage_Capacity` varchar(100) DEFAULT NULL,
  `OS_Firmware` varchar(100) DEFAULT NULL,
  `IP_Address` varchar(50) DEFAULT NULL,
  `MAC_Address` varchar(50) DEFAULT NULL,
  `Port_Speed` varchar(50) DEFAULT NULL,
  `Connection_Status` varchar(50) DEFAULT NULL,
  `Condition_Status` varchar(50) DEFAULT NULL,
  `Issues_Found` text,
  `Remarks` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `it_equipments`
--

INSERT INTO `it_equipments` (`EquipmentID`, `SerialNumber`, `TypeID`, `LocationID`, `UserID`, `BudgetID`, `ReceiveYear`, `CPU_Model`, `RAM_GB`, `Storage_Capacity`, `OS_Firmware`, `IP_Address`, `MAC_Address`, `Port_Speed`, `Connection_Status`, `Condition_Status`, `Issues_Found`, `Remarks`) VALUES
(1, 'SN-SRV-2022-001', 101, 101, 101, 101, 2565, 'Intel Xeon Silver 4310', 64, '4TB SSD RAID 5', 'Windows Server 2022', '192.168.10.5', '00:1A:2B:3C:4D:5E', '10 Gbps (Fiber)', 'ปกติ', 'ดีมาก', '-', 'ใช้งานเป็น Primary Database Server'),
(2, 'SN-NB-2023-012', 102, 102, 102, 102, 2566, 'Intel Core i7-1255U', 16, '512GB NVMe SSD', 'Windows 11 Pro', '192.168.1.101', '00:1A:2B:3C:4D:5F', 'Wi-Fi 6 (802.11ax)', 'ปกติ', 'ดี', '-', 'มีประกันบำรุงรักษาถึงปี 2569'),
(3, 'SN-PC-2019-088', 103, 103, 103, 103, 2562, 'Intel Core i3-8100', 4, '1TB HDD', 'Windows 10 Home', '192.168.1.115', '22:33:44:55:66:77', '100/1000 Mbps (LAN)', 'ออฟไลน์', 'ชำรุด/ทำงานช้า', 'เปิดเครื่องช้า และโปรแกรมค้างบ่อย', 'ควรพิจารณาปลดซ่อมและตั้งงบจัดซื้อใหม่'),
(4, 'SN-MAC-2022-005', 104, 104, 104, 104, 2565, 'Apple M1', 8, '256GB SSD', 'macOS Monterey', '192.168.1.120', 'AA:BB:CC:DD:EE:FF', 'Wi-Fi 5 (802.11ac)', 'ปกติ', 'ดี', '-', 'ใช้สำหรับงานออกแบบและตัดต่อวิดีโอ'),
(5, 'SN-SW-2021-003', 105, 105, 105, 105, 2564, '-', NULL, '-', 'Cisco IOS v15.2', '192.168.10.2', '11:22:33:44:55:66', '1 Gbps (Ethernet)', 'ปกติ', 'ดี', '-', 'สวิตช์หลักสำหรับชั้น 3'),
(6, 'SN-PRN-2020-041', 106, 106, 106, 106, 2563, '-', NULL, '-', 'HP Firmware v2.8', '192.168.1.150', '99:88:77:66:55:44', '100/100 Mbps (LAN)', 'ปกติ', 'พอใช้', 'หมึกพิมพ์ใกล้หมด และกระดาษติดบ่อย', 'แจ้งฝ่ายซ่อมบำรุงเข้าตรวจเช็กชุดดึงกระดาษ'),
(7, 'SN-AIO-2020-019', 107, 107, 107, 107, 2563, 'Intel Pentium Gold G6400', 4, '256GB SSD', 'Windows 10 Home', '192.168.1.140', 'FE:DC:BA:98:76:54', '1000/1000 Mbps (LAN)', 'สัญญาณขาดหาย', 'พอใช้', 'สาย LAN หลวม หน้าจอค้างเป็นบางครั้ง', 'เปลี่ยนสาย LAN ใหม่ และเข้าหัวใหม่'),
(8, 'SN-PRJ-2021-007', 108, 108, 108, 108, 2564, '-', NULL, '-', 'Android TV OS 11', '192.168.1.160', '55:44:33:22:11:00', 'Wi-Fi 5 (802.11ac)', 'ออฟไลน์', 'รอการซ่อมแซม', 'สีเพี้ยน และเชื่อมต่อ Wi-Fi ไม่ได้', 'เปลี่ยนหลอดภาพโปรเจกเตอร์'),
(9, 'SN-PC-2023-045', 109, 109, 109, 109, 2566, 'AMD Ryzen 5 5600G', 16, '512GB SSD', 'Windows 11 Pro', '192.168.1.118', 'AB:CD:EF:12:34:56', '1000/1000 Mbps (LAN)', 'ปกติ', 'ดี', '-', 'อุปกรณ์อยู่ในสภาพสมบูรณ์'),
(10, 'SN-UPS-2018-002', 110, 110, 110, 110, 2561, '-', NULL, '-', 'Microcontroller FW v1.0', '192.168.10.250', '77:66:55:44:33:22', 'SNMP Card (LAN)', 'ปกติ', 'แบตเตอรี่เสื่อม', 'ไม่สามารถสำรองไฟได้เมื่อไฟดับ', 'รออนุมัติงบเปลี่ยนแบตเตอรี่ใหม่');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `LocationID` int(11) NOT NULL,
  `Building` varchar(100) NOT NULL,
  `Floor` varchar(10) DEFAULT NULL,
  `Room` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`LocationID`, `Building`, `Floor`, `Room`) VALUES
(101, 'อาคารอำนวยการ', '3', 'ห้อง Server 301'),
(102, 'อาคารอำนวยการ', '2', 'ห้อง HR 204'),
(103, 'อาคารอำนวยการ', '1', 'ห้องบัญชี 102'),
(104, 'อาคารอำนวยการ', '2', 'ห้องการตลาด 201'),
(105, 'อาคารอำนวยการ', '3', 'ห้อง Network 302'),
(106, 'อาคารอำนวยการ', '1', 'จุดบริการเอกสาร 105'),
(107, 'อาคารอำนวยการ', '1', 'เคาน์เตอร์บริการ 1'),
(108, 'อาคารอำนวยการ', '4', 'ห้องประชุมใหญ่ 401'),
(109, 'อาคารอำนวยการ', '2', 'ห้องจัดซื้อ 208'),
(110, 'อาคารอำนวยการ', '3', 'ห้อง Server 301');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `UserID` int(11) NOT NULL,
  `FullName` varchar(100) NOT NULL,
  `DepartmentID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`UserID`, `FullName`, `DepartmentID`) VALUES
(101, 'สมชาย ใจดี', 101),
(102, 'สมหญิง รักงาน', 102),
(103, 'วิชัย ทำบัญชี', 103),
(104, 'มาลี ขายเก่ง', 104),
(105, 'แอดมินระบบ', 105),
(106, '(ใช้งานส่วนรวม)', 106),
(107, 'จุดบริการลูกค้า 1', 107),
(108, 'ห้องประชุม A', 108),
(109, 'สมศักดิ์ จัดซื้อ', 109),
(110, 'ผู้ดูแลระบบ', 110);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `budgets`
--
ALTER TABLE `budgets`
  ADD PRIMARY KEY (`BudgetID`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`DepartmentID`);

--
-- Indexes for table `device_types`
--
ALTER TABLE `device_types`
  ADD PRIMARY KEY (`TypeID`);

--
-- Indexes for table `it_equipments`
--
ALTER TABLE `it_equipments`
  ADD PRIMARY KEY (`EquipmentID`),
  ADD UNIQUE KEY `SerialNumber` (`SerialNumber`),
  ADD KEY `TypeID` (`TypeID`),
  ADD KEY `LocationID` (`LocationID`),
  ADD KEY `UserID` (`UserID`),
  ADD KEY `BudgetID` (`BudgetID`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`LocationID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`UserID`),
  ADD KEY `DepartmentID` (`DepartmentID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `budgets`
--
ALTER TABLE `budgets`
  MODIFY `BudgetID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `DepartmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `device_types`
--
ALTER TABLE `device_types`
  MODIFY `TypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `it_equipments`
--
ALTER TABLE `it_equipments`
  MODIFY `EquipmentID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `LocationID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `UserID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=111;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `it_equipments`
--
ALTER TABLE `it_equipments`
  ADD CONSTRAINT `it_equipments_ibfk_1` FOREIGN KEY (`TypeID`) REFERENCES `device_types` (`TypeID`) ON DELETE SET NULL,
  ADD CONSTRAINT `it_equipments_ibfk_2` FOREIGN KEY (`LocationID`) REFERENCES `locations` (`LocationID`) ON DELETE SET NULL,
  ADD CONSTRAINT `it_equipments_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`) ON DELETE SET NULL,
  ADD CONSTRAINT `it_equipments_ibfk_4` FOREIGN KEY (`BudgetID`) REFERENCES `budgets` (`BudgetID`) ON DELETE SET NULL;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_ibfk_1` FOREIGN KEY (`DepartmentID`) REFERENCES `departments` (`DepartmentID`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
