-- -------------------------------------------------------------
-- TablePlus 4.6.4(414)
--
-- https://tableplus.com/
--
-- Database: becle
-- Generation Time: 2024-09-07 23:20:15.1920
-- -------------------------------------------------------------


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


CREATE TABLE `artist` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `image_id` bigint(20) DEFAULT NULL,
  `name` varchar(50) NOT NULL,
  `job` varchar(50) NOT NULL,
  `introduction` text DEFAULT NULL,
  `name_en` varchar(50) NOT NULL,
  `job_en` varchar(50) NOT NULL,
  `introduction_en` text DEFAULT NULL,
  `name_jp` varchar(50) NOT NULL,
  `job_jp` varchar(50) NOT NULL,
  `introduction_jp` text DEFAULT NULL,
  `is_posted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'main 에 게시 여부',
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `priority` int(11) NOT NULL DEFAULT 0 COMMENT 'main 에 게시 시에 순서',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_custom_file_TO_artist` (`image_id`),
  CONSTRAINT `FK_custom_file_TO_artist` FOREIGN KEY (`image_id`) REFERENCES `custom_file` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `artist_group` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `artist_id` bigint(20) NOT NULL,
  `project_id` bigint(20) NOT NULL,
  `reward_id` bigint(20) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_artist_TO_artist_group` (`artist_id`),
  KEY `FK_project_TO_artist_group` (`project_id`),
  KEY `FK_reward_TO_artist_group` (`reward_id`),
  CONSTRAINT `FK_artist_TO_artist_group` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`),
  CONSTRAINT `FK_project_TO_artist_group` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`),
  CONSTRAINT `FK_reward_TO_artist_group` FOREIGN KEY (`reward_id`) REFERENCES `reward` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=490 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `board` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `type` varchar(20) DEFAULT NULL,
  `alias` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_reply` tinyint(1) NOT NULL DEFAULT 0,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `is_editable` tinyint(1) NOT NULL DEFAULT 1,
  `is_deletable` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `code_project` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL COMMENT '자동으로 main 에 표시 추가되도록',
  `name_en` varchar(50) NOT NULL COMMENT '자동으로 main 에 표시 추가되도록',
  `name_jp` varchar(50) NOT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `code_reward_request` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL,
  `name_en` varchar(50) NOT NULL,
  `name_jp` varchar(50) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `custom_file` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `topic_id` bigint(20) DEFAULT NULL COMMENT 'if target == topic',
  `artist_id` bigint(20) DEFAULT NULL COMMENT 'if type == artist_profile',
  `type` varchar(50) NOT NULL DEFAULT 'image' COMMENT 'image|video',
  `target` varchar(50) NOT NULL DEFAULT 'topic' COMMENT 'topic|main|relation|project|user_profile|artist_profile|artist_preview',
  `path` text NOT NULL,
  `symbolic_path` text NOT NULL,
  `relative_path` text NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(50) NOT NULL,
  `time` int(11) DEFAULT 0 COMMENT 'sec',
  `width` int(11) NOT NULL DEFAULT 0,
  `height` int(11) NOT NULL DEFAULT 0,
  `poster` longblob DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `identifier` varchar(7) DEFAULT NULL,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_topic_TO_custom_file` (`topic_id`),
  KEY `FK_artist_TO_custom_file` (`artist_id`),
  CONSTRAINT `FK_artist_TO_custom_file` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`),
  CONSTRAINT `FK_topic_TO_custom_file` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=226 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `project` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code_project_id` bigint(20) NOT NULL,
  `image_id` bigint(20) DEFAULT NULL,
  `background_id` bigint(20) DEFAULT NULL,
  `mobile_background_id` bigint(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'close' COMMENT 'close|open',
  `start_date` datetime DEFAULT NULL,
  `end_date` datetime DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `content` text NOT NULL,
  `title_en` varchar(100) NOT NULL,
  `content_en` text NOT NULL,
  `title_jp` varchar(100) NOT NULL,
  `content_jp` text NOT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `is_authenticated` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'artist 할당된 비밀번호 필요 유무 (링크만으로 가능한지)',
  `is_posted` tinyint(1) NOT NULL DEFAULT 0,
  `is_posted_popular` tinyint(1) NOT NULL DEFAULT 0,
  `access_hash` varchar(10) DEFAULT NULL,
  `password` varchar(100) DEFAULT NULL COMMENT 'admin에서 관리가능',
  `priority` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_custom_file_TO_project` (`image_id`),
  KEY `FK_code_project_to_project` (`code_project_id`),
  KEY `FK_custom_file_TO_project1` (`background_id`),
  KEY `FK_custom_file_TO_project2` (`mobile_background_id`),
  CONSTRAINT `FK_code_project_to_project` FOREIGN KEY (`code_project_id`) REFERENCES `code_project` (`id`),
  CONSTRAINT `FK_custom_file_TO_project` FOREIGN KEY (`image_id`) REFERENCES `custom_file` (`id`),
  CONSTRAINT `FK_custom_file_TO_project1` FOREIGN KEY (`background_id`) REFERENCES `custom_file` (`id`),
  CONSTRAINT `FK_custom_file_TO_project2` FOREIGN KEY (`mobile_background_id`) REFERENCES `custom_file` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `purchase` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `reward_id` bigint(20) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'created' COMMENT 'created|paid|refunded|completed',
  `paid` int(11) NOT NULL DEFAULT 0,
  `refunded` int(11) NOT NULL DEFAULT 0,
  `purchaser_name` varchar(50) NOT NULL,
  `purchaser_email` varchar(50) NOT NULL,
  `pg` varchar(50) NOT NULL COMMENT 'created|paid|refunded|completed',
  `imp_uid` varchar(50) DEFAULT NULL COMMENT 'created|paid|refunded|completed',
  `merchant_uid` varchar(50) DEFAULT NULL COMMENT 'created|paid|refunded|completed',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_user_TO_purchase` (`user_id`),
  KEY `FK_reward_TO_purchase` (`reward_id`),
  CONSTRAINT `FK_reward_TO_purchase` FOREIGN KEY (`reward_id`) REFERENCES `reward` (`id`),
  CONSTRAINT `FK_user_TO_purchase` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `purchase_item` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `purchase_id` bigint(20) NOT NULL,
  `code_reward_request_id` bigint(20) NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `inquirer_name` varchar(50) NOT NULL,
  `inquirer_email` varchar(50) NOT NULL,
  `inquirer_comment` text NOT NULL,
  `memo` text DEFAULT NULL COMMENT '관리자가 요청 번역해서 저장',
  `is_mine` tinyint(1) NOT NULL DEFAULT 1,
  `is_agreed` tinyint(1) NOT NULL DEFAULT 1,
  `is_refunded` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_purchase_TO_purchase_item` (`purchase_id`),
  KEY `FK_code_reward_request_TO_purchase_item` (`code_reward_request_id`),
  CONSTRAINT `FK_code_reward_request_TO_purchase_item` FOREIGN KEY (`code_reward_request_id`) REFERENCES `code_reward_request` (`id`),
  CONSTRAINT `FK_purchase_TO_purchase_item` FOREIGN KEY (`purchase_id`) REFERENCES `purchase` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=78 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `purchase_item_reward` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `purchase_item_id` bigint(20) NOT NULL,
  `artist_id` bigint(20) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'waiting' COMMENT 'waiting|received|confirmed',
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_purchase_item_TO_purchase_item_reward` (`purchase_item_id`),
  KEY `FK_artist_TO_purchase_item_reward` (`artist_id`),
  CONSTRAINT `FK_artist_TO_purchase_item_reward` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`),
  CONSTRAINT `FK_purchase_item_TO_purchase_item_reward` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchase_item` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=60 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `question` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `question_board_id` bigint(20) NOT NULL,
  `questioner_id` bigint(20) DEFAULT NULL,
  `respondent_id` bigint(20) DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'requested' COMMENT 'requested | accepted',
  `question_comment` text NOT NULL,
  `respond_comment` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_question_board_TO_question` (`question_board_id`),
  KEY `FK_user_TO_question` (`questioner_id`),
  KEY `FK_user_TO_question1` (`respondent_id`),
  CONSTRAINT `FK_question_board_TO_question` FOREIGN KEY (`question_board_id`) REFERENCES `question_board` (`id`),
  CONSTRAINT `FK_user_TO_question` FOREIGN KEY (`questioner_id`) REFERENCES `user` (`id`),
  CONSTRAINT `FK_user_TO_question1` FOREIGN KEY (`respondent_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `question_board` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `is_deletable` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `reaction` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `artist_id` bigint(20) NOT NULL,
  `purchase_item_id` bigint(20) NOT NULL,
  `comment` text DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_user_TO_reaction` (`user_id`),
  KEY `FK_purchase_item_TO_reaction` (`purchase_item_id`),
  KEY `FK_artist_TO_reaction` (`artist_id`),
  CONSTRAINT `FK_artist_TO_reaction` FOREIGN KEY (`artist_id`) REFERENCES `artist` (`id`),
  CONSTRAINT `FK_purchase_item_TO_reaction` FOREIGN KEY (`purchase_item_id`) REFERENCES `purchase_item` (`id`),
  CONSTRAINT `FK_user_TO_reaction` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `reply` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `topic_id` bigint(20) NOT NULL,
  `reply_id` bigint(20) DEFAULT NULL,
  `content` text NOT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `depth` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_user_TO_reply` (`user_id`),
  KEY `FK_topic_TO_reply` (`topic_id`),
  KEY `FK_reply_TO_reply` (`reply_id`),
  CONSTRAINT `FK_reply_TO_reply` FOREIGN KEY (`reply_id`) REFERENCES `reply` (`id`),
  CONSTRAINT `FK_topic_TO_reply` FOREIGN KEY (`topic_id`) REFERENCES `topic` (`id`),
  CONSTRAINT `FK_user_TO_reply` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `reward` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `project_id` bigint(20) NOT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'all',
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `title_en` varchar(100) NOT NULL,
  `content_en` text DEFAULT NULL,
  `title_jp` varchar(100) NOT NULL,
  `content_jp` text NOT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `total_count` int(11) NOT NULL DEFAULT 0,
  `limited_count` int(11) NOT NULL DEFAULT 0,
  `purchased_count` int(11) NOT NULL DEFAULT 0,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `priority` int(11) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_project_TO_reward` (`project_id`),
  CONSTRAINT `FK_project_TO_reward` FOREIGN KEY (`project_id`) REFERENCES `project` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='리워드';

CREATE TABLE `reward_file` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `purchase_item_reward_id` bigint(20) DEFAULT NULL,
  `type` varchar(50) NOT NULL DEFAULT 'image' COMMENT 'image|video',
  `path` text NOT NULL,
  `symbolic_path` text NOT NULL,
  `relative_path` text NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `mime_type` varchar(50) NOT NULL,
  `time` int(11) NOT NULL DEFAULT 0,
  `width` int(11) NOT NULL DEFAULT 0,
  `height` int(11) NOT NULL DEFAULT 0,
  `poster` longblob DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_purchase_item_reward_TO_reward_file` (`purchase_item_reward_id`),
  CONSTRAINT `FK_purchase_item_reward_TO_reward_file` FOREIGN KEY (`purchase_item_reward_id`) REFERENCES `purchase_item_reward` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci COMMENT='리워드가 유독 많을것 같아 분리';

CREATE TABLE `setting` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `type` varchar(20) NOT NULL DEFAULT 'text' COMMENT 'text | long-text | number | bool',
  `name` varchar(50) NOT NULL,
  `is_editable` tinyint(1) DEFAULT 1,
  `value` text DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `UQ_code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `topic` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) NOT NULL,
  `board_id` bigint(20) NOT NULL,
  `identifier` varchar(7) DEFAULT NULL,
  `title` varchar(100) NOT NULL,
  `content` text DEFAULT NULL,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `FK_board_TO_topic` (`board_id`),
  KEY `FK_user_TO_topic` (`user_id`),
  CONSTRAINT `FK_board_TO_topic` FOREIGN KEY (`board_id`) REFERENCES `board` (`id`),
  CONSTRAINT `FK_user_TO_topic` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `user` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT 'artist 타입은 random generate',
  `type` varchar(20) NOT NULL DEFAULT 'user' COMMENT 'admin|member|user',
  `password` varchar(100) NOT NULL,
  `name` varchar(50) DEFAULT NULL,
  `email` varchar(50) NOT NULL,
  `is_notification` tinyint(1) NOT NULL DEFAULT 1,
  `is_deleted` tinyint(1) NOT NULL DEFAULT 0,
  `kakao_id` bigint(20) DEFAULT NULL,
  `naver_id` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

CREATE TABLE `verification_code` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `email` varchar(50) NOT NULL,
  `code` varchar(8) NOT NULL,
  `is_used` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;



/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
