<?php
require '../db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$nome = trim($data['alimento_nome'] ?? '');
$preco = floatval($data['preco'] ?? 0);
$unidade = trim($data['unidade'] ?? '');
if ($nome==='' || $unidade==='' || $preco <= 0) { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
// upsert
$stmt = $pdo->prepare("INSERT INTO precos (alimento_nome, preco, unidade, data_atualizacao)
VALUES (?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE preco = VALUES(preco), data_atualizacao = NOW()");
$stmt->execute([$nome, $preco, $unidade]);
echo json_encode(['success'=>true]);
