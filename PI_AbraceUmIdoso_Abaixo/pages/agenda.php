<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';

require_login();

function agenda_foto_src(?string $foto): string
{
    $foto = trim((string) $foto);
    if ($foto === '') {
        return '../assets/img/fotoPerfil.png';
    }

    if (preg_match('#^(https?:)?//#i', $foto)) {
        return $foto;
    }

    if (str_starts_with($foto, '../') || str_starts_with($foto, './') || str_starts_with($foto, '/')) {
        return $foto;
    }

    return '../' . ltrim($foto, '/');
}

$db = db();
$user = current_user();
$data = $_GET['data'] ?? date('Y-m-d');
$rows = [];

$sql = "SELECT a.idAgendamento,
               a.dataAgendamento,
               a.horaAgendamento,
               a.status,
               i.aceitaVisita,
               p.nomePessoa AS nomeIdoso,
               p.fotoPerfil AS fotoIdoso,
               p.sobre AS hobbiesIdoso,
               pv.nomePessoa AS nomeVoluntario,
               inst.nomeInstituicao
        FROM agendamento a
        INNER JOIN idoso i ON i.idIdoso = a.idIdoso
        INNER JOIN pessoa p ON p.idPessoa = i.idPessoa
        INNER JOIN voluntario v ON v.idVoluntario = a.idVoluntario
        INNER JOIN pessoa pv ON pv.idPessoa = v.idPessoa
        INNER JOIN instituicao inst ON inst.idInstituicao = i.idInstituicao
        WHERE a.dataAgendamento = ?";

$types = 's';
$params = [$data];

if ($user['tipo'] === 'V') {
    $sql .= ' AND a.idVoluntario = ?';
    $types .= 'i';
    $params[] = (int) $user['id'];
} elseif (in_array($user['tipo'], ['I', 'F'], true)) {
    $sql .= ' AND i.idInstituicao = ?';
    $types .= 'i';
    $params[] = (int) $user['idInstituicao'];
}

$sql .= ' ORDER BY a.horaAgendamento ASC, p.nomePessoa ASC';

$stmt = $db->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();
$rows = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$stmt->close();
?>
<main class="page container">
  <h1 class="section-title">Agenda de visitas</h1>
  <p class="muted">
    <?php if ($user['tipo'] === 'V'): ?>
      Consulte suas visitas agendadas e acompanhe o status de aprovação.
    <?php else: ?>
      Visualize as visitas da instituição e aprove somente quando o idoso estiver disponível.
    <?php endif; ?>
  </p>

  <form method="GET" class="form-box visit-filter-box" style="max-width:560px; margin-left:0;">
    <label for="data">Filtrar por data</label>
    <input type="date" name="data" id="data" value="<?= h($data) ?>">
    <div class="actions-row">
      <button type="submit">Filtrar</button>
      <?php if ($user['tipo'] === 'V'): ?>
        <a class="botao" href="agendar_visita.php">Novo agendamento</a>
      <?php endif; ?>
    </div>
  </form>

  <?php if (!$rows): ?>
    <section class="card fade-in">
      <p class="muted">Nenhuma visita encontrada para a data selecionada.</p>
    </section>
  <?php else: ?>
    <section class="visit-schedule-list fade-in">
      <?php foreach ($rows as $r): ?>
        <article class="card visit-schedule-item">
          <div class="visit-schedule-main">
            <img class="visit-schedule-photo" src="<?= h(agenda_foto_src($r['fotoIdoso'] ?? '')) ?>" alt="Foto de <?= h($r['nomeIdoso']) ?>">
            <div class="visit-schedule-content">
              <div class="visit-schedule-topline">
                <h2><?= h($r['nomeIdoso']) ?></h2>
                <span class="visit-status-badge visit-status-<?= strtolower($r['status']) ?>"><?= h($r['status']) ?></span>
              </div>

              <p><strong>Horário:</strong> <?= h(date('d/m/Y', strtotime($r['dataAgendamento']))) ?> às <?= h(substr($r['horaAgendamento'], 0, 5)) ?></p>
              <p><strong>Voluntário:</strong> <?= h($r['nomeVoluntario']) ?></p>
              <p><strong>Instituição:</strong> <?= h($r['nomeInstituicao']) ?></p>
              <?php if ($user['tipo'] === 'V'): ?>
                <p><strong>Hobbies / interesses:</strong> <?= h(trim((string) ($r['hobbiesIdoso'] ?? '')) !== '' ? $r['hobbiesIdoso'] : 'Não informado.') ?></p>
              <?php endif; ?>
              <?php if (in_array($user['tipo'], ['I', 'F'], true)): ?>
                <p><strong>Disponível para visita:</strong> <?= (int) $r['aceitaVisita'] === 1 ? 'Sim' : 'Não' ?></p>
              <?php endif; ?>
            </div>
          </div>

          <?php if (in_array($user['tipo'], ['I', 'F'], true) && $r['status'] === 'Pendente'): ?>
            <div class="actions-row visit-admin-actions">
              <a class="botao<?= (int) $r['aceitaVisita'] !== 1 ? ' visit-action-disabled' : '' ?>" href="<?= (int) $r['aceitaVisita'] === 1 ? '../actions/processa_visita.php?id=' . (int) $r['idAgendamento'] . '&acao=aprovar' : '#' ?>" <?= (int) $r['aceitaVisita'] !== 1 ? 'aria-disabled="true" onclick="return false;"' : '' ?>>Aprovar</a>
              <a class="botao visit-action-secondary" href="../actions/processa_visita.php?id=<?= (int) $r['idAgendamento'] ?>&acao=recusar">Recusar</a>
            </div>
          <?php endif; ?>
        </article>
      <?php endforeach; ?>
    </section>
  <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
