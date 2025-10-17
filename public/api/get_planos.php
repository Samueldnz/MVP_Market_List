<?php
require '../db_connect.php';
$stmt = $pdo->query("SELECT id, nome FROM planos ORDER BY data_criacao DESC");
echo json_encode($stmt->fetchAll());
