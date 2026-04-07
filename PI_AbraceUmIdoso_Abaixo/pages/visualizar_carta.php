<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_login();
$id = (int)($_GET['id'] ?? 0);
$stmt = db()->prepare("SELECT c.idCarta, c.textoCarta, c.dataCarta, c.statusCarta, c.idVoluntario,
                             pv.nomePessoa AS nomeVoluntario,
                             pi.nomePessoa AS nomeIdoso,
                             inst.nomeInstituicao,
                             i.idInstituicao
                      FROM carta c
                      JOIN voluntario v ON v.idVoluntario = c.idVoluntario
                      JOIN pessoa pv ON pv.idPessoa = v.idPessoa
                      JOIN idoso i ON i.idIdoso = c.idIdoso
                      JOIN pessoa pi ON pi.idPessoa = i.idPessoa
                      JOIN instituicao inst ON inst.idInstituicao = i.idInstituicao
                      WHERE c.idCarta = ? LIMIT 1");
$stmt->bind_param('i', $id);
$stmt->execute();
$carta = $stmt->get_result()->fetch_assoc();
if (!$carta) exit('Carta não encontrada.');
$user = current_user();
if ($user['tipo'] === 'V' && (int)$carta['idVoluntario'] !== (int)$user['id']) exit('Acesso negado.');
if ($user['tipo'] === 'I' && (int)$carta['idInstituicao'] !== (int)$user['id']) exit('Acesso negado.');
if ($user['tipo'] === 'F' && (int)$carta['idInstituicao'] !== (int)$user['idInstituicao']) exit('Acesso negado.');
?>
<main class="page container">
  <h1 class="section-title">Visualização da carta</h1>
  <article class="card carta fade-in" style="max-width: 820px; margin: 0 auto;">
    <p><strong>De:</strong> <?= h($carta['nomeVoluntario']) ?></p>
    <p><strong>Para:</strong> <?= h($carta['nomeIdoso']) ?></p>
    <p><strong>Instituição:</strong> <?= h($carta['nomeInstituicao']) ?></p>
    <p><strong>Enviada em:</strong> <?= h($carta['dataCarta']) ?></p>
    <p><strong>Status:</strong> <span class="badge"><?= h($carta['statusCarta']) ?></span></p>
    <hr>
    <div style="white-space: pre-wrap; line-height: 1.8;"><?= h($carta['textoCarta']) ?></div>
    <div class="actions-row">
      <?php if (in_array($user['tipo'], ['I','F'], true) && $carta['statusCarta'] !== 'Respondida'): ?><a class="botao" href="responder_carta.php?id=<?= (int)$carta['idCarta'] ?>">Responder carta</a><?php endif; ?>
      <a class="botao" href="javascript:history.back()">Voltar</a>
    </div>
  </article>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
