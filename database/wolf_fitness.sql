-- --------------------------------------------------------
-- Hôte:                         127.0.0.1
-- Version du serveur:           8.0.30 - MySQL Community Server - GPL
-- SE du serveur:                Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


-- Listage de la structure de la base pour wolf_fitness
CREATE DATABASE IF NOT EXISTS `wolf_fitness` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_bin */ /*!80016 DEFAULT ENCRYPTION='N' */;
USE `wolf_fitness`;

-- Listage de la structure de table wolf_fitness. comment
CREATE TABLE IF NOT EXISTS `comment` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `comment_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `created_at` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '""',
  `comment_author` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '""',
  `comment_pp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '""',
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table wolf_fitness.comment : ~0 rows (environ)

-- Listage de la structure de table wolf_fitness. exercice
CREATE TABLE IF NOT EXISTS `exercice` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '?',
  `training_id` int NOT NULL,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table wolf_fitness.exercice : ~6 rows (environ)
INSERT INTO `exercice` (`id`, `name`, `training_id`, `description`, `user_id`) VALUES
	(17, 'bench press', 29, 'the traget here will be 4 sets of 8 - 10 reps', 24),
	(18, 'incline dumbell press', 29, 'target here will be 4 sets of 8 - 10 reps', 24),
	(19, 'Chest fly', 29, 'target here will be 15 - 20 reps for 5 sets', 24),
	(20, 'Read delts with pulley crossover', 29, 'here we will be doing 4 sets of 25 reps', 24),
	(21, 'Lateral raises', 29, 'target here will be to do 4 sets of 12 - 15 reps', 24),
	(22, 'military press', 29, 'here it will be strengh so it\'s gonna be 4 sets of 4 - 6 reps', 24),
	(23, 'tets', 30, 'test', 24),
	(24, 'test', 30, 'teste', 24),
	(25, 'test brand new', 31, 'test brand new', 25),
	(26, 'test brand new', 31, 'test brand new', 25);

-- Listage de la structure de table wolf_fitness. exercise_logs
CREATE TABLE IF NOT EXISTS `exercise_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `training_exercise_id` int NOT NULL,
  `reps` int NOT NULL,
  `weight` decimal(10,2) NOT NULL,
  `logged_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table wolf_fitness.exercise_logs : ~2 rows (environ)
INSERT INTO `exercise_logs` (`id`, `training_exercise_id`, `reps`, `weight`, `logged_at`, `user_id`) VALUES
	(7, 17, 12, 32.00, '2024-07-17 08:07:58', 24),
	(8, 17, 32, 65.00, '2024-07-17 08:08:06', 24);

-- Listage de la structure de table wolf_fitness. follow
CREATE TABLE IF NOT EXISTS `follow` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `followed_user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `follower_id` (`user_id`) USING BTREE,
  UNIQUE KEY `followed_id` (`followed_user_id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table wolf_fitness.follow : ~0 rows (environ)

-- Listage de la structure de table wolf_fitness. post
CREATE TABLE IF NOT EXISTS `post` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin,
  `post_content` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '""',
  `created_at` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '""',
  `post_author` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '""',
  `media` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '""',
  `pp_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '""',
  `likes` int DEFAULT '0',
  `user_id` int NOT NULL,
  `training_id` int DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=116 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table wolf_fitness.post : ~2 rows (environ)
INSERT INTO `post` (`id`, `post_description`, `post_content`, `created_at`, `post_author`, `media`, `pp_user`, `likes`, `user_id`, `training_id`) VALUES
	(109, 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus iaculis nulla vel dictum euismod. Interdum et malesuada fames ac ante ipsum primis in faucibus. Donec a tristique ipsum. Suspendisse potenti. Aenean tincidunt ut nunc sed iaculis. Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Mauris vel consequat urna. Aenean interdum luctus nulla sit amet luctus. Nullam sem augue, malesuada eu nisi non, scelerisque pretium ipsum. Vestibulum quis pharetra ipsum, sed convallis ex. Sed arcu augue, tristique vitae ullamcorper in, consequat sodales sem. Morbi aliquam', '""', '2024-07-10 10:29:27', 'test', '../../uploads/0f4ff3e49c67c68402de926dc53421fb.png', '../../uploads/39e9b3688f095627b21cb16d47bcb9ac.webp', 2, 23, NULL),
	(114, 'this is a test for the public profiles', '""', '2024-07-27 06:39:20', 'slush', NULL, '../../uploads/68780cd9f7a366ae2e74647792ab91a1.jpeg', 1, 24, 30),
	(115, 'test brand new', '""', '2024-07-27 06:41:38', 'new', NULL, '../../uploads/99734bc469ca604fc6f9c5c6339f5fe1.png', 0, 25, 31);

-- Listage de la structure de table wolf_fitness. post_likes
CREATE TABLE IF NOT EXISTS `post_likes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `post_id` (`post_id`,`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table wolf_fitness.post_likes : ~0 rows (environ)
INSERT INTO `post_likes` (`id`, `post_id`, `user_id`) VALUES
	(44, 109, 24),
	(46, 109, 25),
	(43, 110, 23),
	(45, 114, 24);

-- Listage de la structure de table wolf_fitness. training
CREATE TABLE IF NOT EXISTS `training` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `creator` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '""',
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `nbrExercices` int NOT NULL DEFAULT '0',
  `created_at` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_id` int NOT NULL,
  `training_id` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table wolf_fitness.training : ~0 rows (environ)
INSERT INTO `training` (`id`, `name`, `creator`, `description`, `nbrExercices`, `created_at`, `user_id`, `training_id`) VALUES
	(29, 'Monday push', 'slush', 'this a push day that will focus on the chest and shoulders', 6, '2024-07-10 10:34:12', 24, 29),
	(30, 'this is a test', 'slush', 'well hello there handsome', 2, '2024-07-27 06:37:32', 24, 30),
	(31, 'test brand new', 'new', 'test brand new', 2, '2024-07-27 06:41:13', 25, 31);

-- Listage de la structure de table wolf_fitness. users
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `bio` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT 'empty bio',
  `profile_pic` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `followers` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- Listage des données de la table wolf_fitness.users : ~3 rows (environ)
INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `bio`, `profile_pic`, `followers`) VALUES
	(23, 'test', 'test@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$WXJxYmphSkRvbnVBUHQyMw$8Hh735SGywV28zQZiOHPIuUlVwhuTORCCUhso59DkD8', 'empty bio', '../../uploads/39e9b3688f095627b21cb16d47bcb9ac.webp', 0),
	(24, 'slush', 'slush@bork.com', '$argon2id$v=19$m=65536,t=4,p=1$SVFWWDBreVFxZWFPWGdNVA$tDCNnbKvhAUWYJ9lxiHZctAxe5Jy45vl5cbawTSLFCw', 'empty bio', '../../uploads/68780cd9f7a366ae2e74647792ab91a1.jpeg', 0),
	(25, 'new', 'new@gmail.com', '$argon2id$v=19$m=65536,t=4,p=1$QWZmekpMbEpLeS41YmhqRw$6qIt7/rxsNnlgdU2sCx5CBuOh0vJ6Y3KC8GUUYK9+FA', 'empty bio', '../../uploads/99734bc469ca604fc6f9c5c6339f5fe1.png', 1);

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
