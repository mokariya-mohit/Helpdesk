<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306", 'root', '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 1. Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `helpdesk` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    $pdo->exec("USE `helpdesk`;");

    // 2. Create Projects Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `projects` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL UNIQUE,
            `is_default` TINYINT(1) DEFAULT 0,
            `created` DATETIME NULL,
            `modified` DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 3. Create Work Logs Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `work_logs` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `project_id` INT NULL,
            `log_date` DATE NOT NULL,
            `content` LONGTEXT NULL,
            `task_count` INT DEFAULT 0,
            `created` DATETIME NULL,
            `modified` DATETIME NULL,
            UNIQUE KEY `unique_date_project` (`log_date`, `project_id`),
            CONSTRAINT `fk_worklogs_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 4. Create Daily Updates Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `daily_updates` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `project_id` INT NULL,
            `update_date` DATE NOT NULL,
            `client_name` VARCHAR(100) DEFAULT 'Hitesh sir',
            `project_name` VARCHAR(150) NULL,
            `tl_name` VARCHAR(100) NULL,
            `done_tasks` TEXT NULL,
            `progress_tasks` TEXT NULL,
            `remaining_tasks` TEXT NULL,
            `queries` TEXT NULL,
            `notes` TEXT NULL,
            `created` DATETIME NULL,
            `modified` DATETIME NULL,
            UNIQUE KEY `unique_update_date_project` (`update_date`, `project_id`),
            CONSTRAINT `fk_dailyupdates_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 5. Create Clients Table
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `clients` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `created` DATETIME NULL,
            `modified` DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 6. Seed Projects
    $stmt = $pdo->prepare("INSERT IGNORE INTO `projects` (`name`, `is_default`, `created`, `modified`) VALUES (?, ?, NOW(), NOW())");
    $stmt->execute(['8.bloqs', 1]);
    $stmt->execute(['client_portal', 0]);
    $stmt->execute(['seo_reports', 0]);

    // 7. Seed Clients
    $stmt = $pdo->prepare("INSERT INTO `clients` (`name`, `created`, `modified`) SELECT ?, NOW(), NOW() WHERE NOT EXISTS (SELECT 1 FROM `clients` WHERE `name` = ?)");
    $stmt->execute(['Hitesh sir', 'Hitesh sir']);
    $stmt->execute(['Suresh sir', 'Suresh sir']);

    // 8. Seed default sample log for 8.bloqs
    $projId = $pdo->query("SELECT id FROM `projects` WHERE `name` = '8.bloqs'")->fetchColumn();
    if ($projId) {
        $sampleText = "26-08-2026  8.bloqs\n-------------------\nBackend:\n- Fixed the issues in the Domain Analytics, Competitors Domain, Keyword Gap & Local SERP, and Position Tracking reports, including data errors, target domain handling, report name/edit functionality, and layout styling problems.\n- Fixed the Page SEO Checker errors for the 4,000-domain test, including the issue with checking additional pages and handling missing error data properly.\n- Added checkboxes to the \"Top Ranked Keywords\" report preview so the selected keyword can be passed to the Keyword Overview report. https://prnt.sc/98snTitR755h\n- Added a new Keyword Overview report tab to the SEO Reports section and added a keyword_overview column to the seo_reports table to store the API response.\n- Created a dedicated Keyword Overview report template with a form, response preview, skeleton loader for data loading, and an AI prompt for generating the report summary.";
        $stmt = $pdo->prepare("INSERT IGNORE INTO `work_logs` (`project_id`, `log_date`, `content`, `task_count`, `created`, `modified`) VALUES (?, '2026-08-26', ?, 5, NOW(), NOW())");
        $stmt->execute([$projId, $sampleText]);
    }

    echo "Database and tables created successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
