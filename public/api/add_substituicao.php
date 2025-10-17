<?php
require '../db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$refeicao_id = $data['refeicao_id'] ?? null;
$nome = trim($data['nome'] ?? '');
if (!$refeicao_id || $nome==='') { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
$stmt = $pdo->prepare("INSERT INTO substituicoes (refeicao_id, nome) VALUES (?, ?)");
$stmt->execute([$refeicao_id, $nome]);
echo json_encode(['success'=>true, 'id'=>$pdo->lastInsertId()]);
