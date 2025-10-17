<?php
require 'db_connect.php';

// buscar lista única de nomes de alimentos (de alimentos e de substituições)
$sth = $pdo->query("SELECT DISTINCT nome FROM alimentos UNION SELECT DISTINCT nome FROM alimentos_substituicao ORDER BY nome");
$alimentos = $sth->fetchAll(PDO::FETCH_COLUMN);

// buscar precos existentes
$sth2 = $pdo->query("SELECT * FROM precos ORDER BY alimento_nome");
$precos_db = [];
foreach($sth2->fetchAll() as $row) {
    $precos_db[$row['alimento_nome'] . '||' . $row['unidade']] = $row;
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Gerenciar Preços</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Gerenciar Preços</h1>
      <a href="index.php" class="btn">Voltar</a>
    </header>

    <main>
      <section class="panel">
        <h2>Alimentos detectados</h2>
        <p>Use este formulário para cadastrar o preço por unidade (por ex: 4.50 por "unidade", 8.90 por "kg", 0.50 por "fatia", etc.)</p>

        <table class="table">
          <thead><tr><th>Alimento</th><th>Unidade</th><th>Preço</th><th>Ações</th></tr></thead>
          <tbody id="precos-list">
            <?php foreach($alimentos as $nome): ?>
              <tr>
                <td><?=htmlspecialchars($nome)?></td>
                <td><input class="input-unidade" data-nome="<?=htmlspecialchars($nome)?>" placeholder="ex: kg, g, unidade, fatia" /></td>
                <td><input class="input-preco" data-nome="<?=htmlspecialchars($nome)?>" placeholder="ex: 3.50" /></td>
                <td><button class="btn save-preco" data-nome="<?=htmlspecialchars($nome)?>">Salvar</button></td>
              </tr>
            <?php endforeach; ?>
            <!-- mostrar preços já cadastrados -->
            <?php if(empty($alimentos)): ?>
              <tr><td colspan="4">Nenhum alimento detectado ainda (adicione alimentos no plano).</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </section>

      <section class="panel">
        <h3>Preços cadastrados</h3>
        <table class="table">
          <thead><tr><th>Alimento</th><th>Unidade</th><th>Preço</th><th>Atualizado em</th><th>Ação</th></tr></thead>
          <tbody>
            <?php
            $sth = $pdo->query("SELECT * FROM precos ORDER BY alimento_nome");
            foreach($sth->fetchAll() as $p): ?>
              <tr>
                <td><?=htmlspecialchars($p['alimento_nome'])?></td>
                <td><?=htmlspecialchars($p['unidade'])?></td>
                <td>R$ <?=number_format($p['preco'],2,',','.')?></td>
                <td><?=htmlspecialchars($p['data_atualizacao'])?></td>
                <td><button class="btn small delete-btn" data-table="precos" data-id="<?=$p['id']?>">Excluir</button></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </section>
    </main>
  </div>

  <script src="assets/js/app.js"></script>
</body>
</html>
