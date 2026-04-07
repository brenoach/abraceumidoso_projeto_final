<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_login();

$user = current_user();
if ($user['tipo'] === 'I') {
    $instituicoes = null;
    $idInstituicaoPadrao = (int)$user['id'];
} elseif ($user['tipo'] === 'F' && !empty($user['idInstituicao'])) {
    $instituicoes = null;
    $idInstituicaoPadrao = (int)$user['idInstituicao'];
} else {
    $instituicoes = db()->query('SELECT idInstituicao, nomeInstituicao FROM instituicao ORDER BY nomeInstituicao');
    $idInstituicaoPadrao = 0;
}
?>
<main class="page container">
  <form class="form-box fade-in" action="../actions/salvar_idoso.php" method="POST">
    <h1 class="section-title">Cadastrar idoso</h1>
    <div class="form-grid">
      <div><label>Nome</label><input type="text" name="nomePessoa" required></div>
      <div><label>CPF</label><input type="text" name="cpf" required></div>
      <div><label>Data de nascimento</label><input type="date" name="dataNascimento" required></div>
      <div><label>Instituição</label>
        <?php if ($instituicoes): ?>
          <select name="idInstituicao" required>
            <?php while($inst = $instituicoes->fetch_assoc()): ?>
              <option value="<?= (int)$inst['idInstituicao'] ?>"><?= h($inst['nomeInstituicao']) ?></option>
            <?php endwhile; ?>
          </select>
        <?php else: ?>
          <input type="hidden" name="idInstituicao" value="<?= $idInstituicaoPadrao ?>">
          <input type="text" value="Instituição da sessão atual" disabled>
        <?php endif; ?>
      </div>
      <div><label>Aceita visita?</label><select name="aceitaVisita"><option value="1">Sim</option><option value="0">Não</option></select></div>
      <div><label>Aceita carta?</label><select name="aceitaCarta"><option value="1">Sim</option><option value="0">Não</option></select></div>
      <div style="grid-column:1/-1"><label>Sobre</label><textarea name="sobre"></textarea></div>
    </div>
    <div class="actions-row"><button type="submit">Salvar idoso</button></div>
  </form>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
