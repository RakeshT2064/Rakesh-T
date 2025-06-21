-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 09, 2025 at 06:37 PM
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
-- Database: `moviebooking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `showtime_id` int(11) DEFAULT NULL,
  `seats_booked` int(11) DEFAULT NULL,
  `seat_numbers` varchar(255) DEFAULT NULL,
  `total_amount` decimal(10,2) DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `bookingdate` datetime DEFAULT current_timestamp(),
  `transaction_id` varchar(50) DEFAULT NULL,
  `payment_status` varchar(20) DEFAULT 'pending',
  `num_tickets` int(11) NOT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `status` enum('active','cancelled') DEFAULT 'active',
  `cancelled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`booking_id`, `user_id`, `showtime_id`, `seats_booked`, `seat_numbers`, `total_amount`, `payment_method`, `booking_date`, `bookingdate`, `transaction_id`, `payment_status`, `num_tickets`, `movie_id`, `status`, `cancelled_at`) VALUES
(112, 5, 37, 2, '7,8', 200.00, 'netbanking', '2025-05-19 16:01:16', '2025-05-19 21:31:16', 'NB682b55ceee99d', 'completed', 2, NULL, 'active', NULL),
(113, 5, 39, 2, '20,21', 300.00, 'netbanking', '2025-05-19 16:01:32', '2025-05-19 21:31:32', 'NB682b55de6f27f', 'completed', 2, NULL, 'active', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `booking_seats`
--

CREATE TABLE `booking_seats` (
  `booking_seat_id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `seat_number` varchar(10) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `contact_messages`
--

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(200) NOT NULL,
  `message` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('unread','read') DEFAULT 'unread'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `contact_messages`
--

INSERT INTO `contact_messages` (`id`, `name`, `email`, `subject`, `message`, `created_at`, `status`) VALUES
(4, 'rakesh', 'rakeshrake2064@gmail.com', 'booking issue', 'booking issue', '2025-06-09 15:42:25', 'unread');

-- --------------------------------------------------------

--
-- Table structure for table `genres`
--

CREATE TABLE `genres` (
  `genre_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `genres`
--

INSERT INTO `genres` (`genre_id`, `name`) VALUES
(1, 'Action'),
(2, 'Adventure'),
(3, 'Animation'),
(4, 'Comedy'),
(5, 'Crime'),
(6, 'Documentary'),
(7, 'Drama'),
(8, 'Family'),
(9, 'Fantasy'),
(10, 'Horror'),
(11, 'Mystery'),
(12, 'Romance'),
(13, 'Sci-Fi'),
(14, 'Thriller'),
(15, 'War'),
(16, 'Western'),
(17, 'Musical'),
(18, 'Biography'),
(19, 'Sport'),
(20, 'Superhero');

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `language_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `languages`
--

INSERT INTO `languages` (`language_id`, `name`) VALUES
(1, 'English'),
(2, 'Hindi'),
(6, 'Kannada'),
(5, 'Malayalam'),
(3, 'Tamil'),
(4, 'Telugu');

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `movie_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `movie_type` enum('2D','3D') DEFAULT '2D',
  `duration` int(11) DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `poster_url` varchar(255) DEFAULT NULL,
  `status` enum('now_showing','coming_soon') DEFAULT 'coming_soon',
  `poster` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `language_id` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`movie_id`, `title`, `description`, `language`, `movie_type`, `duration`, `release_date`, `poster_url`, `status`, `poster`, `created_at`, `language_id`) VALUES
(40, 'Toxic: A Fairy Tale for Grown-ups', 'About the movie\r\n\r\nDirected by Geetu Mohandas, Toxic: A Fairy Tale For Grown-ups features Yash in the lead.', NULL, '2D', 0, '2026-03-19', 'uploads/posters/68163bb8d33c8.png', 'coming_soon', '', '2025-05-03 15:52:24', 6),
(46, 'Kantara A Legend: Chapter 1', 'Exploring the origins of Kaadubettu Shiva during the Kadamba dynasty era, it delves into the untamed wilderness and forgotten lore surrounding his past.', NULL, '2D', 0, '2025-10-02', 'uploads/posters/684702214ccd6.png', 'coming_soon', NULL, '2025-06-09 15:47:45', 6),
(47, 'War 2', 'jr ntr', NULL, '2D', 0, '2025-08-14', 'uploads/posters/6847030d3bd9d.png', 'coming_soon', NULL, '2025-06-09 15:51:41', 4),
(48, 'Hari Hara Veera Mallu', 'The first Indian to orchestrate a revolt against the Mughal empire. The early life of Veera Mallu and the mission he chooses to raise revolution against the ghastly actions of the army generals in the Mughal empire.', NULL, '2D', 0, '2025-06-12', 'uploads/posters/684703ed81a7b.png', 'coming_soon', NULL, '2025-06-09 15:55:25', 4),
(49, 'The Raja Saab', 'A young heir embraces both his royal heritage and rebellious spirit as he rises to power, establishing unprecedented rules during his reign as Raja Saab.', NULL, '2D', 0, '2025-12-05', 'uploads/posters/6847045a6392c.png', 'coming_soon', NULL, '2025-06-09 15:57:14', 4),
(50, 'Mission: Impossible - The Final Reckoning', 'Our lives are the sum of our choices. Tom Cruise is Ethan Hunt in Mission: Impossible - The Final Reckoning.', NULL, '2D', 0, '2025-05-17', 'uploads/posters/684704e1d7f8d.png', 'now_showing', NULL, '2025-06-09 15:59:29', 1),
(51, 'College Kalavida', 'Two students share an unspoken love, waiting for the perfect moment to confess. Destiny separates them before they can. Years later, their paths cross again. Will they finally speak their hearts, or let the moment slip away once more?', NULL, '2D', 0, '2025-05-29', 'uploads/posters/6847055ade164.png', 'now_showing', NULL, '2025-06-09 16:01:30', 6),
(52, 'Bhairavam', 'The relationship between three childhood friends begins to crumble as their loyalty is tested.', NULL, '2D', 0, '0000-00-00', 'uploads/posters/684705d84cb59.png', 'now_showing', NULL, '2025-06-09 16:03:36', 4),
(53, 'Maadeva', 'A stoic villager named Maadeva falls for Parvathi, discovering emotions and human bonds. As he opens his heart to life and relationships, conflicts with rival families test his newfound understanding.', NULL, '2D', 0, '2025-05-30', 'uploads/posters/68470652b9e03.png', 'now_showing', NULL, '2025-06-09 16:05:38', 6),
(54, 'Neethi', 'A women (Kushee Ravi) gets into a relationship that ends up in a domestic violence. Eager to die, she finds ways to kill herself. However, an opportunity presents itself in the form of a thief (Sampath Maithreya).', NULL, '2D', 0, '2025-06-01', 'uploads/posters/684706dd13c5b.png', 'now_showing', NULL, '2025-06-09 16:07:57', 6),
(55, 'Housefull 5 A', 'In this murder mystery comedy, several imposters claiming to be the son of a recently deceased billionaire compete for his fortune aboard a luxury cruise ship.', NULL, '2D', 0, '2025-06-06', 'uploads/posters/6847075761a7a.png', 'now_showing', NULL, '2025-06-09 16:09:59', 2),
(56, 'Sri Sri Sri Raja Vaaru', 'Two political party workers from Atreyapuram face tragedy when one\'s pregnant wife goes into labor during floods. Unable to reach hospital, they get a midwife, but the baby is stillborn and the mother\'s health declines.', NULL, '2D', 0, '2025-06-06', 'uploads/posters/684707ec385ba.png', 'now_showing', NULL, '2025-06-09 16:12:28', 4),
(57, 'Lakshmi Narasimha', 'A story about good-gone-bad cop.', NULL, '2D', 135, '2004-01-14', 'uploads/posters/684708a47a9c2.png', 'now_showing', NULL, '2025-06-09 16:15:32', 4);

-- --------------------------------------------------------

--
-- Table structure for table `movie_genres`
--

CREATE TABLE `movie_genres` (
  `movie_id` int(11) NOT NULL,
  `genre_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movie_genres`
--

INSERT INTO `movie_genres` (`movie_id`, `genre_id`) VALUES
(33, 12),
(33, 22),
(33, 23),
(34, 5),
(34, 11),
(34, 27),
(37, 7),
(37, 8),
(37, 27),
(38, 1),
(38, 27),
(39, 1),
(39, 7),
(39, 27),
(40, 1),
(40, 27),
(44, 1),
(45, 1),
(45, 4),
(46, 1),
(46, 7),
(47, 1),
(47, 2),
(47, 5),
(47, 7),
(48, 1),
(48, 9),
(49, 1),
(49, 2),
(49, 5),
(49, 14),
(50, 1),
(50, 5),
(51, 4),
(51, 7),
(52, 1),
(52, 4),
(52, 7),
(53, 1),
(53, 4),
(53, 7),
(54, 14),
(55, 1),
(55, 4),
(55, 11),
(55, 14),
(56, 1),
(56, 4),
(56, 5),
(56, 7),
(57, 1),
(57, 7);

-- --------------------------------------------------------

--
-- Table structure for table `showtimes`
--

CREATE TABLE `showtimes` (
  `showtime_id` int(11) NOT NULL,
  `movie_id` int(11) NOT NULL,
  `theater_id` int(11) NOT NULL,
  `show_date` date NOT NULL,
  `show_time` time NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `showtimes`
--

INSERT INTO `showtimes` (`showtime_id`, `movie_id`, `theater_id`, `show_date`, `show_time`, `price`, `created_at`) VALUES
(37, 34, 5, '2025-05-19', '22:00:00', 100.00, '2025-05-19 15:29:32'),
(38, 33, 4, '2025-05-20', '02:00:00', 100.00, '2025-05-19 15:30:04'),
(39, 37, 1, '2025-05-20', '14:00:00', 150.00, '2025-05-19 15:30:21'),
(40, 38, 7, '2025-05-20', '10:00:00', 150.00, '2025-05-19 15:30:35'),
(41, 34, 5, '2025-06-09', '21:11:00', 100.00, '2025-06-09 15:39:24');

-- --------------------------------------------------------

--
-- Table structure for table `theaters`
--

CREATE TABLE `theaters` (
  `theater_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `city` varchar(100) DEFAULT NULL,
  `seats_capacity` int(11) NOT NULL,
  `seats_per_row` int(11) NOT NULL DEFAULT 12,
  `number_of_rows` int(11) NOT NULL DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theaters`
--

INSERT INTO `theaters` (`theater_id`, `name`, `city`, `seats_capacity`, `seats_per_row`, `number_of_rows`) VALUES
(1, 'Raghavendra Theatre ', 'Ballari', 154, 12, 10),
(2, 'Radhika Theater ', NULL, 154, 12, 10),
(3, 'Shiva ', NULL, 154, 12, 10),
(4, 'Ganga ', NULL, 154, 12, 10),
(5, 'Nataraj Theatre Complex', NULL, 154, 12, 10),
(6, 'uma', NULL, 54, 12, 10),
(7, 'SLN Screen 1', NULL, 154, 12, 10),
(8, 'SLN Screen 2', NULL, 154, 12, 10),
(9, 'SLN Screen 2', NULL, 154, 12, 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `reset_token` varchar(64) DEFAULT NULL,
  `reset_expiry` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `email`, `phone`, `password`, `created_at`, `updated_at`, `reset_token`, `reset_expiry`) VALUES
(5, 'rakesh', 'rakeshrake2064@gmail.com', '8431504059', '$2y$10$.JwRsB9mgRlWlPS6BE8T9uDnD2H1pqqWbUXqsQ2iQ4cykN5KnPVIa', '2025-05-03 13:47:56', '2025-05-03 13:47:56', NULL, NULL),
(6, 'rakeshrocky', 'rakeshrakhi0046@gmail.com', '9876543210', '$2y$10$SSAGdovt8oHs54it9S3nJOwZ2u0v46RpadSLHawEslkITjzYKhsAK', '2025-05-05 14:29:44', '2025-05-05 14:29:44', NULL, NULL),
(7, 'rakesh T', 'rakeshrakee20644@gmail.com', '9876543210', '$2y$10$xsjGM0Lv9GZLwhRfN4p/1uX4XgQRRL/mRY5SUiSMfUtlJZC4sM38S', '2025-05-15 13:29:42', '2025-05-15 13:29:42', NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `showtime_id` (`showtime_id`),
  ADD KEY `movie_id` (`movie_id`);

--
-- Indexes for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD PRIMARY KEY (`booking_seat_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `contact_messages`
--
ALTER TABLE `contact_messages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `genres`
--
ALTER TABLE `genres`
  ADD PRIMARY KEY (`genre_id`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`language_id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`movie_id`),
  ADD KEY `movies_ibfk_1` (`language_id`);

--
-- Indexes for table `movie_genres`
--
ALTER TABLE `movie_genres`
  ADD PRIMARY KEY (`movie_id`,`genre_id`),
  ADD KEY `genre_id` (`genre_id`);

--
-- Indexes for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`showtime_id`),
  ADD UNIQUE KEY `unique_showtime` (`movie_id`,`theater_id`,`show_date`,`show_time`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `theater_id` (`theater_id`);

--
-- Indexes for table `theaters`
--
ALTER TABLE `theaters`
  ADD PRIMARY KEY (`theater_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `booking_seats`
--
ALTER TABLE `booking_seats`
  MODIFY `booking_seat_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `contact_messages`
--
ALTER TABLE `contact_messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `genres`
--
ALTER TABLE `genres`
  MODIFY `genre_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `language_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `movie_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `showtime_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `theaters`
--
ALTER TABLE `theaters`
  MODIFY `theater_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`showtime_id`),
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE;

--
-- Constraints for table `booking_seats`
--
ALTER TABLE `booking_seats`
  ADD CONSTRAINT `booking_seats_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`booking_id`);

--
-- Constraints for table `movies`
--
ALTER TABLE `movies`
  ADD CONSTRAINT `movies_ibfk_1` FOREIGN KEY (`language_id`) REFERENCES `languages` (`language_id`);

--
-- Constraints for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `showtimes_ibfk_2` FOREIGN KEY (`theater_id`) REFERENCES `theaters` (`theater_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
