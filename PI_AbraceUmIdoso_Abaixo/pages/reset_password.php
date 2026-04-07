<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
$token = trim($_GET['token'] ?? $_POST['token'] ?? '');
$erro = null; $sucesso = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova = $_POST['nova_senha'] ?? '';
    if ($token === '' || $nova === '') {
        $erro = 'Informe o token e a nova senha.';
    } else {
        $stmt = db()->prepare('SELECT * FROM recuperacao_senha WHERE token = ? AND usado = 0 AND expiraEm >= NOW() ORDER BY idRecuperacao DESC LIMIT 1');
        $stmt->bind_param('s', $token);
        $stmt->execute();
        $rec = $stmt->get_result()->fetch_assoc();
        if (!$rec) {
            $erro = 'Token inválido ou expirado.';
        } else {
            $tabela = match($rec['tipoUsuario']) { 'V' => 'voluntario', 'I' => 'instituicao', 'F' => 'funcionario' };
            $campoId = match($rec['tipoUsuario']) { 'V' => 'idVoluntario', 'I' => 'idInstituicao', 'F' => 'idFuncionario' };
            $up = db()->prepare("UPDATE $tabela SET senha = ? WHERE $campoId = ?");
            $idUsuario = (int)$rec['idUsuario'];
            $up->bind_param('si', $nova, $idUsuario);
            $up->execute();
            $done = db()->prepare('UPDATE recuperacao_senha SET usado = 1 WHERE idRecuperacao = ?');
            $idRec = (int)$rec['idRecuperacao'];
            $done->bind_param('i', $idRec);
            $done->execute();
            $sucesso = 'Senha redefinida com sucesso.';
        }
    }
}
?>
<main class="page container">
  <form method="POST" class="form-box fade-in" style="max-width: 650px;">
    <h1 class="section-title">Definir nova senha</h1>
    <?php if ($erro): ?><div class="alert error"><?= h($erro) ?></div><?php endif; ?>
    <?php if ($sucesso): ?><div class="alert success"><?= h($sucesso) ?></div><?php endif; ?>
    <label>Token</label>
    <input type="text" name="token" value="<?= h($token) ?>" required>
    <label>Nova senha</label>
    <input type="password" name="nova_senha" required>
    <div class="actions-row"><button type="submit">Salvar nova senha</button><a class="botao" href="login.php">Ir para login</a></div>
  </form>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
