CREATE DATABASE IF NOT EXISTS ftlw_recordkeeping;

USE ftlw_recordkeeping;

--
-- Table structure for table `customers`
--


CREATE TABLE IF NOT EXISTS `ftlw_recordkeepings` (
  `recordNumber` int(11) NOT NULL AUTO_INCREMENT,
  `recordDate` date NOT NULL,
  `recordName` varchar(50) NOT NULL,
  `cash` DECIMAL(7,2) NOT NULL,
  `less_expense` DECIMAL(7,2) NOT NULL,
  `new_cash` DECIMAL(7,2) NOT NULL,
  `checks` DECIMAL(7,2) NOT NULL,
  `total` DECIMAL(7,2) NOT NULL,
  `hundredsCt` int(3) NULL,
  `fiftysCt` int(3) NULL,
  `twentysCt` int(3) NULL,
  `tensCt` int(3) NULL,
  `fivesCt` int(3) NULL,
  `onesCt` int(3) NULL,
  `coinsCt` int(3) NULL,
  PRIMARY KEY (`recordNumber`)
) COMMENT='Record Table';

CREATE TABLE IF NOT EXISTS `ftlw_recordattendance` (
  `attendanceNumber` int(11) NOT NULL AUTO_INCREMENT,
  `attendanceDate` date NOT NULL,
  `user` varchar(50) NOT NULL,
  `adults` int(3) NULL,
  `children` int(3) NULL,
  `total` int(3) NULL,
  PRIMARY KEY (`attendanceNumber`)
) COMMENT='Attendance Table';

CREATE TABLE IF NOT EXISTS `ftlw_recorddate` (
  `dateID` int(11) NOT NULL AUTO_INCREMENT,
  `recordDate` date NOT NULL,
  `status` varchar(45) NOT NULL,
  PRIMARY KEY (`dateID`),
  UNIQUE KEY `recordDate_UNIQUE` (`recordDate`)
) COMMENT='Date Table';

CREATE TABLE IF NOT EXISTS `ftlw_givingfund` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `fund_name` VARCHAR(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `fund_name_UNIQUE` (`fund_name` ASC)
) COMMENT = 'Giving Fund Table';

CREATE TABLE IF NOT EXISTS `ftlw_users` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(200) NOT NULL,
  `approved` tinyint(1) NOT NULL DEFAULT '0',
  `role` varchar(45) NOT NULL DEFAULT 'User',
  `locked` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uid`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) COMMENT = 'Users Table';

CREATE TABLE login_attempts (
   `ip` varchar(20),
   `attempts` int default 0,
   `lastlogin` datetime default NULL,
   `username` varchar(100) NOT NULL
) COMMENT='Login Attempts Table';

--
-- Dumping data for table `customers`
--

--INSERT INTO `angularcode_customers` (`customerNumber`, `customerName`, `email`, `address`, `city`, `state`, `postalCode`, `country`) VALUES
--(103, 'Atelier graphique', 'Nantes@gmail.com', '54, rue Royale', 'Nantes', NULL, '44000', 'France'),
--(112, 'Signal Gift Stores', 'LasVegas@gmail.com', '8489 Strong St.', 'Las Vegas', 'NV', '83030', 'USA'),
--(114, 'Australian Collectors, Co.', 'Melbourne@gmail.com', '636 St Kilda Road', 'Melbourne', 'Victoria', '3004', 'Australia'),
--(119, 'La Rochelle Gifts', 'Nantes@gmail.com', '67, rue des Cinquante Otages', 'Nantes', NULL, '44000', 'France'),
--(121, 'Baane Mini Imports', 'Stavern@gmail.com', 'Erling Skakkes gate 78', 'Stavern', NULL, '4110', 'Norway'),
--(124, 'Mini Gifts Distributors Ltd.', 'SanRafael@gmail.com', '5677 Strong St.', 'San Rafael', 'CA', '97562', 'USA'),
--(125, 'Havel & Zbyszek Co', 'Warszawa@gmail.com', 'ul. Filtrowa 68', 'Warszawa', NULL, '01-012', 'Poland'),
--(128, 'Blauer See Auto, Co.', 'Frankfurt@gmail.com', 'Lyonerstr. 34', 'Frankfurt', NULL, '60528', 'Germany'),
--(129, 'Mini Wheels Co.', 'SanFrancisco@gmail.com', '5557 North Pendale Street', 'San Francisco', 'CA', '94217', 'USA'),
--(131, 'Land of Toys Inc.', 'NYC@gmail.com', '897 Long Airport Avenue', 'NYC', 'NY', '10022', 'USA'),
--(141, 'Euro+ Shopping Channel', 'Madrid@gmail.com', 'C/ Moralzarzal, 86', 'Madrid', NULL, '28034', 'Spain'),
--(145, 'Danish Wholesale Imports', 'Kobenhavn@gmail.com', 'Vinbltet 34', 'Kobenhavn', NULL, '1734', 'Denmark'),
--(146, 'Saveley & Henriot, Co.', 'Lyon@gmail.com', '2, rue du Commerce', 'Lyon', NULL, '69004', 'France'),
--(148, 'Dragon Souveniers, Ltd.', 'Singapore@gmail.com', 'Bronz Sok.', 'Singapore', NULL, '079903', 'Singapore'),
--(151, 'Muscle Machine Inc', 'NYC@gmail.com', '4092 Furth Circle', 'NYC', 'NY', '10022', 'USA'),
--(157, 'Diecast Classics Inc.', 'Allentown@gmail.com', '7586 Pompton St.', 'Allentown', 'PA', '70267', 'USA'),
--(161, 'Technics Stores Inc.', 'Burlingame@gmail.com', '9408 Furth Circle', 'Burlingame', 'CA', '94217', 'USA'),
--(166, 'Handji Gifts& Co', 'Singapore@gmail.com', '106 Linden Road Sandown', 'Singapore', NULL, '069045', 'Singapore'),
--(167, 'Herkku Gifts', 'Bergen@gmail.com', 'Brehmen St. 121', 'Bergen', NULL, 'N 5804', 'Norway  '),
--(168, 'American Souvenirs Inc', 'NewHaven@gmail.com', '149 Spinnaker Dr.', 'New Haven', 'CT', '97823', 'USA');
--
