<?php
require '../db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$refeicao_id = $data['refeicao_id'] ?? null;
$nome = trim($data['nome'] ?? '');
$quantidade = $data['quantidade'] ?? 0;
$unidade = trim($data['unidade'] ?? '');
if (!$refeicao_id || $nome==='') { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
$stmt = $pdo->prepare("INSERT INTO alimentos (refeicao_id, nome, quantidade, unidade) VALUES (?, ?, ?, ?)");
$stmt->execute([$refeicao_id, $nome, $quantidade, $unidade]);
echo json_encode(['success'=>true, 'id'=>$pdo->lastInsertId()]);
