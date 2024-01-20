-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 25, 2023 at 02:46 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `upload_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `abouttable`
--

CREATE TABLE `abouttable` (
  `about_id` int(11) NOT NULL,
  `about_content` text NOT NULL,
  `about_img` varchar(255) NOT NULL,
  `about_component` varchar(20) NOT NULL,
  `about_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `abouttable`
--

INSERT INTO `abouttable` (`about_id`, `about_content`, `about_img`, `about_component`, `about_status`) VALUES
(9, 'Civic Welfare Training Service (CWTS): The CWTS component emphasizes community service and engagement. Students involved in CWTS participate in various activities that contribute to community development, such as environmental projects, health programs, and literacy campaigns.', '../assets/img/aboutimg/cwtsbook.png', 'CWTS', 1),
(10, 'Reserve Officer Training Corps (ROTC): This component focuses on military training and instilling discipline, leadership, and patriotism among students. It involves physical training, drills, and lessons on national defense.', '../assets/img/aboutimg/rotcflag.png', 'ROTC', 1);

-- --------------------------------------------------------

--
-- Table structure for table `activitylocation`
--

CREATE TABLE `activitylocation` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(255) NOT NULL,
  `location_latitude` double(9,6) NOT NULL,
  `location_longitude` double(9,6) NOT NULL,
  `group_id` int(11) NOT NULL,
  `publish` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `activitylocation`
--

INSERT INTO `activitylocation` (`location_id`, `location_name`, `location_latitude`, `location_longitude`, `group_id`, `publish`) VALUES
(2, 'Nawasa Road Silangan I, Rosario, Cavite, Philippines', 14.414317, 120.860365, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `announcementtable`
--

CREATE TABLE `announcementtable` (
  `announcement_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `recipient_id` int(11) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `subject` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `reciever` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `activity_scheduled` datetime DEFAULT NULL,
  `announcement_batch` int(11) NOT NULL,
  `view_status` tinyint(5) NOT NULL DEFAULT 1,
  `sender_view` tinyint(4) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `announcementtable`
--

INSERT INTO `announcementtable` (`announcement_id`, `sender_id`, `recipient_id`, `email_address`, `subject`, `message`, `reciever`, `created_at`, `activity_scheduled`, `announcement_batch`, `view_status`, `sender_view`) VALUES
(1, 48, 64, 'RaffWilflinger@cvsu.edu.ph', 'Incoming Schedule Activity', 'Who: all student\nWhat: Testing Meeting\nWhen: 2023-11-16 22:50 - 23:50\nWhere: Nawasa Road Silangan I, Rosario, Cavite, Philippines\nNotes: Sample notes.', 'Raff N. Wilflinger', '2023-11-10 13:50:41', NULL, 1, 1, 1),
(2, 1, 47, 'eduardo.tacorda@cvsu.edu.ph', 'tesitn', 'sadasd', 'Coordinator A. First', '2023-11-11 01:35:00', NULL, 2, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendancetable`
--

CREATE TABLE `attendancetable` (
  `attendance_id` int(11) NOT NULL,
  `user_account_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `student_name` varchar(150) NOT NULL,
  `student_number` int(11) NOT NULL,
  `activity_date` date DEFAULT NULL,
  `time-in` time DEFAULT NULL,
  `time-out` time DEFAULT NULL,
  `attendance_status` varchar(50) NOT NULL,
  `remark` varchar(255) NOT NULL,
  `remark_status` varchar(11) DEFAULT NULL,
  `trigger_remark` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `calendar_event`
--

CREATE TABLE `calendar_event` (
  `event_id` int(11) NOT NULL,
  `event_name` varchar(255) DEFAULT NULL,
  `event_start_date` date DEFAULT NULL,
  `event_end_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `componenttable`
--

CREATE TABLE `componenttable` (
  `component_id` int(11) NOT NULL,
  `component_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `componenttable`
--

INSERT INTO `componenttable` (`component_id`, `component_name`) VALUES
(1, 'ROTC'),
(2, 'CWTS');

-- --------------------------------------------------------

--
-- Table structure for table `coursetable`
--

CREATE TABLE `coursetable` (
  `course_id` int(11) NOT NULL,
  `course_name` varchar(40) DEFAULT NULL,
  `course_code` varchar(50) NOT NULL,
  `department_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coursetable`
--

INSERT INTO `coursetable` (`course_id`, `course_name`, `course_code`, `department_id`) VALUES
(1, 'Bachelor of Secondary Education', 'BSE', 3),
(2, 'Bachelor Of Technical Vocational Teacher', 'BTVED', 3),
(3, 'BS Business Management', 'BSBM', 5),
(4, 'BS Computer Engineering', 'BSCPE', 6),
(5, 'BS Computer Science', 'BSCOS', 2),
(6, 'BS Electrical Engineering', 'BSEE', 6),
(7, 'BS Hospitality Management', 'BSHM', 5),
(8, 'BS Industrial Education', '', 1),
(9, 'BS Industrial Technology', 'BSIT', 1),
(10, 'BS Information Technology', 'BSINFOTECH', 2),
(11, 'BS Business Administration', 'BSBA', 5);

-- --------------------------------------------------------

--
-- Table structure for table `emergencycontact`
--

CREATE TABLE `emergencycontact` (
  `emergency_id` int(11) NOT NULL,
  `studentNumber` int(11) DEFAULT NULL,
  `guardianName` varchar(50) DEFAULT NULL,
  `guardianRelationship` varchar(30) DEFAULT NULL,
  `guardianContactNumber` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `enrolledstudent`
--

CREATE TABLE `enrolledstudent` (
  `enrolledstudent_id` int(11) NOT NULL,
  `student_number` bigint(20) NOT NULL,
  `student_name` text NOT NULL,
  `student_email` varchar(100) DEFAULT NULL,
  `registration_status` enum('Registered','Not Registered') NOT NULL DEFAULT 'Not Registered',
  `schoolyear_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `enrolledstudent`
--

INSERT INTO `enrolledstudent` (`enrolledstudent_id`, `student_number`, `student_name`, `student_email`, `registration_status`, `schoolyear_id`, `semester_id`) VALUES
(1, 202339978, 'Connor Haruard', 'ConnorHaruard@cvsu.edu.ph', 'Registered', 1, 1),
(2, 202387680, 'Waneta Shapter', 'WanetaShapter@cvsu.edu.ph', 'Registered', 1, 1),
(3, 202371045, 'Clary Wybourne', 'ClaryWybourne@cvsu.edu.ph', 'Registered', 1, 1),
(4, 202336768, 'Von Blumire', 'VonBlumire@cvsu.edu.ph', 'Registered', 1, 1),
(5, 202397651, 'Rochette Bernuzzi', 'RochetteBernuzzi@cvsu.edu.ph', 'Registered', 1, 1),
(6, 202342406, 'Aeriel Tesoe', 'AerielTesoe@cvsu.edu.ph', 'Registered', 1, 1),
(7, 202332582, 'Jae Grandin', 'JaeGrandin@cvsu.edu.ph', 'Registered', 1, 1),
(8, 202395022, 'Cortie Huguenet', 'CortieHuguenet@cvsu.edu.ph', 'Registered', 1, 1),
(9, 202391183, 'Halli Whatham', 'HalliWhatham@cvsu.edu.ph', 'Registered', 1, 1),
(10, 202374285, 'Merl Keyte', 'MerlKeyte@cvsu.edu.ph', 'Registered', 1, 1),
(11, 202335975, 'Shana Vasyanin', 'ShanaVasyanin@cvsu.edu.ph', 'Registered', 1, 1),
(12, 202349667, 'Jeffie Hedlestone', 'JeffieHedlestone@cvsu.edu.ph', 'Registered', 1, 1),
(13, 202330587, 'Randolf Coulthurst', 'RandolfCoulthurst@cvsu.edu.ph', 'Registered', 1, 1),
(14, 202301274, 'Lauralee Stubbe', 'LauraleeStubbe@cvsu.edu.ph', 'Registered', 1, 1),
(15, 202317876, 'Raff Wilflinger', 'RaffWilflinger@cvsu.edu.ph', 'Registered', 1, 1),
(16, 202371035, 'Viva Mart', 'VivaMart@cvsu.edu.ph', 'Not Registered', 1, 1),
(17, 202320057, 'Marlena Burton', 'MarlenaBurton@cvsu.edu.ph', 'Not Registered', 1, 1),
(18, 202332685, 'Udale Stotherfield', 'UdaleStotherfield@cvsu.edu.ph', 'Not Registered', 1, 1),
(19, 202344815, 'Brana McSwan', 'BranaMcSwan@cvsu.edu.ph', 'Not Registered', 1, 1),
(20, 202397701, 'Eldridge Fretter', 'EldridgeFretter@cvsu.edu.ph', 'Not Registered', 1, 1),
(21, 202396973, 'Averil Gloucester', 'AverilGloucester@cvsu.edu.ph', 'Not Registered', 1, 1),
(22, 202379458, 'Kelsey Janoch', 'KelseyJanoch@cvsu.edu.ph', 'Not Registered', 1, 1),
(23, 202376911, 'Susannah Jeeks', 'SusannahJeeks@cvsu.edu.ph', 'Not Registered', 1, 1),
(24, 202355211, 'Sapphira Abrahmson', 'SapphiraAbrahmson@cvsu.edu.ph', 'Not Registered', 1, 1),
(25, 202389297, 'Billie Valeri', 'BillieValeri@cvsu.edu.ph', 'Not Registered', 1, 1),
(26, 202367674, 'Sly Orpin', 'SlyOrpin@cvsu.edu.ph', 'Not Registered', 1, 1),
(27, 202394295, 'Flss Gave', 'FlssGave@cvsu.edu.ph', 'Not Registered', 1, 1),
(28, 202361959, 'Marilin Molder', 'MarilinMolder@cvsu.edu.ph', 'Not Registered', 1, 1),
(29, 202309603, 'Siana Glauber', 'SianaGlauber@cvsu.edu.ph', 'Not Registered', 1, 1),
(30, 202396473, 'Paige Sidwell', 'PaigeSidwell@cvsu.edu.ph', 'Not Registered', 1, 1),
(31, 202391029, 'Avram Rushe', 'AvramRushe@cvsu.edu.ph', 'Not Registered', 1, 1),
(32, 202359264, 'Alexina Dorrity', 'AlexinaDorrity@cvsu.edu.ph', 'Not Registered', 1, 1),
(33, 202333286, 'Carlynn Marmyon', 'CarlynnMarmyon@cvsu.edu.ph', 'Not Registered', 1, 1),
(34, 202378421, 'Shayne Kenyon', 'ShayneKenyon@cvsu.edu.ph', 'Not Registered', 1, 1),
(35, 202353834, 'Abbye Tysall', 'AbbyeTysall@cvsu.edu.ph', 'Not Registered', 1, 1),
(36, 202347092, 'Greggory Tourmell', 'GreggoryTourmell@cvsu.edu.ph', 'Not Registered', 1, 1),
(37, 202327052, 'Pooh Maleham', 'PoohMaleham@cvsu.edu.ph', 'Not Registered', 1, 1),
(38, 202312815, 'Loria Roycroft', 'LoriaRoycroft@cvsu.edu.ph', 'Not Registered', 1, 1),
(39, 202316931, 'Chicky Pankethman', 'ChickyPankethman@cvsu.edu.ph', 'Not Registered', 1, 1),
(40, 202310191, 'Moses Vanne', 'MosesVanne@cvsu.edu.ph', 'Not Registered', 1, 1),
(41, 202354756, 'Domeniga Maw', 'DomenigaMaw@cvsu.edu.ph', 'Not Registered', 1, 1),
(42, 202382066, 'Nannette Eyton', 'NannetteEyton@cvsu.edu.ph', 'Not Registered', 1, 1),
(43, 202308217, 'Guglielmo Blinkhorn', 'GuglielmoBlinkhorn@cvsu.edu.ph', 'Not Registered', 1, 1),
(44, 202335993, 'Catlaina Touson', 'CatlainaTouson@cvsu.edu.ph', 'Not Registered', 1, 1),
(45, 202398106, 'Anatollo Gabbidon', 'AnatolloGabbidon@cvsu.edu.ph', 'Not Registered', 1, 1),
(46, 202312515, 'Dean Hollow', 'DeanHollow@cvsu.edu.ph', 'Not Registered', 1, 1),
(47, 202387206, 'Irita Richmond', 'IritaRichmond@cvsu.edu.ph', 'Not Registered', 1, 1),
(48, 202327885, 'Linnell Milnthorpe', 'LinnellMilnthorpe@cvsu.edu.ph', 'Not Registered', 1, 1),
(49, 202373268, 'Roby Sexcey', 'RobySexcey@cvsu.edu.ph', 'Not Registered', 1, 1),
(50, 202306899, 'Egor Pelcheur', 'EgorPelcheur@cvsu.edu.ph', 'Not Registered', 1, 1),
(51, 202364100, 'Valencia Di Napoli', 'ValenciaDiNapoli@cvsu.edu.ph', 'Not Registered', 1, 1),
(52, 202329753, 'Jacobo Duchart', 'JacoboDuchart@cvsu.edu.ph', 'Not Registered', 1, 1),
(53, 202316398, 'Wood Wisher', 'WoodWisher@cvsu.edu.ph', 'Not Registered', 1, 1),
(54, 202319778, 'Isa Aphale', 'IsaAphale@cvsu.edu.ph', 'Not Registered', 1, 1),
(55, 202396035, 'Layton Rubinlicht', 'LaytonRubinlicht@cvsu.edu.ph', 'Not Registered', 1, 1),
(56, 202307218, 'Gina Darkins', 'GinaDarkins@cvsu.edu.ph', 'Not Registered', 1, 1),
(57, 202349555, 'Stanislaw Flahive', 'StanislawFlahive@cvsu.edu.ph', 'Not Registered', 1, 1),
(58, 202304998, 'Farrah Redhole', 'FarrahRedhole@cvsu.edu.ph', 'Not Registered', 1, 1),
(59, 202388171, 'Ardine Petriello', 'ArdinePetriello@cvsu.edu.ph', 'Not Registered', 1, 1),
(60, 202333953, 'Winnah Poll', 'WinnahPoll@cvsu.edu.ph', 'Not Registered', 1, 1),
(61, 202324273, 'Marla Schwanden', 'MarlaSchwanden@cvsu.edu.ph', 'Not Registered', 1, 1),
(62, 202330513, 'Robinia Baughen', 'RobiniaBaughen@cvsu.edu.ph', 'Not Registered', 1, 1),
(63, 202308344, 'Kathy Shearmur', 'KathyShearmur@cvsu.edu.ph', 'Not Registered', 1, 1),
(64, 202387504, 'Deana Wantling', 'DeanaWantling@cvsu.edu.ph', 'Not Registered', 1, 1),
(65, 202378202, 'Darelle Gadault', 'DarelleGadault@cvsu.edu.ph', 'Not Registered', 1, 1),
(66, 202320118, 'Dehlia Pye', 'DehliaPye@cvsu.edu.ph', 'Not Registered', 1, 1),
(67, 202378280, 'Juditha Esch', 'JudithaEsch@cvsu.edu.ph', 'Not Registered', 1, 1),
(68, 202378970, 'Damien Bangle', 'DamienBangle@cvsu.edu.ph', 'Not Registered', 1, 1),
(69, 202384091, 'Felike De La Cote', 'FelikeDeLaCote@cvsu.edu.ph', 'Not Registered', 1, 1),
(70, 202386283, 'Babette Manach', 'BabetteManach@cvsu.edu.ph', 'Not Registered', 1, 1),
(71, 202305520, 'Kipp Futter', 'KippFutter@cvsu.edu.ph', 'Not Registered', 1, 1),
(72, 202384954, 'Kathryn Caplin', 'KathrynCaplin@cvsu.edu.ph', 'Not Registered', 1, 1),
(73, 202343873, 'Monica Hedan', 'MonicaHedan@cvsu.edu.ph', 'Not Registered', 1, 1),
(74, 202398736, 'Morly Grzeszczyk', 'MorlyGrzeszczyk@cvsu.edu.ph', 'Not Registered', 1, 1),
(75, 202391655, 'Raimund Skeech', 'RaimundSkeech@cvsu.edu.ph', 'Not Registered', 1, 1),
(76, 202339623, 'Missie Limer', 'MissieLimer@cvsu.edu.ph', 'Not Registered', 1, 1),
(77, 202361176, 'Karon Yates', 'KaronYates@cvsu.edu.ph', 'Not Registered', 1, 1),
(78, 202313729, 'Merilyn Oakton', 'MerilynOakton@cvsu.edu.ph', 'Not Registered', 1, 1),
(79, 202393002, 'Bonita Banke', 'BonitaBanke@cvsu.edu.ph', 'Not Registered', 1, 1),
(80, 202382961, 'Erek Carvil', 'ErekCarvil@cvsu.edu.ph', 'Not Registered', 1, 1),
(81, 202360510, 'Faye Cassell', 'FayeCassell@cvsu.edu.ph', 'Not Registered', 1, 1),
(82, 202368191, 'Horatia Gladden', 'HoratiaGladden@cvsu.edu.ph', 'Not Registered', 1, 1),
(83, 202306628, 'Fletcher Starte', 'FletcherStarte@cvsu.edu.ph', 'Not Registered', 1, 1),
(84, 202349074, 'Fanya McGillicuddy', 'FanyaMcGillicuddy@cvsu.edu.ph', 'Not Registered', 1, 1),
(85, 202369446, 'Kathie Puddicombe', 'KathiePuddicombe@cvsu.edu.ph', 'Not Registered', 1, 1),
(86, 202359896, 'Evita Satterthwaite', 'EvitaSatterthwaite@cvsu.edu.ph', 'Not Registered', 1, 1),
(87, 202359771, 'Hervey Rosso', 'HerveyRosso@cvsu.edu.ph', 'Not Registered', 1, 1),
(88, 202318331, 'Dalton Goodding', 'DaltonGoodding@cvsu.edu.ph', 'Not Registered', 1, 1),
(89, 202386730, 'Gusta Hindshaw', 'GustaHindshaw@cvsu.edu.ph', 'Not Registered', 1, 1),
(90, 202382454, 'Osborn Royston', 'OsbornRoyston@cvsu.edu.ph', 'Not Registered', 1, 1),
(91, 202394369, 'Annabelle Dorsey', 'AnnabelleDorsey@cvsu.edu.ph', 'Not Registered', 1, 1),
(92, 202357070, 'Shawna Butland', 'ShawnaButland@cvsu.edu.ph', 'Not Registered', 1, 1),
(93, 202395393, 'Vere Jacobs', 'VereJacobs@cvsu.edu.ph', 'Not Registered', 1, 1),
(94, 202367960, 'Sybille Chaytor', 'SybilleChaytor@cvsu.edu.ph', 'Not Registered', 1, 1),
(95, 202381515, 'Eunice Batisse', 'EuniceBatisse@cvsu.edu.ph', 'Not Registered', 1, 1),
(96, 202382474, 'Arabella Gorgler', 'ArabellaGorgler@cvsu.edu.ph', 'Not Registered', 1, 1),
(97, 202394056, 'Jerri Durrad', 'JerriDurrad@cvsu.edu.ph', 'Not Registered', 1, 1),
(98, 202392754, 'Skylar Piddocke', 'SkylarPiddocke@cvsu.edu.ph', 'Not Registered', 1, 1),
(99, 202330960, 'Clerkclaude Locker', 'ClerkclaudeLocker@cvsu.edu.ph', 'Not Registered', 1, 1),
(100, 202394291, 'Emanuel Darell', 'EmanuelDarell@cvsu.edu.ph', 'Not Registered', 1, 1),
(101, 202377523, 'Dewitt Mottershaw', 'DewittMottershaw@cvsu.edu.ph', 'Not Registered', 1, 1),
(102, 202361460, 'Garey Garcia', 'GareyGarcia@cvsu.edu.ph', 'Not Registered', 1, 1),
(103, 202303500, 'Emmy Tinkham', 'EmmyTinkham@cvsu.edu.ph', 'Not Registered', 1, 1),
(104, 202375743, 'Daniele Batcheldor', 'DanieleBatcheldor@cvsu.edu.ph', 'Not Registered', 1, 1),
(105, 202357210, 'Francisca Leaver', 'FranciscaLeaver@cvsu.edu.ph', 'Not Registered', 1, 1),
(106, 202321281, 'Nathaniel Silverton', 'NathanielSilverton@cvsu.edu.ph', 'Not Registered', 1, 1),
(107, 202319631, 'Benedetta Creese', 'BenedettaCreese@cvsu.edu.ph', 'Not Registered', 1, 1),
(108, 202320419, 'Gwendolin Deverose', 'GwendolinDeverose@cvsu.edu.ph', 'Not Registered', 1, 1),
(109, 202391007, 'Aldrich Beade', 'AldrichBeade@cvsu.edu.ph', 'Not Registered', 1, 1),
(110, 202368970, 'Kinny Hamstead', 'KinnyHamstead@cvsu.edu.ph', 'Not Registered', 1, 1),
(111, 202395301, 'Bibby Brickett', 'BibbyBrickett@cvsu.edu.ph', 'Not Registered', 1, 1),
(112, 202348514, 'Jennica Bellson', 'JennicaBellson@cvsu.edu.ph', 'Not Registered', 1, 1),
(113, 202367805, 'Jesselyn Sackes', 'JesselynSackes@cvsu.edu.ph', 'Not Registered', 1, 1),
(114, 202320556, 'Matelda Dewes', 'MateldaDewes@cvsu.edu.ph', 'Not Registered', 1, 1),
(115, 202391944, 'Chrissy Grieves', 'ChrissyGrieves@cvsu.edu.ph', 'Not Registered', 1, 1),
(116, 202339513, 'Berta Kalker', 'BertaKalker@cvsu.edu.ph', 'Not Registered', 1, 1),
(117, 202313529, 'Tarah Rabier', 'TarahRabier@cvsu.edu.ph', 'Not Registered', 1, 1),
(118, 202359893, 'Angelico Di Domenico', 'AngelicoDiDomenico@cvsu.edu.ph', 'Not Registered', 1, 1),
(119, 202399090, 'Cory Saddleton', 'CorySaddleton@cvsu.edu.ph', 'Not Registered', 1, 1),
(120, 202363542, 'Zsazsa Calltone', 'ZsazsaCalltone@cvsu.edu.ph', 'Not Registered', 1, 1),
(121, 202384484, 'Minda Bartoleyn', 'MindaBartoleyn@cvsu.edu.ph', 'Not Registered', 1, 1),
(122, 202334208, 'Ruthie Mariner', 'RuthieMariner@cvsu.edu.ph', 'Not Registered', 1, 1),
(123, 202334783, 'Tedda Paulot', 'TeddaPaulot@cvsu.edu.ph', 'Not Registered', 1, 1),
(124, 202371250, 'Alaine MacGilmartin', 'AlaineMacGilmartin@cvsu.edu.ph', 'Not Registered', 1, 1),
(125, 202367552, 'Christiano Le land', 'ChristianoLeland@cvsu.edu.ph', 'Not Registered', 1, 1),
(126, 202381492, 'Cosette Trevillion', 'CosetteTrevillion@cvsu.edu.ph', 'Not Registered', 1, 1),
(127, 202379072, 'Winni Cholwell', 'WinniCholwell@cvsu.edu.ph', 'Not Registered', 1, 1),
(128, 202347670, 'Wain Labin', 'WainLabin@cvsu.edu.ph', 'Not Registered', 1, 1),
(129, 202379463, 'Geneva Ellison', 'GenevaEllison@cvsu.edu.ph', 'Not Registered', 1, 1),
(130, 202301731, 'Chickie Zelake', 'ChickieZelake@cvsu.edu.ph', 'Not Registered', 1, 1),
(131, 202319029, 'Norrie Dowber', 'NorrieDowber@cvsu.edu.ph', 'Not Registered', 1, 1),
(132, 202366814, 'Carmelia Ovize', 'CarmeliaOvize@cvsu.edu.ph', 'Not Registered', 1, 1),
(133, 202304933, 'Alana Kruszelnicki', 'AlanaKruszelnicki@cvsu.edu.ph', 'Not Registered', 1, 1),
(134, 202312781, 'Jon Steabler', 'JonSteabler@cvsu.edu.ph', 'Not Registered', 1, 1),
(135, 202359608, 'Tomasine Stockton', 'TomasineStockton@cvsu.edu.ph', 'Not Registered', 1, 1),
(136, 202331888, 'Cris Spackman', 'CrisSpackman@cvsu.edu.ph', 'Not Registered', 1, 1),
(137, 202340974, 'Benoite Vipan', 'BenoiteVipan@cvsu.edu.ph', 'Not Registered', 1, 1),
(138, 202320245, 'Carmelle Songer', 'CarmelleSonger@cvsu.edu.ph', 'Not Registered', 1, 1),
(139, 202304490, 'Thelma Crosby', 'ThelmaCrosby@cvsu.edu.ph', 'Not Registered', 1, 1),
(140, 202385554, 'Avrom Garci', 'AvromGarci@cvsu.edu.ph', 'Not Registered', 1, 1),
(141, 202386698, 'Nelli Lapthorne', 'NelliLapthorne@cvsu.edu.ph', 'Not Registered', 1, 1),
(142, 202350886, 'Hanson Ruprich', 'HansonRuprich@cvsu.edu.ph', 'Not Registered', 1, 1),
(143, 202366535, 'Laurie Bougourd', 'LaurieBougourd@cvsu.edu.ph', 'Not Registered', 1, 1),
(144, 202360439, 'Garreth Royl', 'GarrethRoyl@cvsu.edu.ph', 'Not Registered', 1, 1),
(145, 202367094, 'Vitoria McCosker', 'VitoriaMcCosker@cvsu.edu.ph', 'Not Registered', 1, 1),
(146, 202303674, 'Editha Igoe', 'EdithaIgoe@cvsu.edu.ph', 'Not Registered', 1, 1),
(147, 202314940, 'Demetrius Haslum', 'DemetriusHaslum@cvsu.edu.ph', 'Not Registered', 1, 1),
(148, 202346101, 'Barret Wing', 'BarretWing@cvsu.edu.ph', 'Not Registered', 1, 1),
(149, 202339538, 'Shantee Stoffer', 'ShanteeStoffer@cvsu.edu.ph', 'Not Registered', 1, 1),
(150, 202320480, 'Waly McCoveney', 'WalyMcCoveney@cvsu.edu.ph', 'Not Registered', 1, 1),
(151, 202347601, 'Paddie Lambis', 'PaddieLambis@cvsu.edu.ph', 'Not Registered', 1, 1),
(152, 202314328, 'Ermina Burrell', 'ErminaBurrell@cvsu.edu.ph', 'Not Registered', 1, 1),
(153, 202359735, 'Patsy Woodyear', 'PatsyWoodyear@cvsu.edu.ph', 'Not Registered', 1, 1),
(154, 202362153, 'Basil Forward', 'BasilForward@cvsu.edu.ph', 'Not Registered', 1, 1),
(155, 202332961, 'Parsifal Fitzpatrick', 'ParsifalFitzpatrick@cvsu.edu.ph', 'Not Registered', 1, 1),
(156, 202337411, 'Addie Buckeridge', 'AddieBuckeridge@cvsu.edu.ph', 'Not Registered', 1, 1),
(157, 202324952, 'Effie Shreeves', 'EffieShreeves@cvsu.edu.ph', 'Not Registered', 1, 1),
(158, 202312632, 'Kirsti Yaknov', 'KirstiYaknov@cvsu.edu.ph', 'Not Registered', 1, 1),
(159, 202375518, 'Joelie Inchley', 'JoelieInchley@cvsu.edu.ph', 'Not Registered', 1, 1),
(160, 202380605, 'Tamar Eisikowitch', 'TamarEisikowitch@cvsu.edu.ph', 'Not Registered', 1, 1),
(161, 202377266, 'Marianna Waugh', 'MariannaWaugh@cvsu.edu.ph', 'Not Registered', 1, 1),
(162, 202388015, 'Ernest Metzel', 'ErnestMetzel@cvsu.edu.ph', 'Not Registered', 1, 1),
(163, 202339205, 'Mellie Gouinlock', 'MellieGouinlock@cvsu.edu.ph', 'Not Registered', 1, 1),
(164, 202358431, 'Erv Shackle', 'ErvShackle@cvsu.edu.ph', 'Not Registered', 1, 1),
(165, 202332068, 'Lilyan Klejna', 'LilyanKlejna@cvsu.edu.ph', 'Not Registered', 1, 1),
(166, 202349252, 'Alys Buckerfield', 'AlysBuckerfield@cvsu.edu.ph', 'Not Registered', 1, 1),
(167, 202309812, 'Viviene Hillock', 'VivieneHillock@cvsu.edu.ph', 'Not Registered', 1, 1),
(168, 202339241, 'Gabby Colbertson', 'GabbyColbertson@cvsu.edu.ph', 'Not Registered', 1, 1),
(169, 202370400, 'Seward Vernon', 'SewardVernon@cvsu.edu.ph', 'Not Registered', 1, 1),
(170, 202335170, 'Cesare Bridewell', 'CesareBridewell@cvsu.edu.ph', 'Not Registered', 1, 1),
(171, 202378957, 'Dennis Presnell', 'DennisPresnell@cvsu.edu.ph', 'Not Registered', 1, 1),
(172, 202391206, 'Shena O\'Sullivan', 'ShenaO\'Sullivan@cvsu.edu.ph', 'Not Registered', 1, 1),
(173, 202375912, 'Alanson Leas', 'AlansonLeas@cvsu.edu.ph', 'Not Registered', 1, 1),
(174, 202391460, 'Temp Borne', 'TempBorne@cvsu.edu.ph', 'Not Registered', 1, 1),
(175, 202361062, 'Parker Hessle', 'ParkerHessle@cvsu.edu.ph', 'Not Registered', 1, 1),
(176, 202352790, 'Hernando Hussell', 'HernandoHussell@cvsu.edu.ph', 'Not Registered', 1, 1),
(177, 202300230, 'Patience Bader', 'PatienceBader@cvsu.edu.ph', 'Not Registered', 1, 1),
(178, 202396271, 'Jocelyne Wisden', 'JocelyneWisden@cvsu.edu.ph', 'Not Registered', 1, 1),
(179, 202379140, 'Chance Bearfoot', 'ChanceBearfoot@cvsu.edu.ph', 'Not Registered', 1, 1),
(180, 202330158, 'Aarika Morrott', 'AarikaMorrott@cvsu.edu.ph', 'Not Registered', 1, 1),
(181, 202340414, 'Mellisent Attryde', 'MellisentAttryde@cvsu.edu.ph', 'Not Registered', 1, 1),
(182, 202394688, 'Matteo Drache', 'MatteoDrache@cvsu.edu.ph', 'Not Registered', 1, 1),
(183, 202322540, 'Tansy Gates', 'TansyGates@cvsu.edu.ph', 'Not Registered', 1, 1),
(184, 202373164, 'Myrvyn Aspital', 'MyrvynAspital@cvsu.edu.ph', 'Not Registered', 1, 1),
(185, 202312633, 'Baily Ourtic', 'BailyOurtic@cvsu.edu.ph', 'Not Registered', 1, 1),
(186, 202314362, 'Kristin Sterman', 'KristinSterman@cvsu.edu.ph', 'Not Registered', 1, 1),
(187, 202342710, 'Sibley Byer', 'SibleyByer@cvsu.edu.ph', 'Not Registered', 1, 1),
(188, 202392200, 'Robinet Havill', 'RobinetHavill@cvsu.edu.ph', 'Not Registered', 1, 1),
(189, 202312213, 'Claudius Gavrieli', 'ClaudiusGavrieli@cvsu.edu.ph', 'Not Registered', 1, 1),
(190, 202321303, 'Chastity Lee', 'ChastityLee@cvsu.edu.ph', 'Not Registered', 1, 1),
(191, 202383692, 'Shannan Beste', 'ShannanBeste@cvsu.edu.ph', 'Not Registered', 1, 1),
(192, 202319788, 'Jo-anne Rhule', 'Jo-anneRhule@cvsu.edu.ph', 'Not Registered', 1, 1),
(193, 202358822, 'Clair Benmore', 'ClairBenmore@cvsu.edu.ph', 'Not Registered', 1, 1),
(194, 202329295, 'North Bedo', 'NorthBedo@cvsu.edu.ph', 'Not Registered', 1, 1),
(195, 202334754, 'Tommy Barthelme', 'TommyBarthelme@cvsu.edu.ph', 'Not Registered', 1, 1),
(196, 202369967, 'Genevieve Sturrock', 'GenevieveSturrock@cvsu.edu.ph', 'Not Registered', 1, 1),
(197, 202300284, 'Peterus Grigoryov', 'PeterusGrigoryov@cvsu.edu.ph', 'Not Registered', 1, 1),
(198, 202370033, 'Durant Hendrickson', 'DurantHendrickson@cvsu.edu.ph', 'Not Registered', 1, 1),
(199, 202338574, 'Raddy Bunford', 'RaddyBunford@cvsu.edu.ph', 'Not Registered', 1, 1),
(200, 202380480, 'Bertram Durnill', 'BertramDurnill@cvsu.edu.ph', 'Not Registered', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `faqtable`
--

CREATE TABLE `faqtable` (
  `faq_id` int(11) NOT NULL,
  `faq_question` text NOT NULL,
  `faq_answer` text NOT NULL,
  `faq_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `faqtable`
--

INSERT INTO `faqtable` (`faq_id`, `faq_question`, `faq_answer`, `faq_status`) VALUES
(12, 'What is NSTP?', 'NSTP stands for National Service Training Program, which is a civic education and defense preparedness program that aims to develop civic consciousness and patriotism among the Filipino youth.', 1),
(13, 'Who are required to take NSTP?', 'All Filipino students who are enrolled in any baccalaureate degree or at least two-year technical-vocational courses are required to take NSTP', 1),
(14, 'What are the benefits of taking NSTP?', 'The benefits of taking NSTP include developing civic consciousness, patriotism, leadership skills, physical fitness, disaster response skills, and social responsibility, among others. It is also a requirement for graduation in most colleges and universities in the Philippines.', 1),
(15, 'What are the components of NSTP?', 'There are two components of NSTP: the Reserve Officers’ Training Corps (ROTC) and the Literacy Training Service (LTS) or the Civic Welfare Training Service (CWTS).', 1),
(16, 'What is ROTC?', 'ROTC is a component of NSTP that trains students in military skills and knowledge, including leadership, discipline, and physical fitness. It is also designed to prepare students for national defense and disaster response.', 1),
(20, 'What is CWTS?', 'CWTS is a component of NSTP that involves community service and outreach programs, such as tree planting, feeding programs, and disaster response activities. It aims to promote social responsibility and citizenship among students.', 1);

-- --------------------------------------------------------

--
-- Table structure for table `filetable`
--

CREATE TABLE `filetable` (
  `file_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `title` text NOT NULL,
  `description` text NOT NULL,
  `file_name` text NOT NULL,
  `file_type` text NOT NULL,
  `file_size` int(11) NOT NULL,
  `date_upload` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `filetable`
--

INSERT INTO `filetable` (`file_id`, `group_id`, `title`, `description`, `file_name`, `file_type`, `file_size`, `date_upload`) VALUES
(1, 3, 'Sample MIS Data', 'Dummy Data Student Number, Student Name, Student Email', 'MISStudent(1).csv', 'text/csv', 5126, '2023-11-10'),
(2, 3, '2nd MIS Data Sample', 'Dummy Data Student Number, Student Name, Student Email', 'MISStudent(2).csv', 'text/csv', 5213, '2023-11-10'),
(3, 3, 'Sample Data of Uploading Grade', 'Dummy Data Student Number, Student Grade', 'uploadGrade.csv', 'text/csv', 2956, '2023-11-10');

-- --------------------------------------------------------

--
-- Table structure for table `gallerytable`
--

CREATE TABLE `gallerytable` (
  `gallery_id` int(11) NOT NULL,
  `gallery_title` varchar(100) NOT NULL,
  `gallery_img` varchar(255) NOT NULL,
  `gallery_component` varchar(20) NOT NULL,
  `gallery_status` int(11) NOT NULL,
  `gallery_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gallerytable`
--

INSERT INTO `gallerytable` (`gallery_id`, `gallery_title`, `gallery_img`, `gallery_component`, `gallery_status`, `gallery_time`) VALUES
(23, 'Tree Planting ', '../assets/img/galleryimg/treeplanting1.jpg', 'CWTS', 1, '2023-08-05 08:46:30'),
(24, 'Tree Planting ', '../assets/img/galleryimg/treeplanting2.jpg', 'CWTS', 1, '2023-08-05 08:46:44'),
(25, 'Tree Planting ', '../assets/img/galleryimg/treeplanting.png', 'CWTS', 1, '2023-08-05 08:46:53'),
(26, 'ROTC Training', '../assets/img/galleryimg/training1.jpg', 'ROTC', 1, '2023-08-05 08:48:45'),
(27, 'ROTC Training', '../assets/img/galleryimg/training2.jpg', 'ROTC', 1, '2023-08-05 08:49:02'),
(28, 'ROTC Training', '../assets/img/galleryimg/training3.jpg', 'ROTC', 1, '2023-08-05 08:49:18');

-- --------------------------------------------------------

--
-- Table structure for table `gradetable`
--

CREATE TABLE `gradetable` (
  `grade_id` int(11) NOT NULL,
  `student_grade` varchar(50) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `group_id` int(11) DEFAULT NULL,
  `schoolyear_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `schedcode` int(11) NOT NULL,
  `timestamp` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `gradetable`
--

INSERT INTO `gradetable` (`grade_id`, `student_grade`, `student_id`, `group_id`, `schoolyear_id`, `semester_id`, `schedcode`, `timestamp`) VALUES
(1, '4', 64, 3, 1, 1, 1, '2023-11-10 13:14:33'),
(2, '3.25', 60, 4, 1, 1, 2, '2023-11-10 13:53:15'),
(3, '2.5', 59, 4, 1, 1, 3, '2023-11-10 13:53:15'),
(4, '3.25', 52, 4, 1, 1, 4, '2023-11-10 13:53:15'),
(5, '2.75', 58, 4, 1, 1, 5, '2023-11-10 13:53:15'),
(6, '4', 53, 4, 1, 1, 6, '2023-11-10 13:53:16');

-- --------------------------------------------------------

--
-- Table structure for table `grouptable`
--

CREATE TABLE `grouptable` (
  `group_id` int(11) NOT NULL,
  `group_name` varchar(50) NOT NULL,
  `incharge_person` int(11) DEFAULT NULL,
  `component_id` int(11) NOT NULL,
  `number_student` int(11) NOT NULL,
  `schoolyear_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `date_created` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `grouptable`
--

INSERT INTO `grouptable` (`group_id`, `group_name`, `incharge_person`, `component_id`, `number_student`, `schoolyear_id`, `semester_id`, `date_created`, `date_updated`) VALUES
(2, 'Theoretical', 4, 1, 37, 1, 1, '2023-11-09 12:41:08', '2023-11-09 13:25:23'),
(3, 'Medic', 3, 1, 37, 1, 1, '2023-11-09 12:41:08', '2023-11-09 13:25:18'),
(4, 'Cluster A', 3, 2, 60, 1, 1, '2023-11-09 12:57:55', '2023-11-09 13:25:14'),
(5, 'Cluster B', NULL, 2, 60, 1, 1, '2023-11-10 12:15:56', '2023-11-10 12:15:56'),
(6, 'Alpha 1st', NULL, 1, 37, 1, 1, '2023-11-10 12:15:56', '2023-11-10 12:15:56');

-- --------------------------------------------------------

--
-- Table structure for table `hometable`
--

CREATE TABLE `hometable` (
  `home_id` int(11) NOT NULL,
  `home_content` text NOT NULL,
  `home_img` varchar(255) NOT NULL,
  `home_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `hometable`
--

INSERT INTO `hometable` (`home_id`, `home_content`, `home_img`, `home_status`) VALUES
(17, 'The \"National Service Training Program (NSTP)\" is a program aimed at enhancing civic consciousness and defense preparedness among the youth. It achieves this by fostering the ethics of service and patriotism through training in its three program components. These components are specifically designed to empower the youth to actively contribute to the general welfare.', '../assets/img/homeimg/hero1.png', 1);

-- --------------------------------------------------------

--
-- Table structure for table `newsupdatetable`
--

CREATE TABLE `newsupdatetable` (
  `newsupdate_id` int(11) NOT NULL,
  `newsupdate_title` varchar(255) DEFAULT NULL,
  `newsupdate_content` text DEFAULT NULL,
  `newsupdate_img` varchar(255) DEFAULT NULL,
  `newsupdate_status` int(11) DEFAULT NULL,
  `newsupdate_date` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `newsupdatetable`
--

INSERT INTO `newsupdatetable` (`newsupdate_id`, `newsupdate_title`, `newsupdate_content`, `newsupdate_img`, `newsupdate_status`, `newsupdate_date`) VALUES
(6, 'Enrollment', 'sample enrollment incoming September.', '../assets/img/newsupdateImg/EnrollNowImage.jpg', 1, '2023-08-05'),
(7, 'Incoming Tree Planting', 'sample content for tree planting incoming sunday', '../assets/img/newsupdateImg/treeplanting.JPG', 1, '2023-08-05'),
(8, 'incoming training ', 'sample content for incoming training in this sunday.', '../assets/img/newsupdateImg/incomingativity.jpg', 1, '2023-08-05');

-- --------------------------------------------------------

--
-- Table structure for table `otptable`
--

CREATE TABLE `otptable` (
  `otp_id` int(11) NOT NULL,
  `otp_user_account_id` int(11) NOT NULL,
  `otp_email` varchar(50) NOT NULL,
  `otp_number` int(11) NOT NULL,
  `otp_request` time NOT NULL,
  `otp_status` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `otptable`
--

INSERT INTO `otptable` (`otp_id`, `otp_user_account_id`, `otp_email`, `otp_number`, `otp_request`, `otp_status`) VALUES
(24, 1063, 'eduardo.tacorda@cvsu.edu.ph', 249437, '18:57:41', NULL),
(25, 54, 'eduardo.tacorda@cvsu.edu.ph', 519984, '12:21:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `profilepicture`
--

CREATE TABLE `profilepicture` (
  `picture_id` int(11) NOT NULL,
  `user_account_id` int(11) NOT NULL,
  `picture_pathfile` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remarkstatustable`
--

CREATE TABLE `remarkstatustable` (
  `remarkstatus_id` int(11) NOT NULL,
  `remarkstatus_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `remarkstatustable`
--

INSERT INTO `remarkstatustable` (`remarkstatus_id`, `remarkstatus_name`) VALUES
(1, 'Pending Report'),
(2, 'Missed Time-out'),
(3, 'Valid Reason'),
(4, 'Invalid Reason/Warning'),
(5, 'Participated'),
(6, 'Inactive Student');

-- --------------------------------------------------------

--
-- Table structure for table `roleaccount`
--

CREATE TABLE `roleaccount` (
  `role_account_id` int(11) NOT NULL,
  `role_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roleaccount`
--

INSERT INTO `roleaccount` (`role_account_id`, `role_name`) VALUES
(1, 'Admin'),
(2, 'Student'),
(3, 'Teacher'),
(4, 'Disable');

-- --------------------------------------------------------

--
-- Table structure for table `schedcodetable`
--

CREATE TABLE `schedcodetable` (
  `schedcode_id` int(11) NOT NULL,
  `schedcode_number` int(11) NOT NULL,
  `lastfourdigit` int(11) NOT NULL,
  `department_id` int(11) NOT NULL,
  `year_level` int(11) NOT NULL,
  `course` varchar(40) NOT NULL,
  `student_section` varchar(5) NOT NULL,
  `schoolyear_id` int(11) NOT NULL,
  `semester_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schedcodetable`
--

INSERT INTO `schedcodetable` (`schedcode_id`, `schedcode_number`, `lastfourdigit`, `department_id`, `year_level`, `course`, `student_section`, `schoolyear_id`, `semester_id`) VALUES
(1, 23110001, 1, 1, 1, 'BS Industrial Education', 'B', 1, 1),
(2, 23150002, 2, 5, 1, 'BS Business Management', 'D', 1, 1),
(3, 23110003, 3, 1, 1, 'BS Industrial Education', 'D', 1, 1),
(4, 23130004, 4, 3, 1, 'Bachelor Of Technical Vocational Teacher', 'A', 1, 1),
(5, 23160005, 5, 6, 1, 'BS Computer Engineering', 'C', 1, 1),
(6, 23160006, 6, 6, 1, 'BS Electrical Engineering', 'E', 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `scheduletable`
--

CREATE TABLE `scheduletable` (
  `schedule_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `location_id` int(11) DEFAULT NULL,
  `schedule_title` text DEFAULT NULL,
  `schedule_date` date NOT NULL,
  `schedule_date_end` date DEFAULT NULL,
  `schedule_start` time DEFAULT NULL,
  `schedule_end` time DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scheduletable`
--

INSERT INTO `scheduletable` (`schedule_id`, `group_id`, `location_id`, `schedule_title`, `schedule_date`, `schedule_date_end`, `schedule_start`, `schedule_end`) VALUES
(1, 3, 2, 'Testing Meeting', '2023-11-16', '2023-11-16', '22:50:00', '23:50:00');

-- --------------------------------------------------------

--
-- Table structure for table `schoolyeartable`
--

CREATE TABLE `schoolyeartable` (
  `schoolyear_id` int(11) NOT NULL,
  `schoolyear_start` varchar(20) DEFAULT NULL,
  `schoolyear_end` varchar(20) DEFAULT NULL,
  `schoolyear` varchar(20) NOT NULL,
  `semester_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `schoolyeartable`
--

INSERT INTO `schoolyeartable` (`schoolyear_id`, `schoolyear_start`, `schoolyear_end`, `schoolyear`, `semester_id`) VALUES
(1, '2023', '2024', '2023', 1);

-- --------------------------------------------------------

--
-- Table structure for table `semestertable`
--

CREATE TABLE `semestertable` (
  `semester_id` int(11) NOT NULL,
  `semester` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `semestertable`
--

INSERT INTO `semestertable` (`semester_id`, `semester`) VALUES
(1, 'First Semester'),
(2, 'Second Semester');

-- --------------------------------------------------------

--
-- Table structure for table `teachertable`
--

CREATE TABLE `teachertable` (
  `teacher_id` int(11) NOT NULL,
  `teacher_name` varchar(100) NOT NULL,
  `teacher_contactnumber` varchar(100) NOT NULL,
  `teacher_address` varchar(255) NOT NULL,
  `teacher_email` varchar(100) NOT NULL,
  `group_id` varchar(50) DEFAULT NULL,
  `component_name` varchar(50) NOT NULL DEFAULT 'CWTS',
  `teacher_uniquenumber` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachertable`
--

INSERT INTO `teachertable` (`teacher_id`, `teacher_name`, `teacher_contactnumber`, `teacher_address`, `teacher_email`, `group_id`, `component_name`, `teacher_uniquenumber`) VALUES
(3, 'Coordinator A. First', '+639465419569', '611 a.c Mercado St. Wawa II Rosa Manila', 'eduardo.tacorda@cvsu.edu.ph', '4', 'CWTS', '74906044');

-- --------------------------------------------------------

--
-- Table structure for table `teamtable`
--

CREATE TABLE `teamtable` (
  `team_id` int(11) NOT NULL,
  `team_name` varchar(50) NOT NULL,
  `team_role` varchar(50) NOT NULL,
  `team_content` varchar(100) NOT NULL,
  `team_fb` varchar(100) DEFAULT NULL,
  `team_twitter` varchar(100) DEFAULT NULL,
  `team_instagram` varchar(100) DEFAULT NULL,
  `team_picture` varchar(150) NOT NULL,
  `team_status` int(11) NOT NULL,
  `onOff` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teamtable`
--

INSERT INTO `teamtable` (`team_id`, `team_name`, `team_role`, `team_content`, `team_fb`, `team_twitter`, `team_instagram`, `team_picture`, `team_status`, `onOff`) VALUES
(10, 'Eduardo Tacorda', 'Developer', 'It\'s going to be hard, but doesn\'t mean impossible.', 'https://www.facebook.com/eduardotacorda17', '', '', '../assets/img/teamimg/1.jpg', 1, 0),
(11, 'Norvine Hermeno', 'Designer', 'Push harder than yesterday if you want a different tommorrow.', '', '', '', '../assets/img/teamimg/4.jpg', 1, 0),
(12, 'Shiela Permacio', 'Content Creator', 'Success is the sum of small efforts, repeated day in and day out.', 'None', 'None', 'None', '../assets/img/teamimg/3.jpg', 1, 0),
(13, 'John Lawrence Jaril', 'Researcher', 'Success is the sum of small efforts, repeated day in and day out.', '', '', '', '../assets/img/teamimg/2(1).jpg', 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `trainertable`
--

CREATE TABLE `trainertable` (
  `trainer_id` int(11) NOT NULL,
  `trainer_name` varchar(100) NOT NULL,
  `trainer_contactnumber` varchar(100) NOT NULL,
  `trainer_address` varchar(255) NOT NULL,
  `trainer_email` varchar(100) NOT NULL,
  `group_id` varchar(100) DEFAULT NULL,
  `component_name` varchar(100) NOT NULL DEFAULT 'ROTC',
  `trainer_uniquenumber` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trainertable`
--

INSERT INTO `trainertable` (`trainer_id`, `trainer_name`, `trainer_contactnumber`, `trainer_address`, `trainer_email`, `group_id`, `component_name`, `trainer_uniquenumber`) VALUES
(3, 'Trainer A. First', '+639465419569', '611 a.c Mercado St. Wawa II Rosa Manila', 'eduardo.tacorda@cvsu.edu.ph', '3', 'ROTC', '40683644'),
(4, 'Trainer A. Second', '09381766918', '611 a.c Mercado St. Wawa II Rosa Manila', 'eduardo.tacorda@cvsu.edu.ph', '2', 'ROTC', '23846922');

-- --------------------------------------------------------

--
-- Table structure for table `useraccount`
--

CREATE TABLE `useraccount` (
  `user_account_id` int(11) NOT NULL,
  `serialNumber` text DEFAULT NULL,
  `password` text DEFAULT NULL,
  `role_account_id` int(11) NOT NULL,
  `surname` varchar(100) NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `middlename` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) NOT NULL,
  `email_address` varchar(255) NOT NULL,
  `contactNumber` varchar(50) NOT NULL,
  `baranggay` varchar(100) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `province` varchar(100) DEFAULT NULL,
  `homeaddress` varchar(255) DEFAULT NULL,
  `course` varchar(100) DEFAULT NULL,
  `year_level` varchar(20) DEFAULT NULL,
  `student_section` varchar(20) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `birthday` varchar(50) NOT NULL,
  `placeOfBirth` text DEFAULT NULL,
  `studentReligion` varchar(50) DEFAULT NULL,
  `studentAge` int(11) DEFAULT NULL,
  `student_number` varchar(100) NOT NULL,
  `studentStatus` varchar(50) DEFAULT NULL,
  `studentHeight` varchar(20) DEFAULT NULL,
  `studentWeight` varchar(20) DEFAULT NULL,
  `studentComplexion` varchar(50) DEFAULT NULL,
  `studentBloodType` varchar(10) DEFAULT NULL,
  `component_name` varchar(20) NOT NULL,
  `spouseName` varchar(50) DEFAULT NULL,
  `spouseContactNumber` varchar(20) DEFAULT NULL,
  `spouseOccupation` varchar(100) DEFAULT NULL,
  `group_id` varchar(50) DEFAULT NULL,
  `user_status` varchar(100) NOT NULL,
  `qrimage` varchar(255) DEFAULT NULL,
  `picture` text DEFAULT NULL,
  `schoolyear_id` int(11) DEFAULT NULL,
  `semester_id` int(11) DEFAULT NULL,
  `sendEmail` int(11) DEFAULT NULL,
  `timeStamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `useraccount`
--

INSERT INTO `useraccount` (`user_account_id`, `serialNumber`, `password`, `role_account_id`, `surname`, `firstname`, `middlename`, `full_name`, `email_address`, `contactNumber`, `baranggay`, `city`, `province`, `homeaddress`, `course`, `year_level`, `student_section`, `gender`, `birthday`, `placeOfBirth`, `studentReligion`, `studentAge`, `student_number`, `studentStatus`, `studentHeight`, `studentWeight`, `studentComplexion`, `studentBloodType`, `component_name`, `spouseName`, `spouseContactNumber`, `spouseOccupation`, `group_id`, `user_status`, `qrimage`, `picture`, `schoolyear_id`, `semester_id`, `sendEmail`, `timeStamp`) VALUES
(1, NULL, 'YWRtaW4=', 1, 'Admin', 'Default', 'Account', 'Default A. Admin', 'admin@gmail', '09465419569', '', '', '', '', NULL, NULL, NULL, '', '', NULL, NULL, 0, '000000001', NULL, NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, NULL, '', NULL, NULL, 1, 1, NULL, '2023-10-17 11:22:50'),
(47, NULL, 'UUdkYWR3dTI=', 3, 'First', 'Coordinator', 'Account', 'Coordinator A. First', 'eduardo.tacorda@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'Department of Art and Sciences', NULL, NULL, '', '2/19/2005', NULL, NULL, NULL, '74906044', NULL, NULL, NULL, NULL, NULL, 'CWTS', NULL, NULL, NULL, '4', 'active', NULL, NULL, 1, 1, NULL, '2023-11-09 13:25:14'),
(48, NULL, 'eENISzZ3cTA=', 3, 'First', 'Trainer', 'Account', 'Trainer A. First', 'eduardo.tacorda@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'Department of Computer Studies', NULL, NULL, 'male', '4/19/2005', NULL, NULL, NULL, '40683644', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '3', 'active', NULL, NULL, 1, 1, NULL, '2023-11-09 13:25:18'),
(49, NULL, 'SXA2dWFpUGg=', 3, 'Second', 'Trainer', 'Account', 'Trainer A. Second', 'eduardo.tacorda@cvsu.edu.ph', '09381766918', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', '', NULL, NULL, 'male', '9/23/2001', NULL, NULL, NULL, '23846922', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '2', 'active', NULL, NULL, 1, 1, NULL, '2023-11-09 13:25:23'),
(50, NULL, 'U2FtcGxlQDEyMw==', 2, 'Haruard', 'Connor', 'Na', 'Connor N. Haruard', 'ConnorHaruard@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'Bachelor of Secondary Education', 'First Year', 'A', 'male', '1/19/1998', NULL, NULL, NULL, '202339978', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '2', 'active', 'qrcodes/ConnorN.Haruard.png', NULL, 1, 1, NULL, '2023-11-09 13:31:02'),
(51, NULL, 'U2FtcGxlQDEyMw==', 2, 'Shapter', 'Waneta', 'Na', 'Waneta N. Shapter', 'WanetaShapter@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosario', 'Cavite', '611 a.c Mercado St. Wawa II Rosario Cavite', 'Bachelor of Secondary Education', 'First Year', 'B', 'male', '1/26/1998', NULL, NULL, NULL, '202387680', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '2', 'active', 'qrcodes/WanetaN.Shapter.png', NULL, 1, 1, NULL, '2023-11-09 13:31:47'),
(52, NULL, 'U2FtcGxlQDEyMw==', 2, 'Wybourne', 'Clary', 'Na', 'Clary N. Wybourne', 'ClaryWybourne@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'Bachelor Of Technical Vocational Teacher', 'First Year', 'A', 'male', '7/19/1998', NULL, NULL, NULL, '202371045', NULL, NULL, NULL, NULL, NULL, 'CWTS', NULL, NULL, NULL, '4', 'active', 'qrcodes/ClaryN.Wybourne.png', NULL, 1, 1, NULL, '2023-11-09 13:32:41'),
(53, NULL, 'U2FtcGxlQDEyMw==', 2, 'Blumire', 'Von', 'Na', 'Von N. Blumire', 'VonBlumire@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'BS Electrical Engineering', 'First Year', 'E', 'male', '2/19/1998', NULL, NULL, NULL, '202336768', NULL, NULL, NULL, NULL, NULL, 'CWTS', NULL, NULL, NULL, '4', 'active', 'qrcodes/VonN.Blumire.png', NULL, 1, 1, NULL, '2023-11-09 13:33:23'),
(54, NULL, 'bmV3U2FtcGxlQDEyMw==', 2, 'Bernuzzi', 'Rochette', 'Na', 'Rochette N. Bernuzzi', 'eduardo.tacorda@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'BS Information Technology', 'First Year', 'B', 'male', '3/3/2009', NULL, NULL, NULL, '202397651', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '2', 'active', 'qrcodes/RochetteN.Bernuzzi.png', NULL, 1, 1, NULL, '2023-11-12 04:22:50'),
(55, NULL, 'U2FtcGxlQDEyMw==', 2, 'Tesoe', 'Aeriel', 'Na', 'Aeriel N. Tesoe', 'AerielTesoe@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'BS Hospitality Management', 'First Year', 'A', 'male', '11/19/1998', NULL, NULL, NULL, '202342406', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '2', 'active', 'qrcodes/AerielN.Tesoe.png', NULL, 1, 1, NULL, '2023-11-09 13:52:18'),
(56, NULL, 'U2FtcGxlQDEyMw==', 2, 'Grandin', 'Jae', 'Na', 'Jae N. Grandin', 'JaeGrandin@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'BS Hospitality Management', 'First Year', 'D', 'male', '1/19/1998', NULL, NULL, NULL, '202332582', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '2', 'active', 'qrcodes/JaeN.Grandin.png', NULL, 1, 1, NULL, '2023-11-09 13:53:02'),
(57, NULL, 'U2FtcGxlQDEyMw==', 2, 'Huguenet', 'Cortie', 'Na', 'Cortie N. Huguenet', 'CortieHuguenet@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'BS Hospitality Management', 'First Year', 'B', 'female', '2/19/2005', NULL, NULL, NULL, '202395022', NULL, NULL, NULL, NULL, NULL, 'CWTS', NULL, NULL, NULL, '4', 'active', 'qrcodes/CortieN.Huguenet.png', NULL, 1, 1, NULL, '2023-11-09 13:53:40'),
(58, NULL, 'U2FtcGxlQDEyMw==', 2, 'Whatham', 'Halli', 'Na', 'Halli N. Whatham', 'HalliWhatham@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosa', 'Manila', '611 a.c Mercado St. Wawa II Rosa Manila', 'BS Computer Engineering', 'First Year', 'C', 'female', '2/19/1998', NULL, NULL, NULL, '202391183', NULL, NULL, NULL, NULL, NULL, 'CWTS', NULL, NULL, NULL, '4', 'active', 'qrcodes/HalliN.Whatham.png', NULL, 1, 1, NULL, '2023-11-09 13:54:24'),
(59, NULL, 'U2FtcGxlQDEyMw==', 2, 'Keyte', 'Merl', 'Na', 'Merl N. Keyte', 'MerlKeyte@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosario', 'Cavite', '611 a.c Mercado St. Wawa II Rosario Cavite', 'BS Industrial Education', 'First Year', 'D', 'female', '2/26/2011', NULL, NULL, NULL, '202374285', NULL, NULL, NULL, NULL, NULL, 'CWTS', NULL, NULL, NULL, '4', 'active', 'qrcodes/MerlN.Keyte.png', NULL, 1, 1, NULL, '2023-11-09 13:55:10'),
(60, NULL, 'U2FtcGxlQDEyMw==', 2, 'Vasyanin', 'Shana', 'Na', 'Shana N. Vasyanin', 'ShanaVasyanin@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosario', 'Cavite', '611 a.c Mercado St. Wawa II Rosario Cavite', 'BS Business Management', 'First Year', 'D', 'male', '2/26/1998', NULL, NULL, NULL, '202335975', NULL, NULL, NULL, NULL, NULL, 'CWTS', NULL, NULL, NULL, '4', 'active', 'qrcodes/ShanaN.Vasyanin.png', NULL, 1, 1, NULL, '2023-11-09 13:56:02'),
(61, NULL, 'U2FtcGxlQDEyMw==', 2, 'Hedlestone', 'Jeffie', 'Na', 'Jeffie N. Hedlestone', 'JeffieHedlestone@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosario', 'Cavite', '611 a.c Mercado St. Wawa II Rosario Cavite', 'BS Business Management', 'First Year', 'A', 'male', '2/26/1998', NULL, NULL, NULL, '202349667', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '2', 'active', 'qrcodes/JeffieN.Hedlestone.png', NULL, 1, 1, NULL, '2023-11-09 13:56:47'),
(62, NULL, 'U2FtcGxlQDEyMw==', 2, 'Coulthurst', 'Randolf', 'Na', 'Randolf N. Coulthurst', 'RandolfCoulthurst@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosario', 'Cavite', '611 a.c Mercado St. Wawa II Rosario Cavite', 'BS Industrial Technology', 'Second Year', 'B', 'male', '2/26/1998', NULL, NULL, NULL, '202330587', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '2', 'active', 'qrcodes/RandolfN.Coulthurst.png', NULL, 1, 1, NULL, '2023-11-09 13:57:33'),
(63, NULL, 'U2FtcGxlQDEyMw==', 2, 'Stubbe', 'Lauralee', 'Na', 'Lauralee N. Stubbe', 'LauraleeStubbe@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosario', 'Cavite', '611 a.c Mercado St. Wawa II Rosario Cavite', 'BS Industrial Education', 'First Year', 'C', 'male', '2/26/1998', NULL, NULL, NULL, '202301274', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '2', 'active', 'qrcodes/LauraleeN.Stubbe.png', NULL, 1, 1, NULL, '2023-11-09 13:58:16'),
(64, NULL, 'U2FtcGxlQDEyMw==', 2, 'Wilflinger', 'Raff', 'Na', 'Raff N. Wilflinger', 'RaffWilflinger@cvsu.edu.ph', '+639465419569', '611 a.c Mercado St. Wawa II', 'Rosario', 'Cavite', '611 a.c Mercado St. Wawa II Rosario Cavite', 'BS Industrial Education', 'First Year', 'B', 'female', '2/26/1998', NULL, NULL, NULL, '202317876', NULL, NULL, NULL, NULL, NULL, 'ROTC', NULL, NULL, NULL, '3', 'active', 'qrcodes/RaffN.Wilflinger.png', NULL, 1, 1, NULL, '2023-11-09 13:58:56');

-- --------------------------------------------------------

--
-- Table structure for table `videostable`
--

CREATE TABLE `videostable` (
  `video_id` int(11) NOT NULL,
  `video_title` text DEFAULT NULL,
  `video_link` text DEFAULT NULL,
  `video_status` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `videostable`
--

INSERT INTO `videostable` (`video_id`, `video_title`, `video_link`, `video_status`) VALUES
(7, 'CAVITE STATE UNIVERSITY - CCAT CAMPUS - PROMOTIONAL VIDEO', '<iframe width=\"950\" height=\"534\" src=\"https://www.youtube.com/embed/f7KY7DTg0tM\" title=\"CAVITE STATE UNIVERSITY - CCAT CAMPUS - PROMOTIONAL VIDEO\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen=\"\"></iframe>', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `abouttable`
--
ALTER TABLE `abouttable`
  ADD PRIMARY KEY (`about_id`);

--
-- Indexes for table `activitylocation`
--
ALTER TABLE `activitylocation`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `announcementtable`
--
ALTER TABLE `announcementtable`
  ADD PRIMARY KEY (`announcement_id`);

--
-- Indexes for table `attendancetable`
--
ALTER TABLE `attendancetable`
  ADD PRIMARY KEY (`attendance_id`);

--
-- Indexes for table `calendar_event`
--
ALTER TABLE `calendar_event`
  ADD PRIMARY KEY (`event_id`);

--
-- Indexes for table `componenttable`
--
ALTER TABLE `componenttable`
  ADD PRIMARY KEY (`component_id`);

--
-- Indexes for table `coursetable`
--
ALTER TABLE `coursetable`
  ADD PRIMARY KEY (`course_id`);

--
-- Indexes for table `emergencycontact`
--
ALTER TABLE `emergencycontact`
  ADD PRIMARY KEY (`emergency_id`);

--
-- Indexes for table `enrolledstudent`
--
ALTER TABLE `enrolledstudent`
  ADD PRIMARY KEY (`enrolledstudent_id`);

--
-- Indexes for table `faqtable`
--
ALTER TABLE `faqtable`
  ADD PRIMARY KEY (`faq_id`);

--
-- Indexes for table `filetable`
--
ALTER TABLE `filetable`
  ADD PRIMARY KEY (`file_id`);

--
-- Indexes for table `gallerytable`
--
ALTER TABLE `gallerytable`
  ADD PRIMARY KEY (`gallery_id`);

--
-- Indexes for table `gradetable`
--
ALTER TABLE `gradetable`
  ADD PRIMARY KEY (`grade_id`);

--
-- Indexes for table `grouptable`
--
ALTER TABLE `grouptable`
  ADD PRIMARY KEY (`group_id`);

--
-- Indexes for table `hometable`
--
ALTER TABLE `hometable`
  ADD PRIMARY KEY (`home_id`);

--
-- Indexes for table `newsupdatetable`
--
ALTER TABLE `newsupdatetable`
  ADD PRIMARY KEY (`newsupdate_id`);

--
-- Indexes for table `otptable`
--
ALTER TABLE `otptable`
  ADD PRIMARY KEY (`otp_id`);

--
-- Indexes for table `profilepicture`
--
ALTER TABLE `profilepicture`
  ADD PRIMARY KEY (`picture_id`);

--
-- Indexes for table `remarkstatustable`
--
ALTER TABLE `remarkstatustable`
  ADD PRIMARY KEY (`remarkstatus_id`);

--
-- Indexes for table `roleaccount`
--
ALTER TABLE `roleaccount`
  ADD PRIMARY KEY (`role_account_id`);

--
-- Indexes for table `schedcodetable`
--
ALTER TABLE `schedcodetable`
  ADD PRIMARY KEY (`schedcode_id`);

--
-- Indexes for table `scheduletable`
--
ALTER TABLE `scheduletable`
  ADD PRIMARY KEY (`schedule_id`);

--
-- Indexes for table `schoolyeartable`
--
ALTER TABLE `schoolyeartable`
  ADD PRIMARY KEY (`schoolyear_id`);

--
-- Indexes for table `semestertable`
--
ALTER TABLE `semestertable`
  ADD PRIMARY KEY (`semester_id`);

--
-- Indexes for table `teachertable`
--
ALTER TABLE `teachertable`
  ADD PRIMARY KEY (`teacher_id`);

--
-- Indexes for table `teamtable`
--
ALTER TABLE `teamtable`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `trainertable`
--
ALTER TABLE `trainertable`
  ADD PRIMARY KEY (`trainer_id`);

--
-- Indexes for table `useraccount`
--
ALTER TABLE `useraccount`
  ADD PRIMARY KEY (`user_account_id`),
  ADD KEY `role_account_id` (`role_account_id`);

--
-- Indexes for table `videostable`
--
ALTER TABLE `videostable`
  ADD PRIMARY KEY (`video_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `abouttable`
--
ALTER TABLE `abouttable`
  MODIFY `about_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `activitylocation`
--
ALTER TABLE `activitylocation`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `announcementtable`
--
ALTER TABLE `announcementtable`
  MODIFY `announcement_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `attendancetable`
--
ALTER TABLE `attendancetable`
  MODIFY `attendance_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calendar_event`
--
ALTER TABLE `calendar_event`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `componenttable`
--
ALTER TABLE `componenttable`
  MODIFY `component_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `coursetable`
--
ALTER TABLE `coursetable`
  MODIFY `course_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `emergencycontact`
--
ALTER TABLE `emergencycontact`
  MODIFY `emergency_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `enrolledstudent`
--
ALTER TABLE `enrolledstudent`
  MODIFY `enrolledstudent_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `faqtable`
--
ALTER TABLE `faqtable`
  MODIFY `faq_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `filetable`
--
ALTER TABLE `filetable`
  MODIFY `file_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `gallerytable`
--
ALTER TABLE `gallerytable`
  MODIFY `gallery_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `gradetable`
--
ALTER TABLE `gradetable`
  MODIFY `grade_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `grouptable`
--
ALTER TABLE `grouptable`
  MODIFY `group_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `hometable`
--
ALTER TABLE `hometable`
  MODIFY `home_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `newsupdatetable`
--
ALTER TABLE `newsupdatetable`
  MODIFY `newsupdate_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `otptable`
--
ALTER TABLE `otptable`
  MODIFY `otp_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `profilepicture`
--
ALTER TABLE `profilepicture`
  MODIFY `picture_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remarkstatustable`
--
ALTER TABLE `remarkstatustable`
  MODIFY `remarkstatus_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `roleaccount`
--
ALTER TABLE `roleaccount`
  MODIFY `role_account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `schedcodetable`
--
ALTER TABLE `schedcodetable`
  MODIFY `schedcode_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `scheduletable`
--
ALTER TABLE `scheduletable`
  MODIFY `schedule_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `schoolyeartable`
--
ALTER TABLE `schoolyeartable`
  MODIFY `schoolyear_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `semestertable`
--
ALTER TABLE `semestertable`
  MODIFY `semester_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teachertable`
--
ALTER TABLE `teachertable`
  MODIFY `teacher_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `teamtable`
--
ALTER TABLE `teamtable`
  MODIFY `team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `trainertable`
--
ALTER TABLE `trainertable`
  MODIFY `trainer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `useraccount`
--
ALTER TABLE `useraccount`
  MODIFY `user_account_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT for table `videostable`
--
ALTER TABLE `videostable`
  MODIFY `video_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `useraccount`
--
ALTER TABLE `useraccount`
  ADD CONSTRAINT `useraccount_ibfk_1` FOREIGN KEY (`role_account_id`) REFERENCES `roleaccount` (`role_account_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
