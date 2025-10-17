<?php
require 'db_connect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    if ($nome === '') {
        $error = "Nome é obrigatório.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO planos (nome, descricao) VALUES (?, ?)");
        $stmt->execute([$nome, $descricao]);
        $id = $pdo->lastInsertId();
        header("Location: editar_plano.php?id=$id");
        exit;
    }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Criar Plano</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <header><h1>Criar Plano</h1></header>
    <main>
      <?php if(!empty($error)): ?><p class="error"><?=$error?></p><?php endif; ?>
      <form method="post">
        <label>Nome do Plano
          <input name="nome" required>
        </label>
        <label>Descrição
          <textarea name="descricao"></textarea>
        </label>
        <div class="actions">
          <button class="btn" type="submit">Criar</button>
          <a href="index.php" class="btn link">Voltar</a>
        </div>
      </form>
    </main>
  </div>
</body>
</html>
