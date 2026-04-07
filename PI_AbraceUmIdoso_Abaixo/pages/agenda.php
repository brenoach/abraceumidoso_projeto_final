<?php
/** Visualiza a agenda de visitas filtrada por data. */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_login();

$data = $_GET['data'] ?? date('Y-m-d');
$user = current_user();

$sql = "SELECT a.idAgendamento, a.dataAgendamento, a.horaAgendamento, a.status,
               p.nomePessoa AS nomeIdoso,
               pv.nomePessoa AS nomeVoluntario,
               inst.nomeInstituicao
        FROM agendamento a
        JOIN idoso i ON i.idIdoso = a.idIdoso
        JOIN pessoa p ON p.idPessoa = i.idPessoa
        JOIN voluntario v ON v.idVoluntario = a.idVoluntario
        JOIN pessoa pv ON pv.idPessoa = v.idPessoa
        JOIN instituicao inst ON inst.idInstituicao = i.idInstituicao
        WHERE a.dataAgendamento = ?";

if ($user['tipo'] === 'V') {
    $sql .= ' AND a.idVoluntario = ' . (int)$user['id'];
}
if ($user['tipo'] === 'I') {
    $sql .= ' AND inst.idInstituicao = ' . (int)$user['id'];
}
if ($user['tipo'] === 'F') {
    $sql .= ' AND inst.idInstituicao = ' . (int)$user['idInstituicao'];
}
$sql .= ' ORDER BY a.horaAgendamento';

$stmt = db()->prepare($sql);
$stmt->bind_param('s', $data);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<main class="page container">
  <h1 class="section-title">Visualizar agenda de visitas</h1>
  <form method="GET" class="form-box" style="max-width:480px; margin-left:0;">
    <label>Filtrar por data</label>
    <input type="date" name="data" value="<?= h($data) ?>">
    <div class="actions-row">
      <button type="submit">Filtrar</button>
      <?php if ($user['tipo'] === 'V'): ?><a class="botao" href="agendar_visita.php">Novo agendamento</a><?php endif; ?>
    </div>
  </form>
  <div class="table-wrap fade-in">
    <table>
      <thead><tr><th>Hora</th><th>Idoso</th><th>Voluntário</th><th>Instituição</th><th>Status</th></tr></thead>
      <tbody>
      <?php if (!$rows): ?>
        <tr><td colspan="5">Nenhum registro na data selecionada.</td></tr>
      <?php else: foreach ($rows as $r): ?>
        <tr>
          <td><?= h($r['horaAgendamento']) ?></td>
          <td><?= h($r['nomeIdoso']) ?></td>
          <td><?= h($r['nomeVoluntario']) ?></td>
          <td><?= h($r['nomeInstituicao']) ?></td>
          <td><?= h($r['status']) ?></td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
