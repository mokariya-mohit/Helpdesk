-- Helpdesk Database Dump
-- Generated on 2026-08-31 12:35:53

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `api_key` text COLLATE utf8mb4_unicode_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `api_key`, `created`, `modified`) VALUES ('2', 'Mohit Mokariya', 'admin@gmail.com', '$2y$10$VqTT4vaJknS8bnXmY51sP.mGKdT/aZPrnjjRzpUzY7aaE6hnbUSSW', 'ewNvVwSiitGMyzblgsGXMzo6TFNIbXFzNW42dVBQV0xnTWtlaGdmQU5UamVhYjdkakRQeFdKUDRPUFlJNTFsTkJqMHVSV2czb2xBamZiRW5saXU0Z1RzQThreGFacTB3enRJZDlYS3c9PQ==', '2026-08-31 09:47:18', '2026-08-31 17:00:06');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `api_key`, `created`, `modified`) VALUES ('3', 'Pooja Patel', 'pooja@gmail.com', '$2y$10$BclSM6BNWgizbkol5ZsQ8uy..LCXDsuLDgtVuo/pCNclgkg3yaexS', NULL, '2026-08-31 15:36:29', '2026-08-31 15:36:29');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `api_key`, `created`, `modified`) VALUES ('4', 'user1', 'user1@gmail.com', '$2y$10$UfjDUEJpPbB7f05gP2PUcumbJXALSlhcVE.ZEUM4t2d7OlT4ECCge', NULL, '2026-08-31 15:44:17', '2026-08-31 15:44:17');

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_project_name` (`user_id`,`name`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` (`id`, `user_id`, `name`, `is_default`, `created`, `modified`) VALUES ('12', '2', '8 Bloqs', '0', '2026-08-31 17:56:25', '2026-08-31 17:56:25');
INSERT INTO `projects` (`id`, `user_id`, `name`, `is_default`, `created`, `modified`) VALUES ('13', '2', 'Bubbles', '0', '2026-08-31 17:59:50', '2026-08-31 17:59:50');

DROP TABLE IF EXISTS `work_logs`;
CREATE TABLE `work_logs` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `client_id` int DEFAULT NULL,
  `log_date` date NOT NULL,
  `content` longtext COLLATE utf8mb4_unicode_ci,
  `task_count` int DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_date_project_user` (`log_date`,`project_id`,`user_id`),
  UNIQUE KEY `user_date` (`user_id`,`log_date`),
  KEY `fk_worklogs_project` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `fk_worklogs_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `work_logs` (`id`, `user_id`, `project_id`, `client_id`, `log_date`, `content`, `task_count`, `created`, `modified`) VALUES ('7', '2', '12', '2', '2026-08-31', '31-08-2026\n-------------------\nBackend:\n- Enhanced the existing dashboard report with a dedicated section for Traffic Analytics, featuring placeholders for dynamic data.\n- Introduced a new Traffic Analytics tab in the SEO report navigation.\n- Implemented skeleton loading UI for the Traffic Analytics report, including KPI cards, charts, historical performance tables, and dynamic AI summary generation.\n- Created a new template file for the Traffic Analytics report skeleton to ensure a consistent loading experience.\n- Added a new \'traffic_analytics\' column to the \'seo_dashboard\' table and a \'historical_rank_overview_response\' column to the \'seo_reports\' table.\n', '5', '2026-08-31 18:00:09', '2026-08-31 18:04:33');

DROP TABLE IF EXISTS `daily_updates`;
CREATE TABLE `daily_updates` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `project_id` int DEFAULT NULL,
  `update_date` date NOT NULL,
  `client_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT 'Hitesh sir',
  `project_name` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tl_name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `done_tasks` text COLLATE utf8mb4_unicode_ci,
  `progress_tasks` text COLLATE utf8mb4_unicode_ci,
  `remaining_tasks` text COLLATE utf8mb4_unicode_ci,
  `queries` text COLLATE utf8mb4_unicode_ci,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_update_date_project_user` (`update_date`,`project_id`,`user_id`),
  KEY `fk_dailyupdates_project` (`project_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_dailyupdates_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `daily_updates` (`id`, `user_id`, `project_id`, `update_date`, `client_name`, `project_name`, `tl_name`, `done_tasks`, `progress_tasks`, `remaining_tasks`, `queries`, `notes`, `created`, `modified`) VALUES ('5', '2', '12', '2026-08-31', 'Suresh Sir', '8 Bloqs', 'Suresh Sir.', 'Backend:\n- Enhanced the existing dashboard report with a dedicated section for Traffic Analytics, featuring placeholders for dynamic data.\n- Introduced a new Traffic Analytics tab in the SEO report navigation.\n- Implemented skeleton loading UI for the Traffic Analytics report, including KPI cards, charts, historical performance tables, and dynamic AI summary generation.\n- Created a new template file for the Traffic Analytics report skeleton to ensure a consistent loading experience.\n- Added a new \'traffic_analytics\' column to the \'seo_dashboard\' table and a \'historical_rank_overview_response\' column to the \'seo_reports\' table.', '', '', '', '', '2026-08-31 17:58:34', '2026-08-31 18:00:25');
INSERT INTO `daily_updates` (`id`, `user_id`, `project_id`, `update_date`, `client_name`, `project_name`, `tl_name`, `done_tasks`, `progress_tasks`, `remaining_tasks`, `queries`, `notes`, `created`, `modified`) VALUES ('6', '2', '13', '2026-08-31', 'Hitesh Sir', 'Bubbles', 'Hitesh Sir.', '', '', '', '', '', '2026-08-31 18:00:15', '2026-08-31 18:00:15');

SET FOREIGN_KEY_CHECKS=1;
