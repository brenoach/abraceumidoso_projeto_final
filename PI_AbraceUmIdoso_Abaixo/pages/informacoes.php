<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_login();
$data = $_GET['data'] ?? '';
$where = '';
if ($data !== '') { $where = " WHERE DATE(c.dataCarta) = '" . db()->real_escape_string($data) . "'"; }
$cartas = db()->query("SELECT c.dataCarta, c.statusCarta, p.nomePessoa AS idoso, inst.nomeInstituicao
                       FROM carta c
                       JOIN idoso i ON i.idIdoso = c.idIdoso
                       JOIN pessoa p ON p.idPessoa = i.idPessoa
                       JOIN instituicao inst ON inst.idInstituicao = i.idInstituicao
                       $where
                       ORDER BY c.dataCarta DESC");
?>
<main class="page container">
  <h1 class="section-title">Visualizar informações por data</h1>
  <form method="GET" class="form-box" style="max-width:420px; margin-left:0;"><label>Data</label><input type="date" name="data" value="<?= h($data) ?>"><div class="actions-row"><button type="submit">Consultar</button></div></form>
  <div class="table-wrap fade-in">
    <table>
      <thead><tr><th>Data</th><th>Idoso</th><th>Instituição</th><th>Status da carta</th></tr></thead>
      <tbody><?php while($r = $cartas->fetch_assoc()): ?><tr><td><?= h($r['dataCarta']) ?></td><td><?= h($r['idoso']) ?></td><td><?= h($r['nomeInstituicao']) ?></td><td><?= h($r['statusCarta']) ?></td></tr><?php endwhile; ?></tbody>
    </table>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
