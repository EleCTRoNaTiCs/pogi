-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 03, 2025 at 11:07 AM
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
-- Database: `beyond_session`
--

-- --------------------------------------------------------

--
-- Table structure for table `quiz_questions`
--

CREATE TABLE `quiz_questions` (
  `id` int(11) NOT NULL,
  `question` text NOT NULL,
  `option_a` varchar(255) NOT NULL,
  `option_b` varchar(255) NOT NULL,
  `option_c` varchar(255) NOT NULL,
  `option_d` varchar(255) NOT NULL,
  `correct_option` char(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `quiz_questions`
--

INSERT INTO `quiz_questions` (`id`, `question`, `option_a`, `option_b`, `option_c`, `option_d`, `correct_option`) VALUES
(1, 'How can virtual reality help guidance counselors?', 'Immersive relaxation', 'Replacing in-person therapy', 'Adding stress', 'Eliminating emotions', 'A'),
(2, 'What is a key benefit of VR therapy?', 'Increased anxiety', 'Safe exposure therapy', 'Less client interaction', 'Removing human empathy', 'B'),
(3, 'Which VR feature enhances mental wellness?', '3D graphics', 'Controlled environments', 'High cost', 'Fast internet', 'B'),
(4, 'What type of therapy benefits from VR?', 'Exposure therapy', 'Medication-only therapy', 'Surgery', 'None', 'A'),
(5, 'Which is a mental wellness challenge for counselors?', 'Stress management', 'VR addiction', 'Physical injury', 'No challenges', 'A'),
(6, 'What does VR offer to mental health professionals?', 'Escape from clients', 'Enhanced engagement', 'More paperwork', 'Nothing new', 'B'),
(7, 'How can VR sessions track progress?', 'Data analytics', 'No tracking', 'Guesswork', 'Random assessments', 'A'),
(8, 'What is a potential drawback of VR?', 'Motion sickness', 'Superpowers', 'Curing all illnesses', 'None', 'A'),
(9, 'How can VR help in stress reduction?', 'Guided meditation', 'Increased workload', 'Screen addiction', 'No impact', 'A'),
(10, 'What is the primary goal of VR in counseling?', 'Support mental wellness', 'Replace human counselors', 'Increase stress', 'None', 'A');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'Jelmar', '$2y$10$1gXoC2LfI4000rvZtDHtkeFaQfO7UkCDAKr56N2VNCUhLHZ809c5O', '2025-01-30 11:27:06');

-- --------------------------------------------------------

--
-- Table structure for table `user_progress`
--

CREATE TABLE `user_progress` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `attempts` int(11) DEFAULT 0,
  `last_attempt` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_progress`
--

INSERT INTO `user_progress` (`id`, `user_id`, `score`, `attempts`, `last_attempt`) VALUES
(5, 1, 2, 1, '2025-01-30 11:34:49'),
(6, 1, 2, 1, '2025-01-30 11:37:17'),
(7, 1, 3, 1, '2025-01-30 11:39:40'),
(8, 1, 4, 1, '2025-01-30 11:47:46'),
(9, 1, 5, 1, '2025-01-30 12:17:10'),
(10, 1, 1, 1, '2025-01-30 12:17:33'),
(11, 1, 1, 1, '2025-01-30 13:21:51'),
(12, 1, 1, 1, '2025-01-30 14:34:29'),
(13, 1, 0, 1, '2025-01-30 14:50:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `quiz_questions`
--
ALTER TABLE `quiz_questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_progress`
--
ALTER TABLE `user_progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_progress`
--
ALTER TABLE `user_progress`
  ADD CONSTRAINT `user_progress_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
