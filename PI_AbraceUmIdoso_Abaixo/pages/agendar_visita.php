<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_role('V');

$idosos = db()->query("SELECT i.idIdoso, p.nomePessoa, inst.nomeInstituicao
                       FROM idoso i
                       JOIN pessoa p ON p.idPessoa = i.idPessoa
                       JOIN instituicao inst ON inst.idInstituicao = i.idInstituicao
                       WHERE i.aceitaVisita = 1
                       ORDER BY p.nomePessoa");
?>
<main class="page container">
  <form class="form-box fade-in" action="../actions/salvar_agendamento.php" method="POST">
    <h1 class="section-title">Agendar visita</h1>
    <div class="form-grid">
      <div><label>Idoso / instituição</label><select name="idIdoso" required><?php while($i = $idosos->fetch_assoc()): ?><option value="<?= (int)$i['idIdoso'] ?>"><?= h($i['nomePessoa']) ?> - <?= h($i['nomeInstituicao']) ?></option><?php endwhile; ?></select></div>
      <div><label>Data da visita</label><input type="date" name="dataAgendamento" required></div>
      <div><label>Horário</label><input type="time" name="horaAgendamento" required></div>
    </div>
    <div class="actions-row"><button type="submit">Salvar agendamento</button></div>
  </form>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
