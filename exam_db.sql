-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 20, 2026 at 11:42 AM
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
-- Database: `exam_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `battle_rooms`
--

CREATE TABLE `battle_rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `max_members` int(11) DEFAULT NULL,
  `referral_code` varchar(20) DEFAULT NULL,
  `admin_id` int(11) DEFAULT NULL,
  `status` enum('waiting','active','closed') DEFAULT 'waiting',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `total_questions` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `battle_rooms`
--

INSERT INTO `battle_rooms` (`id`, `room_name`, `max_members`, `referral_code`, `admin_id`, `status`, `created_at`, `total_questions`) VALUES
(22, 'morning', 3, '3000', 1, 'waiting', '2026-01-24 01:50:33', 3),
(23, 'Jsisn', 646464, 'Jdus', 1, 'waiting', '2026-01-24 10:35:33', 0);

-- --------------------------------------------------------

--
-- Table structure for table `coin_transactions`
--

CREATE TABLE `coin_transactions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `received_coins` int(11) DEFAULT 0,
  `abort_coin` int(11) DEFAULT 0,
  `type` enum('spin','reward','bet','purchase') DEFAULT 'spin',
  `date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `coin_transactions`
--

INSERT INTO `coin_transactions` (`id`, `user_id`, `received_coins`, `abort_coin`, `type`, `date`) VALUES
(5, 1, 50, 0, 'spin', '2026-01-28 06:02:24'),
(6, 1, 100, 25, 'spin', '2026-01-28 06:02:38'),
(7, 1, 25, 25, 'spin', '2026-01-28 06:02:51'),
(8, 1, 25, 25, 'spin', '2026-01-28 06:03:04'),
(9, 1, 50, 25, 'spin', '2026-01-28 06:03:13'),
(10, 1, 0, 25, 'spin', '2026-01-28 06:12:28'),
(11, 1, 10, 25, 'spin', '2026-01-28 15:02:11'),
(12, 1, 10, 25, 'spin', '2026-01-28 15:03:24'),
(13, 1, 25, 25, 'spin', '2026-01-28 15:03:34'),
(14, 1, 5, 0, 'spin', '2026-01-31 05:28:36'),
(15, 1, 25, 25, 'spin', '2026-01-31 08:59:44'),
(16, 1, 0, 0, 'spin', '2026-02-05 16:26:23'),
(17, 1, 0, 25, 'spin', '2026-02-05 16:26:32'),
(18, 1, 5, 25, 'spin', '2026-02-05 16:26:42');

-- --------------------------------------------------------

--
-- Table structure for table `completed_questions`
--

CREATE TABLE `completed_questions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `paper_type` varchar(50) DEFAULT NULL,
  `paper_year` int(11) DEFAULT NULL,
  `question_no` int(11) DEFAULT NULL,
  `status` tinyint(4) DEFAULT 1,
  `is_hard` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `completed_questions`
--

INSERT INTO `completed_questions` (`id`, `user_id`, `subject`, `paper_type`, `paper_year`, `question_no`, `status`, `is_hard`) VALUES
(25, 1, 'ICT', 'MCQ', 2014, 1, 1, 0),
(26, 1, 'ICT', 'MCQ', 2014, 2, 1, 0),
(27, 1, 'ICT', 'MCQ', 2014, 3, 1, 0),
(28, 1, 'ICT', 'MCQ', 2014, 6, 1, 0),
(29, 1, 'ICT', 'MCQ', 2014, 7, 1, 0),
(30, 1, 'ICT', 'MCQ', 2014, 9, 1, 0),
(31, 1, 'ICT', 'MCQ', 2014, 10, 1, 0),
(32, 1, 'ICT', 'MCQ', 2014, 11, 1, 0),
(34, 1, 'ICT', 'MCQ', 2014, 12, 1, 1),
(36, 1, 'ICT', 'MCQ', 2014, 13, 1, 1),
(37, 1, 'ICT', 'MCQ', 2014, 14, 1, 0),
(38, 1, 'ICT', 'MCQ', 2014, 21, 1, 0),
(40, 1, 'ICT', 'MCQ', 2014, 22, 1, 1),
(41, 1, 'ICT', 'MCQ', 2014, 23, 1, 0),
(42, 1, 'ICT', 'MCQ', 2014, 26, 1, 0),
(43, 1, 'ICT', 'MCQ', 2014, 27, 1, 0),
(46, 1, 'ICT', 'MCQ', 2014, 28, 1, 0),
(47, 1, 'ICT', 'MCQ', 2014, 29, 1, 0),
(48, 1, 'ICT', 'MCQ', 2014, 36, 1, 0),
(49, 1, 'ICT', 'MCQ', 2014, 43, 1, 0),
(53, 1, 'ICT', 'MCQ', 2014, 45, 1, 1),
(54, 1, 'ICT', 'MCQ', 2014, 46, 1, 0),
(55, 1, 'ICT', 'MCQ', 2014, 47, 1, 0),
(56, 1, 'ICT', 'MCQ', 2014, 48, 1, 0),
(57, 1, 'Pure Maths', 'Essay', 2025, 4, 1, 0),
(58, 1, 'Pure Maths', 'Essay', 2024, 4, 1, 0),
(59, 1, 'Pure Maths', 'Essay', 2023, 4, 1, 0),
(60, 1, 'Pure Maths', 'Essay', 2022, 4, 1, 0),
(61, 1, 'Pure Maths', 'Essay', 2021, 4, 1, 0),
(62, 1, 'Pure Maths', 'Essay', 2020, 4, 1, 0),
(63, 1, 'Pure Maths', 'Essay', 2019, 4, 1, 0),
(64, 1, 'Pure Maths', 'Essay', 2018, 4, 1, 0),
(65, 1, 'Pure Maths', 'Essay', 2017, 4, 1, 0),
(66, 1, 'Pure Maths', 'Essay', 2016, 4, 1, 0),
(67, 1, 'Pure Maths', 'Essay', 2015, 4, 1, 0),
(68, 1, 'Pure Maths', 'Essay', 2014, 4, 1, 0),
(72, 1, 'Pure Maths', 'Structured', 2014, 2, 1, 0),
(73, 1, 'Pure Maths', 'Structured', 2015, 2, 1, 0),
(74, 1, 'Pure Maths', 'Structured', 2016, 2, 1, 0),
(75, 1, 'Pure Maths', 'Structured', 2017, 2, 1, 0),
(76, 1, 'Pure Maths', 'Structured', 2018, 2, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `exam_results`
--

CREATE TABLE `exam_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject_name` varchar(255) DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `correct_answers` int(11) DEFAULT NULL,
  `time_taken_seconds` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `exam_results`
--

INSERT INTO `exam_results` (`id`, `user_id`, `subject_name`, `total_questions`, `correct_answers`, `time_taken_seconds`, `created_at`) VALUES
(1, 1, 'ICT', 5, 5, 60, '2026-01-22 08:26:58'),
(2, 1, 'ICT', 5, 2, 35, '2026-01-22 14:22:57'),
(3, 1, 'ICT', 5, 1, 5, '2026-01-22 14:26:02'),
(4, 1, 'ICT', 3, 2, 8, '2026-01-22 14:29:35'),
(5, 1, 'ICT', 2, 1, 16, '2026-01-22 14:32:50'),
(6, 1, 'ICT', 2, 0, 6, '2026-01-22 15:32:22'),
(7, 1, 'ICT', 5, 1, 17, '2026-01-31 11:22:31'),
(8, 1, 'ICT', 2, 1, 13, '2026-01-31 13:56:55'),
(9, 1, 'Physics', 1, 0, 5, '2026-01-31 14:19:56'),
(10, 1, 'Pure Maths', 1, 0, 26, '2026-01-31 15:01:24'),
(11, 1, 'Pure Maths', 1, 0, 14, '2026-01-31 15:04:07'),
(12, 1, 'ICT', 1, 0, 56, '2026-01-31 15:31:50'),
(13, 1, 'ICT', 1, 0, 17, '2026-01-31 15:34:49'),
(14, 1, 'ICT', 1, 0, 16, '2026-01-31 15:36:16');

-- --------------------------------------------------------

--
-- Table structure for table `friend_requests`
--

CREATE TABLE `friend_requests` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `status` enum('pending','accepted','declined') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friend_requests`
--

INSERT INTO `friend_requests` (`id`, `sender_id`, `receiver_id`, `status`, `created_at`) VALUES
(1, 4, 1, 'accepted', '2026-01-27 07:25:45'),
(2, 2, 1, 'accepted', '2026-01-27 07:40:43');

-- --------------------------------------------------------

--
-- Table structure for table `goals`
--

CREATE TABLE `goals` (
  `goal_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `goal_duration_seconds` int(11) DEFAULT NULL,
  `focus_period` int(11) DEFAULT NULL,
  `interval_period` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `done` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `goals`
--

INSERT INTO `goals` (`goal_id`, `user_id`, `goal_duration_seconds`, `focus_period`, `interval_period`, `created_at`, `done`) VALUES
(5, 1, 300, 4, 1, '2026-01-28 02:39:37', 1),
(6, 1, 180, 2, 1, '2026-01-28 02:54:07', 1),
(7, 1, 60, 1, 0, '2026-01-28 04:50:17', 1),
(8, 1, 120, 1, 1, '2026-01-28 15:29:07', 1),
(9, 1, 120, 1, 1, '2026-01-31 11:17:25', 1),
(10, 1, 120, 1, 1, '2026-02-05 12:53:41', 1),
(11, 1, 120, 1, 1, '2026-02-05 13:05:29', 1),
(12, 1, 7200, 45, 10, '2026-02-05 16:28:42', 0),
(13, 1, 3600, 30, 5, '2026-02-14 13:47:59', 0);

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `sender_id` int(11) DEFAULT NULL,
  `receiver_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `message`, `is_read`, `created_at`) VALUES
(3, 4, 1, 'hello', 1, '2026-01-27 07:34:58'),
(4, 1, 4, 'hello', 0, '2026-01-27 07:37:44'),
(5, 4, 1, 'test2', 1, '2026-01-27 07:38:15'),
(6, 1, 2, 'hellowwwwww', 1, '2026-01-27 07:41:11'),
(7, 2, 1, 'hi bro kohomd', 1, '2026-01-27 07:47:27'),
(8, 2, 1, 'rnfgsed', 1, '2026-01-27 07:47:33'),
(9, 1, 2, 'hmm', 1, '2026-01-27 07:48:22'),
(10, 2, 1, 'hmm', 1, '2026-01-27 08:58:23');

-- --------------------------------------------------------

--
-- Table structure for table `poll_votes`
--

CREATE TABLE `poll_votes` (
  `id` int(11) NOT NULL,
  `poll_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `poll_votes`
--

INSERT INTO `poll_votes` (`id`, `poll_id`, `user_id`) VALUES
(1, 2, 2),
(2, 3, 1),
(3, 3, 2);

-- --------------------------------------------------------

--
-- Table structure for table `question_notes`
--

CREATE TABLE `question_notes` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `paper_year` int(11) DEFAULT NULL,
  `paper_type` varchar(50) DEFAULT NULL,
  `question_no` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `image_path` varchar(255) DEFAULT NULL,
  `is_favorite` tinyint(1) DEFAULT 0,
  `revision_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_notes`
--

INSERT INTO `question_notes` (`id`, `user_id`, `subject`, `paper_year`, `paper_type`, `question_no`, `note`, `image_path`, `is_favorite`, `revision_count`) VALUES
(1, 2, 'ICT', 2025, 'GENERAL', 0, '# ISO OSI 7-Layer Architecture\r\n# 7. යෙදුම් ස්තරය (Application Layer - Layer 7)\r\n* කාර්යය: පරිශීලකයාට සෘජුවම ජාල සේවා සපයන ඉහළම ස්තරයයි.\r\n* ප්‍රධාන ප්‍රොටෝකෝල:\r\nHTTP/HTTPS: වෙබ් අඩවි නැරඹීම සඳහා.\r\nFTP: ගොනු උඩුගත කිරීමට සහ බාගත කිරීමට.\r\nSMTP: විද්‍යුත් තැපෑල (Email) යැවීමට.\r\nPOP3/IMAP: විද්‍යුත් තැපෑල කියවීමට සහ බාගත කිරීමට.\r\nDNS: වෙබ් ලිපින IP ලිපින බවට පත් කිරීමට.\r\n# 6. ඉදිරිපත් කිරීමේ ස්තරය (Presentation Layer - Layer 6)\r\n* කාර්යය: යෙදුම් ස්තරය සහ පහළ ස්තර අතර පරිවර්තකයෙකු ලෙස ක්‍රියා කරමින් දත්ත ආකෘති (Data formats) හසුරුවයි.\r\n* වගකීම්: ගුප්ත කේතනය (Encryption), දත්ත සම්පීඩනය (Compression) සහ ආකෘති පරිවර්තනය (ASCII/EBCDIC).\r\n* උදාහරණ: SSL/TLS, JPEG, MP3, MPEG.\r\n# 5. සැසි ස්තරය (Session Layer - Layer 5)\r\n* කාර්යය: යෙදුම් අතර සන්නිවේදන සැසි ස්ථාපිත කිරීම, කළමනාකරණය සහ අවසන් කිරීම සිදු කරයි.\r\n* වැදගත්කම: දත්ත ගලායාමට චෙක්පොයින්ට් (Checkpoints) එකතු කිරීම මගින් බිඳ වැටුණු තැන සිට නැවත ආරම්භ කිරීමට (Synchronization) ඉඩ සලසයි.\r\n* උදාහරණ: NetBIOS, RPC, SQL sessions.\r\n# 4. ප්‍රවාහන ස්තරය (Transport Layer - Layer 4)\r\n* කාර්යය: යෙදුම් (Applications) අතර අන්තයේ සිට අන්තයට (End-to-end) විශ්වසනීය දත්ත හුවමාරුව සහතික කරයි.\r\n* ප්‍රධාන කාර්යයන්: දත්ත කොටස් කිරීම (Segmentation), දෝෂ ප්‍රතිසාධනය (Error recovery) සහ අනුපිළිවෙල සහතික කිරීම.\r\n* TCP: ගොනු බාගත කිරීම වැනි 100% ක් නිවැරදිව දත්ත ලැබිය යුතු අවස්ථා සඳහා.\r\n* UDP: වීඩියෝ ස්ට්‍රීමින් වැනි නිරවද්‍යතාවයට වඩා වේගය වැදගත් වන අවස්ථා සඳහා.\r\n# 3. ජාල ස්තරය (Network Layer - Layer 3)\r\n* කාර්යය: තාර්කික ලිපින (Logical addressing) සහ දත්ත පැකට් (Packets) ගමනාන්තය වෙත යොමු කිරීම (Routing) සිදු කරයි.\r\n* උදාහරණ: Routers, IP (IPv4/IPv6), ICMP.\r\n* ප්‍රායෝගික අවස්ථාව: ඊමේල් එකක් යැවීමේදී රවුටරය මගින් IP ලිපින භාවිතා කර හොඳම මාර්ගය තෝරා ගැනීම.\r\n# 2. දත්ත සබැඳි ස්තරය (Data Link Layer - Layer 2)\r\n* කාර්යය: සෘජුවම සම්බන්ධිත නෝඩ් දෙකක් අතර දත්ත රාමු (Frames) දෝෂ රහිතව හුවමාරු කිරීම සහතික කරයි.\r\n* වගකීම්: MAC ලිපින හැසිරවීම, දෝෂ හඳුනා ගැනීම (CRC) සහ ප්‍රවාහ පාලනය (Flow control).\r\n* උදාහරණ: Switches, Bridges, Ethernet.\r\n* ප්‍රායෝගික අවස්ථාව: LAN එකක් තුළ දත්ත නිවැරදි උපාංගයටම යැවීමට පරිගණකයේ MAC ලිපිනය භාවිතා කිරීම.\r\n# 1. භෞතික ස්තරය (Physical Layer - Layer 1)\r\n* කාර්යය: කේබල්, රේඩියෝ තරංග හෝ ෆයිබර් ඔප්ටික්ස් හරහා අමු දත්ත (බිටු: 0 සහ 1) භෞතිකව සම්ප්‍රේෂණය කිරීම සිදු කරයි.\r\n* උදාහරණ: Twisted Pair කේබල්, Hubs, Repeaters, Connectors.\r\n* ප්‍රායෝගික අවස්ථාව: LAN කේබලයක් හරහා විදුලි සංඥා ලෙස බිටු හුවමාරු කිරීම.\r\n', '', 0, 0),
(2, 1, 'ICT', 2026, 'GENERAL', 0, '# මෘදුකාංග පරීක්ෂාව පිළිබඳ පූර්ණ සටහන (Software Testing Note)\nමෘදුකාංගයක් පරිශීලකයා වෙත ලබා දීමට පෙර එහි ඇති දෝෂ හඳුනාගෙන, එහි ගුණාත්මකභාවය සහතික කිරීමේ ක්‍රියාවලිය මෘදුකාංග පරීක්ෂාව (Software Testing) ලෙස හැඳින්වේ.\n\n# පරීක්ෂණ ක්‍රමවේද (Testing Methods)\nමෘදුකාංගය පරීක්ෂා කරන ආකාරය අනුව ප්‍රධාන ක්‍රම දෙකකි:\n* White Box Testing (ශ්වේත මංජුසා පරීක්ෂාව):\n- මෙහිදී මෘදුකාංගයේ අභ්‍යන්තර කේතකරණය (Source Code) සහ ව්‍යුහය පිළිබඳව අවධානය යොමු කරයි.\n- සාමාන්‍යයෙන් මෙය ක්‍රමලේඛකයන් (Developers) විසින් සිදු කරනු ලබයි.\n* Black Box Testing (කෘෂ්ණ මංජුසා පරීක්ෂාව):\n- මෘදුකාංගයේ අභ්‍යන්තර කේතය පිළිබඳව නොසලකා, එහි ක්‍රියාකාරීත්වය (Functionality) පමණක් පරීක්ෂා කරයි.\n- ආදානයක් (Input) ලබා දුන් විට අපේක්ෂිත ප්‍රතිදානය (Expected Output) ලැබෙන්නේ දැයි මෙහිදී බලයි.\n# පරීක්ෂණ මට්ටම් (Levels of Testing)\nමෘදුකාංග සංවර්ධන ක්‍රියාවලියේ විවිධ අවස්ථා වලදී සිදු කරන පරීක්ෂණ පියවර 4කි:\n* Unit Testing (ඒකක පරීක්ෂාව): මෘදුකාංගයේ කුඩාම කොටස් හෝ මොඩියුල (Modules) වෙන් වෙන් වශයෙන් පරීක්ෂා කිරීම.\n* Integration Testing (සංකලන පරීක්ෂාව): වෙන් වෙන්ව පරීක්ෂා කළ ඒකක එකිනෙක සම්බන්ධ කර, ඒවා අතර දත්ත හුවමාරුව නිවැරදි දැයි පරීක්ෂා කිරීම.\n* System Testing (පද්ධති පරීක්ෂාව): මුළු පද්ධතියම එකක් ලෙස ගෙන, එය අවශ්‍යතා වලට අනුව ක්‍රියා කරන්නේ දැයි සම්පූර්ණයෙන් පරීක්ෂා කිරීම.\n* Acceptance Testing (පිළිගැනීමේ පරීක්ෂාව): පද්ධතිය පරිශීලකයාගේ අවශ්‍යතා සපුරාලන්නේ දැයි අවසාන වශයෙන් පරීක්ෂා කිරීම.\n# පරීක්ෂණ වර්ග (Types of Testing)\nපරීක්ෂා කරන අරමුණ අනුව තවත් ප්‍රධාන වර්ග දෙකක් පවතී:\n* Functional Testing (කාර්යබද්ධ පරීක්ෂාව):\n- පද්ධතිය විසින් සිදු කළ යුතු කාර්යයන් (උදා: Login වීම, දත්ත ගණනය කිරීම) නිවැරදිව සිදුවේදැයි බැලීම.\n* Non-functional Testing (කාර්යබද්ධ නොවන පරීක්ෂාව)\n- පද්ධතියේ ගුණාංග පරීක්ෂා කිරීම.\n- Performance: වේගය සහ කාර්යක්ෂමතාව.\n- Usability: භාවිත කිරීමට ඇති පහසුව.\n- Security: දත්ත වල ආරක්ෂාව.\n# පිළිගැනීමේ පරීක්ෂාවල අවස්ථා (Alpha & Beta Testing)\nමෙය ප්‍රධාන වශයෙන් කොටස් දෙකකට බෙදේ:\n* Alpha Testing\n- මෘදුකාංගය නිපදවූ ආයතනය තුළදීම (In-house) සංවර්ධකයන් සහ පරීක්ෂකයන් විසින් සිදු කරන පරීක්ෂාවයි.\n* Beta Testing\n- මෘදුකාංගය වෙළඳපොළට නිකුත් කිරීමට පෙර, තෝරාගත් සැබෑ පරිශීලකයන් පිරිසක් (Real Users) ලවා ඔවුන්ගේ පරිසරය තුළ සිදු කරවන පරීක්ෂාවයි.\n# පරීක්ෂණ වාර්තාකරණය (Test Reporting)\nපරීක්ෂාවකදී සොයා ගන්නා දෝෂ වාර්තා කිරීමේදී පහත දෑ ඇතුළත් විය යුතුය\n* Bug ID: දෝෂය හඳුනාගැනීමේ අංකය.\n* Description: දෝෂය පිළිබඳ විස්තරය.\n* Severity: දෝෂයේ බරපතලකම (High, Medium, Low).\n* Status: වත්මන් තත්ත්වය (Open, Fixed, Pending).', 'uploads/1769514952_6978a7c8e10f4.jpeg', 0, 0),
(3, 1, 'ICT', 2014, 'MCQ', 1, '# ABC (Atanasoff-Berry Computer)\r\n* නිර්මාණය: ජෝන් ඇටනසොෆ් සහ ක්ලිෆර්ඩ් බෙරි.\r\n* විශේෂත්වය: ලොව ප්‍රථම අර්ධ-ඉලෙක්ට්‍රොනික ඩිජිටල් පරිගණකය.\r\n* තාක්ෂණය: ප්‍රථම වරට ද්විමය පද්ධතිය (Binary) භාවිතා කරන ලදී.\r\n#  ENIAC (Electronic Numerical Integrator and Computer)\r\n* නිර්මාණය: ජෝන් මවුක්ලි සහ ජේ. ප්‍රෙස්පර් එකර්ට්.\r\n* විශේෂත්වය: ලොව ප්‍රථම පූර්ණ ඉලෙක්ට්‍රොනික, පොදු කාර්ය පරිගණකය.\r\n* තාක්ෂණය: රික්තක නල (Vacuum Tubes) විශාල ප්‍රමාණයක් භාවිතා විය.\r\n#  EDSAC (Electronic Delay Storage Automatic Calculator)\r\n* නිර්මාණය: මොරිස් විල්ක්ස්.\r\n* විශේෂත්වය: වැඩසටහන් ගබඩා කළ හැකි (Stored Program) සංකල්පය මත මුලින්ම සාදා නිම කළ පරිගණකය (1949).\r\n# EDVAC (Electronic Discrete Variable Automatic Computer)\r\n* නිර්මාණය: ජෝන් මවුක්ලි, ජේ. ප්‍රෙස්පර් එකර්ට් සහ ජෝන් වොන් නියුමන්.\r\n* විශේෂත්වය: වොන් නියුමන් වාස්තු විද්‍යාව (Architecture) සහ ද්විමය පද්ධතිය යන දෙකම ඇතුළත් විය (1951).\r\n# UNIVAC (Universal Automatic Computer)\r\n* නිර්මාණය: ජෝන් මවුක්ලි සහ ජේ. ප්‍රෙස්පර් එකර්ට්.\r\n* විශේෂත්වය: වාණිජමය වශයෙන් සාර්ථක වූ ප්‍රථම පරිගණකය.\r\n* තාක්ෂණය: දත්ත ගබඩා කිරීමට චුම්බක පටි (Magnetic Tapes) භාවිතා විය.', 'uploads/notes/1769061295_6971bbafb07a8.png', 0, 0),
(4, 1, 'ICT', 2014, 'MCQ', 3, '#  මැෂින් භාෂාව (Machine Language - 1GL)\r\n* පළමු පරම්පරාවේ (1st Generation) භාෂාවයි.\r\n* සම්පූර්ණයෙන්ම 0 සහ 1 (Binary) වලින් සමන්විත වේ.\r\n* පරිගණකයට සෘජුවම තේරුම් ගත හැකිය (පරිවර්තක අවශ්‍ය නොවේ).\r\n* මිනිසාට තේරුම් ගැනීමට සහ වැරදි සෙවීමට ඉතා අපහසුය.\r\n#  ඇසෙම්බ්ලි භාෂාව (Assembly Language - 2GL)\r\n* දෙවන පරම්පරාවේ (2nd Generation) භාෂාවයි.\r\n* 0 සහ 1 වෙනුවට කෙටි ඉංග්‍රීසි වචන (Mnemonics) භාවිතා වේ (උදා: ADD, SUB, MOV).\r\n* මෙය මැෂින් භාෂාවට හැරවීමට Assembler නමැති පරිවර්තකය අවශ්‍ය වේ.\r\n* මැෂින් භාෂාවට වඩා ලිවීමට සහ කියවීමට පහසුය.', NULL, 0, 0),
(5, 1, 'ICT', 2014, 'MCQ', 7, '#  මෝඩමයක් (Modem)\r\n* Modulation (මොඩියුලනය) පරිගණකයකින් නිකුත් වන සංඛ්‍යාංක (Digital) සංඥා, දුරකථන රැහැන් හරහා ගෙන යා හැකි පරිදි ප්‍රතිසම (Analog) සංඥා බවට හැරවීමයි.\r\n* Demodulation (විමොඩියුලනය): දුරකථන රැහැන් හරහා ලැබෙන ප්‍රතිසම (Analog) සංඥා නැවත පරිගණකයට තේරුම් ගත හැකි සංඛ්‍යාංක (Digital) සංඥා බවට හැරවීමයි.\r\n# ජාල අතුරු මුහුණත් කාඩ්පත (NIC)\r\n*  මෙය පරිගණකයක් ජාලයකට (Network) සම්බන්ධ කිරීමට භාවිතා කරන දෘඩාංගයකි.\r\n# බහු පථ කාරකය (Multiplexer)\r\n*  සන්නිවේදන මාර්ග කිහිපයකින් ලැබෙන දත්ත එක් මාර්ගයක් ඔස්සේ යැවීමට භාවිතා කරයි.\r\n# බ්ලූටූත් අනුවර්තකය (Bluetooth Adaptor)\r\n* රැහැන් රහිතව කෙටි දුරක් ඇතුළත දත්ත හුවමාරු කිරීමට භාවිතා කරයි.\r\n# Wi-Fi කාඩ්පත\r\n* පරිගණකයක් රැහැන් රහිත ජාලයකට (Wireless Network) සම්බන්ධ කිරීමට භාවිතා කරයි.\r\n#  හබ් (Hub)\r\n* ජාලයක ඇති උපාංග කිහිපයක් එකිනෙකට සම්බන්ධ කිරීමට භාවිතා කරයි.\r\n* මෙය බුද්ධිමත් උපාංගයක් නොවේ; ලැබෙන දත්ත ජාලයේ ඇති සියලුම උපාංග වෙත එකසේ යවයි (Broadcasting). මේ නිසා දත්ත තදබදය වැඩි විය හැක.\r\n#  ස්විචය (Switch)\r\n* Hub එකකට වඩා බුද්ධිමත් උපාංගයකි.\r\n* මෙය දත්ත යැවිය යුත්තේ කුමන උපාංගයටද යන්න හඳුනාගෙන, අදාළ උපාංගයට පමණක් දත්ත යොමු කරයි. \r\n* මේ මගින් ජාලයේ වේගය සහ ආරක්ෂාව වැඩි වේ.\r\n#  රවුටරය (Router)\r\n* විවිධ ජාලයන් (Networks) දෙකක් හෝ කිහිපයක් එකිනෙකට සම්බන්ධ කිරීමට භාවිතා කරයි (උදා: ඔබේ නිවසේ ජාලය අන්තර්ජාලය සමඟ සම්බන්ධ කිරීම).\r\n* දත්ත ගමන් කළ යුතු හොඳම සහ වේගවත්ම මාර්ගය තෝරා ගැනීම මෙහි ප්‍රධාන කාර්යයයි.\r\n# පාලම (Bridge)\r\n* එකම වර්ගයේ ජාල පද්ධති දෙකක් (උදා: LAN දෙකක්) එකිනෙක සම්බන්ධ කිරීමට භාවිතා කරයි.\r\n* විශාල ජාලයක් කොටස් දෙකකට බෙදා දත්ත තදබදය පාලනය කිරීමට මෙය යොදා ගනී.\r\n#  ගේට්වේ (Gateway):\r\n* වෙනස්ම ආකාරයේ නීති (Protocols) භාවිතා කරන ජාල දෙකක් එකිනෙකට සම්බන්ධ කිරීමේදී \"දොරටුවක්\" ලෙස ක්‍රියා කරයි.', NULL, 0, 0),
(6, 1, 'ICT', 2014, 'MCQ', 14, '# රෙජිස්ටර් (Registers)\r\n* ස්ථානය: මධ්‍ය සැකසුම් ඒකකය (CPU) තුළම පිහිටා ඇත.\r\n* වේගය: පරිගණකයක ඇති වේගවත්ම මතකයයි.\r\n* ධාරිතාව: ඉතාමත් කුඩා වේ (බයිට් කිහිපයකි).\r\n* මිල: ඒකක ධාරිතාවක් සඳහා යන මිල ඉහළම වේ.\r\n* කාර්යය: CPU එක මගින් එම මොහොතේම ක්‍රියාත්මක කරන උපදෙස් සහ දත්ත රඳවා ගැනීම.\r\n# කෑෂ් මතකය (Cache Memory)\r\n* ස්ථානය: CPU එක තුළ හෝ එයට ඉතා ආසන්නව පිහිටා ඇත.\r\n* වේගය: රෙජිස්ටර් වලට වඩා මදක් අඩු නමුත් RAM එකට වඩා ඉතා වේගවත්ය.\r\n* ධාරිතාව: සාමාන්‍යයෙන් මෙගාබයිට් (MB) කිහිපයකි.\r\n* කාර්යය: නිතර නිතර භාවිතා වන දත්ත තාවකාලිකව ගබඩා කර තබා ගැනීම මගින් CPU එකේ වේගය වැඩි කිරීම.\r\n# ප්‍රධාන මතකය (Main Memory - RAM)\r\n* ස්වභාවය: නෂ්‍ය මතකයකි (Volatile) - විදුලිය විසන්ධි වූ විට දත්ත මැකී යයි.\r\n* වේගය: ද්විතීයික ගබඩාවලට වඩා වේගවත්ය.\r\n* ධාරිතාව: සාමාන්‍යයෙන් ගිගාබයිට් (GB) ප්‍රමාණයෙන් පවතී.\r\n* කාර්යය: පරිගණකය ක්‍රියාත්මක වන අවස්ථාවේ අවශ්‍ය වන මෘදුකාංග සහ දත්ත රඳවා ගැනීම.\r\n# ද්විතීයික මතකය (Secondary Memory - SSD/HDD):\r\n* ස්වභාවය: අනෂ්‍ය මතකයකි (Non-volatile) - දත්ත ස්ථිරව ගබඩා වේ.\r\n* වේගය: RAM එකට වඩා වේගය අඩුය.\r\n* ධාරිතාව: ඉතා ඉහළයි (ටෙරාබයිට් - TB ප්‍රමාණයෙන්).\r\n* මිල: ඒකක ධාරිතාවක් සඳහා වැයවන මිල සාපේක්ෂව ඉතා අඩුය.\r\n\r\n\r\n* වේගවත්ම: රෙජිස්ටර් > කෑෂ් > RAM > SSD > HDD.\r\n* ධාරිතාව වැඩිම: HDD > SSD > RAM > කෑෂ් > රෙජිස්ටර්.\r\n* මිල අධිකම (1GB සඳහා): රෙජිස්ටර් > කෑෂ් > RAM > SSD > HDD.', 'uploads/notes/1769063265_6971c361ea5f5.png', 0, 0),
(7, 1, 'ICT', 2014, 'MCQ', 23, '# Default Gateway\r\n* අර්ථ දැක්වීම: දේශීය ජාලයක (LAN) ඇති උපාංගයකට, එම ජාලයෙන් පිටත ඇති වෙනත් ජාලයක් (උදා: අන්තර්ජාලය) සමඟ සන්නිවේදනය කිරීමට ඇති ප්‍රධාන දොරටුව හෝ මාර්ගය මෙය වේ.\r\n* රධාන කාර්යය: තමන්ගේම ජාලයට අයත් නොවන (Destination IP address එක වෙනත් ජාලයක ඇති) දත්ත පැකට්ටුවක් ලැබුණු විට, එය අදාළ බාහිර ජාලය වෙත යොමු කිරීම මෙහි ප්‍රධාන කාර්යයයි.\r\n* උපාංගය: සාමාන්‍යයෙන් ජාලයක ඇති රවුටරය (Router) Default Gateway එක ලෙස ක්‍රියා කරයි.\r\n* IP ලිපිනය: ජාලයක ඇති පරිගණකයකට අන්තර්ජාලයට පිවිසීමට නම්, එම පරිගණකයේ TCP/IP configuration වල රවුටරයේ IP ලිපිනය \"Default Gateway\" ලෙස ඇතුළත් කළ යුතුය.\r\n* වැදගත්කම:විවිධ ප්‍රොටෝකෝල (Protocols) භාවිතා කරන ජාල අතර සම්බන්ධතාවය ඇති කිරීම,දේශීය ජාලයේ දත්ත තදබදය පාලනය කිරීම,බාහිර ජාල සමඟ සන්නිවේදනයට මග පෙන්වීම.\r\n* සීමාව: Default Gateway එකක් නොමැතිව වුවද එකම LAN එක තුළ ඇති පරිගණක අතර දත්ත හුවමාරු කළ හැකි නමුත්, අන්තර්ජාලයට පිවිසීමට එය අනිවාර්ය වේ.', 'uploads/notes/1769067530_6971d40a09cf2.png', 0, 0),
(8, 1, 'ICT', 2014, 'MCQ', 26, '# Application Layer (යෙදුම් ස්ථරය)\r\n* PDU: Data\r\n* කාර්යය: පරිශීලකයාට සහ මෘදුකාංගවලට ජාල සේවාවන් ලබා ගැනීමට අවශ්‍ය අතුරුමුහුණත (Interface) සපයයි.\r\n* ජාලය හරහා ගොනු හුවමාරුව (File Transfer), විද්‍යුත් තැපෑල (E-mail) සහ වෙබ් සේවාවන් මෙහෙයවයි.\r\n* සම්පත් පවතින බව පරීක්ෂා කිරීම සහ සන්නිවේදනය සමමුහුර්ත කිරීම සිදු කරයි.\r\n* ප්‍රොටෝකෝල: HTTP, HTTPS, FTP, SMTP, DNS, DHCP, Telnet, POP3.\r\n# Presentation Layer (ඉදිරිපත් කිරීමේ ස්ථරය)\r\n* PDU: Data\r\n* කාර්යය: උපාංග දෙකක් අතර හුවමාරු වන දත්ත එකිනෙකාට තේරුම් ගත හැකි පොදු ආකෘතියකට (Format) පරිවර්තනය කරයි.\r\n* Translation: උදා - ASCII සිට EBCDIC දක්වා පරිවර්තනය\r\n* Encryption & Decryption: දත්තවල ආරක්ෂාව තහවුරු කරයි.\r\n* Compression: ජාලයේ කලාප පළල (Bandwidth) ඉතිරි කිරීමට දත්ත සම්පීඩනය කරයි.\r\n* ආකෘති: JPEG, MP3, MPEG, GIF, SSL/TLS.\r\n# Session Layer (සම්මේලන ස්ථරය)\r\n* PDU: Data\r\n* කාර්යය: සන්නිවේදනය කරන උපාංග අතර සම්බන්ධතාවය (Session) ස්ථාපිත කිරීම, පවත්වාගෙන යාම සහ අවසන් කිරීම.\r\n* Dialogue Control: \"Half-duplex\" හෝ \"Full-duplex\" ලෙස සන්නිවේදන ක්‍රමය තීරණය කිරීම.\r\n* Synchronization: දත්ත හුවමාරුව අතරතුර \'Checkpoints\' තැබීම (මගදී සම්බන්ධතාව බිඳ වැටුනහොත් අවසන් Checkpoint එකේ සිට නැවත ආරම්භ කිරීමට).\r\n* ප්‍රොටෝකෝල: NetBIOS, SAP.\r\n# Transport Layer (ප්‍රවාහන ස්ථරය)\r\n* PDU: Segment\r\n* කාර්යය: මූලාශ්‍රයේ සිට ගමනාන්තය දක්වා දත්තවල විශ්වසනීයත්වය (End-to-end reliability) තහවුරු කිරීම.\r\n* Segmentation: දත්ත කුඩා කොටස්වලට (Segments) වෙන් කිරීම සහ ඒවාට අංකයක් (Sequence number) ලබා දීම.\r\n* Flow Control: ලබන්නාගේ වේගයට අනුව දත්ත යැවීම පාලනය කිරීම.\r\n* Error Control: දත්ත ලැබුණු බව තහවුරු කිරීම (Acknowledgement) සහ දෝෂ සහිත දත්ත නැවත යැවීම (Retransmission).\r\n* ප්‍රොටෝකෝල: TCP (Connection-oriented), UDP (Connectionless).\r\n# Network Layer (ජාල ස්ථරය)\r\n* PDU: Packet\r\n* කාර්යය: විවිධ ජාල හරහා දත්ත පැකට් යැවීම සඳහා හොඳම මාර්ගය තේරීම (Routing).\r\n* Logical Addressing: මූලාශ්‍රය සහ ගමනාන්තය හඳුනා ගැනීමට IP ලිපින (IPv4/IPv6) භාවිතා කරයි.\r\n* Routing: Routing Table භාවිතයෙන් දත්ත යැවිය යුතු කෙටිම සහ හොඳම මාර්ගය තීරණය කිරීම.\r\n* උපාංග: Router, Layer 3 Switch.\r\n* ප්‍රොටෝකෝල: IP, ICMP, ARP.\r\n# Data Link Layer (දත්ත සබැඳි ස්ථරය)\r\n* PDU: Frame\r\n* කාර්යය: එකම ජාලයක (Local Network) ඇති උපාංග දෙකක් අතර දත්ත හුවමාරුව වැරදිවලින් තොරව සිදු කිරීම.\r\n* Physical Addressing: දෘඩාංග හඳුනා ගැනීමට MAC ලිපින භාවිතා කරයි.\r\n* Framing: Network layer එකෙන් එන පැකට් වලට Header සහ Trailer එකතු කර \'Frame\' එකක් සාදයි.\r\n* Error Detection: Frame එකේ අගට CRC (Cyclic Redundancy Check) වැනි කේත එකතු කර වැරදි පරීක්ෂා කරයි\r\n* උපාංග: Switch, Bridge, NIC.\r\n# Physical Layer (භෞතික ස්ථරය)\r\n* PDU: Bits\r\n* කාර්යය: දත්ත බිටු (Bits) විද්‍යුත්, ආලෝක හෝ රේඩියෝ සංඥා ලෙස භෞතික මාධ්‍ය හරහා සම්ප්‍රේෂණය කිරීම.\r\n* භෞතික ලක්ෂණ තීරණය කරයි (කේබල් වර්ග, පින් සැකසුම, වෝල්ටීයතාව).\r\n* දත්ත සම්ප්‍රේෂණ වේගය (Bit rate) පාලනය කරයි.\r\n* ජාල ස්ථල විද්‍යාව (Network Topology) ක්‍රියාත්මක වන මට්ටමයි.\r\n* උපාංග: Hub, Repeater, Cables, Connectors (RJ45).', 'uploads/notes/1769068341_6971d735ec33d.png', 0, 0),
(9, 1, 'ICT', 2014, 'MCQ', 27, '# වෙබ් සේවාදායකය (Web Server)\r\n* වෙබ් අඩවිවලට අදාළ ලිපිගොනු (HTML, CSS, JS, Images) ගබඩා කර තබා ගනිමින් පරිශීලකයාගේ ඉල්ලීම මත ඒවා ලබා දීම සිදු කරයි.\r\n* ප්‍රධාන ප්‍රොටෝකෝල: HTTP (Port 80), HTTPS (Port 443).\r\n* Web Hosting වර්ග:01)Shared Hosting:    එක් සේවාදායකයක සම්පත් බොහෝ පරිශීලකයන් අතර බෙදා ගනී. (මිල අඩුයි, කුඩා අඩවි වලට සුදුසුයි).   02)   Dedicated Hosting: සම්පූර්ණ සේවාදායකයක්ම එක් සේවාලාභියෙකුට පමණක් වෙන් කෙරේ. (මිල අධිකයි, ඉහළ ආරක්ෂාවක් ඇත).   03)   VPS (Virtual Private Server): එක් භෞතික සේවාදායකයක් මෘදුකාංග මගින් ස්වාධීන කොටස් වලට වෙන් කර ඇත.\r\n# ගොනු සේවාදායකය (File Server)\r\n* ජාලයක් තුළ සිටින පරිශීලකයන්ට පොදුවේ ගොනු (Files) ගබඩා කිරීමට, කළමනාකරණය කිරීමට සහ හුවමාරු කිරීමට ඉඩ සලසයි.\r\n* ප්‍රධාන ප්‍රොටෝකෝල:01)   FTP (File Transfer Protocol): ගොනු උඩුගත (Upload) සහ බාගත (Download) කිරීමට. (Port 20, 21).   02)   SFTP: FTP වල ආරක්ෂිත සංස්කරණය (Encrypted).\r\n# යෙදුම් සේවාදායකය (Application Server)\r\n* වෙබ් සේවාදායකයකින් ලැබෙන ඉල්ලීම්වලට අනුව සංකීර්ණ ගණනය කිරීම් සහ මෘදුකාංග ක්‍රියාත්මක කිරීම (Business Logic) සිදු කරයි.\r\n* කාර්යය: වෙබ් අඩවියක ගතික දත්ත (Dynamic content) උත්පාදනය සඳහා දත්ත සමුදායන් (Databases) සමඟ සම්බන්ධ වේ.\r\n# ප්‍රොක්සි සේවාදායකය (Proxy Server)\r\n* සේවාලාභියා (Client) සහ අන්තර්ජාලය (Internet) අතර අතරමැදියෙකු ලෙස ක්‍රියා කරයි.\r\n* Anonymity: පරිශීලකයාගේ සැබෑ IP ලිපිනය සඟවයි.\r\n* Caching: නිතර භාවිතා වන වෙබ් පිටු ගබඩා කර තබා ගැනීමෙන් වේගය වැඩි කරයි.\r\n* Filtering: අනවශ්‍ය වෙබ් අඩවි අවහිර කිරීම.\r\n# DHCP සේවාදායකය (DHCP Server)\r\n* ජාලයකට සම්බන්ධ වන උපාංග සඳහා ස්වයංක්‍රීයව IP ලිපින ලබා දීම සිදු කරයි (Dynamic Host Configuration Protocol).\r\n* ප්‍රයෝජන: IP ලිපින අතින් ඇතුළත් කිරීමේ අපහසුව මගහරවයි (Avoids IP conflicts).\r\n# විද්‍යුත් තැපැල් සේවාදායකය (Mail Server)\r\n* ඊමේල් පණිවිඩ යැවීම, ලැබීම සහ ගබඩා කිරීම පාලනය කරයි.\r\n* SMTP (Simple Mail Transfer Protocol): ඊමේල් යැවීමට (Sending). (Port 25).\r\n* POP3 (Post Office Protocol v3): ඊමේල් පරිගණකයට බාගත කිරීමට. බාගත කළ පසු සේවාදායකයෙන් මැකී යයි. (Port 110).\r\n* IMAP (Internet Message Access Protocol): ඊමේල් සේවාදායකයේ තබාගෙන කියවීමට. උපාංග කිහිපයක් අතර සමමුහුර්ත (Sync) වේ. (Port 143).', 'uploads/notes/1769069431_6971db7749242.png', 0, 0),
(11, 1, 'ICT', 2014, 'MCQ', 28, '# Subnet Mask (උපජාල ආවරණය)\r\n* IP ලිපිනයක ඇති Network කොටස සහ Host කොටස වෙන් කර හඳුනා ගැනීම සඳහා භාවිතා කරන බිටු 32 කින් යුත් අංකය Subnet Mask එකක් ලෙස හැඳින්වේ\r\n# 1. ප්‍රධාන කාර්යයන් (Functions)\r\n* IP ලිපිනයක ජාලයට (Network) අදාළ කොටස සහ උපාංගයට (Host) අදාළ කොටස කුමක්දැයි තීරණය කිරීම.IP ලිපිනයක ජාලයට (Network) අදාළ කොටස සහ උපාංගයට (Host) අදාළ කොටස කුමක්දැයි තීරණය කිරීම.\r\n* දත්ත පැකට්ටුවක් යැවීමේදී එය තම උපජාලය ඇතුළත (Internal) පවතින උපාංගයකටද නැතිනම් පිටත (External) ජාලයකටද යන්න හඳුනා ගැනීමට රවුටරයට උදවු කිරීම.\r\n# ස්වරූපය (Format)\r\n* IP ලිපිනයක් මෙන්ම බිටු 32 කින් සමන්විතය.\r\n* දශම තිත් අංකනය (Dotted Decimal Notation) මගින් ලියා දක්වයි (උදා: 255.255.255.0).\r\n* මෙහි Network කොටස සඳහා වූ බිටු සියල්ල \'1\' ලෙසද, Host කොටස සඳහා වූ බිටු සියල්ල \'0\' ලෙසද දක්වයි.\r\n# පෙරනිමි උපජාල ආවරණ (Default Subnet Masks)\r\n* Class A	255.0.0.0	පළමු Octet එක ජාලය සඳහා වෙන් වේ.\r\n* Class B	255.255.0.0	මුල් Octet දෙක ජාලය සඳහා වෙන් වේ.\r\n* Class C	255.255.255.0	මුල් Octet තුන ජාලය සඳහා වෙන් වේ.', NULL, 0, 0),
(12, 1, 'ICT', 2014, 'MCQ', 29, '# TCP (Transmission Control Protocol)\r\n* සම්බන්ධතා මූලික වේ (Connection-oriented): දත්ත යැවීමට පෙර යවන්නා සහ ලබන්නා අතර ස්ථිර සන්නිවේදන මාර්ගයක් (Handshake) සාදා ගනී.\r\n* විශ්වසනීයත්වය (Reliable): දත්ත ලැබුණු බවට තහවුරු කිරීමේ පණිවිඩ (Acknowledgements) ලබා දෙයි. දත්තයක් අතරමගදී නැති වුවහොත් එය නැවත යවයි (Retransmission).\r\n* අනුපිළිවෙල (Ordering): දත්ත කොටස් (Segments) යැවූ අනුපිළිවෙලටම ලබන්නා වෙත ලැබීම සහතික කරයි.\r\n* වේගය: පාලන තොරතුරු වැඩි නිසා වේගය සාපේක්ෂව අඩුයි.\r\n* භාවිතයන්: Web browsing (HTTP), Email (SMTP), File transfer (FTP).\r\n# UDP (User Datagram Protocol)\r\n* සම්බන්ධතා රහිත වේ (Connectionless): දත්ත යැවීමට පෙර මාර්ගයක් සාදා නොගනී; දත්ත ලැබෙනවාද නැද්ද යන්න සොයා නොබලා කෙළින්ම යවයි.\r\n* විශ්වසනීයත්වය අඩුයි (Unreliable): දත්ත ලැබුණු බව තහවුරු නොකරයි. දත්ත නැති වුවහොත් ඒවා නැවත යවන්නේ නැත.\r\n* අනුපිළිවෙල: දත්ත ලැබෙන අනුපිළිවෙල වෙනස් විය හැක.\r\n* වේගය: පාලන තොරතුරු අඩු නිසා ඉතා වේගවත්ය.\r\n* භාවිතයන්: Live streaming (Youtube live), Video calls (Skype/Zoom), Online gaming, DNS.', 'uploads/notes/1769089215_697228bf425ae.png', 0, 0),
(13, 1, 'ICT', 2014, 'MCQ', 43, '# RAM (Random Access Memory)\r\n* RAM යනු තාවකාලිකව දත්ත ගබඩා කරන (Volatile), කියවීමට සහ ලිවීමට හැකි මතකයකි. මෙය ප්‍රධාන වශයෙන් වර්ග දෙකකි:\r\n# 1. SRAM (Static Random Access Memory)\r\n* තාක්ෂණය: මෙහි දත්ත ගබඩා කිරීමට Flip-flops (ට්‍රාන්සිස්ටර සමූහයක්) භාවිතා කරයි.\r\n* Refresh කිරීම: දත්ත රඳවා තබා ගැනීමට නිතර Refresh කිරීම අවශ්‍ය නොවේ.\r\n* වේගය: ඉතා වේගවත්ය.\r\n* පිරිවැය: නිෂ්පාදන වියදම අධිකය.\r\n* භාවිතය: ප්‍රධාන වශයෙන් පරිගණකයේ Cache Memory එක ලෙස භාවිතා කරයි.\r\n* ප්‍රමාණය: භෞතිකව විශාල වන අතර ධාරිතාව සාපේක්ෂව අඩුය.\r\n# 2. DRAM (Dynamic Random Access Memory)\r\n* තාක්ෂණය: මෙහි දත්ත ගබඩා කිරීමට Capacitors (ධාරිත්‍රක) සහ ට්‍රාන්සිස්ටර භාවිතා කරයි.\r\n* Refresh කිරීම: ධාරිත්‍රකවල ඇති විද්‍යුත් ආරෝපණය කාලයත් සමඟ කාන්දු වන බැවින්, දත්ත රඳවා තබා ගැනීමට තත්පරයකට දහස් වාරයක් Refresh කළ යුතුය.\r\n* වේගය: SRAM වලට වඩා වේගය අඩුය.\r\n* පිරිවැය: නිෂ්පාදන වියදම අඩුය.\r\n* භාවිතය: පරිගණකයේ ප්‍රධාන මතකය (Main Memory / RAM) ලෙස භාවිතා කරයි.\r\n* ප්‍රමාණය: කුඩා ඉඩක වැඩි ධාරිතාවක් (High density) ගබඩා කළ හැක.', 'uploads/notes/1769089568_69722a20e7b6a.png', 0, 1),
(16, 1, 'ICT', 2025, 'GENERAL', 0, '# ISO OSI 7-Layer Architecture\n# 7. යෙදුම් ස්තරය (Application Layer - Layer 7)\n* කාර්යය: පරිශීලකයාට සෘජුවම ජාල සේවා සපයන ඉහළම ස්තරයයි.\n* ප්‍රධාන ප්‍රොටෝකෝල:\nHTTP/HTTPS: වෙබ් අඩවි නැරඹීම සඳහා.\nFTP: ගොනු උඩුගත කිරීමට සහ බාගත කිරීමට.\nSMTP: විද්‍යුත් තැපෑල (Email) යැවීමට.\nPOP3/IMAP: විද්‍යුත් තැපෑල කියවීමට සහ බාගත කිරීමට.\nDNS: වෙබ් ලිපින IP ලිපින බවට පත් කිරීමට.\n# 6. ඉදිරිපත් කිරීමේ ස්තරය (Presentation Layer - Layer 6)\n* කාර්යය: යෙදුම් ස්තරය සහ පහළ ස්තර අතර පරිවර්තකයෙකු ලෙස ක්‍රියා කරමින් දත්ත ආකෘති (Data formats) හසුරුවයි.\n* වගකීම්: ගුප්ත කේතනය (Encryption), දත්ත සම්පීඩනය (Compression) සහ ආකෘති පරිවර්තනය (ASCII/EBCDIC).\n* උදාහරණ: SSL/TLS, JPEG, MP3, MPEG.\n# 5. සැසි ස්තරය (Session Layer - Layer 5)\n* කාර්යය: යෙදුම් අතර සන්නිවේදන සැසි ස්ථාපිත කිරීම, කළමනාකරණය සහ අවසන් කිරීම සිදු කරයි.\n* වැදගත්කම: දත්ත ගලායාමට චෙක්පොයින්ට් (Checkpoints) එකතු කිරීම මගින් බිඳ වැටුණු තැන සිට නැවත ආරම්භ කිරීමට (Synchronization) ඉඩ සලසයි.\n* උදාහරණ: NetBIOS, RPC, SQL sessions.\n# 4. ප්‍රවාහන ස්තරය (Transport Layer - Layer 4)\n* කාර්යය: යෙදුම් (Applications) අතර අන්තයේ සිට අන්තයට (End-to-end) විශ්වසනීය දත්ත හුවමාරුව සහතික කරයි.\n* ප්‍රධාන කාර්යයන්: දත්ත කොටස් කිරීම (Segmentation), දෝෂ ප්‍රතිසාධනය (Error recovery) සහ අනුපිළිවෙල සහතික කිරීම.\n* TCP: ගොනු බාගත කිරීම වැනි 100% ක් නිවැරදිව දත්ත ලැබිය යුතු අවස්ථා සඳහා.\n* UDP: වීඩියෝ ස්ට්‍රීමින් වැනි නිරවද්‍යතාවයට වඩා වේගය වැදගත් වන අවස්ථා සඳහා.\n# 3. ජාල ස්තරය (Network Layer - Layer 3)\n* කාර්යය: තාර්කික ලිපින (Logical addressing) සහ දත්ත පැකට් (Packets) ගමනාන්තය වෙත යොමු කිරීම (Routing) සිදු කරයි.\n* උදාහරණ: Routers, IP (IPv4/IPv6), ICMP.\n* ප්‍රායෝගික අවස්ථාව: ඊමේල් එකක් යැවීමේදී රවුටරය මගින් IP ලිපින භාවිතා කර හොඳම මාර්ගය තෝරා ගැනීම.\n# 2. දත්ත සබැඳි ස්තරය (Data Link Layer - Layer 2)\n* කාර්යය: සෘජුවම සම්බන්ධිත නෝඩ් දෙකක් අතර දත්ත රාමු (Frames) දෝෂ රහිතව හුවමාරු කිරීම සහතික කරයි.\n* වගකීම්: MAC ලිපින හැසිරවීම, දෝෂ හඳුනා ගැනීම (CRC) සහ ප්‍රවාහ පාලනය (Flow control).\n* උදාහරණ: Switches, Bridges, Ethernet.\n* ප්‍රායෝගික අවස්ථාව: LAN එකක් තුළ දත්ත නිවැරදි උපාංගයටම යැවීමට පරිගණකයේ MAC ලිපිනය භාවිතා කිරීම.\n# 1. භෞතික ස්තරය (Physical Layer - Layer 1)\n* කාර්යය: කේබල්, රේඩියෝ තරංග හෝ ෆයිබර් ඔප්ටික්ස් හරහා අමු දත්ත (බිටු: 0 සහ 1) භෞතිකව සම්ප්‍රේෂණය කිරීම සිදු කරයි.\n* උදාහරණ: Twisted Pair කේබල්, Hubs, Repeaters, Connectors.\n* ප්‍රායෝගික අවස්ථාව: LAN කේබලයක් හරහා විදුලි සංඥා ලෙස බිටු හුවමාරු කිරීම.\n\n\n--- Added on 1/27/2026, 3:06:04 PM ---\n# මෘදුකාංග පරීක්ෂාව පිළිබඳ පූර්ණ සටහන (Software Testing Note)\nමෘදුකාංගයක් පරිශීලකයා වෙත ලබා දීමට පෙර එහි ඇති දෝෂ හඳුනාගෙන, එහි ගුණාත්මකභාවය සහතික කිරීමේ ක්‍රියාවලිය මෘදුකාංග පරීක්ෂාව (Software Testing) ලෙස හැඳින්වේ.\n# පරීක්ෂණ ක්‍රමවේද (Testing Methods)\nමෘදුකාංගය පරීක්ෂා කරන ආකාරය අනුව ප්‍රධාන ක්‍රම දෙකකි:\n* White Box Testing (ශ්වේත මංජුසා පරීක්ෂාව):\nමෙහිදී මෘදුකාංගයේ අභ්‍යන්තර කේතකරණය (Source Code) සහ ව්‍යුහය පිළිබඳව අවධානය යොමු කරයි.\nසාමාන්‍යයෙන් මෙය ක්‍රමලේඛකයන් (Developers) විසින් සිදු කරනු ලබයි.\n* Black Box Testing (කෘෂ්ණ මංජුසා පරීක්ෂාව):\nමෘදුකාංගයේ අභ්‍යන්තර කේතය පිළිබඳව නොසලකා, එහි ක්‍රියාකාරීත්වය (Functionality) පමණක් පරීක්ෂා කරයි.\nආදානයක් (Input) ලබා දුන් විට අපේක්ෂිත ප්‍රතිදානය (Expected Output) ලැබෙන්නේ දැයි මෙහිදී බලයි.\n# පරීක්ෂණ මට්ටම් (Levels of Testing)\nමෘදුකාංග සංවර්ධන ක්‍රියාවලියේ විවිධ අවස්ථා වලදී සිදු කරන පරීක්ෂණ පියවර 4කි:\n* Unit Testing (ඒකක පරීක්ෂාව): මෘදුකාංගයේ කුඩාම කොටස් හෝ මොඩියුල (Modules) වෙන් වෙන් වශයෙන් පරීක්ෂා කිරීම.\n* Integration Testing (සංකලන පරීක්ෂාව): වෙන් වෙන්ව පරීක්ෂා කළ ඒකක එකිනෙක සම්බන්ධ කර, ඒවා අතර දත්ත හුවමාරුව නිවැරදි දැයි පරීක්ෂා කිරීම.\n* System Testing (පද්ධති පරීක්ෂාව): මුළු පද්ධතියම එකක් ලෙස ගෙන, එය අවශ්‍යතා වලට අනුව ක්‍රියා කරන්නේ දැයි සම්පූර්ණයෙන් පරීක්ෂා කිරීම.\n* Acceptance Testing (පිළිගැනීමේ පරීක්ෂාව): පද්ධතිය පරිශීලකයාගේ අවශ්‍යතා සපුරාලන්නේ දැයි අවසාන වශයෙන් පරීක්ෂා කිරීම.\n# පරීක්ෂණ වර්ග (Types of Testing)\nපරීක්ෂා කරන අරමුණ අනුව තවත් ප්‍රධාන වර්ග දෙකක් පවතී:\n* Functional Testing (කාර්යබද්ධ පරීක්ෂාව): පද්ධතිය විසින් සිදු කළ යුතු කාර්යයන් (උදා: Login වීම, දත්ත ගණනය කිරීම) නිවැරදිව සිදුවේදැයි බැලීම.\n* Non-functional Testing (කාර්යබද්ධ නොවන පරීක්ෂාව): පද්ධතියේ ගුණාංග පරීක්ෂා කිරීම.\nPerformance: වේගය සහ කාර්යක්ෂමතාව.\nUsability: භාවිත කිරීමට ඇති පහසුව.\nSecurity: දත්ත වල ආරක්ෂාව.\n# පිළිගැනීමේ පරීක්ෂාවල අවස්ථා (Alpha & Beta Testing)\nමෙය ප්‍රධාන වශයෙන් කොටස් දෙකකට බෙදේ:\n* Alpha Testing: මෘදුකාංගය නිපදවූ ආයතනය තුළදීම (In-house) සංවර්ධකයන් සහ පරීක්ෂකයන් විසින් සිදු කරන පරීක්ෂාවයි.\n* Beta Testing: මෘදුකාංගය වෙළඳපොළට නිකුත් කිරීමට පෙර, තෝරාගත් සැබෑ පරිශීලකයන් පිරිසක් (Real Users) ලවා ඔවුන්ගේ පරිසරය තුළ සිදු කරවන පරීක්ෂාවයි.\n# පරීක්ෂණ වාර්තාකරණය (Test Reporting)\nපරීක්ෂාවකදී සොයා ගන්නා දෝෂ වාර්තා කිරීමේදී පහත දෑ ඇතුළත් විය යුතුය:\n* Bug ID: දෝෂය හඳුනාගැනීමේ අංකය.\n* Description: දෝෂය පිළිබඳ විස්තරය.\n* Severity: දෝෂයේ බරපතලකම (High, Medium, Low).\n* Status: වත්මන් තත්ත්වය (Open, Fixed, Pending).', 'uploads/notes/1769506564_69788704bb8a0.jpeg', 0, 0),
(17, 2, 'ICT', 2014, 'MCQ', 29, '# TCP (Transmission Control Protocol)\r\n* සම්බන්ධතා මූලික වේ (Connection-oriented): දත්ත යැවීමට පෙර යවන්නා සහ ලබන්නා අතර ස්ථිර සන්නිවේදන මාර්ගයක් (Handshake) සාදා ගනී.\r\n* විශ්වසනීයත්වය (Reliable): දත්ත ලැබුණු බවට තහවුරු කිරීමේ පණිවිඩ (Acknowledgements) ලබා දෙයි. දත්තයක් අතරමගදී නැති වුවහොත් එය නැවත යවයි (Retransmission).\r\n* අනුපිළිවෙල (Ordering): දත්ත කොටස් (Segments) යැවූ අනුපිළිවෙලටම ලබන්නා වෙත ලැබීම සහතික කරයි.\r\n* වේගය: පාලන තොරතුරු වැඩි නිසා වේගය සාපේක්ෂව අඩුයි.\r\n* භාවිතයන්: Web browsing (HTTP), Email (SMTP), File transfer (FTP).\r\n# UDP (User Datagram Protocol)\r\n* සම්බන්ධතා රහිත වේ (Connectionless): දත්ත යැවීමට පෙර මාර්ගයක් සාදා නොගනී; දත්ත ලැබෙනවාද නැද්ද යන්න සොයා නොබලා කෙළින්ම යවයි.\r\n* විශ්වසනීයත්වය අඩුයි (Unreliable): දත්ත ලැබුණු බව තහවුරු නොකරයි. දත්ත නැති වුවහොත් ඒවා නැවත යවන්නේ නැත.\r\n* අනුපිළිවෙල: දත්ත ලැබෙන අනුපිළිවෙල වෙනස් විය හැක.\r\n* වේගය: පාලන තොරතුරු අඩු නිසා ඉතා වේගවත්ය.\r\n* භාවිතයන්: Live streaming (Youtube live), Video calls (Skype/Zoom), Online gaming, DNS.', 'uploads/notes/1769089215_697228bf425ae.png', 0, 0),
(29, 1, 'ICT', 2026, 'GENERAL', 1, '\r\n# Error Detection in OSI Layers (දෝෂ හඳුනාගැනීමේ ක්‍රියාවලිය)\r\nජාලයක් හරහා දත්ත හුවමාරු වන විට විවිධ බාධා කිරීම් (Noise/Interference) නිසා දත්ත වෙනස් විය හැක. මෙය හඳුනාගැනීම සඳහා OSI මාදිලියේ ප්‍රධාන ස්ථර දෙකක් ක්‍රියා කරයි.\r\n# Data Link Layer (දෙවන ස්ථරය)\r\nData Link Layer එකේදී දෝෂ හඳුනාගැනීම ප්‍රධාන වශයෙන් පණිවිඩය ලැබුණු වහාම සිදු කෙරේ.\r\n* Hop-by-Hop Error Detection:\r\n- මෙහිදී දත්ත පැකට්ටුවක් එක් උපාංගයක සිට ඊළඟට ආසන්නව ඇති උපාංගය (Node-to-Node) වෙත ගමන් කරන සෑම අවස්ථාවකදීම දෝෂ පරීක්ෂා කරයි.\r\n* CRC (Cyclic Redundancy Check):\r\n- දත්ත Frame එකේ අගට එකතු කරන විශේෂිත ගණිතමය අගයක් (Frame Check Sequence - FCS) මගින් දත්තවල වෙනසක් වී ඇත්දැයි පරීක්ෂා කරයි.\r\n* Frame Discarding:\r\n- දත්ත Frame එකක දෝෂයක් ඇති බව හඳුනාගත් විට, එම Frame එක ප්‍රතික්ෂේප කර (Discard) නැවත එවන ලෙස දන්වනු ලබයි.\r\n# Transport Layer (හතරවන ස්ථරය)\r\nTransport Layer එකේදී දෝෂ හඳුනාගැනීම වඩාත් පුළුල් මට්ටමකින් සිදු වේ.\r\n* End-to-End Error Detection:\r\n- දත්ත මූලාශ්‍රයේ (Source) සිට අවසාන ගමනාන්තය (Destination) දක්වාම අතරමැදි උපාංග නොසලකා සමස්ත පණිවිඩයම පරීක්ෂා කිරීම මෙහිදී සිදු වේ.\r\n* Checksum Method:\r\n- දත්ත කොටස් (Segments) වල එකතුවක් (Sum) ගණනය කර එය පණිවිඩය සමඟ එවනු ලබයි. ගමනාන්තයේදී නැවත එම ගණනය කිරීම සිදු කර අගයන් දෙක සංසන්දනය කෙරේ.\r\n* TCP Reliability:\r\n- TCP ප්‍රොටෝකෝලය භාවිතා කරන විට, ලැබුණු දත්තවල දෝෂයක් ඇත්නම් හෝ දත්ත කොටසක් මඟ හැරී ඇත්නම් එය නැවත එවන ලෙස ඉල්ලා සිටියි (Retransmission).', 'uploads/1769523932_6978cadc5b48e.jpeg', 0, 0),
(36, 1, 'ICT', 2026, 'GENERAL', 44, '\r\n# IPv4 ලිපින පන්ති සහ පරාසයන් (IP Classes and Ranges)\r\nIPv4 (Internet Protocol version 4) ලිපින පද්ධතියේදී ජාලයේ විශාලත්වය සහ අවශ්‍යතාවය අනුව IP ලිපින ප්‍රධාන පන්ති 5කට බෙදා ඇත.\r\n# IP ලිපින පන්ති බෙදා වෙන් කිරීම (IP Address Classes)\r\nIP ලිපිනයක පළමු බයිටයේ (First Octet) අගය අනුව පන්ති වෙන් කරනු ලැබේ:\r\n* Class A:\r\n- පරාසය (Range): 1.0.0.0 සිට 126.255.255.255 දක්වා.\r\n- භාවිතය: ඉතා විශාල ජාල (Large scale networks) සඳහා භාවිතා වේ.\r\n- Default Subnet Mask: 255.0.0.0\r\n* Class B:\r\n- පරාසය (Range): 128.0.0.0 සිට 191.255.255.255 දක්වා.\r\n- භාවිතය: මධ්‍යම ප්‍රමාණයේ ජාල (Medium scale networks) සඳහා භාවිතා වේ.\r\n- Default Subnet Mask: 255.255.0.0\r\n* Class C:\r\n- පරාසය (Range): 192.0.0.0 සිට 223.255.255.255 දක්වා.\r\n- භාවිතය: කුඩා ජාල (Small local area networks) සඳහා බහුලවම භාවිතා වේ.\r\n- Default Subnet Mask: 255.255.255.0\r\n* Class D:\r\n- පරාසය (Range): 224.0.0.0 සිට 239.255.255.255 දක්වා.\r\n- භාවිතය: බහු විකාශන (Multicasting) කටයුතු සඳහා වෙන් කර ඇත.\r\n* Class E:\r\n- පරාසය (Range): 240.0.0.0 සිට 255.255.255.255 දක්වා.\r\n- භාවිතය: පර්යේෂණ කටයුතු (Experimental/Research) සඳහා වෙන් කර ඇත.\r\n# විශේෂිත IP ලිපින (Special IP Addresses)\r\n* Loopback Address (127.0.0.1):\r\n- තම පරිගණකයේම ජාල කාඩ්පත (NIC) පරීක්ෂා කිරීම සඳහා භාවිතා කරයි.\r\n* Private IP Ranges:\r\n- අභ්‍යන්තර ජාල (Local Networks) සඳහා පමණක් වෙන් කළ ලිපින:\r\n- Class A: 10.0.0.0 - 10.255.255.255\r\n- Class B: 172.16.0.0 - 172.31.255.255\r\n- Class C: 192.168.0.0 - 192.168.255.255\r\n# ජාල සහ සත්කාරක කොටස් (Network and Host IDs)\r\n* සෑම IP ලිපිනයකම කොටස් දෙකක් ඇත:\r\n- Network ID: අදාළ ජාලය හඳුනා ගැනීමට භාවිතා කරයි.\r\n- Host ID: එම ජාලය තුළ ඇති උපාංගය (Computer/Printer) හඳුනා ගැනීමට භාවිතා කරයි.\r\n* A	8	24	126\r\n* B	16	16	16,384\r\n* C	24	8	2,097,152\r\n# Subnet Mask එකක අවශ්‍යතාවය\r\n* IP ලිපිනයක ඇති ජාල කොටස (Network part) සහ සත්කාරක කොටස (Host part) වෙන් කර හඳුනා ගැනීමට භාවිතා වේ.\r\n* දත්ත පැකට්ටුවක් අභ්‍යන්තර ජාලයකටද නැතිනම් පිටත ජාලයකටද යැවිය යුතු දැයි තීරණය කිරීමට රවුටර වලට උදව් වේ.', 'uploads/1769525539_6978d1232056f.jpeg', 0, 0),
(37, 1, 'Physics', 2026, 'GENERAL', 1, '# චලිත ප්‍රස්ථාර (Motion Graphs)\n* චලිතයක ස්වභාවය තේරුම් ගැනීමට ප්‍රධාන ප්‍රස්ථාර වර්ග 3ක් භාවිතා වේ:\n- විස්ථාපන - කාල (s-t ) ප්‍රස්ථාර\n- ප්‍රවේග - කාල (v-t) ප්‍රස්ථාර\n- ත්වරණ - කාල (a-t) ප්‍රස්ථාර\n# නිශ්චලතාවයේ පවතින වස්තුවක් (Object at Rest)\n*  s-t ප්‍රස්ථාරය: කාල අක්ෂයට සමාන්තර තිරස් සරල රේඛාවකි.\n*  v-t ප්‍රස්ථාරය: ප්‍රවේගය ශුන්‍ය බැවින් කාල අක්ෂය මතම පිහිටයි (v = 0).\n# ඒකාකාර ප්‍රවේගයෙන් සිදුවන චලිතය (Uniform Velocity)\n\nමෙහිදී ප්‍රවේගය නියත බැවින් ත්වරණය ශුන්‍ය වේ.\n* s-t ප්‍රස්ථාරය: මූල ලක්ෂ්‍යය හරහා යන ආනත සරල රේඛාවකි. (මෙහි අනුක්‍රමණයෙන් ප්‍රවේගය ලැබේ).\n* v-t ප්‍රස්ථාරය: කාල අක්ෂයට සමාන්තර තිරස් සරල රේඛාවකි.\n* a-t ප්‍රස්ථාරය: ත්වරණය ශුන්‍ය බැවින් කාල අක්ෂය මතම පිහිටයි (a = 0).\n\n#ඒකාකාර ත්වරණයෙන් සිදුවන චලිතය (Uniform Acceleration)\n\nමෙහිදී ප්‍රවේගය සමාන කාලාන්තර තුළ සමාන ප්‍රමාණයන්ගෙන් වැඩි වේ.\n*s-t ප්‍රස්ථාරය: පරාවලයික හැඩයක් ගනී (අනුක්‍රමණය ක්‍රමයෙන් වැඩි වේ).\n* v-t ප්‍රස්ථාරය: ආනත සරල රේඛාවකි. (මෙහි අනුක්‍රමණයෙන් ත්වරණය ද, ප්‍රස්ථාරය යටතේ වර්ගඵලයෙන් විස්ථාපනය ද ලැබේ).\n* a-t ප්‍රස්ථාරය: කාල අක්ෂයට සමාන්තර තිරස් සරල රේඛාවකි (a ධන අගයක් ගනී).\n# ඒකාකාර මන්දනයෙන් සිදුවන චලිතය (Uniform Retardation)\nමෙහිදී ප්‍රවේගය කාලය සමඟ ඒකාකාරව අඩු වේ.\n* s-t ප්‍රස්ථාරය: අනුක්‍රමණය ක්‍රමයෙන් අඩුවන පරාවලයික හැඩයකි.\n* v-t ප්‍රස්ථාරය: සෘණ අනුක්‍රමණයක් සහිත සරල රේඛාවකි.\n* a-t ප්‍රස්ථාරය: සෘණ අක්ෂයේ පිහිටි කාල අක්ෂයට සමාන්තර තිරස් සරල රේඛාවකි.\n\n\n\n* s-t ප්‍රස්ථාරයක අනුක්‍රමණය = ප්‍රවේගය\n* v-t ප්‍රස්ථාරයක අනුක්‍රමණය = ත්වරණය\n* v-t ප්‍රස්ථාරයක වර්ගඵලය = විස්ථාපනය / දුර', 'uploads/1769846969_697db8b9531cd.png', 0, 0),
(38, 1, 'ICT', 2014, 'MCQ', 46, '# ස්වයංක්‍රීය පද්ධති (Automated Systems)\r\nමිනිස් මැදිහත්වීමකින් තොරව හෝ ඉතා අවම මිනිස් මැදිහත්වීමක් සහිතව, යම් ක්‍රියාවලියක් ස්වයංක්‍රීයව පාලනය කරන සහ ක්‍රියාත්මක කරන පද්ධති ස්වයංක්‍රීය පද්ධති ලෙස හැඳින්වේ. මේවා ප්‍රධාන වශයෙන් සංවේදක (Sensors), පාලක (Controllers) සහ ක්‍රියාකරු (Actuators) මත පදනම් වේ.\r\n* 1. ස්වයංක්‍රීය පද්ධතියක ප්‍රධාන සංරචක (Key Components)\r\nස්වයංක්‍රීය පද්ධතියක් සාර්ථකව ක්‍රියා කිරීමට පහත කොටස් අත්‍යවශ්‍ය වේ:\r\n-1.1 සංවේදක (Sensors):     බාහිර පරිසරයෙන් දත්ත ලබා ගැනීම සිදු කරයි (උදා: උෂ්ණත්වය, ආලෝකය, පීඩනය).\r\n-1.2 පාලකය (Controller):     සංවේදක මගින් ලැබෙන දත්ත විශ්ලේෂණය කර තීරණ ලබා ගනී (උදා: Microprocessors, PLC).\r\n-1.3 ක්‍රියාකරු (Actuators):     පාලකයෙන් ලැබෙන තීරණය අනුව භෞතික ක්‍රියාව සිදු කරයි (උදා: මෝටරයක් කැරකැවීම, ලාම්පුවක් දැල්වීම).\r\n# 2. පාලන පද්ධති වර්ග (Types of Control Systems)\r\nස්වයංක්‍රීය පද්ධති ක්‍රියා කරන ආකාරය අනුව වර්ග දෙකකි:\r\n* 2.1 විවෘත පුඩු පද්ධති (Open-loop Systems):\r\n-මෙහිදී ප්‍රතිදානය (Output) මගින් පාලනයට බලපෑමක් ඇති නොකරයි. පද්ධතිය නියමිත කාලයකට හෝ සැකසුමකට පමණක් ක්‍රියා කරයි.\r\n-උදා: රෙදි සෝදන යන්ත්‍රය (Washing Machine), ටෝස්ටරය.\r\n* 2.2 වැසුණු පුඩු පද්ධති (Closed-loop Systems):\r\n-මෙහිදී ප්‍රතිදානය නිරන්තරයෙන් පරීක්ෂා කර පද්ධතිය ස්වයංක්‍රීයව සකස් වේ (Feedback).\r\n-උදා: වායුසමීකරණ යන්ත්‍රය (Air Conditioner), ස්වයංක්‍රීය ජල ටැංකි මට්ටම් පාලකය.\r\n# 3. ස්වයංක්‍රීය පද්ධතිවල වාසි (Advantages)\r\n* කාර්යක්ෂමතාව (Efficiency): මිනිසුන්ට වඩා වේගයෙන් සහ නොකඩවා වැඩ කළ හැකිය.\r\n* නිරවද්‍යතාව (Accuracy): මිනිස් අතින් සිදුවන වැරදි (Human errors) අවම වේ.\r\n* ආරක්ෂාව (Safety): මිනිසුන්ට අන්තරාදායක පරිසරවල (උදා: රසායනික කර්මාන්තශාලා, අභ්‍යවකාශය) වැඩ කිරීමට හැකියාව ඇත.\r\n* පිරිවැය අඩුවීම: දිගුකාලීනව බලන විට ශ්‍රම පිරිවැය (Labor cost) අඩු වේ.\r\n# 4. ස්වයංක්‍රීය පද්ධති සඳහා උදාහරණ (Examples)\r\n* නිවෙස් ස්වයංක්‍රීයකරණය (Smart Home): ස්වයංක්‍රීයව දැල්වෙන විදුලි පහන්, ආරක්ෂක කැමරා පද්ධති.\r\n* කර්මාන්තශාලා (Industrial Robotics): වාහන එකලස් කරන රොබෝ අත්.\r\n* කෘෂිකර්මාන්තය: ස්වයංක්‍රීයව ජලය සපයන පද්ධති (Automatic Irrigation).\r\n* ප්‍රවාහනය: ස්වයංක්‍රීයව පදවන කාර් (Self-driving cars), ගුවන් යානාවල Autopilot පද්ධතිය.\r\n# 5. ස්වයංක්‍රීය පද්ධතිවල අවාසි (Disadvantages)\r\n* ඉහළ ආරම්භක පිරිවැය: පද්ධතිය සවි කිරීමට විශාල මුදලක් වැය වේ.\r\n* රැකියා අහිමි වීම: මිනිස් ශ්‍රමය වෙනුවට යන්ත්‍ර ආදේශ වීම නිසා රැකියා හිඟයක් ඇති විය හැක.\r\n* නඩත්තු කිරීම: පද්ධතියේ දෝෂයක් ඇති වුවහොත් එය නිවැරදි කිරීමට විශේෂඥ දැනුමක් අවශ්‍ය වේ.', NULL, 0, 0),
(40, 1, 'ICT', 2014, 'MCQ', 48, '# කාර්යබද්ධ අවශ්‍යතා (Functional Requirements)\n\nපද්ධතියක් මගින් අනිවාර්යයෙන්ම සිදු කළ යුතු කාර්යයන් (Functions/Services) මොනවාද යන්න මෙයින් අදහස් කෙරේ. එනම්, පරිශීලකයාට පද්ධතිය හරහා ලබාගත හැකි සේවාවන් මෙයට අයත් වේ.\n* ලක්ෂණ සහ උදාහරණ:\n- පද්ධතියට ලබාදෙන ආදාන (Inputs) සහ ලබාගත යුතු ප්‍රතිදාන (Outputs) විස්තර කරයි.\n- පද්ධතිය විසින් සිදුකරන දත්ත සැකසුම් (Data Processing) ක්‍රියාවලිය මෙයට අයත් වේ.\n\n* උදාහරණ (පාසල් පුස්තකාල පද්ධතියක් සඳහා):\n- නව සාමාජිකයන් ලියාපදිංචි කිරීමේ හැකියාව තිබිය යුතුය.\n- පොත් නිකුත් කිරීම සහ නැවත භාරගැනීම සිදු කළ යුතුය.\n- ප්‍රමාද ගාස්තු ස්වයංක්‍රීයව ගණනය කළ යුතුය.\n- පොත් පිළිබඳ තොරතුරු සෙවීමට (Search) පහසුකම් තිබිය යුතුය.\n\n# කාර්යබද්ධ නොවන අවශ්‍යතා (Non-functional Requirements)\n\nපද්ධතියක් විසින් යම් කාර්යයක් සිදු කරන ආකාරය හෝ පද්ධතියේ පවතින ගුණාත්මක ලක්ෂණ (Quality Attributes) මෙයින් අදහස් කෙරේ. පද්ධතිය කෙතරම් හොඳින් ක්‍රියා කරන්නේද යන්න මැන බලන්නේ මෙයිනි.\n* ප්‍රධාන වර්ග සහ උදාහරණ:\n- ක්‍රියාකාරීත්වය (Performance): පද්ධතියේ වේගය.[උදා: ඕනෑම ගනුදෙනුවක් තත්පර 2ක් ඇතුළත අවසන් විය යුතුය.]\n- ආරක්ෂාව (Security): දත්තවල සුරක්ෂිතභාවය.[උදා: පද්ධතියට ඇතුළු වීමට මුරපදයක් (Password) අනිවාර්ය විය යුතුය.]\n- භාවිතයේ පහසුව (Usability): පරිශීලකයාට පද්ධතිය හැසිරවීමට ඇති පහසුව.[උදා: පද්ධතියේ අතුරුමුහුණත (Interface) සිංහල භාෂාවෙන් තිබිය යුතුය.]\n- පවත්වාගෙන යාමේ පහසුව (Maintainability): පද්ධතියේ දෝෂ නිවැරදි කිරීමට හෝ යාවත්කාලීන කිරීමට ඇති හැකියාව.\n- විශ්වාසනීයත්වය (Reliability): පද්ධතිය බිඳ වැටීමකින් තොරව ක්‍රියා කිරීමේ හැකියාව.\n\n# කාර්යබද්ධ අවශ්‍යතා (Functional Requirements)\n\nපද්ධතියක් මගින් අනිවාර්යයෙන්ම සිදු කළ යුතු කාර්යයන් (Functions/Services) මොනවාද යන්න මෙයින් අදහස් කෙරේ. එනම්, පරිශීලකයාට පද්ධතිය හරහා ලබාගත හැකි සේවාවන් මෙයට අයත් වේ.\n* ලක්ෂණ සහ උදාහරණ:\n- පද්ධතියට ලබාදෙන ආදාන (Inputs) සහ ලබාගත යුතු ප්‍රතිදාන (Outputs) විස්තර කරයි.\n- පද්ධතිය විසින් සිදුකරන දත්ත සැකසුම් (Data Processing) ක්‍රියාවලිය මෙයට අයත් වේ.\n\n* උදාහරණ (පාසල් පුස්තකාල පද්ධතියක් සඳහා):\n- නව සාමාජිකයන් ලියාපදිංචි කිරීමේ හැකියාව තිබිය යුතුය.\n- පොත් නිකුත් කිරීම සහ නැවත භාරගැනීම සිදු කළ යුතුය.\n- ප්‍රමාද ගාස්තු ස්වයංක්‍රීයව ගණනය කළ යුතුය.\n- පොත් පිළිබඳ තොරතුරු සෙවීමට (Search) පහසුකම් තිබිය යුතුය.\n\n# කාර්යබද්ධ නොවන අවශ්‍යතා (Non-functional Requirements)\n\nපද්ධතියක් විසින් යම් කාර්යයක් සිදු කරන ආකාරය හෝ පද්ධතියේ පවතින ගුණාත්මක ලක්ෂණ (Quality Attributes) මෙයින් අදහස් කෙරේ. පද්ධතිය කෙතරම් හොඳින් ක්‍රියා කරන්නේද යන්න මැන බලන්නේ මෙයිනි.\n* ප්‍රධාන වර්ග සහ උදාහරණ:\n- ක්‍රියාකාරීත්වය (Performance): පද්ධතියේ වේගය.[උදා: ඕනෑම ගනුදෙනුවක් තත්පර 2ක් ඇතුළත අවසන් විය යුතුය.]\n- ආරක්ෂාව (Security): දත්තවල සුරක්ෂිතභාවය.[උදා: පද්ධතියට ඇතුළු වීමට මුරපදයක් (Password) අනිවාර්ය විය යුතුය.]\n- භාවිතයේ පහසුව (Usability): පරිශීලකයාට පද්ධතිය හැසිරවීමට ඇති පහසුව.[උදා: පද්ධතියේ අතුරුමුහුණත (Interface) සිංහල භාෂාවෙන් තිබිය යුතුය.]\n- පවත්වාගෙන යාමේ පහසුව (Maintainability): පද්ධතියේ දෝෂ නිවැරදි කිරීමට හෝ යාවත්කාලීන කිරීමට ඇති හැකියාව.\n- විශ්වාසනීයත්වය (Reliability): පද්ධතිය බිඳ වැටීමකින් තොරව ක්‍රියා කිරීමේ හැකියාව.', 'uploads/notes/1770299924_6984a214f1d84.png', 0, 0),
(42, 1, 'ICT', 2026, 'Papers', 49, '\r\n# Paper 39\r\n# Question No : 01\r\n* සම්පූර්ණ නම: American Standard Code for Information Interchange.\r\n* බිටු ප්‍රමාණය: මුල්කාලීන (Standard) ASCII කේතයක් සඳහා බිටු 7ක් (7-bit) භාවිතා වේ.\r\n* නිරූපණය කළ හැකි අනුලක්ෂණ ගණන: $2^7$ = 128 කි. (එනම් 0 සිට 127 දක්වා අගයන්).\r\n* Extended ASCII: පසුව මෙය බිටු 8ක් (8-bit) දක්වා වැඩි කළ අතර එමගින් අනුලක්ෂණ 256ක් ($2^8$) නිරූපණය කළ හැකිය.\r\n* ASCII කේත ක්‍රමයේ වාසි සහ අවාසි\r\nවාසි:\r\n- ලෝකයේ ඕනෑම පරිගණක පද්ධතියක පාහේ භාවිතා කළ හැකි වීම (Standardization).\r\n- මතකය ඉතා අඩුවෙන් වැය වීම (එක් අනුලක්ෂණයකට බිටු 7ක් හෝ 8ක් පමණි).\r\n* අවාසි (සීමාවන්):\r\n- මෙහි නිරූපණය කළ හැක්කේ ඉංග්‍රීසි අකුරු සහ සීමිත සංකේත ප්‍රමාණයක් පමණි.\r\n- සිංහල, දෙමළ, චීන වැනි අනෙකුත් භාෂා නිරූපණය කිරීමේ හැකියාවක් මෙයට නැත. (මේ සඳහා Unicode භාවිතා කරයි).\r\n* ASCII සහ Unicode අතර වෙනස\r\n- බිටු ගණන = ASCII [සාමාන්‍යයෙන් බිටු 7 හෝ 8 කි.]  Unicode[බිටු 16 හෝ 32 කි (UTF-8, UTF-16).]\r\n- නිරූපණය = ASCII[ඉංග්‍රීසි අකුරු පමණි.]   Unicode [ලොව පුරා ඇති සියලුම භාෂා නිරූපණය කළ හැක.]\r\n- ධාරිතාව = ASCII [අනුලක්ෂණ 128 හෝ 256 කි.]   Unicode [අනුලක්ෂණ මිලියනයකට වඩා වැඩිය.]\r\n# Question No : 02\r\n* BCD හිදී සෑම දශම ඉලක්කමකටම (Decimal Digit) වෙන් වෙන් වශයෙන් බිටු 4ක (4-bit) ද්විමය අගයක් ලබා දේ.\r\n* මෙහිදී භාවිතා වන්නේ 8-4-2-1  බර තැබීමේ ක්‍රමයයි.\r\n# Question No : 05\r\n* EBCDIC යනු පරිගණක පද්ධති තුළ දත්ත (අකුරු, ඉලක්කම් සහ සංකේත) නිරූපණය කිරීමට භාවිතා කරන තවත් එක් සම්මත කේත ක්‍රමයකි.\r\n* සම්පූර්ණ නම: Extended Binary Coded Decimal Interchange Code.\r\n* නිර්මාණය: මෙය IBM (International Business Machines) ආයතනය විසින් නිර්මාණය කරන ලදී.\r\n* බිටු ප්‍රමාණය: මෙය බිටු 8ක (8-bit) කේත ක්‍රමයකි.\r\n* නිරූපණය කළ හැකි අනුලක්ෂණ ගණන: 256 කි.\r\n* ප්‍රධාන වශයෙන් Mainframe පරිගණක සහ AS/400 වැනි විශාල පරිගණක පද්ධති (IBM computers) තුළ දත්ත නිරූපණයට මෙය භාවිතා වේ.\r\n* සාමාන්‍ය පෞද්ගලික පරිගණක (PC) තුළ මෙයට වඩා ASCII කේතය බහුලව භාවිතා වේ.\r\n# Question No : 07\r\n* ABC (Atanasoff-Berry Computer)\r\n- සම්පූර්ණ නම: Atanasoff-Berry Computer.\r\n- නිර්මාණකරුවන්: ජෝන් ඇටනසොෆ් (John Atanasoff) සහ ක්ලිෆඩ් බෙරී (Clifford Berry).\r\n- විශේෂත්වය: ලොව ප්‍රථම ස්වයංක්‍රීය ඉලෙක්ට්‍රොනික ද්විමය (Electronic Binary) පරිගණකය ලෙස සැලකේ.\r\n* ENIAC (Electronic Numerical Integrator and Computer)\r\n- නිර්මාණකරුවන්: ජෝන් ප්‍රෙස්පර් එකට් සහ ජෝන් මච්ලි (Eckert & Mauchly).\r\n- විශේෂත්වය: ලොව ප්‍රථම මහා පරිමාණ, පොදු කාර්ය (General Purpose) ඉලෙක්ට්‍රොනික පරිගණකයයි.\r\n- තාක්ෂණය: රික්තක නළ (Vacuum tubes) දහස් ගණනක් භාවිතා කරන ලදී. මෙහි දත්ත ගබඩා කිරීමේ හැකියාවක් නොතිබූ අතර වැඩසටහන් ක්‍රියාත්මක කිරීමට වයර් මාරු කිරීම (Hard-wiring) සිදු කළ යුතු විය.\r\n* EDVAC (Electronic Discrete Variable Automatic Computer)\r\n- විශේෂත්වය: \"ගබඩා කළ වැඩසටහන් සංකල්පය\" (Stored Program Concept) ප්‍රායෝගිකව ක්‍රියාත්මක කිරීමට සැලසුම් කළ පරිගණකයකි.\r\n- සංකල්පය: මෙහිදී දත්ත සහ උපදෙස් යන දෙකම එකම මතකයක ගබඩා කළ හැකි විය (Von Neumann Architecture).\r\n* EDSAC (Electronic Delay Storage Automatic Calculator)\r\n- නිර්මාණකරු: මොරිස් විල්ක්ස් (Maurice Wilkes).\r\n- විශේෂත්වය: ගබඩා කළ වැඩසටහන් සංකල්පය මත පදනම්ව ප්‍රායෝගිකව ක්‍රියාත්මක කළ ලොව ප්‍රථම පරිගණකය මෙයයි.\r\n* UNIVAC (Universal Automatic Computer)\r\n- විශේෂත්වය: වාණිජමය වශයෙන් (Commercially) සාර්ථක වූ ලොව ප්‍රථම පරිගණකයයි.\r\n- භාවිතය: මෙය ව්‍යාපාරික සහ රජයේ පරිපාලන කටයුතු (උදා: ජන සංගණන කටයුතු) සඳහා මුලින්ම භාවිතා කරන ලදී.\r\n# Question No : 09\r\n* රික්තක නළ (Vacuum Tubes): දත්ත සැකසීම සඳහා ප්‍රධාන උපාංගය ලෙස රික්තක නළ දහස් ගණනක් භාවිතා කරන ලදී.\r\n* මතකය (Memory): දත්ත ගබඩා කිරීමට චුම්බක ඩ්‍රම් (Magnetic Drums) භාවිතා විය.\r\n* ක්‍රමලේඛන භාෂාව: පරිගණකයට කෙලින්ම තේරුම් ගත හැකි යන්ත්‍ර භාෂාව (Machine Language / Binary) පමණක් භාවිතා විය.\r\n* ආදාන සහ ප්‍රතිදාන: දත්ත ලබාදීමට සිදුරුපත් (Punched Cards) සහ ප්‍රතිදානය ලබා ගැනීමට මුද්‍රිත පිටපත් (Paper Tapes) භාවිතා කරන ලදී.\r\n# Question No : 10\r\n* SMTP (Simple Mail Transfer Protocol)\r\n- කාර්යය: ඊමේල් පණිවිඩයක් එක් පරිගණකයක සිට තවත් සේවාදායකයකට (Server) යැවීම (Sending) සඳහා භාවිතා වේ.\r\n- ක්‍රියාවලිය: ඔබ ඊමේල් එකක් ලියා \'Send\' කළ විට, එය ඔබගේ පරිගණකයේ සිට ඊමේල් සේවාදායකය (Email Server) වෙත ගෙන යන්නේ SMTP මගිනි.\r\n- Port එක: සාමාන්‍යයෙන් Port 25 හෝ 587 භාවිතා කරයි.\r\n* POP3 (Post Office Protocol version 3)\r\n- කාර්යය: සේවාදායකයේ (Server) ඇති ඊමේල් පණිවිඩ ඔබේ පරිගණකයට බාගත කරගැනීම (Download) සඳහා භාවිතා වේ.\r\n- විශේෂත්වය: මෙහිදී පණිවිඩය ඔබේ පරිගණකයට බාගත වූ පසු, සාමාන්‍යයෙන් එම පණිවිඩය ඊමේල් සේවාදායකයෙන් (Server) මැකී යයි.\r\n- සීමාව: ඔබ එක පණිවිඩයක් එක උපාංගයකට (Device) බාගත කළ පසු, වෙනත් උපාංගයකින් (උදා: Phone එකෙන්) එම ඊමේල් එක නැවත බැලීමට නොහැකි විය හැක.\r\n- Port එක: Port 110.\r\n* IMAP (Internet Message Access Protocol)\r\n- කාර්යය: සේවාදායකයේ ඇති ඊමේල් පණිවිඩ පරිගණකයට බාගත නොකර, සේවාදායකය මතදීම නිරීක්ෂණය (Access/Sync) කිරීමට ඉඩ ලබාදේ.\r\n- විශේෂත්වය: මෙහිදී ඊමේල් පණිවිඩ සේවාදායකය තුළම පවතින නිසා, ඔබට විවිධ උපාංග කිහිපයකින් (Phone, Laptop, Tablet) එකම අවස්ථාවේදී ඊමේල් පරීක්ෂා කළ හැක.\r\n- වාසිය: එක උපාංගයකින් ඊමේල් එකක් කියවූ විට හෝ මැකූ විට, අනෙක් සියලුම උපාංගවලද එය ස්වයංක්‍රීයව යාවත්කාලීන (Sync) වේ.\r\n- Port එක: Port 143.', 'uploads/1770308389_6984c3253114b.png', 0, 0),
(44, 1, 'ICT', 2026, 'GENERAL', 50, '\r\n# පද්ධති සංවර්ධන ආකෘති (SDLC Models)\r\n# දියඇලි ආකෘතිය (Waterfall Model)\r\nමෙය වඩාත් පැරණිතම සහ සරලම ආකෘතියයි. මෙහිදී එක් පියවරක් සම්පූර්ණයෙන්ම අවසන් වූ පසු පමණක් මීළඟ පියවර ආරම්භ කරයි.\r\n* ක්‍රියාවලිය: අවශ්‍යතා විශ්ලේෂණය $\\rightarrow$ පද්ධති සැලසුම්කරණය $\\rightarrow$ ක්‍රියාත්මක කිරීම $\\rightarrow$ පිරික්සුම $\\rightarrow$ නඩත්තුව.\r\n* වාසි:\r\n- සරල බැවින් කළමනාකරණය පහසුය.\r\n- අවශ්‍යතාවන් ඉතා පැහැදිලි ව්‍යාපෘති සඳහා සුදුසුය.\r\n* අවාසි:\r\n- නම්‍යශීලී නොවේ (පසුගිය පියවරකට යාම අපහසුය).\r\n- සම්පූර්ණ පද්ධතියම නිමවන තෙක් මෘදුකාංගය දැකගත නොහැක.\r\n# සර්පිල ආකෘතිය (Spiral Model)\r\nමෙම ආකෘතිය නිර්මාණය කර ඇත්තේ ව්‍යාපෘතියේ අවදානම (Risk) කළමනාකරණය කිරීම සඳහාය. මෙහි පියවර සර්පිලාකාරව නැවත නැවත සිදුවේ.\r\n* ප්‍රධාන අදියර:\r\n- අරමුණු හඳුනාගැනීම.\r\n- අවදානම් ඇගයීම (Risk Analysis).\r\n- සංවර්ධනය සහ පිරික්සුම.\r\n- මීළඟ වටය සැලසුම් කිරීම.\r\n* වාසි:\r\n- අවදානම කලින් හඳුනාගෙන පාලනය කළ හැක.\r\n- ඉතා සංකීර්ණ සහ විශාල පද්ධති සඳහා සුදුසුය.\r\n* අවාසි:\r\n- අවදානම් විශ්ලේෂණය සඳහා විශේෂඥ දැනුමක් අවශ්‍ය වීම.\r\n- කුඩා ව්‍යාපෘති සඳහා වියදම් අධික වීම.\r\n# සුචල්ය (Agile Model)\r\nනූතන මෘදුකාංග ක්ෂේත්‍රයේ වඩාත්ම ජනප්‍රිය ක්‍රමය මෙයයි. මෙහිදී පාරිභෝගිකයාගේ අවශ්‍යතා නිතර වෙනස් වන විට ඊට ගැලපෙන පරිදි ඉතා වේගයෙන් (Sprints ලෙස) පද්ධතිය සංවර්ධනය කරයි.\r\n* ලක්ෂණ:\r\n- අඛණ්ඩව පාරිභෝගිකයාගේ අදහස් (Feedback) ලබා ගනී.\r\n- ලේඛනගත කිරීමට වඩා ක්‍රියා කරන මෘදුකාංගයකට මුල් තැන දේ.\r\n* වාසි:\r\n- අවශ්‍යතා වෙනස් කිරීමට ඇති ඉහළ නම්‍යශීලී බව.\r\n- උසස් තත්ත්වයේ මෘදුකාංගයක් කෙටි කලකින් ලබා දිය හැක.\r\n# වේගවත් යෙදුම් සංවර්ධනය (RAD - Rapid Application Development)\r\nRAD යනු ලිඛිත ලේඛන වලට වඩා මූලාකෘති (Prototyping) සෑදීම සහ පරිශීලක ප්‍රතිචාර වලට මුල් තැන දෙන ක්‍රමයකි.\r\n* ක්‍රියාවලිය:\r\n- අවශ්‍යතා සැලසුම් කිරීම \\rightarrow පරිශීලක සැලසුම් (Design) \\rightarrow ඉදිකිරීම (Construction) \\rightarrowක්‍රියාත්මක කිරීම (Cutover).\r\n* විශේෂත්වය:\r\n- මෙහිදී \"CASE Tools\" සහ මෘදුකාංග සංරචක නැවත භාවිතය (Reusable components) මගින් සංවර්ධන වේගය වැඩි කරයි.\r\n* වාසි:\r\n- ඉතා කෙටි කාලයකින් පද්ධතියක් සාදා නිම කළ හැක.\r\n- පරිශීලක අවශ්‍යතා වලට ඉතා සමීප පද්ධතියක් ලැබේ.', 'uploads/1771058225_69903431bda21.png', 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `question_results`
--

CREATE TABLE `question_results` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `question_id` int(11) DEFAULT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `user_answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `attempted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `question_results`
--

INSERT INTO `question_results` (`id`, `user_id`, `question_id`, `subject_name`, `user_answer`, `is_correct`, `attempted_at`) VALUES
(0, 1, 2, 'ICT', '0', 0, '2026-01-31 11:22:31'),
(1, 1, 2, 'ICT', '1', 1, '2026-01-22 08:26:58'),
(2, 1, 5, 'ICT', '3', 1, '2026-01-22 08:26:58'),
(3, 1, 1, 'ICT', '1', 1, '2026-01-22 08:26:58'),
(4, 1, 3, 'ICT', '0', 1, '2026-01-22 08:26:58'),
(5, 1, 4, 'ICT', '2', 1, '2026-01-22 08:26:58'),
(6, 1, 1, 'ICT', '0', 0, '2026-01-22 14:22:57'),
(7, 1, 3, 'ICT', '0', 1, '2026-01-22 14:22:57'),
(8, 1, 4, 'ICT', '2', 1, '2026-01-22 14:22:57'),
(9, 1, 2, 'ICT', '4', 0, '2026-01-22 14:22:57'),
(10, 1, 5, 'ICT', '2', 0, '2026-01-22 14:22:57'),
(11, 1, 4, 'ICT', '1', 0, '2026-01-22 14:26:02'),
(12, 1, 2, 'ICT', '1', 1, '2026-01-22 14:26:02'),
(13, 1, 5, 'ICT', 'N/A', 0, '2026-01-22 14:26:02'),
(14, 1, 1, 'ICT', 'N/A', 0, '2026-01-22 14:26:02'),
(15, 1, 3, 'ICT', 'N/A', 0, '2026-01-22 14:26:02'),
(16, 1, 3, 'ICT', '1', 0, '2026-01-22 14:29:35'),
(17, 1, 4, 'ICT', '2', 1, '2026-01-22 14:29:35'),
(18, 1, 1, 'ICT', '1', 1, '2026-01-22 14:29:35'),
(19, 1, 2, 'ICT', '0', 0, '2026-01-22 14:32:50'),
(20, 1, 4, 'ICT', '2', 1, '2026-01-22 14:32:50'),
(21, 1, 5, 'ICT', '4', 0, '2026-01-22 15:32:22'),
(22, 1, 1, 'ICT', '2', 0, '2026-01-22 15:32:22');

-- --------------------------------------------------------

--
-- Table structure for table `room_members`
--

CREATE TABLE `room_members` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_members`
--

INSERT INTO `room_members` (`id`, `room_id`, `user_id`, `joined_at`) VALUES
(1, 1, 2, '2026-01-27 07:59:05'),
(2, 2, 2, '2026-01-27 08:00:44'),
(4, 2, 1, '2026-01-27 08:01:40'),
(12, 3, 2, '2026-01-27 08:10:53'),
(14, 3, 1, '2026-01-27 08:11:13'),
(17, 4, 1, '2026-01-27 08:16:37'),
(19, 4, 2, '2026-01-27 08:16:54'),
(25, 5, 1, '2026-01-27 08:25:59'),
(27, 5, 2, '2026-01-27 08:26:16'),
(39, 7, 2, '2026-01-27 08:37:31'),
(41, 7, 1, '2026-01-27 08:37:38'),
(45, 8, 1, '2026-01-27 08:49:49'),
(47, 8, 2, '2026-01-27 08:50:34');

-- --------------------------------------------------------

--
-- Table structure for table `room_questions`
--

CREATE TABLE `room_questions` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `question_text` text DEFAULT NULL,
  `correct_answer` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_questions`
--

INSERT INTO `room_questions` (`id`, `room_id`, `user_id`, `question_text`, `correct_answer`, `created_at`) VALUES
(10, 18, 1, '2q3h;wekBCS>Dzxn ', 'qgwasd', '2026-01-23 13:37:20'),
(11, 18, 2, 'q34wfaesd', '4qwe4q2ewf', '2026-01-23 13:37:33'),
(12, 20, 1, 'kghv', 'nj', '2026-01-23 14:05:13'),
(13, 20, 1, 'kj', 'kjhj', '2026-01-23 14:05:22'),
(14, 22, 4, 'Hello', 'Hi', '2026-01-24 01:51:48'),
(15, 22, 2, 'Hi', 'Ho', '2026-01-24 01:52:07'),
(16, 22, 1, 'name', 'janith', '2026-01-24 01:52:30');

-- --------------------------------------------------------

--
-- Table structure for table `room_question_options`
--

CREATE TABLE `room_question_options` (
  `id` int(11) NOT NULL,
  `question_id` int(11) DEFAULT NULL,
  `option_text` text DEFAULT NULL,
  `is_correct` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_question_options`
--

INSERT INTO `room_question_options` (`id`, `question_id`, `option_text`, `is_correct`) VALUES
(16, 7, 'Yo', 1),
(17, 7, 'Boiz', 0),
(18, 8, 'yo', 1),
(19, 8, 'boiz', 0),
(20, 9, 'yo', 1),
(21, 9, 'boiz', 0),
(22, 10, 'qgwasd', 1),
(23, 10, 'q4waefsd', 0),
(24, 11, '4qwe4q2ewf', 1),
(25, 11, '4fwe', 0),
(26, 12, 'nj', 1),
(27, 12, 'j', 0),
(28, 13, 'kjhj', 1),
(29, 13, 'j', 0),
(30, 14, 'Hi', 1),
(31, 14, 'Yo', 0),
(32, 14, 'Boiz', 0),
(33, 15, 'Ho', 1),
(34, 15, 'Yow', 0),
(35, 15, 'Boiz', 0),
(36, 16, 'janith', 1),
(37, 16, 'j', 0),
(38, 16, 'q', 0);

-- --------------------------------------------------------

--
-- Table structure for table `room_results`
--

CREATE TABLE `room_results` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `total_questions` int(11) DEFAULT NULL,
  `answers_json` text DEFAULT NULL,
  `submitted_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room_results`
--

INSERT INTO `room_results` (`id`, `room_id`, `user_id`, `score`, `total_questions`, `answers_json`, `submitted_at`) VALUES
(5, 18, 1, 0, 0, '[]', '2026-01-23 13:38:03'),
(6, 18, 2, 2, 2, '{\"10\":\"22\",\"11\":\"24\"}', '2026-01-23 13:41:05'),
(7, 20, 2, 2, 2, '{\"12\":\"26\",\"13\":\"28\"}', '2026-01-23 14:05:44'),
(8, 20, 1, 2, 2, '{\"12\":\"26\",\"13\":\"28\"}', '2026-01-23 14:05:46'),
(9, 22, 4, 3, 3, '{\"14\":\"30\",\"15\":\"33\",\"16\":\"36\"}', '2026-01-24 01:53:08'),
(10, 22, 1, 3, 3, '{\"14\":\"30\",\"15\":\"33\",\"16\":\"36\"}', '2026-01-24 01:53:16'),
(11, 22, 2, 1, 3, '{\"14\":\"31\",\"15\":\"34\",\"16\":\"36\"}', '2026-01-24 01:53:22');

-- --------------------------------------------------------

--
-- Table structure for table `study_group_messages`
--

CREATE TABLE `study_group_messages` (
  `id` int(11) NOT NULL,
  `room_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_group_messages`
--

INSERT INTO `study_group_messages` (`id`, `room_id`, `user_id`, `message`, `created_at`) VALUES
(8, 8, 2, '[POLL:2]', '2026-01-27 08:51:01'),
(9, 8, 2, 'hello', '2026-01-27 08:54:10'),
(10, 8, 2, 'me', '2026-01-27 08:54:24'),
(11, 8, 2, '[POLL:3]', '2026-01-27 08:54:35'),
(12, 8, 2, 'hello', '2026-01-27 08:57:03'),
(13, 8, 2, '[NOTE_SHARE:17] ICT (2014)', '2026-01-27 08:57:32');

-- --------------------------------------------------------

--
-- Table structure for table `study_polls`
--

CREATE TABLE `study_polls` (
  `id` int(11) NOT NULL,
  `room_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(100) DEFAULT NULL,
  `option_b` varchar(100) DEFAULT NULL,
  `option_c` varchar(100) DEFAULT NULL,
  `option_d` varchar(100) DEFAULT NULL,
  `option_e` varchar(100) DEFAULT NULL,
  `votes_a` int(11) DEFAULT 0,
  `votes_b` int(11) DEFAULT 0,
  `votes_c` int(11) DEFAULT 0,
  `votes_d` int(11) DEFAULT 0,
  `votes_e` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_polls`
--

INSERT INTO `study_polls` (`id`, `room_id`, `user_id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `option_e`, `votes_a`, `votes_b`, `votes_c`, `votes_d`, `votes_e`, `created_at`) VALUES
(3, 8, 2, 'hello', '1', '2', '3', '', '', 2, 0, 0, 0, 0, '2026-01-27 08:54:35');

-- --------------------------------------------------------

--
-- Table structure for table `study_rooms`
--

CREATE TABLE `study_rooms` (
  `id` int(11) NOT NULL,
  `room_name` varchar(100) DEFAULT NULL,
  `room_code` varchar(10) DEFAULT NULL,
  `creator_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_rooms`
--

INSERT INTO `study_rooms` (`id`, `room_name`, `room_code`, `creator_id`, `created_at`) VALUES
(8, 'laster', '60AECA', 1, '2026-01-27 08:49:49');

-- --------------------------------------------------------

--
-- Table structure for table `study_sessions`
--

CREATE TABLE `study_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `duration` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `study_sessions`
--

INSERT INTO `study_sessions` (`id`, `user_id`, `subject`, `start_time`, `end_time`, `duration`) VALUES
(0, 1, 'ICT', '2026-01-27 15:24:20', '2026-01-27 15:24:38', '00:00:18'),
(1, 1, 'ICT', '2026-01-22 19:47:37', '2026-01-22 19:47:56', '00:00:19'),
(2, 1, 'ICT', '2026-01-22 21:01:38', '2026-01-22 21:01:48', '00:00:10');

-- --------------------------------------------------------

--
-- Table structure for table `timer_sessions`
--

CREATE TABLE `timer_sessions` (
  `session_id` int(11) NOT NULL,
  `goal_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `session_note` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `timer_sessions`
--

INSERT INTO `timer_sessions` (`session_id`, `goal_id`, `user_id`, `start_time`, `end_time`, `duration`, `created_at`, `session_note`) VALUES
(8, 5, 1, '2026-01-28 08:09:37', '2026-01-28 08:10:00', 23, '2026-01-28 02:40:00', 'Combine Maths'),
(9, 5, 1, '2026-01-28 08:10:14', '2026-01-28 08:15:54', 277, '2026-01-28 02:45:54', 'Physics'),
(10, 6, 1, '2026-01-28 08:24:07', '2026-01-28 08:28:15', 180, '2026-01-28 02:58:15', 'ICT'),
(11, 7, 1, '2026-01-28 10:21:18', '2026-01-28 10:21:29', 10, '2026-01-28 04:51:29', NULL),
(12, 7, 1, '2026-01-28 10:21:32', '2026-01-28 10:22:28', 50, '2026-01-28 04:52:28', NULL),
(13, 8, 1, '2026-01-28 20:59:07', '2026-01-28 21:02:15', 120, '2026-01-28 15:32:15', NULL),
(14, 9, 1, '2026-01-31 16:47:25', '2026-01-31 16:50:35', 120, '2026-01-31 11:20:35', NULL),
(15, 10, 1, '2026-02-05 18:23:41', '2026-02-05 18:23:48', 6, '2026-02-05 12:53:48', NULL),
(16, 10, 1, '2026-02-05 18:29:19', '2026-02-05 18:31:23', 114, '2026-02-05 13:01:23', NULL),
(17, 11, 1, '2026-02-05 18:35:29', '2026-02-05 18:38:41', 120, '2026-02-05 13:08:41', NULL),
(18, 12, 1, '2026-02-05 22:28:13', '2026-02-05 23:42:54', 3760, '2026-02-05 18:12:54', NULL),
(19, 12, 1, '2026-02-05 23:43:03', '2026-02-05 23:43:30', 27, '2026-02-05 18:13:30', NULL),
(20, 13, 1, '2026-02-14 19:17:59', '2026-02-14 19:28:33', 633, '2026-02-14 13:58:33', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `timetables`
--

CREATE TABLE `timetables` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `content` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `trackers`
--

CREATE TABLE `trackers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  `paper_type` varchar(50) DEFAULT NULL,
  `question_count` int(11) DEFAULT NULL,
  `start_year` int(11) DEFAULT NULL,
  `end_year` int(11) DEFAULT NULL,
  `allocated_time` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trackers`
--

INSERT INTO `trackers` (`id`, `user_id`, `subject`, `paper_type`, `question_count`, `start_year`, `end_year`, `allocated_time`, `created_at`) VALUES
(10, 1, 'ICT', 'MCQ', 50, 2011, 2025, 120, '2026-01-21 16:53:53'),
(11, 1, 'ICT', 'Structured', 4, 2011, 2025, 120, '2026-01-21 16:53:53'),
(12, 1, 'ICT', 'Essay', 6, 2011, 2025, 120, '2026-01-21 16:53:53'),
(13, 1, 'Physics', 'MCQ', 50, 1982, 2025, 120, '2026-01-28 15:27:34'),
(14, 1, 'Physics', 'Structured', 6, 1982, 2025, 180, '2026-01-28 15:27:34'),
(15, 1, 'Physics', 'Essay', 4, 1982, 2025, 180, '2026-01-28 15:27:34'),
(16, 1, 'Pure Maths', 'Structured', 10, 2011, 2025, 120, '2026-02-05 13:31:39'),
(17, 1, 'Pure Maths', 'Essay', 7, 2011, 2025, 240, '2026-02-05 13:31:39'),
(18, 1, 'Applied Maths', 'Structured', 10, 2011, 2025, 120, '2026-02-05 13:32:05'),
(19, 1, 'Applied Maths', 'Essay', 7, 2011, 2025, 240, '2026-02-05 13:32:05');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `global_id` varchar(15) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `exam_year` int(11) DEFAULT NULL,
  `subjects` text DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `streak_count` int(11) DEFAULT 0,
  `last_activity` date DEFAULT NULL,
  `skipped_subjects` text DEFAULT NULL,
  `current_room_id` int(11) DEFAULT NULL,
  `typing_to` int(11) DEFAULT 0,
  `total_coins` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `global_id`, `name`, `email`, `password`, `exam_year`, `subjects`, `exam_date`, `profile_image`, `created_at`, `streak_count`, `last_activity`, `skipped_subjects`, `current_room_id`, `typing_to`, `total_coins`) VALUES
(1, 'NX-17026', 'Janith Karunathilaka', 'jkarunathilaka2007@gmail.com', '$2y$10$aWP0yvpKJHt3P17pofYZY.0DeUVtQBpImUvI3zPujtf26ImHR2Gwq', 2026, 'ICT, Physics, Pure Maths, Applied Maths', '2026-08-10', 'uploads/1769013820_OIP (2).jpg', '2026-01-21 16:43:40', 1, '2026-02-05', 'Combine Maths', 23, 0, 105),
(2, 'NX-66062', 'Kavithra Karunathilaka', 'ksenethmi@gmail.com', '$2y$10$lY79a2VLj0RKsJx0Fw.Z8un7xCFZFiTK8D8ZNRB2DMUEvBEviddge', 2026, 'Physics, Combine maths', '2026-08-04', 'uploads/1769160248_IMG-20260113-WA0060.jpg', '2026-01-23 09:24:08', 0, NULL, NULL, NULL, 0, 0),
(3, 'NX-80789', 'Theekshana', 'theekshanajnith4@gmail.com', '$2y$10$FM22vMVo.fOct8d4c165feJINx5PI.Qwpp2fqolmsxQPCBWF7EWM2', 2026, 'Maths, Science', '2026-08-19', 'uploads/1769174536_IMG-20260101-WA0043.jpg', '2026-01-23 13:22:16', 0, NULL, NULL, NULL, 0, 0),
(4, 'NX-89368', 'Theekshana ', 'theekshanajanith4@gmail.com', '$2y$10$lFh34zMKjBed02hkKADF9u6OdQexfTHDW3jstbqgqcZ.lxshjdTEO', 2025, 'Sinhala, Maths', '2026-08-04', 'uploads/1769219389_1f5848c2e38f2b9c8a5563e4f672c60e8ae97b57-2400x2400.png', '2026-01-24 01:49:49', 0, NULL, NULL, NULL, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `user_formulas`
--

CREATE TABLE `user_formulas` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `lesson_name` varchar(255) NOT NULL,
  `topic` varchar(255) DEFAULT NULL,
  `sub_topic` varchar(255) DEFAULT NULL,
  `formula_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_formulas`
--

INSERT INTO `user_formulas` (`id`, `user_id`, `subject_name`, `lesson_name`, `topic`, `sub_topic`, `formula_text`, `created_at`) VALUES
(1, 1, 'Pure Maths', 'Trigonometric', '3θ සූත්‍ර', '', '\\sin3\\theta=3\\sin\\theta-4\\sin^{3\\theta }', '2026-01-27 15:06:36'),
(3, 1, 'Pure Maths', 'Trigonometric', 'ත්‍රිකෝනාමිතික අනුපාත', '', '\\csc\\theta=\\frac{1 }{\\sin\\theta }', '2026-01-23 00:11:13'),
(4, 1, 'Pure Maths', 'Trigonometric', 'ත්‍රිකෝනාමිතික අනුපාත', '', '\\sec\\theta=\\frac{1 }{ \\cos\\theta}', '2026-01-23 00:11:53'),
(5, 1, 'Pure Maths', 'Trigonometric', 'ත්‍රිකෝනාමිතික අනුපාත', '', '\\cot\\theta=\\frac{1 }{\\tan \\theta}', '2026-01-23 00:12:54'),
(6, 1, 'Pure Maths', 'Trigonometric', 'ත්‍රිකෝනාමිතික අනුපාත', '', '\\tan\\theta=\\frac{\\sin\\theta }{\\cos\\theta}', '2026-01-23 00:13:41'),
(7, 1, 'Pure Maths', 'Trigonometric', 'ත්‍රිකෝනාමිතික අනුපාත', '', '\\cot\\theta=\\frac{\\cos\\theta }{\\sin\\theta}', '2026-01-23 00:14:39'),
(8, 1, 'Pure Maths', 'Trigonometric', 'මූලික ත්‍රිකෝණමිතික සූත්‍ර', '', '\\sin^{2 }\\theta+\\cos^{2 }\\theta=1', '2026-01-23 00:16:48'),
(9, 1, 'Pure Maths', 'Trigonometric', 'බහුගුණ කෝණ සූත්‍ර', '', '\\sin(A+B)=\\sin A \\cos B+\\cos A\\sin B', '2026-01-23 00:31:59'),
(10, 1, 'Pure Maths', 'Trigonometric', 'මූලික ත්‍රිකෝණමිතික සූත්‍ර', '', '1+\\tan^{2 }\\theta=\\sec^{2 }\\theta', '2026-01-23 01:31:50'),
(11, 1, 'Pure Maths', 'Trigonometric', 'මූලික ත්‍රිකෝණමිතික සූත්‍ර', '', '1+\\cot^{2 }\\theta=\\csc^{2 }\\theta', '2026-01-23 01:32:32'),
(12, 1, 'Pure Maths', 'Trigonometric', 'බහුගුණ කෝණ සූත්‍ර', '', '\\sin(A-B)=\\sin A\\cos B-\\cos A\\sin B', '2026-01-23 01:34:53'),
(13, 1, 'Pure Maths', 'Trigonometric', 'බහුගුණ කෝණ සූත්‍ර', '', '\\cos(A+B)=\\cos A\\cos B-\\sin A\\sin B', '2026-01-23 01:36:31'),
(14, 1, 'Pure Maths', 'Trigonometric', 'බහුගුණ කෝණ සූත්‍ර', '', '\\cos(A-B)=\\cos A\\cos B+\\sin A\\sin B', '2026-01-23 01:37:29'),
(15, 1, 'Pure Maths', 'Trigonometric', 'බහුගුණ කෝණ සූත්‍ර', '', '\\tan(A+B)= \\frac{\\tan A+\\tan B}{ 1-\\tan A\\tan B}', '2026-01-23 01:40:11'),
(16, 1, 'Pure Maths', 'Trigonometric', 'බහුගුණ කෝණ සූත්‍ර', '', '\\tan(A-B)=\\frac{\\tan A-\\tan B }{1+\\tan A\\tan B } ', '2026-01-23 01:42:48'),
(18, 1, 'Pure Maths', 'Trigonometric', '2θ සූත්‍ර', '', '\\sin2\\theta=2\\sin\\theta\\cos\\theta', '2026-01-23 02:05:23'),
(19, 1, 'Pure Maths', 'Trigonometric', '2θ සූත්‍ර', '', '\\sin2\\theta=\\frac{2\\tan\\theta }{ 1+\\tan^{2 }\\theta}', '2026-01-23 02:06:31'),
(20, 1, 'Pure Maths', 'Trigonometric', '2θ සූත්‍ර', '', '\\cos2\\theta=\\cos^{2 }\\theta-\\sin^{2 }\\theta', '2026-01-23 02:07:19'),
(21, 1, 'Pure Maths', 'Trigonometric', '2θ සූත්‍ර', '', '\\cos2\\theta=1-2\\sin^{2 }\\theta', '2026-01-23 02:08:02'),
(22, 1, 'Pure Maths', 'Trigonometric', '2θ සූත්‍ර', '', '\\cos2\\theta=2\\cos^{2 }\\theta-1', '2026-01-23 02:08:43'),
(23, 1, 'Pure Maths', 'Trigonometric', '2θ සූත්‍ර', '', '\\cos2\\theta=\\frac{1-\\tan^{2 }\\theta }{1+\\tan^{2 }\\theta }', '2026-01-23 02:10:18'),
(24, 1, 'Pure Maths', 'Trigonometric', '2θ සූත්‍ර', '', '\\tan2\\theta=\\frac{2\\tan\\theta }{ 1-\\tan^{2 }\\theta}', '2026-01-23 02:11:24'),
(27, 1, 'Pure Maths', 'Trigonometric', '3θ සූත්‍ර', '', '\\cos3\\theta=4\\cos^{3 }\\theta-3\\cos\\theta', '2026-01-28 00:21:43'),
(28, 1, 'Pure Maths', 'Trigonometric', '3θ සූත්‍ර', '', '\\tan3\\theta=\\frac{ 3\\tan\\theta-\\tan^{3 }\\theta}{1-3\\tan^{2 }\\theta }', '2026-01-28 00:23:48'),
(29, 1, 'Pure Maths', 'Trigonometric', 'C - D සූත්‍ර', '', '\\sin C+\\sin D=2\\sin(\\frac{C+D }{ 2}) \\cos(\\frac{C-D }{ 2})', '2026-01-28 00:27:35'),
(30, 1, 'Pure Maths', 'Trigonometric', 'C - D සූත්‍ර', '', '\\sin C-\\sin D=2\\cos(\\frac{C+D }{ 2}) \\sin(\\frac{C-D }{ 2})', '2026-01-28 00:28:55'),
(31, 1, 'Pure Maths', 'Trigonometric', 'C - D සූත්‍ර', '', '\\cos C+\\cos D=2\\cos(\\frac{C+D }{ 2})\\cos(\\frac{C-D }{ 2})', '2026-01-28 00:30:58'),
(32, 1, 'Pure Maths', 'Trigonometric', 'C - D සූත්‍ර', '', '\\cos C-\\cos D=-2\\sin(\\frac{C+D }{ 2})\\sin(\\frac{C-D }{ 2})', '2026-01-28 00:32:43'),
(33, 1, 'Pure Maths', 'Trigonometric', 'C-D සූත්‍ර වල විලෝමය', '', '2\\sin A\\cos B=\\sin(A+B)+\\sin(A-B)', '2026-01-28 00:36:36'),
(34, 1, 'Pure Maths', 'Trigonometric', 'C-D සූත්‍ර වල විලෝමය', '', '2\\cos A\\sin B=\\sin(A+B)-\\sin(A-B)', '2026-01-28 00:37:55'),
(35, 1, 'Pure Maths', 'Trigonometric', 'C-D සූත්‍ර වල විලෝමය', '', '2\\cos A\\cos B=\\cos(A+B)+\\cos(A-B)', '2026-01-28 00:38:49'),
(36, 1, 'Pure Maths', 'Trigonometric', 'C-D සූත්‍ර වල විලෝමය', '', '-2\\sin A\\sin B=\\cos(A+B)+\\cos(A-B)', '2026-01-28 00:39:48'),
(37, 1, 'Pure Maths', 'Trigonometric', 'සාධාරණ විසඳුම්', 'sin', 'X=n\\pi+(-1)^{n }\\alpha', '2026-01-28 00:44:57'),
(38, 1, 'Pure Maths', 'Trigonometric', 'සාධාරණ විසඳුම්', 'cos', 'X=2n\\pi\\pm\\alpha', '2026-01-28 00:46:38'),
(39, 1, 'Pure Maths', 'Trigonometric', 'සාධාරණ විසඳුම්', 'tan', 'X=n\\pi+\\alpha', '2026-01-28 00:47:16'),
(40, 1, 'Pure Maths', 'අවකලනය', 'අවකලනයේ මූලික සූත්‍ර', '', '\\frac{d }{ dx}(x)^{n }=nx^{n-1 }', '2026-02-05 18:25:31'),
(41, 1, 'Pure Maths', 'අවකලනය', 'අවකලනයේ මූලික සූත්‍ර', '', '\\frac{d }{ dx}(\\sqrt{x })=\\frac{1 }{2\\sqrt{x } }', '2026-02-05 18:27:41'),
(42, 1, 'Pure Maths', 'අවකලනය', 'අවකලනයේ මූලික සූත්‍ර', '', '\\frac{d }{ dx}(\\ln x)=\\frac{1 }{x }', '2026-02-05 18:29:57'),
(43, 1, 'Pure Maths', 'අවකලනය', 'අවකලනයේ මූලික සූත්‍ර', '', '\\frac{d }{ dx}(e^{x })=e^{x }', '2026-02-05 18:31:36'),
(44, 1, 'Pure Maths', 'අවකලනය', 'අවකලනයේ මූලික සූත්‍ර', 'දාම නීතිය', '\\frac{d }{ dx}=\\frac{dy }{dA }\\times\\frac{dA }{dB }\\times\\frac{dB }{dC }\\times\\frac{dC }{dx }', '2026-02-05 18:34:21'),
(45, 1, 'Pure Maths', 'අවකලනය', 'අවකලනයේ මූලික සූත්‍ර', '', '\\frac{d }{dx }(u+v)=u \\times\\frac{d }{dx }v+v \\times\\frac{d }{dx }u', '2026-02-05 18:36:37'),
(46, 1, 'Pure Maths', 'අවකලනය', 'අවකලනයේ මූලික සූත්‍ර', '', '\\frac{d }{dx }(\\frac{u }{ v})=\\frac{v\\times\\frac{d }{dx }u-u\\times\\frac{d }{dx }v }{ v^{2 }}', '2026-02-05 18:39:01');

-- --------------------------------------------------------

--
-- Table structure for table `user_pdfs`
--

CREATE TABLE `user_pdfs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject_name` varchar(100) NOT NULL,
  `pdf_title` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_pdfs`
--

INSERT INTO `user_pdfs` (`id`, `user_id`, `subject_name`, `pdf_title`, `file_path`, `uploaded_at`) VALUES
(1, 1, 'ICT', ' 2015 A/L Paper', 'uploads/pdfs/1770656227_2015 ICT  Paper.pdf', '2026-02-09 16:57:07'),
(2, 1, 'ICT', '2011 A/L Paper', 'uploads/pdfs/1769060698_2011 ICT  Paper.pdf', '2026-01-22 05:44:58'),
(3, 1, 'ICT', ' 2012 A/L Paper', 'uploads/pdfs/1769060715_2012 ICT  Paper.pdf', '2026-01-22 05:45:15'),
(4, 1, 'ICT', ' 2013 A/L Paper', 'uploads/pdfs/1769060728_2013 ICT  Paper.pdf', '2026-01-22 05:45:28'),
(5, 1, 'ICT', ' 2014 A/L Paper', 'uploads/pdfs/1769060743_2014 ICT  Paper.pdf', '2026-01-22 05:45:43'),
(6, 1, 'ICT', ' 2016 A/L Paper', 'uploads/pdfs/1770656452_2016 ICT  Paper.pdf', '2026-02-09 17:00:52'),
(7, 1, 'ICT', ' 2017 A/L Paper', 'uploads/pdfs/1770656476_2017 ICT  Paper.pdf', '2026-02-09 17:01:16'),
(8, 1, 'ICT', ' 2018 A/L Paper', 'uploads/pdfs/1770656508_2018 ICT  Paper.pdf', '2026-02-09 17:01:48'),
(9, 1, 'ICT', ' 2019 A/L Paper', 'uploads/pdfs/1770656521_2019 ICT  Paper.pdf', '2026-02-09 17:02:01'),
(10, 1, 'ICT', ' 2020 A/L Paper', 'uploads/pdfs/1770656538_2020 ICT Paper.pdf', '2026-02-09 17:02:18'),
(11, 1, 'ICT', ' 2021 A/L Paper Part 1', 'uploads/pdfs/1770656563_2021  ICT  Paper part 1.pdf', '2026-02-09 17:02:43'),
(12, 1, 'ICT', ' 2021 A/L Paper Part 2', 'uploads/pdfs/1770656591_2021  ICT  Paper part 2.pdf', '2026-02-09 17:03:11'),
(13, 1, 'ICT', ' 2022 A/L Paper', 'uploads/pdfs/1770656605_2022  ICT  Paper.pdf', '2026-02-09 17:03:25'),
(14, 1, 'ICT', ' 2023 A/L Paper', 'uploads/pdfs/1770656618_2023  ICT  Paper.pdf', '2026-02-09 17:03:38'),
(15, 1, 'ICT', ' 2024 A/L Paper', 'uploads/pdfs/1770656630_2024  ICT  Paper.pdf', '2026-02-09 17:03:50');

-- --------------------------------------------------------

--
-- Table structure for table `user_revisions`
--

CREATE TABLE `user_revisions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `subject_name` varchar(100) DEFAULT NULL,
  `paper_type` enum('MCQ','Structured','Essay') DEFAULT NULL,
  `lesson_name` varchar(255) DEFAULT NULL,
  `question` longtext DEFAULT NULL,
  `options_json` longtext DEFAULT NULL,
  `correct_idx` int(11) DEFAULT NULL,
  `answer_text` longtext DEFAULT NULL,
  `created_at` date NOT NULL,
  `attempt_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_revisions`
--

INSERT INTO `user_revisions` (`id`, `user_id`, `subject_name`, `paper_type`, `lesson_name`, `question`, `options_json`, `correct_idx`, `answer_text`, `created_at`, `attempt_count`) VALUES
(1, 1, 'Physics', 'Structured', 'යාන්ත්‍ර විද්‍යාව', 'q43er', NULL, NULL, 'q243e', '2026-01-31', 1),
(2, 1, 'ICT', 'MCQ', 'ISO/OSI 7 Layer Arcitecture', 'ISO/OSI සමුද්දේශ ආකෘතියේ, දත්ත සම්ප්‍රේෂණය සඳහා හොඳම මාර්ගය තේරීම (Routing) සහ තාර්කික ලිපින (Logical Addressing) හැසිරවීම සිදුකරන ස්ථරය කුමක්ද?', '[\"Physical Layer\",\"Network Layer\",\"Transport Layer\",\"Data Link Layer\",\"Application Layer\"]', 1, NULL, '2026-01-22', 6),
(3, 1, 'ICT', 'MCQ', 'ISO/OSI 7 Layer Arcitecture', 'විද්‍යුත් තැපැල් සේවාවක (Email) පණිවිඩයක් යැවීම සඳහා (Outgoing mail) භාවිතා කරන ප්‍රධාන ප්‍රොටෝකෝලය කුමක්ද?', '[\"HTTP\",\"SMTP\",\"POP3\",\"IMAP\",\"HTTPS\"]', 1, NULL, '2026-01-22', 6),
(4, 1, 'ICT', 'MCQ', 'Servers', 'එක් භෞතික සේවාදායකයක් (Physical Server) මෘදුකාංග මගින් ස්වාධීන අතථ්‍ය කොටස් කිහිපයකට වෙන් කර ලබා දෙන සත්කාරක සේවා (Hosting) වර්ගය කුමක්ද?', '[\"VPS Hosting\",\"Shared Hosting\",\"Cloud Hosting\",\"Dedicated Hosting\",\"Google hosting\"]', 0, NULL, '2026-01-22', 5),
(5, 1, 'ICT', 'MCQ', 'ISO/OSI 7 Layer Arcitecture', 'OSI ආකෘතියේ Transport Layer (ප්‍රවාහන ස්ථරය) තුළ දත්ත හඳුන්වන ඒකකය (PDU) කුමක්ද?', '[\"Packets\",\"Bits\",\"Segments\",\"Frames\",\"Data\"]', 2, NULL, '2026-01-22', 6),
(6, 1, 'ICT', 'MCQ', 'Servers', 'ජාලයක ඇති සේවාලාභී පරිගණක (Clients) සඳහා ස්වයංක්‍රීයව IP ලිපින ලබා දීමට භාවිතා කරන සේවාදායකය කුමක්ද?', '[\"DNS Servers\",\"Application Server\",\"Proxy Server\",\"DHCP Server\"]', 3, NULL, '2026-01-22', 1),
(7, 1, 'Pure Maths', 'MCQ', 'යාන්ත්‍ර විද්‍යාව', '{\"header_text\":\"qeffffffff\",\"image\":\"\",\"table\":[[\"qqqqqqqqw\",\"qfwwwwww\"],[\"qfw\",\"qwfffffff\"],[\"qwfffffff\",\"qwffffffff\"]]}', '[\"qwffffffffff\",\"qfeeeeeeeeeeee\",\"qeffffffffffff\",\"QQQQQQQQQQQQQQQ\"]', 0, NULL, '2026-01-31', 2),
(8, 1, 'ICT', 'Essay', 'Networking', '{\"header_text\":\"\\u0db4\\u0dc4\\u0dad \\u0daf\\u0dd0\\u0d9a\\u0dca\\u0dc0\\u0dd9\\u0db1 \\u0d86\\u0dba\\u0dad\\u0db1\\u0dba\\u0d9a \\u0db4\\u0dbb\\u0dd2\\u0db4\\u0dcf\\u0dbd\\u0db1 \\u0d9a\\u0dcf\\u0dbb\\u0dca\\u0dba\\u0dcf\\u0dbd\\u0dba (Admin),\\r\\n\\u0db4\\u0dbb\\u0dd2\\u0d9c\\u0dab\\u0d9a \\u0db4\\u0dca\\u200d\\u0dbb\\u0dcf\\u0dba\\u0ddd\\u0d9c\\u0dd2\\u0d9a\\u0dcf\\u0d9c\\u0dcf\\u0dbb\\u0dba (Lab) \\u0dc3\\u0dc4\\r\\n\\u0db4\\u0dd4\\u0dc3\\u0dca\\u0dad\\u0d9a\\u0dcf\\u0dbd\\u0dba (Lib) \\u0dc3\\u0db3\\u0dc4\\u0dcf \\u0db4\\u0dbb\\u0dd2\\u0d9c\\u0dab\\u0d9a \\u0da2\\u0dcf\\u0dbd\\u0dba\\u0d9a\\u0dca (Computer Network) \\u0dc3\\u0dd0\\u0dbd\\u0dc3\\u0dd4\\u0db8\\u0dca \\u0d9a\\u0dd2\\u0dbb\\u0dd3\\u0db8\\u0da7 \\u0dba\\u0ddd\\u0da2\\u0db1\\u0dcf \\u0d9a\\u0dbb \\u0d87\\u0dad.\\r\\n\\u0d92 \\u0dc3\\u0db3\\u0dc4\\u0dcf \\u0db4\\u0dbb\\u0dd2\\u0d9c\\u0dab\\u0d9a \\u0dc4\\u0dcf \\u0db8\\u0dd4\\u0daf\\u0dca\\u200d\\u0dbb\\u0d9a \\u0db4\\u0dc4\\u0dad \\u0db4\\u0dbb\\u0dd2\\u0daf\\u0dd2 \\u0db7\\u0dcf\\u0dc0\\u0dd2\\u0dad\\u0dcf \\u0d9a\\u0dbb \\u0d87\\u0dad.\\u0d94\\u0db6\\u0da7 192.248.16.0\\/24 IP \\u0dbd\\u0dd2\\u0db4\\u0dd2\\u0db1 \\u0db4\\u0dbb\\u0dcf\\u0dc3\\u0dba \\u0dbd\\u0db6\\u0dcf \\u0daf\\u0dd3 \\u0d87\\u0dad.\\r\\n\\r\\n\\u0db4\\u0dc4\\u0dad \\u0d85\\u0dc0\\u0dc1\\u0dca\\u200d\\u0dba\\u0dad\\u0dcf \\u0dc3\\u0dbd\\u0d9a\\u0dcf \\u0db6\\u0dbd\\u0db1\\u0dca\\u0db1.\\r\\n\\r\\n*\\u0dc3\\u0dd2\\u0dba\\u0dbd\\u0dd4\\u0db8 \\u0d92\\u0d9a\\u0d9a \\u0d91\\u0d9a\\u0dd2\\u0db1\\u0dd9\\u0d9a\\u0da7 \\u0dc3\\u0db8\\u0dca\\u0db6\\u0db1\\u0dca\\u0db0 \\u0dc0\\u0dd6 Local Area Network (LAN) \\u0d91\\u0d9a\\u0d9a\\u0dca \\u0dbd\\u0dd9\\u0dc3 \\u0da2\\u0dcf\\u0dbd\\u0dba \\u0dc3\\u0dd0\\u0dbd\\u0dc3\\u0dd4\\u0db8\\u0dca \\u0d9a\\u0dc5 \\u0dba\\u0dd4\\u0dad\\u0dd4\\u0dba.\\r\\n*Admin \\u0d9a\\u0dcf\\u0dbb\\u0dca\\u0dba\\u0dcf\\u0dbd\\u0dba\\u0dda \\u0db7\\u0dcf\\u0dc0\\u0dd2\\u0dad\\u0dcf \\u0d9a\\u0dbb\\u0db1 Student Information System (SIS) \\u0dba\\u0dd9\\u0daf\\u0dd4\\u0db8\\u0da7 Lib \\u0d9a\\u0dcf\\u0dbb\\u0dca\\u0dba\\u0dcf\\u0dbd\\u0dba\\u0dd9\\u0db1\\u0dca \\u0db4\\u0db8\\u0dab\\u0d9a\\u0dca \\u0db4\\u0dca\\u200d\\u0dbb\\u0dc0\\u0dda\\u0dc1 \\u0dc0\\u0dd2\\u0dba \\u0dc4\\u0dd0\\u0d9a\\u0dd2 \\u0dc0\\u0dd2\\u0dba \\u0dba\\u0dd4\\u0dad\\u0dd4\\u0dba.\\r\\n*Lib \\u0d9a\\u0dcf\\u0dbb\\u0dca\\u0dba\\u0dcf\\u0dbd\\u0dba\\u0dda \\u0db7\\u0dcf\\u0dc0\\u0dd2\\u0dad\\u0dcf \\u0d9a\\u0dbb\\u0db1 Library Information System (LIS) \\u0dba\\u0dd9\\u0daf\\u0dd4\\u0db8\\u0da7 Admin \\u0d9a\\u0dcf\\u0dbb\\u0dca\\u0dba\\u0dcf\\u0dbd\\u0dba\\u0dd9\\u0db1\\u0dca \\u0db4\\u0db8\\u0dab\\u0d9a\\u0dca \\u0db4\\u0dca\\u200d\\u0dbb\\u0dc0\\u0dda\\u0dc1 \\u0dc0\\u0dd2\\u0dba \\u0dc4\\u0dd0\\u0d9a\\u0dd2 \\u0dc0\\u0dd2\\u0dba \\u0dba\\u0dd4\\u0dad\\u0dd4\\u0dba.\\r\\n*Lab \\u0d9a\\u0dcf\\u0dbb\\u0dca\\u0dba\\u0dcf\\u0dbd\\u0dba\\u0da7 \\u0d85\\u0db1\\u0dca\\u0dad\\u0dbb\\u0dca\\u0da2\\u0dcf\\u0dbd \\u0db4\\u0dc4\\u0dc3\\u0dd4\\u0d9a\\u0db8 \\u0dbd\\u0db6\\u0dcf \\u0daf\\u0dd2\\u0dba \\u0dba\\u0dd4\\u0dad\\u0dd4 \\u0d85\\u0dad\\u0dbb, \\u0d91\\u0db8 \\u0d85\\u0db1\\u0dca\\u0dad\\u0dbb\\u0dca\\u0da2\\u0dcf\\u0dbd \\u0dc3\\u0db8\\u0dca\\u0db6\\u0db1\\u0dca\\u0db0\\u0dad\\u0dcf\\u0dc0\\u0dba Internet Service Provider (ISP) \\u0dc4\\u0dbb\\u0dc4\\u0dcf \\u0dbd\\u0db6\\u0dcf \\u0d9c\\u0dad \\u0dba\\u0dd4\\u0dad\\u0dd4\\u0dba.\\r\\n*Lab \\u0d9a\\u0dcf\\u0dbb\\u0dca\\u0dba\\u0dcf\\u0dbd\\u0dba\\u0dda \\u0db4\\u0dbb\\u0dd2\\u0d9c\\u0dab\\u0d9a \\u0dc3\\u0db3\\u0dc4\\u0dcf DNS \\u0dc3\\u0dda\\u0dc0\\u0dcf\\u0dc0\\u0d9a\\u0dca (DNS server) \\u0dc3\\u0dc4 proxy server \\u0d91\\u0d9a\\u0d9a\\u0dca \\u0db7\\u0dcf\\u0dc0\\u0dd2\\u0dad\\u0dcf \\u0d9a\\u0dc5 \\u0dba\\u0dd4\\u0dad\\u0dd4\\u0dba.\\r\\n*\\u0da2\\u0dcf\\u0dbd\\u0dba\\u0dda \\u0d86\\u0dbb\\u0d9a\\u0dca\\u0dc2\\u0dcf\\u0dc0 \\u0dc3\\u0db3\\u0dc4\\u0dcf firewall \\u0d91\\u0d9a\\u0d9a\\u0dca \\u0db7\\u0dcf\\u0dc0\\u0dd2\\u0dad\\u0dcf \\u0d9a\\u0dc5 \\u0dba\\u0dd4\\u0dad\\u0dd4\\u0dba.\",\"image\":\"\",\"table\":[[\"\\u0d92\\u0d9a\\u0d9a\\u0dba\",\"\\u0db4\\u0dbb\\u0dd2\\u0d9c\\u0dab\\u0d9a\",\"\\u0db8\\u0dd4\\u0daf\\u0dca\\u200d\\u0dbb\\u0d9a\"],[\"Admin\",\"\\u0db4\\u0dbb\\u0dd2\\u0d9c\\u0dab\\u0d9a 5\",\"\\u0db8\\u0dd4\\u0daf\\u0dca\\u200d\\u0dbb\\u0d9a 1\"],[\"Lab\",\"\\u0db4\\u0dbb\\u0dd2\\u0d9c\\u0dab\\u0d9a 40\",\"\\u0db8\\u0dd4\\u0daf\\u0dca\\u200d\\u0dbb\\u0d9a 1\"],[\"Lib\",\"\\u0db4\\u0dbb\\u0dd2\\u0d9c\\u0dab\\u0d9a 5\",\"\\u0db8\\u0dd4\\u0daf\\u0dca\\u200d\\u0dbb\\u0d9a 1\"]]}', NULL, NULL, '[{\"q\":\"ඉහත දත්ත ඇසුරෙන් පහත වගුව පුරවන්න.\",\"a\":\"123\",\"img\":\"\",\"tbl\":[[\"ඒකකය\",\"Network address\",\"Subnet mask\",\"IP ලිපින පරාසය\"],[\"Admin\",\"\",\"\",\"\"],[\"Lab\",\"\",\"\",\"\"],[\"Lib\",\"\",\"\",\"\"]],\"subs\":[{\"q\":\"සම්පූර්ණ IP ලිපින ගණන කොපමණද?\",\"a\":\"256\",\"img\":\"\",\"tbl\":[],\"subs\":[]},{\"q\":\"Subnet mask එක කුමක්ද?\",\"a\":\"255.255.255.0\",\"img\":\"\",\"tbl\":[],\"subs\":[]}]}]', '2026-01-31', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `battle_rooms`
--
ALTER TABLE `battle_rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `referral_code` (`referral_code`);

--
-- Indexes for table `coin_transactions`
--
ALTER TABLE `coin_transactions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `completed_questions`
--
ALTER TABLE `completed_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`subject`,`paper_type`,`paper_year`,`question_no`);

--
-- Indexes for table `exam_results`
--
ALTER TABLE `exam_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `friend_requests`
--
ALTER TABLE `friend_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `goals`
--
ALTER TABLE `goals`
  ADD PRIMARY KEY (`goal_id`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `poll_votes`
--
ALTER TABLE `poll_votes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `one_vote_per_user` (`poll_id`,`user_id`);

--
-- Indexes for table `question_notes`
--
ALTER TABLE `question_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`,`subject`,`paper_year`,`paper_type`,`question_no`);

--
-- Indexes for table `question_results`
--
ALTER TABLE `question_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_members`
--
ALTER TABLE `room_members`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_id` (`room_id`,`user_id`);

--
-- Indexes for table `room_questions`
--
ALTER TABLE `room_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_question_options`
--
ALTER TABLE `room_question_options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room_results`
--
ALTER TABLE `room_results`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `study_group_messages`
--
ALTER TABLE `study_group_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `study_polls`
--
ALTER TABLE `study_polls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `study_rooms`
--
ALTER TABLE `study_rooms`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_code` (`room_code`);

--
-- Indexes for table `study_sessions`
--
ALTER TABLE `study_sessions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `timer_sessions`
--
ALTER TABLE `timer_sessions`
  ADD PRIMARY KEY (`session_id`);

--
-- Indexes for table `timetables`
--
ALTER TABLE `timetables`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `user_id` (`user_id`);

--
-- Indexes for table `trackers`
--
ALTER TABLE `trackers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `global_id` (`global_id`);

--
-- Indexes for table `user_formulas`
--
ALTER TABLE `user_formulas`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `user_pdfs`
--
ALTER TABLE `user_pdfs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_revisions`
--
ALTER TABLE `user_revisions`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `battle_rooms`
--
ALTER TABLE `battle_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `coin_transactions`
--
ALTER TABLE `coin_transactions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `completed_questions`
--
ALTER TABLE `completed_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `exam_results`
--
ALTER TABLE `exam_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `friend_requests`
--
ALTER TABLE `friend_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `goals`
--
ALTER TABLE `goals`
  MODIFY `goal_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `poll_votes`
--
ALTER TABLE `poll_votes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `question_notes`
--
ALTER TABLE `question_notes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `room_members`
--
ALTER TABLE `room_members`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=49;

--
-- AUTO_INCREMENT for table `room_questions`
--
ALTER TABLE `room_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `room_question_options`
--
ALTER TABLE `room_question_options`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=39;

--
-- AUTO_INCREMENT for table `room_results`
--
ALTER TABLE `room_results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `study_group_messages`
--
ALTER TABLE `study_group_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `study_polls`
--
ALTER TABLE `study_polls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `study_rooms`
--
ALTER TABLE `study_rooms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `timer_sessions`
--
ALTER TABLE `timer_sessions`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `trackers`
--
ALTER TABLE `trackers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `user_formulas`
--
ALTER TABLE `user_formulas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `user_pdfs`
--
ALTER TABLE `user_pdfs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `user_revisions`
--
ALTER TABLE `user_revisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
