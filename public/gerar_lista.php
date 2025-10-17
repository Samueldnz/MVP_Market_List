<?php
require 'db_connect.php';
/**
 * Lógica:
 * - Recebe optional plano_id (se null usa todos os planos)
 * - Para cada refeição do plano:
 *    - calcula custo da "opção principal" (soma de alimentos daquela refeição)
 *    - calcula custo de cada substituição (soma dos alimentos da substituição)
 *    - escolhe a opção de menor custo
 * - soma quantidades por alimento agrupando por nome+unidade
 * - multiplica por 30 (meses) para quantidade mensal
 *
 * Observação: precificação requer que exista um registro em precos com
 * o mesmo alimento_nome e unidade. Se não existir, preco = null -> marcado como "preço ausente"
 */

$plano_id = isset($_GET['plano_id']) ? intval($_GET['plano_id']) : null;

$where = $plano_id ? "AND r.plano_id = {$plano_id}" : "";

$sql_ref = "SELECT r.* FROM refeicoes r WHERE 1 {$where} ORDER BY r.id";
$refStmt = $pdo->query($sql_ref);
$refeicoes = $refStmt->fetchAll();

function buscarPreco($pdo, $nome, $unidade) {
    $stmt = $pdo->prepare("SELECT preco, unidade FROM precos WHERE alimento_nome = ? AND unidade = ? LIMIT 1");
    $stmt->execute([$nome, $unidade]);
    $row = $stmt->fetch();
    return $row ? (float)$row['preco'] : null;
}

$final_selections = []; // por refeição -> selecionado (alimentos list)
$aggregate = []; // chave = nome||unidade -> ['nome','unidade','quantidade','preco_unit','custo_total','refeicoes' => [...]]
$warnings = [];

foreach ($refeicoes as $r) {
    $rid = $r['id'];
    // custo da opção principal (alimentos desta refeição)
    $stmt = $pdo->prepare("SELECT * FROM alimentos WHERE refeicao_id = ?");
    $stmt->execute([$rid]);
    $alimentos = $stmt->fetchAll();

    $option_list = [];
    $cost_option = 0.0;
    $missing_price_flag = false;
    foreach ($alimentos as $a) {
        $price = buscarPreco($pdo, $a['nome'], $a['unidade']);
        if ($price === null) { $missing_price_flag = true; }
        $cost = ($price === null) ? 0.0 : ((float)$a['quantidade'] * $price);
        $cost_option += $cost;
        $option_list[] = ['nome'=>$a['nome'],'quantidade'=>(float)$a['quantidade'],'unidade'=>$a['unidade'],'preco_unit'=>$price, 'custo'=>$cost];
    }
    // agora cada substituição
    $stmt2 = $pdo->prepare("SELECT * FROM substituicoes WHERE refeicao_id = ?");
    $stmt2->execute([$rid]);
    $subs = $stmt2->fetchAll();
    $sub_options = [];
    foreach ($subs as $s) {
        $slist = [];
        $stmt3 = $pdo->prepare("SELECT * FROM alimentos_substituicao WHERE substituicao_id = ?");
        $stmt3->execute([$s['id']]);
        $alist = $stmt3->fetchAll();
        $subcost = 0.0;
        $sub_missing = false;
        foreach ($alist as $ai) {
            $price = buscarPreco($pdo, $ai['nome'], $ai['unidade']);
            if ($price === null) $sub_missing = true;
            $subcost += ($price === null ? 0.0 : ((float)$ai['quantidade'] * $price));
            $slist[] = ['nome'=>$ai['nome'],'quantidade'=>(float)$ai['quantidade'],'unidade'=>$ai['unidade'],'preco_unit'=>$price,'custo'=>($price===null?0.0:((float)$ai['quantidade']*$price))];
        }
        $sub_options[] = ['id'=>$s['id'],'nome'=>$s['nome'],'itens'=>$slist,'custo'=>$subcost,'missing_price'=>$sub_missing];
    }

    // escolher menor custo entre option_list (principal) e cada sub_options
    $best = ['type'=>'principal','itens'=>$option_list,'custo'=>$cost_option,'missing_price'=>$missing_price_flag];
    foreach ($sub_options as $so) {
        if ($so['custo'] < $best['custo'] || $best['missing_price']) {
            // if best had missing price treat subs that have price as better even if cost equal, but keep simple: choose min cost
            $best = ['type'=>'substituicao','sub_id'=>$so['id'],'sub_nome'=>$so['nome'],'itens'=>$so['itens'],'custo'=>$so['custo'],'missing_price'=>$so['missing_price']];
        }
    }

    $final_selections[] = ['refeicao_id'=>$r['id'],'refeicao_nome'=>$r['nome'],'selection'=>$best];

    // agregar quantidades (multiplicar por 30 dias no fim)
    foreach ($best['itens'] as $it) {
        $key = $it['nome'] . '||' . $it['unidade'];
        if (!isset($aggregate[$key])) {
            $aggregate[$key] = [
                'nome'=>$it['nome'],
                'unidade'=>$it['unidade'],
                'quantidade_diaria'=>0.0,
                'preco_unit'=>$it['preco_unit'],
                'custo_unit'=>$it['preco_unit'],
                'refeicoes' => []
            ];
        }
        $aggregate[$key]['quantidade_diaria'] += $it['quantidade'];
        // prefer preco unit não-nulo
        if ($aggregate[$key]['preco_unit'] === null && $it['preco_unit'] !== null) {
            $aggregate[$key]['preco_unit'] = $it['preco_unit'];
        }
        $aggregate[$key]['refeicoes'][] = $r['nome'];
        if ($it['preco_unit'] === null) {
            $warnings[] = "Preço ausente: {$it['nome']} ({$it['unidade']}) utilizado na refeição {$r['nome']}.";
        }
    }
}

$lista = [];
$total_geral = 0.0;
foreach ($aggregate as $k => $v) {
    $quant_mensal = $v['quantidade_diaria'] * 30.0;
    $preco_unit = $v['preco_unit'] ?? null;
    $custo_total = $preco_unit === null ? null : ($preco_unit * $quant_mensal);
    if ($custo_total !== null) $total_geral += $custo_total;
    $lista[] = [
        'nome'=>$v['nome'],
        'unidade'=>$v['unidade'],
        'quantidade_mensal'=>$quant_mensal,
        'preco_unitario'=>$preco_unit,
        'custo_total'=>$custo_total,
        'refeicoes'=>array_values(array_unique($v['refeicoes']))
    ];
}

header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'plano_id'=>$plano_id,
    'selections'=>$final_selections,
    'lista_compras'=>$lista,
    'total_geral'=>$total_geral,
    'warnings'=>array_values(array_unique($warnings))
], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
