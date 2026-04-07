<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_login();
$user = current_user();
$dados = ['nome' => $user['nome'], 'sobre' => '', 'email' => $user['email']];
if ($user['tipo'] === 'V') {
  $sql = "SELECT p.nomePessoa, p.sobre, c.email
          FROM voluntario v
          JOIN pessoa p ON p.idPessoa = v.idPessoa
          JOIN contato c ON c.idContato = v.idContato
          WHERE v.idVoluntario = ?";
  $id = (int)$user['id'];
} elseif ($user['tipo'] === 'I') {
  $sql = "SELECT i.nomeInstituicao AS nomePessoa, '' AS sobre, c.email
          FROM instituicao i
          JOIN contato c ON c.idContato = i.idContato
          WHERE i.idInstituicao = ?";
  $id = (int)$user['id'];
} else {
  $sql = "SELECT p.nomePessoa, f.cargo AS sobre, c.email
          FROM funcionario f
          JOIN pessoa p ON p.idPessoa = f.idPessoa
          JOIN contato c ON c.idContato = f.idContato
          WHERE f.idFuncionario = ?";
  $id = (int)$user['id'];
}
$stmt = db()->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
if ($row) {
    $dados = ['nome' => $row['nomePessoa'], 'sobre' => $row['sobre'], 'email' => $row['email']];
}
?>
<main class="page container">
  <form class="form-box fade-in" action="../actions/salvar_perfil.php" method="POST" style="max-width:700px;">
    <h1 class="section-title">Editar informações</h1>
    <label>Nome</label><input type="text" name="nome" value="<?= h($dados['nome']) ?>" required>
    <label>E-mail</label><input type="email" name="email" value="<?= h($dados['email']) ?>" required>
    <?php if ($user['tipo'] !== 'I'): ?>
      <label>Sobre / observação</label><textarea name="sobre"><?= h($dados['sobre']) ?></textarea>
    <?php else: ?>
      <input type="hidden" name="sobre" value="">
    <?php endif; ?>
    <div class="actions-row"><button type="submit">Salvar alterações</button></div>
  </form>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
