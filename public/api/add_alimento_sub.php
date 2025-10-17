<?php
require '../db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$sub_id = $data['substituicao_id'] ?? null;
$nome = trim($data['nome'] ?? '');
$quantidade = $data['quantidade'] ?? 0;
$unidade = trim($data['unidade'] ?? '');
if (!$sub_id || $nome==='') { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
$stmt = $pdo->prepare("INSERT INTO alimentos_substituicao (substituicao_id, nome, quantidade, unidade) VALUES (?, ?, ?, ?)");
$stmt->execute([$sub_id, $nome, $quantidade, $unidade]);
echo json_encode(['success'=>true, 'id'=>$pdo->lastInsertId()]);
