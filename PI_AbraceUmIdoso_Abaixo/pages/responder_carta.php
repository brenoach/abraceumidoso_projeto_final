<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_login();
$user = current_user();
if (!in_array($user['tipo'], ['I','F'], true)) exit('Acesso negado.');
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT c.idCarta, c.textoCarta, c.statusCarta, i.idInstituicao, p.nomePessoa AS nomeIdoso
                      FROM carta c
                      JOIN idoso i ON i.idIdoso = c.idIdoso
                      JOIN pessoa p ON p.idPessoa = i.idPessoa
                      WHERE c.idCarta = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$carta = $stmt->get_result()->fetch_assoc();
if (!$carta) exit('Carta não encontrada.');
$idInstituicao = $user['tipo'] === 'I' ? (int)$user['id'] : (int)$user['idInstituicao'];
if ((int)$carta['idInstituicao'] !== $idInstituicao) exit('Acesso negado.');
$erro = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $resposta = trim($_POST['respostaCarta'] ?? '');
    if ($resposta === '') {
        $erro = 'Digite a resposta.';
    } else {
        $novoTexto = $carta['textoCarta'] . "

------------------------------
RESPOSTA DA INSTITUIÇÃO:
" . $resposta;
        $up = db()->prepare("UPDATE carta SET textoCarta = ?, statusCarta = 'Respondida' WHERE idCarta = ?");
        $up->bind_param('si', $novoTexto, $id);
        $up->execute();
        flash_set('success', 'Resposta registrada com sucesso.');
        header('Location: visualizar_carta.php?id=' . $id);
        exit;
    }
}
?>
<main class="page container">
  <form method="POST" class="form-box paper fade-in" style="max-width:820px;">
    <h1 class="section-title">Responder carta</h1>
    <?php if ($erro): ?><div class="alert error"><?= h($erro) ?></div><?php endif; ?>
    <p><strong>Idoso:</strong> <?= h($carta['nomeIdoso']) ?></p>
    <div class="card carta"><div style="white-space:pre-wrap; line-height:1.8;"><?= h($carta['textoCarta']) ?></div></div>
    <label>Resposta da instituição / funcionário</label>
    <textarea name="respostaCarta" required></textarea>
    <div class="actions-row"><button type="submit">Salvar resposta</button><a class="botao" href="visualizar_carta.php?id=<?= (int)$id ?>">Cancelar</a></div>
  </form>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
