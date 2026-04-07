<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_login();
$user = current_user();
if (!in_array($user['tipo'], ['I', 'F'], true)) exit('Acesso negado.');
$idInstituicao = $user['tipo'] === 'I' ? (int)$user['id'] : (int)$user['idInstituicao'];
$stmt = db()->prepare("SELECT c.idCarta, c.dataCarta, c.statusCarta,
                             pv.nomePessoa AS nomeVoluntario,
                             pi.nomePessoa AS nomeIdoso,
                             inst.nomeInstituicao
                      FROM carta c
                      JOIN voluntario v ON v.idVoluntario = c.idVoluntario
                      JOIN pessoa pv ON pv.idPessoa = v.idPessoa
                      JOIN idoso i ON i.idIdoso = c.idIdoso
                      JOIN pessoa pi ON pi.idPessoa = i.idPessoa
                      JOIN instituicao inst ON inst.idInstituicao = i.idInstituicao
                      WHERE inst.idInstituicao = ?
                      ORDER BY c.dataCarta DESC");
$stmt->bind_param('i', $idInstituicao);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<main class="page container">
  <h1 class="section-title">Recebimento da carta</h1>
  <p class="muted">Cartas destinadas aos idosos da instituição.</p>
  <section class="cards-grid fade-in">
    <?php if (!$rows): ?><article class="card"><p>Nenhuma carta recebida.</p></article><?php else: foreach($rows as $r): ?><article class="card carta"><h2>Para: <?= h($r['nomeIdoso']) ?></h2><p>De: <?= h($r['nomeVoluntario']) ?></p><p>Data: <?= h($r['dataCarta']) ?></p><p>Status: <?= h($r['statusCarta']) ?></p><a class="card-button" href="visualizar_carta.php?id=<?= (int)$r['idCarta'] ?>">Abrir</a></article><?php endforeach; endif; ?>
  </section>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
