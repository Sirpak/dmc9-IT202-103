-- THIS FILE IS NOT NEEDED
--THE TABLES WHERE CREATED MANUALLY DURING TESTING/PRODUCTION
--THIS IS JUST IN THE AUTH FOLDER AS AN EXAMPLE AND DOES NOT NEED TO BE IMPORTED
--phpMyAdmin SQL Dump
-- version 4.5.1
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: May 09, 2018 at 01:00 PM
-- Server version: 10.1.19-MariaDB
-- PHP Version: 7.2.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `register`
--

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expire_time` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trash`
--

CREATE TABLE `trash` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `user_id` int(6) NOT NULL,
  `deleted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

-- CREATE TABLE `users` (
--   `id` int(6) UNSIGNED NOT NULL AUTO_INCREMENT,
--   `username` varchar(30) NOT NULL,
--   `password` varchar(255) NOT NULL,
--   `email` varchar(100) NOT NULL,
--   `activated` enum('0','1') NOT NULL DEFAULT '0',
--   `avatar` varchar(255) DEFAULT 'uploads/default.jpg',
--   `join_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
--   PRIMARY KEY (`id`),
--   UNIQUE KEY `email` (`email`),
--   UNIQUE KEY `username` (`username`)
-- ) ENGINE=InnoDB DEFAULT CHARSET=latin1;
