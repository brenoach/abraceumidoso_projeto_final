<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_role('V');

$inst_id = isset($_GET['inst']) ? (int)$_GET['inst'] : 0;
$insts = db()->query('SELECT idInstituicao, nomeInstituicao FROM instituicao ORDER BY nomeInstituicao');
$idosos = [];
if ($inst_id > 0) {
  $stmt = db()->prepare('SELECT i.idIdoso, p.nomePessoa
                         FROM idoso i
                         JOIN pessoa p ON p.idPessoa = i.idPessoa
                         WHERE i.idInstituicao = ? AND i.aceitaCarta = 1
                         ORDER BY p.nomePessoa');
  $stmt->bind_param('i', $inst_id);
  $stmt->execute();
  $idosos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>
<main class="page container">
  <form class="form-box paper fade-in" action="../actions/salvar_carta.php" method="POST">
    <h1 class="section-title">Envie sua carta</h1>
    <label>De</label><input type="text" value="<?= h(current_user()['nome']) ?>" disabled>
    <label>Instituição</label>
    <select name="inst" onchange="window.location='escrever_carta.php?inst='+this.value" required>
      <option value="">-- Selecione --</option>
      <?php while($row = $insts->fetch_assoc()): ?><option value="<?= (int)$row['idInstituicao'] ?>" <?= $inst_id === (int)$row['idInstituicao'] ? 'selected' : '' ?>><?= h($row['nomeInstituicao']) ?></option><?php endwhile; ?>
    </select>
    <label>Para</label>
    <select id="idosoSelect" name="idIdoso" <?= $inst_id > 0 ? '' : 'disabled' ?> required>
      <option value="">-- Selecione o idoso --</option>
      <?php foreach($idosos as $i): ?><option value="<?= (int)$i['idIdoso'] ?>"><?= h($i['nomePessoa']) ?></option><?php endforeach; ?>
    </select>
    <label>Texto da carta</label>
    <textarea id="textoCarta" name="textoCarta" placeholder="Escreva com carinho..." required></textarea>
    <div class="actions-row">
      <button type="button" onclick="abrirPreviewCarta()">Pré-visualizar</button>
      <button type="submit">Enviar carta</button>
    </div>
  </form>
</main>
<div class="modal-backdrop" id="previewModal"><div class="modal-content card carta"><h2>Pré-visualização da carta</h2><p><strong>Destinatário:</strong> <span id="previewDestinatario"></span></p><hr><p id="previewMensagem" style="white-space:pre-wrap;"></p><div class="actions-row"><button type="button" onclick="fecharPreviewCarta()">Fechar</button></div></div></div>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
