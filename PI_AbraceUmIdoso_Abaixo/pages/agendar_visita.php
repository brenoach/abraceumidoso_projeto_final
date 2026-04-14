<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';

require_login();
require_role('V');

function visita_foto_src(?string $foto): string
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
$idIdosoSelecionado = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$idosos = [];
$idosoSelecionado = null;
$disponibilidades = [];

$sqlIdosos = "SELECT
                i.idIdoso,
                i.aceitaVisita,
                p.nomePessoa,
                p.fotoPerfil,
                p.sobre,
                inst.nomeInstituicao,
                COALESCE(COUNT(d.idDisponibilidade), 0) AS totalDisponibilidades
              FROM idoso i
              INNER JOIN pessoa p ON p.idPessoa = i.idPessoa
              INNER JOIN instituicao inst ON inst.idInstituicao = i.idInstituicao
              LEFT JOIN disponibilidade d ON d.idoso_idIdoso = i.idIdoso
              WHERE i.aceitaVisita = 1
              GROUP BY i.idIdoso, i.aceitaVisita, p.nomePessoa, p.fotoPerfil, p.sobre, inst.nomeInstituicao
              ORDER BY p.nomePessoa ASC";

$resultIdosos = $db->query($sqlIdosos);
if ($resultIdosos) {
    $idosos = $resultIdosos->fetch_all(MYSQLI_ASSOC);
}

if ($idIdosoSelecionado > 0) {
    foreach ($idosos as $idoso) {
        if ((int) $idoso['idIdoso'] === $idIdosoSelecionado) {
            $idosoSelecionado = $idoso;
            break;
        }
    }
}

if (!$idosoSelecionado && $idosos) {
    $idosoSelecionado = $idosos[0];
    $idIdosoSelecionado = (int) $idosoSelecionado['idIdoso'];
}

if ($idIdosoSelecionado > 0) {
    $sqlDispo = "SELECT dia_semana, hora_inicio, hora_fim
                 FROM disponibilidade
                 WHERE idoso_idIdoso = ?
                 ORDER BY FIELD(dia_semana, 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado', 'Domingo'), hora_inicio";

    $stmtDispo = $db->prepare($sqlDispo);
    if ($stmtDispo) {
        $stmtDispo->bind_param('i', $idIdosoSelecionado);
        $stmtDispo->execute();
        $resultDispo = $stmtDispo->get_result();
        $disponibilidades = $resultDispo ? $resultDispo->fetch_all(MYSQLI_ASSOC) : [];
        $stmtDispo->close();
    }
}
?>
<main class="page container">
  <h1 class="section-title">Agendar visita</h1>
  <p class="muted">Escolha um idoso disponível para visita e agende dentro da sua agenda permitida.</p>

  <?php if (!$idosos): ?>
    <section class="card fade-in">
      <p class="muted">No momento não há idosos disponíveis para receber visitas.</p>
      <div class="actions-row">
        <a class="botao" href="agenda.php">Ver minha agenda</a>
      </div>
    </section>
  <?php else: ?>
    <section class="visit-layout fade-in">
      <article class="card visit-person-card">
        <form method="GET" class="visit-selector-form">
          <label for="id">Idoso disponível</label>
          <select name="id" id="id" onchange="this.form.submit()">
            <?php foreach ($idosos as $idoso): ?>
              <option value="<?= (int) $idoso['idIdoso'] ?>" <?= (int) $idoso['idIdoso'] === $idIdosoSelecionado ? 'selected' : '' ?>>
                <?= h($idoso['nomePessoa']) ?> — <?= h($idoso['nomeInstituicao']) ?>
              </option>
            <?php endforeach; ?>
          </select>
          <noscript><button type="submit">Carregar</button></noscript>
        </form>

        <?php if ($idosoSelecionado): ?>
          <div class="visit-person-header">
            <img class="visit-person-photo" src="<?= h(visita_foto_src($idosoSelecionado['fotoPerfil'] ?? '')) ?>" alt="Foto de <?= h($idosoSelecionado['nomePessoa']) ?>">
            <div>
              <h2><?= h($idosoSelecionado['nomePessoa']) ?></h2>
              <p><strong>Instituição:</strong> <?= h($idosoSelecionado['nomeInstituicao']) ?></p>
              <p><strong>Hobbies / interesses:</strong> <?= h(trim((string) ($idosoSelecionado['sobre'] ?? '')) !== '' ? $idosoSelecionado['sobre'] : 'Não informado.') ?></p>
            </div>
          </div>

          <div class="visit-availability-box">
            <h3>Horários cadastrados</h3>
            <?php if ($disponibilidades): ?>
              <ul class="visit-availability-list">
                <?php foreach ($disponibilidades as $disponibilidade): ?>
                  <li>
                    <strong><?= h($disponibilidade['dia_semana']) ?></strong>
                    <span><?= h(substr($disponibilidade['hora_inicio'], 0, 5)) ?> às <?= h(substr($disponibilidade['hora_fim'], 0, 5)) ?></span>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <p class="muted">Este idoso ainda não possui horários específicos cadastrados. O agendamento continuará sujeito à aprovação da instituição.</p>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </article>

      <article class="card">
        <h2>Confirmar visita</h2>
        <form method="POST" action="../actions/salvar_agendamento.php" class="form-box visit-form-box" style="margin:0; max-width:none; box-shadow:none; padding:0; background:transparent;">
          <input type="hidden" name="idIdoso" value="<?= (int) $idIdosoSelecionado ?>">

          <label for="data_visita">Data da visita</label>
          <input type="date" name="data_visita" id="data_visita" min="<?= date('Y-m-d') ?>" required>

          <label for="hora_visita">Horário</label>
          <input type="time" name="hora_visita" id="hora_visita" required>

          <div class="actions-row">
            <button type="submit">Solicitar agendamento</button>
            <a class="botao" href="agenda.php">Ver minha agenda</a>
          </div>
        </form>
      </article>
    </section>
  <?php endif; ?>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
