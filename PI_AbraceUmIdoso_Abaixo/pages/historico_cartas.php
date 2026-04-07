<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_role('V');
$data = $_GET['data'] ?? '';
$sql = "SELECT c.idCarta, c.dataCarta, c.statusCarta, p.nomePessoa AS nomeIdoso, inst.nomeInstituicao
        FROM carta c
        JOIN idoso i ON i.idIdoso = c.idIdoso
        JOIN pessoa p ON p.idPessoa = i.idPessoa
        JOIN instituicao inst ON inst.idInstituicao = i.idInstituicao
        WHERE c.idVoluntario = ?";
if ($data !== '') $sql .= " AND DATE(c.dataCarta) = ?";
$sql .= ' ORDER BY c.dataCarta DESC';
$stmt = db()->prepare($sql);
$vol = (int)current_user()['id'];
if ($data !== '') { $stmt->bind_param('is', $vol, $data); } else { $stmt->bind_param('i', $vol); }
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<main class="page container">
  <h1 class="section-title">Histórico de cartas por data</h1>
  <form method="GET" class="form-box" style="max-width:420px; margin-left:0;"><label>Filtrar por data</label><input type="date" name="data" value="<?= h($data) ?>"><div class="actions-row"><button type="submit">Filtrar</button><a class="botao" href="escrever_carta.php">Nova carta</a></div></form>
  <section class="cards-grid fade-in">
    <?php if (!$rows): ?><article class="card"><p>Nenhuma carta encontrada.</p></article><?php else: foreach($rows as $r): ?><article class="card carta"><h2>Para: <?= h($r['nomeIdoso']) ?></h2><p>Instituição: <?= h($r['nomeInstituicao']) ?></p><p>Data: <?= h($r['dataCarta']) ?></p><p>Status: <?= h($r['statusCarta']) ?></p><a class="card-button" href="visualizar_carta.php?id=<?= (int)$r['idCarta'] ?>">Abrir</a></article><?php endforeach; endif; ?>
  </section>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
