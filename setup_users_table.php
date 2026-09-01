<?php
try {
    $pdo = new PDO("mysql:host=127.0.0.1;port=3306;dbname=helpdesk", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `users` (
            `id` INT AUTO_INCREMENT PRIMARY KEY,
            `name` VARCHAR(100) NOT NULL,
            `email` VARCHAR(150) NOT NULL UNIQUE,
            `password` VARCHAR(255) NOT NULL,
            `created` DATETIME NULL,
            `modified` DATETIME NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $hash = password_hash("123456", PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT IGNORE INTO `users` (`name`, `email`, `password`, `created`, `modified`) VALUES (?, ?, ?, NOW(), NOW())");
    $stmt->execute(["Mohit Mokariya", "admin@8bloqs.com", $hash]);

    echo "Users table created and test user (admin@8bloqs.com / 123456) seeded successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
