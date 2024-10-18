-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 18, 2024 at 12:10 PM
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
-- Database: `test`
--

-- --------------------------------------------------------

--
-- Table structure for table `followers`
--

CREATE TABLE `followers` (
  `id` int(11) NOT NULL,
  `follower_id` int(11) NOT NULL,
  `followed_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `followers`
--

INSERT INTO `followers` (`id`, `follower_id`, `followed_id`) VALUES
(2, 2, 1);

-- --------------------------------------------------------

--
-- Table structure for table `friend_request`
--

CREATE TABLE `friend_request` (
  `user` int(11) NOT NULL,
  `friend` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `friend_request`
--

INSERT INTO `friend_request` (`user`, `friend`, `state`) VALUES
(3, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `link`
--

CREATE TABLE `link` (
  `id` int(11) NOT NULL,
  `uid` int(11) NOT NULL,
  `roomId` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `link`
--

INSERT INTO `link` (`id`, `uid`, `roomId`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1);

-- --------------------------------------------------------

--
-- Table structure for table `relation`
--

CREATE TABLE `relation` (
  `uid` int(11) NOT NULL,
  `friend` int(11) NOT NULL,
  `state` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `relation`
--

INSERT INTO `relation` (`uid`, `friend`, `state`) VALUES
(2, 3, 0),
(2, 1, 0),
(2, 3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `room`
--

CREATE TABLE `room` (
  `id` int(11) NOT NULL,
  `link` varchar(11) NOT NULL,
  `creator` int(11) NOT NULL,
  `name` varchar(500) NOT NULL DEFAULT 'No name',
  `thumbnail` varchar(300) NOT NULL,
  `public` int(1) NOT NULL DEFAULT 0,
  `date` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `room`
--

INSERT INTO `room` (`id`, `link`, `creator`, `name`, `thumbnail`, `public`, `date`) VALUES
(238, 'yZz1', 3, 'Ed Edd n Eddy Season 1 Episode 9 - Its Way Ed _ Laugh Ed Laugh - YouTube.MP4', '/thumbnail/386z7aay32image_.jpeg', 1, '2024-10-17 15:15:40'),
(239, 'Ya1a', 2, 'World-of-Warcraft-Wrath-of-the-Lich-King-Cinematic-Trailer - 10Convert.com.mp4', '/thumbnail/3497Y5aaW2image_.jpeg', 1, '2024-10-17 15:18:12'),
(240, '54W2', 2, 'Master-Sina-feat-Reda-Taliani-Bye-Bye - 10Youtube.com.mp4', '/thumbnail/3817A7871Zimage_.jpeg', 1, '2024-10-17 15:19:17'),
(241, 'y724', 2, '6-Testing-Player-Movement-Online-Unity-53-Simple-Multiplayer-Game - 10Youtube.com.mp4', '/thumbnail/617X2ZaX52image_.jpeg', 1, '2024-10-17 16:14:56'),
(242, 'zXZy', 2, 'Booba-4G-Clip-Officiel - 10Convert.com.mp4', '/thumbnail/7zz68yX1W7image_.jpeg', 1, '2024-10-17 16:17:31'),
(243, 'zaZ3', 2, 'Booba-4G-Clip-Officiel - 10Convert.com.mp4', '/thumbnail/5Z76yz9z7aimage_.jpeg', 1, '2024-10-17 16:18:10');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `username` varchar(60) NOT NULL DEFAULT 'guest',
  `email` varchar(60) NOT NULL,
  `avatar` varchar(200) NOT NULL DEFAULT '/avatar/default.png',
  `password` varchar(30) NOT NULL DEFAULT 'walid',
  `lastSocketId` varchar(20) NOT NULL DEFAULT 'x'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `avatar`, `password`, `lastSocketId`) VALUES
(1, 'walid', 'walid@gmail.com', '/avatar/Zy3y2W56.jpeg', 'walid', '4NogVKDRjgX69xbFAAAl'),
(2, 'lina', 'lina', '/avatar/2WZa7WXz.jpeg', 'lina', 'jJSg2n8wRPo4WLpiAAAp'),
(3, 'rolan', 'rolan', '/avatar/default.jpeg', 'rolan', 'jPQF_YM-8TDci0sQAAAz'),
(4, 'test', 'test', '/avatar/default.jpeg', 'test', ''),
(5, 'guest', 'linaa', '/avatar/defualt.png', 'linaa', 'x');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `link`
--
ALTER TABLE `link`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `room`
--
ALTER TABLE `room`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `link`
--
ALTER TABLE `link`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `room`
--
ALTER TABLE `room`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=244;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
