<?php
/**
 * Cabeçalho padrão do sistema.
 * Carrega a sessão, links do menu e mensagens flash.
 */
require_once dirname(__DIR__, 2) . '/connection/config.php';
require_once dirname(__DIR__, 2) . '/connection/session.php';

$flash = flash_get();
$user = current_user();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= h(APP_NAME) ?></title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Asap:wght@400;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="../assets/css/style.css">
  <script defer src="../assets/js/script.js"></script>
</head>
<body>
<header class="cabecalho">
  <a href="home.php" class="logo"><img src="../assets/img/logo.png" alt="Logo do projeto"></a>
  <nav class="nav-menu">
    <ul class="top-links">
      <?php if (!$user): ?>
        <li><a href="cadastro_voluntario.php">Cadastrar</a></li>
        <li><a href="login.php">Login</a></li>
      <?php else: ?>
        <li><a href="dashboard.php">👤 <?= h($user['nome']) ?></a></li>
        <li><a href="logout.php">Sair</a></li>
      <?php endif; ?>
    </ul>
    <ul class="main-links">
      <li><a href="home.php">Início</a></li>
      <?php if ($user): ?>
        <li><a href="dashboard.php">Painel</a></li>
        <li><a href="agenda.php">Agenda</a></li>
        <li><a href="cartas.php">Cartas</a></li>
        <!-- <li><a href="informacoes.php">Informações</a></li> -->
      <?php endif; ?>
      <li><a href="contatos.php">Fale conosco</a></li>
    </ul>
  </nav>
</header>
<!-- <div class="linha-inferior"></div> -->
<?php if ($flash): ?>
  <div class="container">
    <div data-auto-close="true" class="alert <?= $flash['tipo'] === 'error' ? 'error' : 'success' ?>">
      <?= h($flash['mensagem']) ?>
    </div>
  </div>
<?php endif; ?>
