<?php
/** Página de login para voluntário, instituição e funcionário. */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';

if (is_logged_in()) {
    header('Location: dashboard.php');
    exit;
}

/**
 * Valida senha aceitando hash moderno e, por compatibilidade,
 * também senha salva em texto puro.
 */
function senhaConfere(string $senhaDigitada, ?string $senhaBanco): bool
{
    if ($senhaBanco === null || $senhaBanco === '') {
        return false;
    }

    // Compatibilidade com registros antigos em texto puro.
    if ($senhaDigitada === $senhaBanco) {
        return true;
    }

    // Registros novos com password_hash().
    return password_verify($senhaDigitada, $senhaBanco);
}

$erro = null;
$tipoSelecionado = 'V';
$emailPreenchido = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipoSelecionado = $_POST['tipo'] ?? 'V';
    $emailPreenchido = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!in_array($tipoSelecionado, ['V', 'I', 'F'], true)) {
        $erro = 'Tipo de acesso inválido.';
    } elseif ($emailPreenchido === '' || $senha === '') {
        $erro = 'Preencha e-mail e senha.';
    } else {
        $db = db();

        if ($tipoSelecionado === 'V') {
            $sql = "SELECT 
                        v.idVoluntario AS id,
                        p.nomePessoa AS nome,
                        c.email AS email,
                        v.senha AS senha_db,
                        NULL AS idInstituicao
                    FROM voluntario v
                    INNER JOIN contato c ON c.idContato = v.idContato
                    INNER JOIN pessoa p ON p.idPessoa = v.idPessoa
                    WHERE c.email = ?
                    LIMIT 1";
        } elseif ($tipoSelecionado === 'I') {
            $sql = "SELECT 
                        i.idInstituicao AS id,
                        i.nomeInstituicao AS nome,
                        c.email AS email,
                        i.senha AS senha_db,
                        i.idInstituicao AS idInstituicao
                    FROM instituicao i
                    INNER JOIN contato c ON c.idContato = i.idContato
                    WHERE c.email = ?
                    LIMIT 1";
        } else {
            $sql = "SELECT 
                        f.idFuncionario AS id,
                        p.nomePessoa AS nome,
                        c.email AS email,
                        f.senha AS senha_db,
                        f.idInstituicao AS idInstituicao
                    FROM funcionario f
                    INNER JOIN contato c ON c.idContato = f.idContato
                    INNER JOIN pessoa p ON p.idPessoa = f.idPessoa
                    WHERE c.email = ?
                    LIMIT 1";
        }

        $stmt = $db->prepare($sql);

        if (!$stmt) {
            $erro = 'Erro ao preparar o login.';
        } else {
            $stmt->bind_param('s', $emailPreenchido);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result ? $result->fetch_assoc() : null;
            $stmt->close();

            if (!$row || !senhaConfere($senha, $row['senha_db'] ?? null)) {
                $erro = 'E-mail ou senha inválidos.';
            } else {
                // Se a senha antiga estiver em texto puro, atualiza automaticamente para hash.
                if (($row['senha_db'] ?? '') === $senha) {
                    $novaSenhaHash = password_hash($senha, PASSWORD_DEFAULT);

                    if ($tipoSelecionado === 'V') {
                        $update = $db->prepare('UPDATE voluntario SET senha = ? WHERE idVoluntario = ?');
                    } elseif ($tipoSelecionado === 'I') {
                        $update = $db->prepare('UPDATE instituicao SET senha = ? WHERE idInstituicao = ?');
                    } else {
                        $update = $db->prepare('UPDATE funcionario SET senha = ? WHERE idFuncionario = ?');
                    }

                    if ($update) {
                        $idUsuario = (int) $row['id'];
                        $update->bind_param('si', $novaSenhaHash, $idUsuario);
                        $update->execute();
                        $update->close();
                    }
                }

                $_SESSION['user'] = [
                    'tipo' => $tipoSelecionado,
                    'id' => (int) $row['id'],
                    'nome' => $row['nome'],
                    'email' => $row['email'] ?? $emailPreenchido,
                    'idInstituicao' => isset($row['idInstituicao']) && $row['idInstituicao'] !== null
                        ? (int) $row['idInstituicao']
                        : null,
                ];

                flash_set('success', 'Login realizado com sucesso.');
                header('Location: dashboard.php');
                exit;
            }
        }
    }
}
?>
<main class="page container">
  <section class="login-center">
    <form method="POST" class="form-box fade-in" style="max-width: 560px; width: 100%;">
      <h1 class="section-title">Login do usuário</h1>
      <p class="muted">Acesse como voluntário, instituição ou funcionário.</p>

      <?php if ($erro): ?>
        <div class="alert error"><?= h($erro) ?></div>
      <?php endif; ?>

      <label for="tipo">Tipo de acesso</label>
      <select name="tipo" id="tipo" required>
        <option value="V" <?= $tipoSelecionado === 'V' ? 'selected' : '' ?>>Voluntário</option>
        <option value="I" <?= $tipoSelecionado === 'I' ? 'selected' : '' ?>>Instituição</option>
        <option value="F" <?= $tipoSelecionado === 'F' ? 'selected' : '' ?>>Funcionário</option>
      </select>

      <label for="email">E-mail</label>
      <input type="email" name="email" id="email" value="<?= h($emailPreenchido) ?>" required>

      <label for="senha">Senha</label>
      <input type="password" name="senha" id="senha" required>

      <div class="actions-row">
        <button type="submit">Entrar</button>
        <a class="botao" href="forgot_password.php">Esqueci minha senha</a>
      </div>
    </form>
  </section>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
