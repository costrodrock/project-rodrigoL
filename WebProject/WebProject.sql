-- phpMyAdmin SQL Dump
-- version 4.6.5.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 09, 2017 at 08:05 PM
-- Server version: 10.1.21-MariaDB
-- PHP Version: 5.6.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `WebProject`
--

-- --------------------------------------------------------

--
-- Table structure for table `users`, `userimages`,`posts`,`themes`,and `admin`
--

CREATE TABLE `users` (
  `userID` INT NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `firstName` varchar(255) NOT NULL,
  `lastName` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`userID`),
  UNIQUE KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `userimages`(
	`userID` INT NOT NULL,
	`contentType` varchar(255) NOT NULL,
	`image` blob NOT NULL,
	PRIMARY KEY (`userID`),
	FOREIGN KEY (`userID`) REFERENCES `users`(`userID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `posts` (
	`postID` INT NOT NULL AUTO_INCREMENT,
	`userID` INT NOT NULL,
	`date` DATE NOT NULL,
	`title` varchar(255) NOT NULL,
	`content` varchar(1000) NOT NULL,
	`likes` INT NOT NULL,
	`liked_by` varchar(255) DEFAULT NULL,
	`disliked_by` varchar(255) DEFAULT NULL,
	`comments` INT NOT NULL,
	`themeID` INT DEFAULT NULL,
	PRIMARY KEY (`postID`),
	FOREIGN KEY (`userID`) REFERENCES `users`(`userID`),
	FOREIGN KEY (`themeID`) REFERENCES `themes`(`themeID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE themes (
	`themeID` INT NOT NULL AUTO_INCREMENT,
	`title` varchar(255) NOT NULL,
	`description` varchar(255) NOT NULL,
	`amount` INT NOT NULL,
	PRIMARY KEY (themeID)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `admin` (
  `adminID` INT NOT NULL AUTO_INCREMENT,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`adminID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE `comments` (
  `commentID` INT NOT NULL AUTO_INCREMENT,
  `postID` INT NOT NULL,
  `userID` INT NOT NULL,
  `parentID` INT DEFAULT NULL,
  `date` DATETIME NOT NULL,
  `content` TEXT NOT NULL,
  `likes` INT NOT NULL,
  `liked_by` varchar(255) DEFAULT NULL,
  `disliked_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`commentID`),
  FOREIGN KEY (`postID`) REFERENCES `posts`(`postID`),
  FOREIGN KEY (`userID`) REFERENCES `users`(`userID`),
  FOREIGN KEY (`parentID`) REFERENCES `comments`(`commentID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
--
-- Dumping data for table `users`
--



--
-- Indexes for dumped tables
--

--
-- Indexes for table `users`
--



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
