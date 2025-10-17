<?php
require 'db_connect.php';
$plano_id = isset($_GET['plano_id']) ? intval($_GET['plano_id']) : null;
$planos = $pdo->query("SELECT id, nome FROM planos ORDER BY data_criacao DESC")->fetchAll();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Lista de Compras</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <div class="container">
    <header>
      <h1>Gerar Lista de Compras</h1>
      <a href="index.php" class="btn">Voltar</a>
    </header>

    <main>
      <section class="panel">
        <label>Selecione Plano:
          <select id="select-plano">
            <option value="">— Todos os planos —</option>
            <?php foreach($planos as $p): ?>
              <option value="<?=$p['id']?>" <?= $plano_id == $p['id'] ? 'selected':'' ?>><?=htmlspecialchars($p['nome'])?></option>
            <?php endforeach; ?>
          </select>
        </label>
        <div class="actions">
          <button id="btn-gerar" class="btn">Gerar Lista</button>
        </div>
      </section>

      <section id="resultado" class="panel" style="display:none;">
        <h2>Resultado</h2>
        <div id="warnings"></div>
        <table class="table">
          <thead><tr><th>Refeição(s)</th><th>Alimento</th><th>Quantidade mensal</th><th>Unidade</th><th>Preço unit.</th><th>Custo total</th></tr></thead>
          <tbody id="resultado-body"></tbody>
        </table>
        <h3 id="total-geral"></h3>
      </section>
    </main>
  </div>

  <script>
  document.getElementById('btn-gerar').addEventListener('click', async function(){
    const planoId = document.getElementById('select-plano').value;
    const url = 'gerar_lista.php' + (planoId ? ('?plano_id=' + planoId) : '');
    document.getElementById('resultado').style.display = 'none';
    const res = await fetch(url);
    if (!res.ok) {
      alert('Erro ao gerar lista');
      return;
    }
    const data = await res.json();
    const tbody = document.getElementById('resultado-body');
    tbody.innerHTML = '';
    data.lista_compras.forEach(item => {
      const tr = document.createElement('tr');
      const refeicoes = item.refeicoes.join(', ');
      tr.innerHTML = `<td>${refeicoes}</td>
                      <td>${item.nome}</td>
                      <td>${(Math.round(item.quantidade_mensal*100)/100).toLocaleString()}</td>
                      <td>${item.unidade}</td>
                      <td>${item.preco_unitario !== null ? 'R$ '+(item.preco_unitario.toFixed(2)) : '—'}</td>
                      <td>${item.custo_total !== null ? 'R$ '+(item.custo_total.toFixed(2)) : '—'}</td>`;
      tbody.appendChild(tr);
    });
    document.getElementById('total-geral').textContent = 'Total mensal estimado: R$ ' + (data.total_geral ? data.total_geral.toFixed(2) : '0.00');
    const warnings = data.warnings || [];
    const warnDiv = document.getElementById('warnings');
    warnDiv.innerHTML = '';
    warnings.forEach(w => { const p = document.createElement('p'); p.className='warning'; p.textContent = w; warnDiv.appendChild(p); });
    document.getElementById('resultado').style.display = 'block';
  });
  </script>
</body>
</html>
