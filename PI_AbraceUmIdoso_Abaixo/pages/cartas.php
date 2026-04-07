<?php require_once __DIR__ . '/../includes/layout_top.php'; require_login(); $tipo = current_user()['tipo']; ?>
<main class="page container">
  <h1 class="section-title">Troca de Cartas Virtuais</h1>
  <section class="cards-grid fade-in">
    <?php if ($tipo === 'V'): ?>
      <article class="card carta"><h2>Enviar carta</h2><p>Escolha um idoso e escreva sua mensagem com carinho.</p><a class="card-button" href="escrever_carta.php">Escrever agora</a></article>
      <article class="card carta"><h2>Histórico de cartas</h2><p>Consulte cartas enviadas e cartas respondidas por data.</p><a class="card-button" href="historico_cartas.php">Ver histórico</a></article>
    <?php else: ?>
      <article class="card carta"><h2>Recebimento de cartas</h2><p>Visualize cartas destinadas aos idosos e registre a resposta.</p><a class="card-button" href="recebimento_cartas.php">Abrir caixa</a></article>
    <?php endif; ?>
  </section>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
