-- Helpdesk Database Dump
-- Generated on 2026-09-11 15:14:41

SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `picture` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password_set` tinyint(1) DEFAULT '1',
  `smtp_password` text COLLATE utf8mb4_unicode_ci,
  `api_key` text COLLATE utf8mb4_unicode_ci,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `google_id`, `picture`, `password_set`, `smtp_password`, `api_key`, `created`, `modified`) VALUES ('1', 'Admin User', 'admin@gmail.com', '$2y$10$wInj6caAmUSwTPdNv4LEu..XmAQCALBzdKZvzz7dWBC93l/Uoiw3e', NULL, NULL, '1', NULL, NULL, '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `users` (`id`, `name`, `email`, `password`, `google_id`, `picture`, `password_set`, `smtp_password`, `api_key`, `created`, `modified`) VALUES ('2', 'Test User', 'user@gmail.com', '$2y$10$wInj6caAmUSwTPdNv4LEu..XmAQCALBzdKZvzz7dWBC93l/Uoiw3e', NULL, NULL, '1', NULL, NULL, '2026-09-11 20:43:57', '2026-09-11 20:43:57');

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
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_projects_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `projects` (`id`, `user_id`, `name`, `is_default`, `created`, `modified`) VALUES ('1', '1', '8 Bloqs', '1', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `projects` (`id`, `user_id`, `name`, `is_default`, `created`, `modified`) VALUES ('2', '1', 'Client Portal', '0', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `projects` (`id`, `user_id`, `name`, `is_default`, `created`, `modified`) VALUES ('3', '1', 'SEO Dashboard', '0', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `projects` (`id`, `user_id`, `name`, `is_default`, `created`, `modified`) VALUES ('4', '2', 'Mobile App', '1', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `projects` (`id`, `user_id`, `name`, `is_default`, `created`, `modified`) VALUES ('5', '2', 'Notification API', '0', '2026-09-11 20:43:57', '2026-09-11 20:43:57');

DROP TABLE IF EXISTS `clients`;
CREATE TABLE `clients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_default` tinyint(1) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `fk_clients_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `clients` (`id`, `user_id`, `name`, `email`, `is_default`, `created`, `modified`) VALUES ('1', '1', 'Hitesh Sir', 'hitesh@8bloqs.com', '1', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `clients` (`id`, `user_id`, `name`, `email`, `is_default`, `created`, `modified`) VALUES ('2', '1', 'Suresh Sir', 'suresh@8bloqs.com', '0', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `clients` (`id`, `user_id`, `name`, `email`, `is_default`, `created`, `modified`) VALUES ('3', '1', 'John Doe', 'john@example.com', '0', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `clients` (`id`, `user_id`, `name`, `email`, `is_default`, `created`, `modified`) VALUES ('4', '2', 'Apex Solutions', 'contact@apexsolutions.com', '1', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `clients` (`id`, `user_id`, `name`, `email`, `is_default`, `created`, `modified`) VALUES ('5', '2', 'Acme Corp', 'billing@acme.com', '0', '2026-09-11 20:43:57', '2026-09-11 20:43:57');

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
  KEY `fk_worklogs_project` (`project_id`),
  KEY `user_id` (`user_id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `fk_worklogs_client` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_worklogs_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_worklogs_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `work_logs` (`id`, `user_id`, `project_id`, `client_id`, `log_date`, `content`, `task_count`, `created`, `modified`) VALUES ('1', '1', '1', '1', '2026-09-11', '11-09-2026\n-------------------\nBackend & System Setup:\n- Successfully initialized the Helpdesk MySQL database schema.\n- Configured user authentication, session persistence, and remember-me tokens.\n- Verified project and client selector integrations.\n- Configured automated daily work notes formatting.\n- Validated task count and save functionality across all modules.', '5', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `work_logs` (`id`, `user_id`, `project_id`, `client_id`, `log_date`, `content`, `task_count`, `created`, `modified`) VALUES ('2', '1', '2', '2', '2026-09-10', '10-09-2026\n-------------------\nArchitecture & Optimizations:\n- Implemented responsive cards for task summaries.\n- Handled database connection exceptions and fallback states.\n- Added client dropdown auto-population based on selected work date.\n- Optimized query execution for daily activity logs.', '4', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `work_logs` (`id`, `user_id`, `project_id`, `client_id`, `log_date`, `content`, `task_count`, `created`, `modified`) VALUES ('3', '1', '3', '1', '2026-09-09', '09-09-2026\n-------------------\nSEO & Reporting Module:\n- Added skeleton loading indicators for performance charts.\n- Configured traffic analytics tab and data endpoints.\n- Refactored email notification service for daily wrap-ups.', '3', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `work_logs` (`id`, `user_id`, `project_id`, `client_id`, `log_date`, `content`, `task_count`, `created`, `modified`) VALUES ('4', '2', '4', '4', '2026-09-11', '11-09-2026\n-------------------\nMobile Frontend:\n- Built responsive layout and navigation drawer for mobile screens.\n- Integrated API authentication and session token handling.\n- Designed ticket detail and comment widgets.', '3', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `work_logs` (`id`, `user_id`, `project_id`, `client_id`, `log_date`, `content`, `task_count`, `created`, `modified`) VALUES ('5', '2', '5', '5', '2026-09-10', '10-09-2026\n-------------------\nNotification Microservice:\n- Setup webhook handler for incoming ticket status changes.\n- Added email template parser for alert triggers.', '2', '2026-09-11 20:43:57', '2026-09-11 20:43:57');

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
  CONSTRAINT `fk_dailyupdates_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_dailyupdates_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `daily_updates` (`id`, `user_id`, `project_id`, `update_date`, `client_name`, `project_name`, `tl_name`, `done_tasks`, `progress_tasks`, `remaining_tasks`, `queries`, `notes`, `created`, `modified`) VALUES ('1', '1', '1', '2026-09-11', 'Hitesh Sir', '8 Bloqs', 'Hitesh Sir', '- Initialized Helpdesk database and seeded demo records.\n- Set up secure bcrypt authentication for admin@gmail.com.\n- Verified full CRUD operations for work logs and client entities.', '- Finalizing end-to-end user experience checks.', '- Deployment review and monitoring.', '', 'System setup completed successfully.', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `daily_updates` (`id`, `user_id`, `project_id`, `update_date`, `client_name`, `project_name`, `tl_name`, `done_tasks`, `progress_tasks`, `remaining_tasks`, `queries`, `notes`, `created`, `modified`) VALUES ('2', '1', '2', '2026-09-10', 'Suresh Sir', 'Client Portal', 'Suresh Sir', '- Built responsive layout for daily updates grid.\n- Configured client-to-project mappings.', '- Performance optimization for log retrieval.', '- Email report dispatching.', '', '', '2026-09-11 20:43:57', '2026-09-11 20:43:57');
INSERT INTO `daily_updates` (`id`, `user_id`, `project_id`, `update_date`, `client_name`, `project_name`, `tl_name`, `done_tasks`, `progress_tasks`, `remaining_tasks`, `queries`, `notes`, `created`, `modified`) VALUES ('3', '2', '4', '2026-09-11', 'Apex Solutions', 'Mobile App', 'Team Lead', '- Completed mobile layout responsive styling.\n- Hooked up API error banners.', '- Offline storage testing.', '- Push notification integration.', '', 'Everything on schedule.', '2026-09-11 20:43:57', '2026-09-11 20:43:57');

SET FOREIGN_KEY_CHECKS=1;
