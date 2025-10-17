// app.js - client side helpers
async function postJson(url, body) {
  const res = await fetch(url, { method:'POST', headers:{'Content-Type':'application/json'}, body: JSON.stringify(body) });
  return res.json();
}

document.addEventListener('DOMContentLoaded', () => {
  // add refeição
  const formRef = document.getElementById('form-add-refeicao');
  if (formRef) {
    formRef.addEventListener('submit', async (e) => {
      e.preventDefault();
      const fd = new FormData(formRef);
      const data = { plano_id: fd.get('plano_id'), nome: fd.get('nome'), horario: fd.get('horario') };
      const r = await postJson('api/add_refeicao.php', data);
      if (r && r.success) location.reload();
      else alert('Erro ao adicionar refeição');
    });
  }

  // adicionar alimentos nas refeições (forms dinâmicos)
  document.querySelectorAll('.form-add-alimento').forEach(f => {
    f.addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.target;
      const fd = new FormData(form);
      const body = { refeicao_id: fd.get('refeicao_id'), nome: fd.get('nome'), quantidade: fd.get('quantidade'), unidade: fd.get('unidade') };
      const r = await postJson('api/add_alimento.php', body);
      if (r && r.success) location.reload();
      else alert('Erro ao adicionar alimento');
    });
  });

  // adicionar substituições
  document.querySelectorAll('.form-add-substituicao').forEach(f => {
    f.addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.target;
      const fd = new FormData(form);
      const body = { refeicao_id: fd.get('refeicao_id'), nome: fd.get('nome') };
      const r = await postJson('api/add_substituicao.php', body);
      if (r && r.success) location.reload();
      else alert('Erro ao adicionar substituição');
    });
  });

  // adicionar alimentos em substituições
  document.querySelectorAll('.form-add-alimento-sub').forEach(f => {
    f.addEventListener('submit', async (e) => {
      e.preventDefault();
      const form = e.target;
      const fd = new FormData(form);
      const body = { substituicao_id: fd.get('substituicao_id'), nome: fd.get('nome'), quantidade: fd.get('quantidade'), unidade: fd.get('unidade') };
      const r = await postJson('api/add_alimento_sub.php', body);
      if (r && r.success) location.reload();
      else alert('Erro ao adicionar alimento (substituição)');
    });
  });

  // deletar botões
  document.querySelectorAll('.delete-btn').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const id = btn.dataset.id;
      const table = btn.dataset.table;
      if (!confirm('Confirmar exclusão?')) return;
      const r = await postJson('api/delete.php', { table, id });
      if (r && r.success) location.reload();
      else alert('Erro ao excluir');
    });
  });

  // salvar preço (precos.php)
  document.querySelectorAll('.save-preco').forEach(btn => {
    btn.addEventListener('click', async (e) => {
      const nome = btn.dataset.nome;
      const unidadeEl = document.querySelector('.input-unidade[data-nome="'+nome+'"]');
      const precoEl = document.querySelector('.input-preco[data-nome="'+nome+'"]');
      const unidade = unidadeEl.value.trim();
      const preco = parseFloat(precoEl.value.replace(',','.'));
      if (!unidade || !preco || preco <= 0) { alert('Informe unidade e preço válidos'); return; }
      const r = await postJson('api/update_preco.php', { alimento_nome: nome, unidade, preco });
      if (r && r.success) {
        alert('Preço salvo');
        location.reload();
      } else alert('Erro');
    });
  });
});
