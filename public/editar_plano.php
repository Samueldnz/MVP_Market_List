<?php
require 'db_connect.php';
$plano_id = intval($_GET['id'] ?? 0);
if (!$plano_id) { header('Location: index.php'); exit; }
$stmt = $pdo->prepare("SELECT * FROM planos WHERE id = ?");
$stmt->execute([$plano_id]);
$plano = $stmt->fetch();
if (!$plano) { header('Location: index.php'); exit; }

// buscar refeições e alimentos e substituições
$sth = $pdo->prepare("SELECT * FROM refeicoes WHERE plano_id = ? ORDER BY id");
$sth->execute([$plano_id]);
$refeicoes = $sth->fetchAll();

$alimentos_map = [];
$sth = $pdo->prepare("SELECT * FROM alimentos WHERE refeicao_id = ?");
foreach ($refeicoes as $r) {
    $sth->execute([$r['id']]);
    $alimentos_map[$r['id']] = $sth->fetchAll();
}

$sth2 = $pdo->prepare("SELECT * FROM substituicoes WHERE refeicao_id = ?");
$subs_map = [];
$alimentos_sub_map = [];
foreach ($refeicoes as $r) {
    $sth2->execute([$r['id']]);
    $subs = $sth2->fetchAll();
    $subs_map[$r['id']] = $subs;
    $sth3 = $pdo->prepare("SELECT * FROM alimentos_substituicao WHERE substituicao_id = ?");
    foreach ($subs as $s) {
        $sth3->execute([$s['id']]);
        $alimentos_sub_map[$s['id']] = $sth3->fetchAll();
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Editar Plano — <?=htmlspecialchars($plano['nome'])?></title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Editar: <?=htmlspecialchars($plano['nome'])?></h1>
      <a href="index.php" class="btn">Voltar</a>
    </header>

    <main>
      <section class="panel">
        <h2>Adicionar Refeição</h2>
        <form id="form-add-refeicao">
          <input type="hidden" name="plano_id" value="<?=$plano_id?>">
          <label>Nome <input name="nome" required></label>
          <label>Horário <input name="horario" type="time"></label>
          <div class="actions">
            <button class="btn" type="submit">Adicionar Refeição</button>
          </div>
        </form>
      </section>

      <section>
        <h2>Refeições</h2>
        <?php if(empty($refeicoes)): ?>
          <p>Nenhuma refeição ainda.</p>
        <?php else: ?>
          <?php foreach($refeicoes as $r): ?>
            <div class="card">
              <div class="card-header">
                <h3><?=htmlspecialchars($r['nome'])?> <small><?= $r['horario'] ?></small></h3>
                <div>
                  <button class="btn small delete-btn" data-table="refeicoes" data-id="<?=$r['id']?>">Excluir</button>
                </div>
              </div>

              <div class="card-body">
                <h4>Alimentos</h4>
                <ul id="alimentos-list-<?=$r['id']?>">
                  <?php foreach($alimentos_map[$r['id']] ?? [] as $a): ?>
                    <li>
                      <?=htmlspecialchars($a['nome'])?> — <?= (float)$a['quantidade'] ?> <?=htmlspecialchars($a['unidade'])?>
                      <button class="btn tiny delete-btn" data-table="alimentos" data-id="<?=$a['id']?>">X</button>
                    </li>
                  <?php endforeach; ?>
                </ul>

                <form class="form-add-alimento" data-ref="<?=$r['id']?>">
                  <input name="refeicao_id" type="hidden" value="<?=$r['id']?>">
                  <input name="nome" placeholder="Nome do alimento" required>
                  <input name="quantidade" placeholder="Quantidade (numérica)" required>
                  <input name="unidade" placeholder="Unidade (g, ml, unid, fatia)" required>
                  <button class="btn" type="submit">Adicionar Alimento</button>
                </form>

                <hr>
                <h4>Substituições</h4>
                <ul>
                  <?php foreach($subs_map[$r['id']] ?? [] as $s): ?>
                    <li>
                      <strong><?=htmlspecialchars($s['nome'])?></strong>
                      <button class="btn tiny delete-btn" data-table="substituicoes" data-id="<?=$s['id']?>">X</button>
                      <ul>
                        <?php foreach($alimentos_sub_map[$s['id']] ?? [] as $as): ?>
                          <li>
                            <?=htmlspecialchars($as['nome'])?> — <?= (float)$as['quantidade'] ?> <?=htmlspecialchars($as['unidade'])?>
                            <button class="btn tiny delete-btn" data-table="alimentos_substituicao" data-id="<?=$as['id']?>">X</button>
                          </li>
                        <?php endforeach; ?>
                      </ul>

                      <form class="form-add-alimento-sub" data-sub="<?=$s['id']?>">
                        <input name="substituicao_id" type="hidden" value="<?=$s['id']?>">
                        <input name="nome" placeholder="Alimento da substituição" required>
                        <input name="quantidade" placeholder="Quantidade" required>
                        <input name="unidade" placeholder="Unidade" required>
                        <button class="btn" type="submit">Adicionar</button>
                      </form>
                    </li>
                  <?php endforeach; ?>
                </ul>

                <form class="form-add-substituicao" data-ref="<?=$r['id']?>">
                  <input name="refeicao_id" type="hidden" value="<?=$r['id']?>">
                  <input name="nome" placeholder="Nome da substituição (ex: Vitamina X)" required>
                  <button class="btn" type="submit">Adicionar Substituição</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </section>
    </main>
  </div>

  <script src="assets/js/app.js"></script>
  <script>
    // inicializa contexto
    window.EditContext = { plano_id: <?=json_encode($plano_id)?> };
  </script>
</body>
</html>
