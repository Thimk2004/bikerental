-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 12, 2025 at 02:34 PM
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
-- Database: `bikerental`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `UserName` varchar(100) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `updationDate` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `UserName`, `Password`, `updationDate`) VALUES
(1, 'admin', '5c428d8875d2948607f3e3fe134d71b4', '2017-06-18 12:22:38');

-- --------------------------------------------------------

--
-- Table structure for table `tblblogposts`
--

CREATE TABLE `tblblogposts` (
  `id` int(11) NOT NULL COMMENT 'unique identifier',
  `PostTitle` varchar(255) NOT NULL COMMENT 'blog title',
  `PostContent` text NOT NULL COMMENT 'Blog post details',
  `PostImage` varchar(255) NOT NULL COMMENT 'Article cover image path (optional)',
  `Author` varchar(100) NOT NULL COMMENT 'Author (optional)',
  `PostingDate` datetime NOT NULL DEFAULT current_timestamp() COMMENT 'Release date',
  `LastUpdated` datetime DEFAULT NULL ON UPDATE current_timestamp() COMMENT 'Last updated date (optional)',
  `Slug` varchar(255) NOT NULL COMMENT 'Friendly name to use for the URL (optional, but recommended)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tblblogposts`
--

INSERT INTO `tblblogposts` (`id`, `PostTitle`, `PostContent`, `PostImage`, `Author`, `PostingDate`, `LastUpdated`, `Slug`) VALUES
(2, 'Text Post', 'test post', 'c83533a95d6dd79d5bae5759f34bff87.jpg', 'Ben', '2025-06-23 23:33:37', NULL, '');

-- --------------------------------------------------------

--
-- Table structure for table `tblbooking`
--

CREATE TABLE `tblbooking` (
  `id` int(11) NOT NULL,
  `userEmail` varchar(100) DEFAULT NULL,
  `VehicleId` int(11) DEFAULT NULL,
  `FromDate` varchar(20) DEFAULT NULL,
  `ToDate` varchar(20) DEFAULT NULL,
  `message` varchar(255) DEFAULT NULL,
  `Status` int(11) DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbooking`
--

INSERT INTO `tblbooking` (`id`, `userEmail`, `VehicleId`, `FromDate`, `ToDate`, `message`, `Status`, `PostingDate`) VALUES
(1, 'test@gmail.com', 2, '22/06/2017', '25/06/2017', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco', 1, '2017-06-19 20:15:43'),
(2, 'test@gmail.com', 3, '30/06/2017', '02/07/2017', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco', 2, '2017-06-26 20:15:43'),
(3, 'test@gmail.com', 4, '02/07/2017', '07/07/2017', 'Lorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ipsumLorem ', 0, '2017-06-26 21:10:06');

-- --------------------------------------------------------

--
-- Table structure for table `tblbrands`
--

CREATE TABLE `tblbrands` (
  `id` int(11) NOT NULL,
  `BrandName` varchar(120) NOT NULL,
  `CreationDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblbrands`
--

INSERT INTO `tblbrands` (`id`, `BrandName`, `CreationDate`, `UpdationDate`) VALUES
(1, 'KTM', '2017-06-18 16:24:34', '2017-06-19 06:42:23'),
(2, 'Bajaj', '2017-06-18 16:24:50', NULL),
(3, 'Honda', '2017-06-18 16:25:03', NULL),
(4, 'Suzuki', '2017-06-18 16:25:13', NULL),
(5, 'Yamaha', '2017-06-18 16:25:24', NULL),
(7, 'Ducati', '2017-06-19 06:22:13', NULL),
(8, 'Kawasaki', '2025-07-07 14:26:54', NULL),
(9, 'BMW', '2025-07-07 14:26:54', NULL),
(10, 'Harley-Davidson', '2025-07-07 14:26:54', NULL),
(11, 'Vespa', '2025-07-07 14:26:54', NULL),
(12, 'Triumph', '2025-07-07 14:26:54', NULL),
(13, 'Aprilia', '2025-07-07 14:26:54', NULL),
(14, 'SYM', '2025-07-07 14:26:54', NULL),
(15, 'KYMCO', '2025-07-07 14:26:54', NULL),
(16, 'Royal Enfield', '2025-07-07 14:26:54', NULL),
(17, 'CFMoto', '2025-07-07 14:26:54', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusinfo`
--

CREATE TABLE `tblcontactusinfo` (
  `id` int(11) NOT NULL,
  `Address` tinytext DEFAULT NULL,
  `EmailId` varchar(255) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcontactusinfo`
--

INSERT INTO `tblcontactusinfo` (`id`, `Address`, `EmailId`, `ContactNo`) VALUES
(1, 'Test Demo test demo																									', 'test@test.com', '8585233222');

-- --------------------------------------------------------

--
-- Table structure for table `tblcontactusquery`
--

CREATE TABLE `tblcontactusquery` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `EmailId` varchar(120) DEFAULT NULL,
  `ContactNumber` char(11) DEFAULT NULL,
  `Message` longtext DEFAULT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblcontactusquery`
--

INSERT INTO `tblcontactusquery` (`id`, `name`, `EmailId`, `ContactNumber`, `Message`, `PostingDate`, `status`) VALUES
(1, 'Harry Den', 'webhostingamigo@gmail.com', '2147483647', 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum', '2017-06-18 10:03:07', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblpages`
--

CREATE TABLE `tblpages` (
  `id` int(11) NOT NULL,
  `PageName` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `detail` longtext NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblpages`
--

INSERT INTO `tblpages` (`id`, `PageName`, `type`, `detail`) VALUES
(1, 'Terms and Conditions', 'terms', '<P align=justify><FONT size=2><STRONG><FONT color=#990000>(1) ACCEPTANCE OF TERMS</FONT><BR><BR></STRONG>Last updated: December 05, 2017\r\nPlease read these Terms and Conditions (\"Terms\", \"Terms and Conditions\") carefully before using the ->code-projects.org/ website (the \"Service\") operated by Code Projects (\"us\", \"we\", or \"our\").\r\nYour access to and use of the Service is conditioned on your acceptance of and compliance with these Terms. These Terms apply to all visitors, users and others who access or use the Service.\r\nBy accessing or using the Service you agree to be bound by these Terms. If you disagree with any part of the terms then you may not access the Service. Terms and Conditions from TermsFeed for Code Projects. Links To Other Web Sites\r\nOur Service may contain links to third-party web sites or services that are not owned or controlled by Code Projects.\r\nCode Projects has no control over, and assumes no responsibility for, the content, privacy policies, or practices of any third party web sites or services. You further acknowledge and agree that Code Projects shall not be responsible or liable, directly or indirectly, for any damage or loss caused or alleged to be caused by or in connection with use of or reliance on any such content, goods or services available on or through any such web sites or services.\r\nWe strongly advise you to read the terms and conditions and privacy policies of any third-party web sites or services that you visit. Governing Law\r\nThese Terms shall be governed and construed in accordance with the laws of New York, United States, without regard to its conflict of law provisions.\r\nOur failure to enforce any right or provision of these Terms will not be considered a waiver of those rights. If any provision of these Terms is held to be invalid or unenforceable by a court, the remaining provisions of these Terms will remain in effect. These Terms constitute the entire agreement between us regarding our Service, and supersede and replace any prior agreements we might have between us regarding the Service. Changes\r\nWe reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material we will try to provide at least 30 days notice prior to any new terms taking effect. What constitutes a material change will be determined at our sole discretion.\r\nBy continuing to access or use our Service after those revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, please stop using the Service. Contact Us\r\nIf you have any questions about these Terms, please contact us. </FONT></P>'),
(2, 'Privacy Policy', 'privacy', '<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">At vero eos et accusamus et iusto odio dignissimos ducimus qui blanditiis praesentium voluptatum deleniti atque corrupti quos dolores et quas molestias excepturi sint occaecati cupiditate non provident, similique sunt in culpa qui officia deserunt mollitia animi, id est laborum et dolorum fuga. Et harum quidem rerum facilis est et expedita distinctio. Nam libero tempore, cum soluta nobis est eligendi optio cumque nihil impedit quo minus id quod maxime placeat facere possimus, omnis voluptas assumenda est, omnis dolor repellendus. Temporibus autem quibusdam et aut officiis debitis aut rerum necessitatibus saepe eveniet ut et voluptates repudiandae sint et molestiae non recusandae. Itaque earum rerum hic tenetur a sapiente delectus, ut aut reiciendis voluptatibus maiores alias consequatur aut perferendis doloribus asperiores repellat</span>'),
(3, 'About Us ', 'aboutus', 'Welcome to A+ motobike platform! We are an online platform specifically designed for motorcycle enthusiasts, dedicated to providing a safe, reliable, and convenient marketplace for used motorcycles, parts, and gear.\r\n\r\nAs fellow members of the motorcycle community, we understand that finding the perfect ride, sourcing the right parts, or upgrading your equipment requires both specialized knowledge and, crucially, trust. That\'\'s why we created A+ motobike platform – to address these very needs for riders everywhere.\r\n\r\nOur Mission\r\nOur mission is to build a vibrant motorcycle trading ecosystem where every user can easily:\r\nDiscover: From classic vintage bikes to the latest models, from OEM parts to aftermarket accessories, and from essential safety gear to stylish riding apparel, you\'ll find what you\'re looking for here.\r\nSell: Safely and efficiently connect your beloved bike, spare parts, or unused gear with its next owner, ensuring nothing goes to waste.\r\nConnect: More than just a marketplace, we aim to be a central hub for motorcycle enthusiasts to share insights, exchange experiences, and build lasting connections.\r\n\r\nWhy Choose Us?\r\nFocused & Expert: We specialize exclusively in the motorcycle domain, understanding its unique characteristics and committing to provide services tailored precisely to the needs of riders.\r\nSeamless User Experience: Our intuitive website design and powerful search functions make Browse effortless and finding what you need quick and easy.\r\nDiverse Product Selection: Whether you\'re searching for a complete bike, engine components, tires, braking systems, or gear like helmets, gloves, and protective jackets, you\'ll find an extensive selection here.\r\nWe believe every motorcycle has a unique story, and every part carries a rider\'s passion. At A+ motobike platform, we hope to help these stories and passions continue\'.\r\n\r\nJoin us and experience the limitless thrill of the motorcycle world!'),
(11, 'FAQs', 'faqs', '																														<span style=\"color: rgb(0, 0, 0); font-family: &quot;Open Sans&quot;, Arial, sans-serif; font-size: 14px; text-align: justify;\">How do I use discounts coupons?\r\nExcept for promotion codes, Our discounts are applied automatically if your reservation meets any of the criteria mentioned above.\r\n\r\nTo use a promotion code, simply enter the code on the homepage widget as you start your reservation. You can do this by selecting the \"Have a promotion code?\" prompt. Promotion codes can also be entered on the checkout page, under Reservation Total. Please note that the promotion code prompt does not appear for certain types of reservations, such as reservations made on the Deals page.\r\n<br>\r\nOur Discounts Terms & Conditions\r\nWe no longer offer or accept returning customer discounts. All discounts are non-transferable and cannot be combined with additional promotions or discounts.</br>\r\n\r\n* Liability in case accident:\r\nThe hirer should have coverage through his own accident and liability insurance.\r\nThe renting company is not responsible under any circumstances for accidents or damages caused to the hirer or which the hirer causes to any third party or cases of liability </span>');

-- --------------------------------------------------------

--
-- Table structure for table `tblsubscribers`
--

CREATE TABLE `tblsubscribers` (
  `id` int(11) NOT NULL,
  `SubscriberEmail` varchar(120) DEFAULT NULL,
  `PostingDate` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblsubscribers`
--

INSERT INTO `tblsubscribers` (`id`, `SubscriberEmail`, `PostingDate`) VALUES
(1, 'anuj.lpu1@gmail.com', '2017-06-22 16:35:32');

-- --------------------------------------------------------

--
-- Table structure for table `tbltestimonial`
--

CREATE TABLE `tbltestimonial` (
  `id` int(11) NOT NULL,
  `UserEmail` varchar(100) NOT NULL,
  `Testimonial` mediumtext NOT NULL,
  `PostingDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tbltestimonial`
--

INSERT INTO `tbltestimonial` (`id`, `UserEmail`, `Testimonial`, `PostingDate`, `status`) VALUES
(1, 'test@gmail.com', 'This is amazing! I mean really such great bike for rent at affordable price. oh this is crazy man!', '2017-06-18 07:44:31', 1),
(2, 'demo@gmail.com', '\nI think this is the one and only top bike rental site in the world. 5-Stars from me - Full satisfaction, no complain at all', '2017-06-18 07:46:05', 1);

-- --------------------------------------------------------

--
-- Table structure for table `tblusers`
--

CREATE TABLE `tblusers` (
  `id` int(11) NOT NULL,
  `FullName` varchar(120) DEFAULT NULL,
  `EmailId` varchar(100) DEFAULT NULL,
  `Password` varchar(100) DEFAULT NULL,
  `ContactNo` char(11) DEFAULT NULL,
  `dob` varchar(100) DEFAULT NULL,
  `Address` varchar(255) DEFAULT NULL,
  `City` varchar(100) DEFAULT NULL,
  `Country` varchar(100) DEFAULT NULL,
  `RegDate` timestamp NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblusers`
--

INSERT INTO `tblusers` (`id`, `FullName`, `EmailId`, `Password`, `ContactNo`, `dob`, `Address`, `City`, `Country`, `RegDate`, `UpdationDate`) VALUES
(1, 'Harry Den', 'demo@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '2147483647', NULL, NULL, NULL, NULL, '2017-06-17 19:59:27', '2017-06-26 21:02:58'),
(2, 'AK', 'anuj@gmail.com', 'f925916e2754e5e03f75dd58a5733251', '8285703354', NULL, NULL, NULL, NULL, '2017-06-17 20:00:49', '2017-06-26 21:03:09'),
(3, 'Mark K', 'webhostingamigo@gmail.com', 'f09df7868d52e12bba658982dbd79821', '09999857868', '03/02/1990', 'PKL', 'PKL', 'PKL', '2017-06-17 20:01:43', '2017-06-17 21:07:41'),
(4, 'Tom K', 'test@gmail.com', '5c428d8875d2948607f3e3fe134d71b4', '9999857868', '', 'PKL', 'XYZ', 'XYZ', '2017-06-17 20:03:36', '2017-06-26 19:18:14'),
(5, 'Kwan King Foon', 'Aug1233@stu.vtc.edu.hk', 'e10adc3949ba59abbe56e057f20f883e', '91555523', NULL, NULL, NULL, NULL, '2025-07-03 08:04:29', NULL),
(6, 'RC C', 'tihiba8420@axcradio.com', '6048a32c51ce9aa0e2d30cf74db3d59a', '26100146', NULL, NULL, NULL, NULL, '2025-07-06 09:14:06', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbluser_contacts`
--

CREATE TABLE `tbluser_contacts` (
  `id` int(11) NOT NULL,
  `UserId` int(11) NOT NULL,
  `ContactType` varchar(50) NOT NULL,
  `ContactValue` varchar(255) NOT NULL,
  `Description` text DEFAULT NULL,
  `IsPreferred` tinyint(1) DEFAULT 0,
  `CreationDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbluser_contacts`
--

INSERT INTO `tbluser_contacts` (`id`, `UserId`, `ContactType`, `ContactValue`, `Description`, `IsPreferred`, `CreationDate`, `UpdationDate`) VALUES
(1, 5, 'Phone', '23254168', 'Whatsapp or phone', 0, '2025-07-03 16:06:51', NULL),
(4, 2, 'email', 'captmichael@ymail.com', 'mr a', 0, '2025-07-06 08:58:22', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblvehicles`
--

CREATE TABLE `tblvehicles` (
  `id` int(11) NOT NULL,
  `VehiclesTitle` varchar(150) DEFAULT NULL,
  `VehiclesBrand` int(11) DEFAULT NULL,
  `UserId` int(11) DEFAULT NULL,
  `BikeType` varchar(50) DEFAULT NULL,
  `VehiclesOverview` longtext DEFAULT NULL,
  `PricePerDay` int(11) DEFAULT NULL,
  `FuelType` varchar(100) DEFAULT NULL,
  `EngineDisplacement` int(11) DEFAULT NULL,
  `ModelYear` int(6) DEFAULT NULL,
  `SeatingCapacity` int(11) DEFAULT NULL,
  `TransactionCount` int(11) DEFAULT 0,
  `Vimage1` varchar(120) DEFAULT NULL,
  `Vimage2` varchar(120) DEFAULT NULL,
  `Vimage3` varchar(120) DEFAULT NULL,
  `Vimage4` varchar(120) DEFAULT NULL,
  `Vimage5` varchar(120) DEFAULT NULL,
  `AirConditioner` int(11) DEFAULT NULL,
  `PowerDoorLocks` int(11) DEFAULT NULL,
  `AntiLockBrakingSystem` int(11) DEFAULT NULL,
  `BrakeAssist` int(11) DEFAULT NULL,
  `PowerSteering` int(11) DEFAULT NULL,
  `DriverAirbag` int(11) DEFAULT NULL,
  `PassengerAirbag` int(11) DEFAULT NULL,
  `PowerWindows` int(11) DEFAULT NULL,
  `CDPlayer` int(11) DEFAULT NULL,
  `CentralLocking` int(11) DEFAULT NULL,
  `CrashSensor` int(11) DEFAULT NULL,
  `LeatherSeats` int(11) DEFAULT NULL,
  `RegDate` timestamp NOT NULL DEFAULT current_timestamp(),
  `UpdationDate` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `tblvehicles`
--

INSERT INTO `tblvehicles` (`id`, `VehiclesTitle`, `VehiclesBrand`, `UserId`, `BikeType`, `VehiclesOverview`, `PricePerDay`, `FuelType`, `EngineDisplacement`, `ModelYear`, `SeatingCapacity`, `TransactionCount`, `Vimage1`, `Vimage2`, `Vimage3`, `Vimage4`, `Vimage5`, `AirConditioner`, `PowerDoorLocks`, `AntiLockBrakingSystem`, `BrakeAssist`, `PowerSteering`, `DriverAirbag`, `PassengerAirbag`, `PowerWindows`, `CDPlayer`, `CentralLocking`, `CrashSensor`, `LeatherSeats`, `RegDate`, `UpdationDate`) VALUES
(6, '1123', 3, 5, 'Naked', 'honda PCX160', 100, 'Petrol', 160, 2025, 2, 2, 'honda_pcx160.png', 'honda_pcx160.png', 'honda_pcx160.png', 'honda_pcx160.png', '', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-06-24 12:23:23', '2025-07-03 16:07:20'),
(7, 'CB400 3', 3, 5, 'Naked', '2007 has car cam', 28000, 'Petrol', 400, 2007, 2, 3, 'cb400-3.jpg', 'cb400-3-2.jpg', 'cb400-3-3.jpg', 'cb400-4.jpg', '', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-06-26 13:51:23', '2025-07-03 16:19:03'),
(10, 'NINJA 300', 4, NULL, 'Sports', '??TEST', 16000, 'Petrol', 300, 2012, 2, 4, '2013_Kawasaki_Ninja_300_Seattle_Motorcycle_Show.jpg', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-07-04 08:44:13', NULL),
(17, 'AUGUR', 5, 6, 'Scooter', '123', 500, 'Petrol', 155, 2023, 2, 0, 'new-yamaha-augur-155cc-scooter-specs-colour-4.jpg', '', '', '', '', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-07-04 09:50:22', '2025-07-10 16:19:14'),
(18, 'NINJA 300', 4, NULL, 'Sports', 'Test 2', 10000, 'Petrol', 300, 2020, 2, 5, '2013_Kawasaki_Ninja_300_Seattle_Motorcycle_Show.jpg', '', '', '', '', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-07-05 10:44:47', NULL),
(19, 'NINJA 300', 4, 2, 'Sports', 'suzuki ninja 300', 300, 'Petrol', 300, 2020, 2, 4, '2013_Kawasaki_Ninja_300_Seattle_Motorcycle_Show.jpg', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '2025-07-05 17:04:45', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tblvehicle_activity_log`
--

CREATE TABLE `tblvehicle_activity_log` (
  `id` int(11) NOT NULL,
  `VehicleId` int(11) NOT NULL,
  `UserId` int(11) DEFAULT NULL,
  `AdminId` int(11) DEFAULT NULL,
  `ActionType` varchar(100) NOT NULL,
  `ActionDescription` text DEFAULT NULL,
  `ActionDate` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_accessories`
--

CREATE TABLE `tbl_accessories` (
  `accessory_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `condition` varchar(50) DEFAULT NULL,
  `image_url1` varchar(255) DEFAULT NULL,
  `image_url2` varchar(255) DEFAULT NULL,
  `image_url3` varchar(255) DEFAULT NULL,
  `post_date` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `transaction_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_accessories`
--

INSERT INTO `tbl_accessories` (`accessory_id`, `user_id`, `title`, `description`, `price`, `condition`, `image_url1`, `image_url2`, `image_url3`, `post_date`, `last_updated`, `is_active`, `transaction_count`) VALUES
(1, 5, '全新 Brembo M4 卡鉗', '從 Kawasaki ZX-10R 拆下，狀況良好，幾乎全新，帶剎車皮。', 3500.00, '全新', 'acc_brembo_m4_1.jpg', 'acc_brembo_m4_2.jpg', NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(2, 2, 'SHOEI GT-Air II 全罩頭盔', '尺寸 M (57-58cm)，使用約一年，無明顯刮痕，附原廠袋。', 2800.00, '二手', 'acc_shoei_gtair2_1.jpg', 'acc_shoei_gtair2_2.jpg', NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(3, 5, 'Akrapovič 碳纖維全段排氣管', '適用於 Yamaha MT-07 (2018-2022)，提升聲浪與性能，附消音塞。', 4200.00, '二手', 'acc_akrapovic_mt07_1.jpg', 'acc_akrapovic_mt07_2.jpg', NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(4, 2, 'Dainese GORE-TEX 防摔手套', '尺寸 L，冬季防風防水款，九成新，適合香港濕冷天氣。', 850.00, '二手', 'acc_dainese_gloves_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(5, 5, 'MOTUL 300V 4T 機油 1L', '全新未開封，5W-40，酯類配方，適用於高性能電單車。', 180.00, '全新', 'acc_motul300v_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(6, 2, 'GIVI 後箱 E43NTL Advanced', '容量 43 公升，附底座和靠背，可放置一頂全罩頭盔，有鎖匙。', 950.00, '二手', 'acc_givi_e43_1.jpg', 'acc_givi_e43_2.jpg', NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(7, 5, 'Ram Mounts X-Grip 手機導航支架', '通用型，適用於各種手機尺寸，安裝簡單穩固，帶防脫落綁帶。', 150.00, '全新', 'acc_ram_mount_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(8, 2, 'KTM Duke 390 原廠舒適坐墊', '2017 Duke 390 拆下，狀況良好，無破損，比原廠更舒適。', 600.00, '二手', 'acc_duke_seat_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(9, 5, 'ABUS Detecto 7000 RS1 碟煞鎖', '高強度鋼材，帶警報功能，增加安全性，內置震動傳感器。', 780.00, '全新', 'acc_abus_disc_lock_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(10, 2, '復古電單車皮革工具包', '純手工製作，掛載於車側或前叉，風格獨特，全新未用。', 480.00, '全新', 'acc_vintage_bag_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(11, 5, 'NGK Iridium 火星塞 CR9EIA-9', '全新兩支，適用於多種日系跑車，提升點火效率。', 250.00, '全新', 'acc_ngk_sparkplug_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(12, 2, 'GP-Pro 碳纖維手把', '輕量化設計，直徑22mm，適合街車改裝，小刮痕。', 700.00, '二手', 'acc_gp_pro_handlebar_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(13, 5, 'EBC Sintered 剎車皮', '全新一套，適用於Honda CB650R 前輪，高摩擦係數。', 450.00, '全新', 'acc_ebc_brakepads_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(14, 2, ' Sena 50S 藍牙通訊系統', '單機組，使用約半年，功能正常，無配件破損。', 2000.00, '二手', 'acc_sena_50s_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(15, 5, 'K&N 高流量空濾', '適用於 Yamaha R3，可重複清洗使用，提升進氣效率。', 380.00, '二手', 'acc_kn_airfilter_1.jpg', NULL, NULL, '2025-07-08 20:11:40', NULL, 1, 0),
(16, 6, '全地形越野摩托車把手保護罩 - 碳纖維強化', '這是一款專為越野和冒險摩托車設計的碳纖維強化把手保護罩。它能有效保護您的雙手和把手控制裝置免受碎石、樹枝和其他障礙物的傷害，同時提供卓越的抗衝擊性和輕量化設計。易於安裝，適用於多種主流摩托車型號，是追求極限越野體驗騎士的理想選擇。', 580.00, 'New', 'd507981c4fc73a95b20d2525fa463b82.jpeg', '', '', '2025-07-10 00:50:34', NULL, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_accessory_attributes`
--

CREATE TABLE `tbl_accessory_attributes` (
  `attribute_id` int(11) NOT NULL,
  `attribute_name` varchar(100) NOT NULL,
  `attribute_type` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_accessory_attributes`
--

INSERT INTO `tbl_accessory_attributes` (`attribute_id`, `attribute_name`, `attribute_type`) VALUES
(1, '品牌', 'text'),
(2, '尺寸', 'text'),
(3, '材質', 'text'),
(4, '適用車型', 'text'),
(5, '顏色', 'text'),
(6, '容量', 'number'),
(7, '類型', 'text'),
(8, '年份', 'number'),
(9, '保固', 'text'),
(10, '功能', 'text');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_accessory_attribute_values`
--

CREATE TABLE `tbl_accessory_attribute_values` (
  `id` int(11) NOT NULL,
  `accessory_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `attribute_value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_accessory_attribute_values`
--

INSERT INTO `tbl_accessory_attribute_values` (`id`, `accessory_id`, `attribute_id`, `attribute_value`) VALUES
(1, 1, 1, 'Brembo'),
(2, 1, 4, 'Kawasaki ZX-10R'),
(3, 2, 1, 'SHOEI'),
(4, 2, 2, 'M'),
(5, 3, 1, 'Akrapovič'),
(6, 3, 4, 'Yamaha MT-07'),
(7, 4, 1, 'Dainese'),
(8, 4, 2, 'L'),
(9, 5, 1, 'MOTUL'),
(10, 5, 7, '5W-40'),
(11, 6, 1, 'GIVI'),
(12, 6, 6, '43'),
(13, 7, 1, 'Ram Mounts'),
(14, 7, 7, '通用型'),
(15, 8, 1, 'KTM'),
(16, 8, 4, 'Duke 390'),
(17, 9, 1, 'ABUS'),
(18, 9, 10, '警報'),
(19, 10, 3, '皮革'),
(20, 10, 7, '工具包'),
(21, 11, 1, 'NGK'),
(22, 11, 7, 'Iridium'),
(23, 12, 1, 'GP-Pro'),
(24, 12, 2, '22mm'),
(25, 13, 1, 'EBC'),
(26, 13, 4, 'Honda CB650R'),
(27, 14, 1, 'Sena'),
(28, 14, 7, '藍牙通訊'),
(29, 15, 1, 'K&N'),
(30, 15, 4, 'Yamaha R3');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_accessory_categories`
--

CREATE TABLE `tbl_accessory_categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `creation_date` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_accessory_categories`
--

INSERT INTO `tbl_accessory_categories` (`category_id`, `category_name`, `description`, `creation_date`, `last_updated`) VALUES
(1, '電單車零件', '各種用於維修、替換和升級電單車內部及外部組件的配件。', '2025-07-08 20:11:40', NULL),
(2, '駕駛者裝備', '為電單車駕駛者提供保護、舒適和便利的個人穿戴裝備。', '2025-07-08 20:11:40', NULL),
(3, '改裝與性能部品', '旨在提升電單車性能、操控性或個性化外觀的專業部件。', '2025-07-08 20:11:40', NULL),
(4, '維護與清潔用品', '用於電單車日常保養、清潔和長期維護的消耗品及工具。', '2025-07-08 20:11:40', NULL),
(5, '電氣與電子產品', '電單車上的電子設備、照明、線路及其他輔助電器。', '2025-07-08 20:11:40', NULL),
(6, '儲物與旅行裝備', '為長短途旅行提供額外儲物空間及便利性的配件。', '2025-07-08 20:11:40', NULL),
(7, '安全與防護配件', '除個人裝備外，額外提供電單車本身及駕駛者安全保障的產品。', '2025-07-08 20:11:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_accessory_category_map`
--

CREATE TABLE `tbl_accessory_category_map` (
  `accessory_id` int(11) NOT NULL,
  `subcategory_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_accessory_category_map`
--

INSERT INTO `tbl_accessory_category_map` (`accessory_id`, `subcategory_id`) VALUES
(1, 2),
(2, 8),
(3, 15),
(4, 11),
(5, 21),
(6, 31),
(7, 27),
(8, 5),
(9, 36),
(10, 32),
(11, 1),
(12, 19),
(13, 2),
(14, 27),
(15, 16),
(16, 11);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_accessory_contacts`
--

CREATE TABLE `tbl_accessory_contacts` (
  `id` int(11) NOT NULL,
  `accessory_id` int(11) NOT NULL,
  `contact_type` varchar(50) NOT NULL,
  `contact_value` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `creation_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_accessory_contacts`
--

INSERT INTO `tbl_accessory_contacts` (`id`, `accessory_id`, `contact_type`, `contact_value`, `description`, `creation_date`) VALUES
(1, 1, 'Phone', '98765432', 'WhatsApp 或直接撥打', '2025-07-08 12:11:40'),
(2, 2, 'Email', 'user2_contact@example.com', '請發送郵件查詢', '2025-07-08 12:11:40'),
(3, 3, 'WeChat', 'akrapovic_seller', '微信詳談', '2025-07-08 12:11:40'),
(4, 4, 'Phone', '91234567', '最好是下午聯絡', '2025-07-08 12:11:40'),
(5, 5, 'Email', 'motul_dealer@example.com', '批量購買可議價', '2025-07-08 12:11:40'),
(6, 6, 'Phone', '95551234', '可屯門面交', '2025-07-08 12:11:40'),
(7, 7, 'Email', 'gadget_seller@example.com', '支援多種支付方式', '2025-07-08 12:11:40'),
(8, 8, 'Phone', '90008888', '限自取', '2025-07-08 12:11:40'),
(9, 9, 'WhatsApp', 'disc_lock_hk', '提供安裝指導', '2025-07-08 12:11:40'),
(10, 10, 'Email', 'vintage_gear@example.com', '運費另計', '2025-07-08 12:11:40'),
(11, 11, 'Phone', '97776666', '全新火星塞，一次買兩支優惠', '2025-07-08 12:11:40'),
(12, 12, 'Email', 'bike_parts_hk@example.com', '灣仔可交收', '2025-07-08 12:11:40'),
(13, 13, 'WhatsApp', 'brakepads_hk', '多款剎車皮可詢問', '2025-07-08 12:11:40'),
(14, 14, 'Phone', '92221111', 'Sena 藍牙耳機，聲音清晰', '2025-07-08 12:11:40'),
(15, 15, 'Email', 'kn_filter_shop@example.com', '定期清洗可重複使用', '2025-07-08 12:11:40'),
(16, 16, 'WhatsApp', '91234567', '', '2025-07-09 16:50:34');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_accessory_subcategories`
--

CREATE TABLE `tbl_accessory_subcategories` (
  `subcategory_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `subcategory_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `creation_date` datetime DEFAULT current_timestamp(),
  `last_updated` datetime DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tbl_accessory_subcategories`
--

INSERT INTO `tbl_accessory_subcategories` (`subcategory_id`, `category_id`, `subcategory_name`, `description`, `creation_date`, `last_updated`) VALUES
(1, 1, '引擎與傳動系統', '包括汽缸、活塞、離合器、鏈條、皮帶等關鍵動力部件。', '2025-07-08 20:11:40', NULL),
(2, 1, '制動系統', '剎車碟盤、卡鉗、剎車油管、總泵、剎車片等。', '2025-07-08 20:11:40', NULL),
(3, 1, '懸吊系統', '前叉、後避震器、彈簧、油封、連桿等緩衝組件。', '2025-07-08 20:11:40', NULL),
(4, 1, '輪胎與輪圈', '各種尺寸和類型的電單車輪胎、輪框及相關配件。', '2025-07-08 20:11:40', NULL),
(5, 1, '車架與車身零件', '車架、副車架、腳踏、側支架、後照鏡、車殼等。', '2025-07-08 20:11:40', NULL),
(6, 1, '供油系統', '化油器、噴油嘴、燃油泵、燃油濾清器等。', '2025-07-08 20:11:40', NULL),
(7, 1, '轉向系統', '把手、轉向軸承、轉向阻尼器。', '2025-07-08 20:11:40', NULL),
(8, 2, '頭盔', '全罩、3/4罩、半罩、越野盔及相關配件，如風鏡。', '2025-07-08 20:11:40', NULL),
(9, 2, '防摔衣', '皮革防摔衣、紡織防摔衣、網眼防摔衣及內襯。', '2025-07-08 20:11:40', NULL),
(10, 2, '防摔褲', '賽車皮褲、防摔牛仔褲、紡織防摔褲。', '2025-07-08 20:11:40', NULL),
(11, 2, '手套', '夏季手套、冬季手套、賽車手套、GORE-TEX 防水手套。', '2025-07-08 20:11:40', NULL),
(12, 2, '車靴', '賽車靴、休閒騎行靴、防水靴。', '2025-07-08 20:11:40', NULL),
(13, 2, '護具', '護肘、護膝、護背、頸部護具、防護背心。', '2025-07-08 20:11:40', NULL),
(14, 2, '內襯與底層衣', '吸濕排汗內襯、保暖底層衣。', '2025-07-08 20:11:40', NULL),
(15, 3, '排氣系統升級', '改裝消音器、全段排氣管、中段管、觸媒轉換器。', '2025-07-08 20:11:40', NULL),
(16, 3, '動力與電控改裝', '性能空濾、改裝ECU、快速換檔器、動力指令器。', '2025-07-08 20:11:40', NULL),
(17, 3, '燈光與視覺改裝', 'LED大燈、改裝方向燈、尾燈、整流罩、風鏡。', '2025-07-08 20:11:40', NULL),
(18, 3, '煞車與懸吊升級', '高性能卡鉗、浮動碟盤、改裝避震器、防甩頭。', '2025-07-08 20:11:40', NULL),
(19, 3, '輕量化與外觀件', '碳纖維部件、鋁合金腳踏後移、拉桿、把手。', '2025-07-08 20:11:40', NULL),
(20, 3, '音響與娛樂系統', '電單車音響、藍牙喇叭。', '2025-07-08 20:11:40', NULL),
(21, 4, '機油與潤滑劑', '引擎機油、齒輪油、剎車油、鏈條油、黃油等。', '2025-07-08 20:11:40', NULL),
(22, 4, '清潔與保養品', '洗車精、打蠟劑、鏈條清潔劑、鍍膜產品、皮革保養劑。', '2025-07-08 20:11:40', NULL),
(23, 4, '基礎維修工具', '扳手套組、螺絲刀套組、胎壓計、鏈條工具。', '2025-07-08 20:11:40', NULL),
(24, 4, '駐車架與頂車機', '前輪駐車架、後輪駐車架、剪式頂車機。', '2025-07-08 20:11:40', NULL),
(25, 4, '濾芯與耗材', '空氣濾芯、機油濾芯、燃油濾芯、火星塞。', '2025-07-08 20:11:40', NULL),
(26, 5, '行車記錄器', '前後雙錄、單錄行車記錄器、配件。', '2025-07-08 20:11:40', NULL),
(27, 5, '導航與通訊系統', 'GPS導航儀、藍牙通訊耳機、對講機。', '2025-07-08 20:11:40', NULL),
(28, 5, 'USB充電與電源轉換', '電單車專用USB充電孔、點煙器插座、電源轉換器。', '2025-07-08 20:11:40', NULL),
(29, 5, '感應器與監測', '胎壓監測系統(TPMS)、電壓表。', '2025-07-08 20:11:40', NULL),
(30, 5, '燈光與電線', '輔助照明燈、線束、保險絲。', '2025-07-08 20:11:40', NULL),
(31, 6, '後箱與側箱', '硬殼後箱、軟殼側箱、邊包、箱架。', '2025-07-08 20:11:40', NULL),
(32, 6, '油箱包與尾包', '磁吸油箱包、綁帶油箱包、後座包、防水尾包。', '2025-07-08 20:11:40', NULL),
(33, 6, '行李網與綁帶', '彈力網、行李綁帶、行李繩。', '2025-07-08 20:11:40', NULL),
(34, 6, '防水袋與背包', '防水背包、防水行李袋、捲口包。', '2025-07-08 20:11:40', NULL),
(35, 6, '車罩', '室內防塵車罩、室外防水防曬車罩。', '2025-07-08 20:11:40', NULL),
(36, 7, '防盜系統', '碟煞鎖、鏈條鎖、警報器、GPS追蹤器。', '2025-07-08 20:11:40', NULL),
(37, 7, '防摔球與保險桿', '引擎防摔球、車身保險桿、水箱護網。', '2025-07-08 20:11:40', NULL),
(38, 7, '胎壓監測系統', '胎內式、胎外式胎壓監測器。', '2025-07-08 20:11:40', NULL),
(39, 7, '輔助照明與警示', '輔助霧燈、高亮度方向燈、爆閃尾燈。', '2025-07-08 20:11:40', NULL),
(40, 7, '緊急救援用品', '補胎工具、急救包。', '2025-07-08 20:11:40', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblblogposts`
--
ALTER TABLE `tblblogposts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblbooking`
--
ALTER TABLE `tblbooking`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblbrands`
--
ALTER TABLE `tblbrands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblpages`
--
ALTER TABLE `tblpages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblsubscribers`
--
ALTER TABLE `tblsubscribers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbltestimonial`
--
ALTER TABLE `tbltestimonial`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tblusers`
--
ALTER TABLE `tblusers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbluser_contacts`
--
ALTER TABLE `tbluser_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserId` (`UserId`);

--
-- Indexes for table `tblvehicles`
--
ALTER TABLE `tblvehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_user_vehicle` (`UserId`);

--
-- Indexes for table `tblvehicle_activity_log`
--
ALTER TABLE `tblvehicle_activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `VehicleId` (`VehicleId`),
  ADD KEY `UserId` (`UserId`),
  ADD KEY `AdminId` (`AdminId`);

--
-- Indexes for table `tbl_accessories`
--
ALTER TABLE `tbl_accessories`
  ADD PRIMARY KEY (`accessory_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `tbl_accessory_attributes`
--
ALTER TABLE `tbl_accessory_attributes`
  ADD PRIMARY KEY (`attribute_id`),
  ADD UNIQUE KEY `attribute_name` (`attribute_name`);

--
-- Indexes for table `tbl_accessory_attribute_values`
--
ALTER TABLE `tbl_accessory_attribute_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_accessory_attribute_unique` (`accessory_id`,`attribute_id`),
  ADD KEY `attribute_id` (`attribute_id`);

--
-- Indexes for table `tbl_accessory_categories`
--
ALTER TABLE `tbl_accessory_categories`
  ADD PRIMARY KEY (`category_id`),
  ADD UNIQUE KEY `category_name` (`category_name`);

--
-- Indexes for table `tbl_accessory_category_map`
--
ALTER TABLE `tbl_accessory_category_map`
  ADD PRIMARY KEY (`accessory_id`,`subcategory_id`),
  ADD KEY `subcategory_id` (`subcategory_id`);

--
-- Indexes for table `tbl_accessory_contacts`
--
ALTER TABLE `tbl_accessory_contacts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `accessory_id` (`accessory_id`);

--
-- Indexes for table `tbl_accessory_subcategories`
--
ALTER TABLE `tbl_accessory_subcategories`
  ADD PRIMARY KEY (`subcategory_id`),
  ADD UNIQUE KEY `subcategory_name` (`subcategory_name`),
  ADD KEY `category_id` (`category_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblblogposts`
--
ALTER TABLE `tblblogposts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'unique identifier', AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblbooking`
--
ALTER TABLE `tblbooking`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tblbrands`
--
ALTER TABLE `tblbrands`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tblcontactusinfo`
--
ALTER TABLE `tblcontactusinfo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblcontactusquery`
--
ALTER TABLE `tblcontactusquery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tblpages`
--
ALTER TABLE `tblpages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tblsubscribers`
--
ALTER TABLE `tblsubscribers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `tbltestimonial`
--
ALTER TABLE `tbltestimonial`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tblusers`
--
ALTER TABLE `tblusers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbluser_contacts`
--
ALTER TABLE `tbluser_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tblvehicles`
--
ALTER TABLE `tblvehicles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `tblvehicle_activity_log`
--
ALTER TABLE `tblvehicle_activity_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_accessories`
--
ALTER TABLE `tbl_accessories`
  MODIFY `accessory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_accessory_attributes`
--
ALTER TABLE `tbl_accessory_attributes`
  MODIFY `attribute_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_accessory_attribute_values`
--
ALTER TABLE `tbl_accessory_attribute_values`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tbl_accessory_categories`
--
ALTER TABLE `tbl_accessory_categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_accessory_contacts`
--
ALTER TABLE `tbl_accessory_contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `tbl_accessory_subcategories`
--
ALTER TABLE `tbl_accessory_subcategories`
  MODIFY `subcategory_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `tbluser_contacts`
--
ALTER TABLE `tbluser_contacts`
  ADD CONSTRAINT `tbluser_contacts_ibfk_1` FOREIGN KEY (`UserId`) REFERENCES `tblusers` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `tblvehicles`
--
ALTER TABLE `tblvehicles`
  ADD CONSTRAINT `fk_user_vehicle` FOREIGN KEY (`UserId`) REFERENCES `tblusers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tblvehicle_activity_log`
--
ALTER TABLE `tblvehicle_activity_log`
  ADD CONSTRAINT `tblvehicle_activity_log_ibfk_1` FOREIGN KEY (`VehicleId`) REFERENCES `tblvehicles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tblvehicle_activity_log_ibfk_2` FOREIGN KEY (`UserId`) REFERENCES `tblusers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tblvehicle_activity_log_ibfk_3` FOREIGN KEY (`AdminId`) REFERENCES `admin` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `tbl_accessories`
--
ALTER TABLE `tbl_accessories`
  ADD CONSTRAINT `tbl_accessories_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `tblusers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `tbl_accessory_attribute_values`
--
ALTER TABLE `tbl_accessory_attribute_values`
  ADD CONSTRAINT `tbl_accessory_attribute_values_ibfk_1` FOREIGN KEY (`accessory_id`) REFERENCES `tbl_accessories` (`accessory_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_accessory_attribute_values_ibfk_2` FOREIGN KEY (`attribute_id`) REFERENCES `tbl_accessory_attributes` (`attribute_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_accessory_category_map`
--
ALTER TABLE `tbl_accessory_category_map`
  ADD CONSTRAINT `tbl_accessory_category_map_ibfk_1` FOREIGN KEY (`accessory_id`) REFERENCES `tbl_accessories` (`accessory_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `tbl_accessory_category_map_ibfk_2` FOREIGN KEY (`subcategory_id`) REFERENCES `tbl_accessory_subcategories` (`subcategory_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_accessory_contacts`
--
ALTER TABLE `tbl_accessory_contacts`
  ADD CONSTRAINT `tbl_accessory_contacts_ibfk_1` FOREIGN KEY (`accessory_id`) REFERENCES `tbl_accessories` (`accessory_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `tbl_accessory_subcategories`
--
ALTER TABLE `tbl_accessory_subcategories`
  ADD CONSTRAINT `tbl_accessory_subcategories_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `tbl_accessory_categories` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
