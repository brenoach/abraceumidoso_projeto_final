<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';

require_login();
require_role('V');

function painel_foto_src(?string $foto): string
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
$minhasVisitas = [];
$idososDisponiveis = [];

$sqlMinhasVisitas = "SELECT a.dataAgendamento,
                            a.horaAgendamento,
                            a.status,
                            p.nomePessoa AS nomeIdoso,
                            p.fotoPerfil AS fotoIdoso,
                            p.sobre AS hobbiesIdoso,
                            inst.nomeInstituicao
                     FROM agendamento a
                     INNER JOIN idoso i ON a.idIdoso = i.idIdoso
                     INNER JOIN pessoa p ON i.idPessoa = p.idPessoa
                     INNER JOIN instituicao inst ON inst.idInstituicao = i.idInstituicao
                     WHERE a.idVoluntario = ?
                     ORDER BY a.dataAgendamento ASC, a.horaAgendamento ASC";
$stmtMinhas = $db->prepare($sqlMinhasVisitas);
$stmtMinhas->bind_param('i', $user['id']);
$stmtMinhas->execute();
$resultMinhas = $stmtMinhas->get_result();
$minhasVisitas = $resultMinhas ? $resultMinhas->fetch_all(MYSQLI_ASSOC) : [];
$stmtMinhas->close();

$sqlIdosos = "SELECT i.idIdoso,
                     p.nomePessoa,
                     p.fotoPerfil,
                     p.sobre,
                     inst.nomeInstituicao
              FROM idoso i
              INNER JOIN pessoa p ON i.idPessoa = p.idPessoa
              INNER JOIN instituicao inst ON i.idInstituicao = inst.idInstituicao
              WHERE i.aceitaVisita = 1
              ORDER BY p.nomePessoa ASC";
$resultIdosos = $db->query($sqlIdosos);
if ($resultIdosos) {
    $idososDisponiveis = $resultIdosos->fetch_all(MYSQLI_ASSOC);
}
?>
<main class="page container">
  <section class="card fade-in" style="margin-bottom: 24px;">
    <h1 class="section-title">Olá, <?= h($user['nome']) ?>!</h1>
    <p class="muted">Aqui você encontra sua agenda e os idosos disponíveis para receber visitas.</p>
    <div class="actions-row">
      <a class="botao" href="agenda.php">Minha agenda</a>
      <a class="botao" href="agendar_visita.php">Agendar visita</a>
    </div>
  </section>

  <section class="card fade-in" style="margin-bottom: 24px;">
    <h2 style="margin-top:0;">Minha agenda</h2>
    <?php if (!$minhasVisitas): ?>
      <p class="muted">Nenhuma visita marcada ainda.</p>
    <?php else: ?>
      <div class="visit-schedule-list compact">
        <?php foreach ($minhasVisitas as $visita): ?>
          <article class="visit-schedule-item visit-schedule-item-compact">
            <img class="visit-schedule-photo compact" src="<?= h(painel_foto_src($visita['fotoIdoso'] ?? '')) ?>" alt="Foto de <?= h($visita['nomeIdoso']) ?>">
            <div class="visit-schedule-content">
              <div class="visit-schedule-topline">
                <h3><?= h($visita['nomeIdoso']) ?></h3>
                <span class="visit-status-badge visit-status-<?= strtolower($visita['status']) ?>"><?= h($visita['status']) ?></span>
              </div>
              <p><strong>Quando:</strong> <?= h(date('d/m/Y', strtotime($visita['dataAgendamento']))) ?> às <?= h(substr($visita['horaAgendamento'], 0, 5)) ?></p>
              <p><strong>Instituição:</strong> <?= h($visita['nomeInstituicao']) ?></p>
              <p><strong>Hobbies / interesses:</strong> <?= h(trim((string) ($visita['hobbiesIdoso'] ?? '')) !== '' ? $visita['hobbiesIdoso'] : 'Não informado.') ?></p>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <section class="fade-in">
    <h2 style="margin-top:0;">Idosos disponíveis para visita</h2>
    <?php if (!$idososDisponiveis): ?>
      <section class="card">
        <p class="muted">No momento não há idosos disponíveis para visita.</p>
      </section>
    <?php else: ?>
      <div class="visit-elder-grid">
        <?php foreach ($idososDisponiveis as $idoso): ?>
          <article class="card visit-elder-card">
            <img class="visit-elder-photo" src="<?= h(painel_foto_src($idoso['fotoPerfil'] ?? '')) ?>" alt="Foto de <?= h($idoso['nomePessoa']) ?>">
            <h3><?= h($idoso['nomePessoa']) ?></h3>
            <p><strong>Instituição:</strong> <?= h($idoso['nomeInstituicao']) ?></p>
            <p><strong>Hobbies / interesses:</strong> <?= h(trim((string) ($idoso['sobre'] ?? '')) !== '' ? $idoso['sobre'] : 'Não informado.') ?></p>
            <div class="actions-row">
              <a class="botao" href="agendar_visita.php?id=<?= (int) $idoso['idIdoso'] ?>">Agendar visita</a>
            </div>
          </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
