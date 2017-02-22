SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `account` (
  `account` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `name` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `file` (
  `name` varchar(255) NOT NULL,
  `extension` varchar(30) NOT NULL,
  `MIME2` varchar(30) NOT NULL,
  `MIME` varchar(30) NOT NULL,
  `filename` varchar(32) NOT NULL,
  `inuse` tinyint(1) NOT NULL DEFAULT '1',
  `filehash` varchar(32) NOT NULL,
  `id` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `login_session` (
  `account` varchar(20) NOT NULL,
  `cookie` varchar(32) NOT NULL,
  `time` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `plan` (
  `year` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `tag` json NOT NULL,
  `file` json NOT NULL,
  `inuse` tinyint(1) NOT NULL DEFAULT '1',
  `id` varchar(8) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `plan_type` (
  `id` int(11) NOT NULL,
  `name` varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `plan_type` (`id`, `name`) VALUES
(1, '教案'),
(2, '題目');


ALTER TABLE `account`
  ADD UNIQUE KEY `account` (`account`);

ALTER TABLE `file`
  ADD UNIQUE KEY `id` (`id`);

ALTER TABLE `login_session`
  ADD UNIQUE KEY `time` (`time`);

ALTER TABLE `plan`
  ADD UNIQUE KEY `id` (`id`) USING BTREE;

ALTER TABLE `plan_type`
  ADD UNIQUE KEY `id` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
