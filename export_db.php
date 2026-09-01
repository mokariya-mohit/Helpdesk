<?php
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=helpdesk', 'root', '');
$tables = ['users', 'projects', 'work_logs', 'daily_updates'];
$sql = "-- Helpdesk Database Dump\n-- Generated on " . date('Y-m-d H:i:s') . "\n\nSET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($tables as $t) {
    $stmt = $pdo->query("SHOW CREATE TABLE `{$t}`");
    if ($stmt) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $sql .= "DROP TABLE IF EXISTS `{$t}`;\n" . $row['Create Table'] . ";\n\n";

        $rows = $pdo->query("SELECT * FROM `{$t}`")->fetchAll(PDO::FETCH_ASSOC);
        if (!empty($rows)) {
            foreach ($rows as $r) {
                $keys = array_map(fn($k) => "`{$k}`", array_keys($r));
                $vals = array_map(fn($v) => $v === null ? 'NULL' : $pdo->quote($v), array_values($r));
                $sql .= "INSERT INTO `{$t}` (" . implode(', ', $keys) . ") VALUES (" . implode(', ', $vals) . ");\n";
            }
            $sql .= "\n";
        }
    }
}

$sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
file_put_contents(__DIR__ . '/database_schema.sql', $sql);
echo "database_schema.sql updated successfully!\n";
