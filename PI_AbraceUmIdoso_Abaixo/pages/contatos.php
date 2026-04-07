<?php
require_once dirname(__DIR__, 2) . '/connection/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
?>

<main class="page container">
  <section class="banner fade-in">
    <h1>Como você prefere falar com a gente?</h1>
    <p>Estamos disponíveis em diferentes canais para ouvir você com carinho e atenção.</p>
  </section>

  <section class="cards-grid fade-in" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
    
    <article class="card center">
      <img src="../assets/img/insta.png" alt="Instagram" style="max-width: 72px; margin: 0 auto 12px;">
      <h2>Instagram</h2>
      <p>@abraceumidoso</p>
    </article>

    <article class="card center">
      <img src="../assets/img/email.png" alt="E-mail" style="max-width: 72px; margin: 0 auto 12px;">
      <h2>E-mail</h2>
      <p>abraceumidoso@gmail.com</p>
    </article>

    <article class="card center">
      <img src="../assets/img/whatsapp.png" alt="WhatsApp" style="max-width: 72px; margin: 0 auto 12px;">
      <h2>WhatsApp</h2>
      <p>(11) 1111-1111</p>
    </article>

    <article class="card center">
      <img src="../assets/img/telefone.png" alt="Telefone" style="max-width: 72px; margin: 0 auto 12px;">
      <h2>Telefone</h2>
      <p>(11) 1111-1111</p>
    </article>

  </section>
</main>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>