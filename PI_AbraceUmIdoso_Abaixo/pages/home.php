<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/layout_top.php';
?>
<main class="page container">
  <section class="banner fade-in">
    <h1>Seja um farol de carinho.</h1>
    <p>Conecte voluntários, instituições, funcionários e idosos em uma rede de afeto, visitas e cartas.</p>
    <div class="banner-actions">
      <a class="botao" href="cadastro_voluntario.php">Quero visitar</a>
      <a class="botao" href="cadastro_instituicao.php">Quero receber visitas</a>
      <a class="botao" href="login.php">Entrar no sistema</a>
    </div>
  </section>

  <section class="missao-section fade-in">
    <h1 class="section-title">Nossa missão</h1>
    <div class="missao-content">
      <div class="missao-item"><div class="icone-circulo"><img src="../assets/img/maos.png" alt="Mãos"></div><strong>Conexão</strong></div>
      <div class="missao-item"><div class="icone-circulo"><img src="../assets/img/idosos.png" alt="Idosos"></div><strong>Carinho</strong></div>
      <div class="missao-item"><div class="icone-circulo"><img src="../assets/img/pessoas.png" alt="Pessoas"></div><strong>Comunidade</strong></div>
    </div>
  </section>

  <section class="cards-grid fade-in">
    <article class="card center"><h2>Recuperação de Senha</h2><p>Login com sessão e recuperação de senha por token e e-mail.</p><a class="card-button" href="login.php">Acessar</a></article>
    <article class="card center"><h2>Cadastros</h2><p>Instituições, idosos, voluntários e funcionários usando o mesmo banco.</p><a class="card-button" href="cadastro_instituicao.php">Cadastrar</a></article>
    <article class="card center carta"><h2>Cartas</h2><p>Envio, visualização, histórico e recebimento mantendo a aparência de papel.</p><a class="card-button" href="cartas.php">Abrir módulo</a></article>
    <article class="card center"><h2>Visitas</h2><p>Agendamento, agenda por data e acompanhamento do status.</p><a class="card-button" href="agenda.php">Ver agenda</a></article>
  </section>
</main>
<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
