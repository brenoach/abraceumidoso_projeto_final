<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
$mensagem = null; $erro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo = $_POST['tipo'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $token = bin2hex(random_bytes(16));
    $expira = date('Y-m-d H:i:s', strtotime('+1 hour'));
    $tabela = match($tipo) { 'V' => 'voluntario', 'I' => 'instituicao', 'F' => 'funcionario', default => '' };
    $campoId = match($tipo) { 'V' => 'idVoluntario', 'I' => 'idInstituicao', 'F' => 'idFuncionario', default => '' };
    if ($tabela === '' || $email === '') {
        $erro = 'Informe os dados corretamente.';
    } else {
        $sql = "SELECT t.$campoId AS id FROM $tabela t JOIN contato c ON c.idContato = t.idContato WHERE c.email = ? LIMIT 1";
        $stmt = db()->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $row = $stmt->get_result()->fetch_assoc();
        if (!$row) {
            $erro = 'Usuário não encontrado para este e-mail.';
        } else {
            $ins = db()->prepare('INSERT INTO recuperacao_senha (tipoUsuario, idUsuario, email, token, expiraEm) VALUES (?,?,?,?,?)');
            $idUsuario = (int)$row['id'];
            $ins->bind_param('sisss', $tipo, $idUsuario, $email, $token, $expira);
            $ins->execute();
            $resetLink = 'reset_password.php?token=' . urlencode($token);
            $assunto = 'Recuperação de senha - Abrace Um Idoso';
            $corpo = "Seu token foi gerado com sucesso.

Use este link para redefinir a senha:
$resetLink

Token: $token
Expira em 1 hora.";
            @mail($email, $assunto, $corpo);
            $mensagem = 'Token gerado. O sistema tentou enviar por e-mail usando mail(). Em hospedagens que bloqueiam e-mail, use o token exibido abaixo para teste: ' . $token;
        }
    }
}
?>
<main class="page container">
  <form method="POST" class="form-box fade-in" style="max-width: 650px;">
    <h1 class="section-title">Recuperar senha do usuário</h1>
    <p class="muted">RF2 - gera token e tenta enviar por e-mail.</p>
    <?php if ($erro): ?><div class="alert error"><?= h($erro) ?></div><?php endif; ?>
    <?php if ($mensagem): ?><div class="alert success"><?= h($mensagem) ?></div><?php endif; ?>
    <label>Tipo de acesso</label>
    <select name="tipo" required>
      <option value="V">Voluntário</option><option value="I">Instituição</option><option value="F">Funcionário</option>
    </select>
    <label>E-mail</label>
    <input type="email" name="email" required>
    <div class="actions-row"><button type="submit">Enviar token</button><a class="botao" href="login.php">Voltar ao login</a></div>
  </form>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
