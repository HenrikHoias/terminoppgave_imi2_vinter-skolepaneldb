-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 04, 2023 at 09:53 AM
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
-- Database: `skolepanel`
--

-- --------------------------------------------------------

--
-- Table structure for table `annotations`
--

CREATE TABLE `annotations` (
  `annotation_id` int(50) NOT NULL,
  `student_id` int(50) NOT NULL,
  `subject_type` varchar(50) NOT NULL,
  `annotation_text` varchar(200) NOT NULL,
  `annotation_date` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `annotations`
--

INSERT INTO `annotations` (`annotation_id`, `student_id`, `subject_type`, `annotation_text`, `annotation_date`) VALUES
(4, 19, 'Engelsk', 'DDD', '2023-11-02'),
(11, 14, 'Engelsk', 'Test', '03.11.2023'),
(203, 10, 'Engelsk', 'Kom forsinket', '03.11.2023'),
(204, 10, 'Engelsk', 'Forstyrret medelever', '05.06.2023'),
(205, 10, 'Norsk', 'Kasta snøball etter tredje advarsel', '13.11.2023'),
(206, 10, 'Mattematikk', 'Hentet ikke lærerbok etter å ha fått beskjed gjentatte ganger', '16.10.2023'),
(207, 12, 'Mattematikk', 'DD', '2023-11-03'),
(208, 12, 'Norsk', 'k', '2023-11-15'),
(209, 12, 'Mattematikk', 'd', '2023-11-03'),
(212, 6, 'Engelsk', 'ghh', '2023-11-03'),
(213, 21, 'Norsk', 'test', '2023-11-15'),
(214, 27, 'Norsk', 'Slo medelev.', '10.11.2023'),
(218, 1, 'Norsk', 'Kom forsinket', '2023-11-30'),
(219, 2, 'Engelsk', 'DDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD', '2023-11-30'),
(220, 3, 'Engelsk', 'f', '2023-11-30');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(50) NOT NULL,
  `class_name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `class_name`) VALUES
(1, '10A'),
(2, '10B'),
(3, '10C'),
(4, '10D'),
(5, '10E');

-- --------------------------------------------------------

--
-- Table structure for table `feedback`
--

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL,
  `feedback` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `feedback`
--

INSERT INTO `feedback` (`id`, `feedback`) VALUES
(1, '0'),
(5, 'fffefef'),
(6, 'test345');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `class` varchar(50) NOT NULL,
  `absence` varchar(50) NOT NULL,
  `comment` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `first_name`, `last_name`, `class`, `absence`, `comment`) VALUES
(1, 'Geir', 'Hovde', '10C', 'Udokumentert', 'Ut eleifend et ex eu commodo. Cras scelerisque porttitor nibh maximus rutrum. Vestibulum rhoncus nunc porttitor pretium vestibulum. In consequat sem vitae ante molestie scelerisque.'),
(2, 'Linnea', 'Birkeland', '10C', 'Til stede', ''),
(3, 'Mathilde', 'Pedersen', '10B', 'Ingen', ''),
(4, 'Tone', 'Brekke', '10D', 'Ingen', ''),
(5, 'Kristine', 'Lunde', '10D', 'Ingen', ''),
(6, 'Hanna', 'Hovde', '10B', 'Til stede', ''),
(8, 'Vilde', 'Hovland', '10D', 'Ingen', ''),
(9, 'Tiril', 'Karlsen', '10C', 'Udokumentert', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ipsum est, fermentum ut fermentum vitae, semper vitae justo. Maecenas vulputate tellus in mauris volutpat, in dictum odio vestibulum. Cras'),
(10, 'Magnus', 'Aas', '10C', 'Til stede', ''),
(11, 'Knut', 'Pettersen', '10D', 'Ingen', ''),
(12, 'Knut', 'Hansen', '10B', 'Ingen', ''),
(13, 'Bjørn', 'Aas', '10B', 'Dokumentert', 'Quisque tortor velit, placerat non sapien in, gravida consequat nisl. Curabitur vel porttitor velit. Integer ut varius lacus. '),
(14, 'Nikolai', 'Myhre', '10A', 'Ingen', ''),
(16, 'Daniel', 'Bjerke', '10D', 'Ingen', ''),
(18, 'Ida', 'Hovland', '10B', 'Udokumentert', 'Quisque tortor velit, placerat non sapien in, gravida consequat nisl. Curabitur vel porttitor velit. Integer ut varius lacus. '),
(19, 'Amalie', 'Bakke', '10C', 'Til stede', ''),
(21, 'Martin', 'Halvorsen', '10A', 'Ingen', ''),
(22, 'Sofie', 'Betina', '10A', 'Ingen', ''),
(23, 'Adrian', 'Petterson', '', 'Ingen', ''),
(24, '', '', '', 'Ingen', ''),
(25, '', '', '', 'Ingen', ''),
(27, 'Adrian', 'Paulus', '10D', 'Ingen', ''),
(28, 'Nikolas', 'Bjørklund', '2D', 'Ingen', ''),
(30, '', '', '', 'Ingen', ''),
(31, '', '', '', 'Ingen', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `mail` varchar(50) NOT NULL,
  `password` varchar(200) NOT NULL,
  `access` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `mail`, `password`, `access`) VALUES
(1, 'rektor', 'rrektorr@osloskolen.no', '2acfa5bc90c2a1077a408cee690d778e61dd8394d212c85e2c57474422f24d1d', '*'),
(2, '10alærer', '10alærer@osloskolen.no', '8c70002d1aa500608d6495c0786859e9426a3d6226d9f5853c1e961fbfd9e69c', '1'),
(3, '10bclærer', '10bclærer@osloskolen.no', 'c62e6cb6c06727636296dbb97ef73f149a3fe837557221726add6210479e9e84', '2-3'),
(4, '10dlærer', '10dlærer@osloskolen.no', '74a9b50d3369e15627772491cf7eace35cc0c857769a7c9f651db653ac14a78c', '4');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `annotations`
--
ALTER TABLE `annotations`
  ADD PRIMARY KEY (`annotation_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `feedback`
--
ALTER TABLE `feedback`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `annotations`
--
ALTER TABLE `annotations`
  MODIFY `annotation_id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=221;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `feedback`
--
ALTER TABLE `feedback`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
