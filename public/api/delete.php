<?php
require '../db_connect.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$data = json_decode(file_get_contents('php://input'), true);
$table = preg_replace('/[^a-z_]/', '', $data['table'] ?? '');
$id = intval($data['id'] ?? 0);
$allowed = ['planos','refeicoes','alimentos','substituicoes','alimentos_substituicao','precos'];
if (!$table || !in_array($table, $allowed) || $id<=0) { http_response_code(400); echo json_encode(['error'=>'Invalid']); exit; }
$stmt = $pdo->prepare("DELETE FROM {$table} WHERE id = ?");
$stmt->execute([$id]);
echo json_encode(['success'=>true]);
