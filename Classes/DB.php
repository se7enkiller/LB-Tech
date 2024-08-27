<?php

namespace Classes;

use PDO;
use PDOException;

class DB
{
    public PDO $pdo;

    public function __construct() {

        $host = 'localhost';
        $db = 'voiptime';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $user, $pass, $options);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
    }

    public function migrate(): void
    {
        $sqlDirectory = $_SERVER['DOCUMENT_ROOT'] . '/migration/create_orders_table.sql';

        $files = glob($sqlDirectory . '/*.sql');

        foreach ($files as $file) {
            $sql = file_get_contents($file);

            $this->pdo->exec($sql);

            echo "Executed: $file\n";
        }

        echo "All SQL scripts executed successfully.";
    }
}
