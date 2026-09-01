<?php
$ports = [3306, 3307, 3308];
foreach ($ports as $port) {
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;port={$port}", 'root', '');
        echo "Port {$port} connected successfully!\n";
        $stmt = $pdo->query("SHOW DATABASES");
        $dbs = $stmt->fetchAll(PDO::FETCH_COLUMN);
        echo "Databases: " . implode(', ', $dbs) . "\n";
    } catch (Exception $e) {
        echo "Port {$port} failed: " . $e->getMessage() . "\n";
    }
}
