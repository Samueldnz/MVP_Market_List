<?php
require 'db_connect.php';
$sth = $pdo->query("SELECT * FROM planos ORDER BY data_criacao DESC");
$planos = $sth->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Dashboard — Planos Alimentares</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Planos Alimentares — MVP</h1>
      <nav>
        <a href="criar_plano.php" class="btn">Criar Plano</a>
        <a href="precos.php" class="btn">Gerenciar Preços</a>
        <a href="lista_compras.php" class="btn">Gerar Lista de Compras</a>
      </nav>
    </header>

    <main>
      <h2>Planos</h2>
      <?php if(empty($planos)): ?>
        <p>Nenhum plano encontrado. <a href="criar_plano.php">Crie um agora</a>.</p>
      <?php else: ?>
        <div class="grid">
          <?php foreach($planos as $p): ?>
            <div class="card">
              <h3><?=htmlspecialchars($p['nome'])?></h3>
              <p><?=nl2br(htmlspecialchars(substr($p['descricao'],0,200)))?></p>
              <small>Criado: <?=htmlspecialchars($p['data_criacao'])?></small>
              <div class="card-actions">
                <a href="editar_plano.php?id=<?=$p['id']?>" class="btn small">Editar</a>
                <a href="lista_compras.php?plano_id=<?=$p['id']?>" class="btn small">Lista de Compras</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </main>
  </div>
</body>
</html>
