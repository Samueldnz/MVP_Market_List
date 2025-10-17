<?php
// db_connect.php
$DB_HOST = '127.0.0.1';
$DB_NAME = 'plano_alimentar';
$DB_USER = 'root';
$DB_PASS = ''; // coloque a senha do seu MySQL

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4", $DB_USER, $DB_PASS, $options);
} catch (PDOException $e) {
    http_response_code(500);
    echo "Erro na conexão: " . $e->getMessage();
    exit;
}
