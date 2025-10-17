<?php
require '../db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$plano_id = $data['plano_id'] ?? null;
$nome = trim($data['nome'] ?? '');
$horario = $data['horario'] ?? null;
if (!$plano_id || $nome === '') { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
$stmt = $pdo->prepare("INSERT INTO refeicoes (plano_id, nome, horario) VALUES (?, ?, ?)");
$stmt->execute([$plano_id, $nome, $horario]);
echo json_encode(['success'=>true, 'id'=>$pdo->lastInsertId()]);
