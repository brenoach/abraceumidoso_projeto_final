<?php

require_once '../includes/auth.php';

require_once '../includes/db.php';
require_once '../includes/helpers.php'; 
require_once ROOT_PATH .'/includes/header.php';

// 2. Identificação de quem está logado
// $id_logado = $_SESSION['idPessoa']; 
$id_inst = $_SESSION['idInstituicao']; 

// =========================================================================
// 3. BUSCA DOS RESIDENTES (Aqui está o código que você perguntou!)
// Isso vai pegar todos os idosos que pertencem à instituição logada
// =========================================================================
$sqlIdosos = "SELECT i.idIdoso, p.nomePessoa, p.fotoPerfil, p.sobre 
              FROM idoso i 
              JOIN pessoa p ON i.idPessoa = p.idPessoa 
              WHERE i.idInstituicao = ?";
              
$stmtIdosos = $pdo->prepare($sqlIdosos);
$stmtIdosos->execute([$id_inst]);

// Guardamos tudo na variável $listaIdosos para o HTML usar lá embaixo
$listaIdosos = $stmtIdosos->fetchAll(PDO::FETCH_ASSOC);


// (Nota: Se você tiver a query que busca as Visitas/Agenda, ela entra aqui!)
// =========================================================================
// 4. BUSCA DOS AGENDAMENTOS (VISITAS)
// Busca todas as visitas marcadas para os idosos desta instituição
// =========================================================================
$sqlVisitas = "SELECT 
                a.idAgendamento, 
                a.dataAgendamento, 
                a.horaAgendamento, 
                a.status,
                p_idoso.nomePessoa AS nome_idoso,
                p_vol.nomePessoa AS nome_voluntario
               FROM agendamento a
               JOIN idoso i ON a.idIdoso = i.idIdoso
               JOIN pessoa p_idoso ON i.idPessoa = p_idoso.idPessoa
               JOIN voluntario v ON a.idVoluntario = v.idVoluntario
               JOIN pessoa p_vol ON v.idPessoa = p_vol.idPessoa
               WHERE i.idInstituicao = ?
               ORDER BY a.dataAgendamento ASC, a.horaAgendamento ASC";

$stmtVisitas = $pdo->prepare($sqlVisitas);
$stmtVisitas->execute([$id_inst]);

// Cria a variável $visitas que o seu HTML está pedindo!
$visitas = $stmtVisitas->fetchAll(PDO::FETCH_ASSOC);    

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel Administrativo</title>
    
<body>

    <div class="container">
    <h1 style="color: #5b3a26;">Painel Administrativo</h1>
    
    <div class="atalhos-grid">
        <div class="card-atalho">
            <span>👴👵</span>
            <h3>Nossos Residentes</h3>
            <p>Cadastre novos idosos na sua unidade.</p>
            <a href="cadastrar_idoso.php" class="btn-principal">Cadastrar Novo</a>
        </div>

        <div class="card-atalho">
            <span>📋</span>
            <h3>Lista de Residentes</h3>
            <p>Gerencie quem já está cadastrado.</p>
            <a href="listar_idosos.php" class="btn-principal">Ver Lista Completa</a>
        </div>
    </div>
    
    <div class="tabela-container">
    <h2>📅 Agenda de Visitas</h2>
    <table>
        <thead>
            <tr><th>Residente</th><th>Data</th><th>Status</th></tr>
        </thead>
        <tbody>
            <?php foreach ($visitas as $v): ?>
                <tr>
                    <td><?= $v['nome_idoso'] ?></td>
                    <td><?= date('d/m/Y', strtotime($v['dataAgendamento'])) ?></td>
                    <td><span class="badge badge-<?= strtolower($v['status']) ?>"><?= $v['status'] ?></span></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<h2 style="color: #5b3a26; margin-top: 40px; margin-bottom: 20px;">👴 Nossos Residentes</h2>

<div class="grid-idosos">
    <?php foreach ($listaIdosos as $idoso): ?>
        <div class="card-residente">
            <?php echo exibirFoto($idoso['fotoPerfil'], $idoso['nomePessoa']); ?>
            
            <h3><?= htmlspecialchars($idoso['nomePessoa']) ?></h3>
            <p class="sobre-resumo"><?= htmlspecialchars($idoso['sobre']) ?></p>
            
            <!-- <div class="agenda-bloco">
                <small><?=$idoso['agenda_completa'] ?: 'Sem horários' ?></small>
            </div> -->
            
            <a href="editar_idoso.php?id=<?= $idoso['idIdoso'] ?>" class="btn-detalhes">Ver Detalhes</a>
        </div>
    <?php endforeach; ?>
</div>

<style>
    /* O "Container" que segura os cards e diz quantos aparecem por linha */
    .grid-idosos {
        display: grid;
        /* Cria colunas automáticas com tamanho mínimo de 280px */
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 25px; /* Espaço entre os cards */
        margin-bottom: 50px;
    }

    /* O Estilo de cada Card individual */
    .card-residente {
        background: #fff;
        border-radius: 15px;
        padding: 25px;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0,0,0,0.08); /* Sombra suave */
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border: 1px solid #eee;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* Efeito de "levitar" quando passa o mouse */
    .card-residente:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(106, 27, 154, 0.15); /* Sombra roxa no hover */
    }

    /* Estilo da Foto redonda */
    .foto-perfil-idoso {
        width: 110px;
        height: 110px;
        border-radius: 50%; /* Faz ficar redondo */
        object-fit: cover; /* Não distorce a imagem */
        margin-bottom: 15px;
        border: 4px solid #fdf8f4; /* Borda creme */
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }

    .card-residente h3 {
        color: #5b3a26; /* Marrom */
        margin-bottom: 10px;
        font-size: 1.3em;
    }

    .sobre-resumo {
        color: #666;
        font-size: 0.9em;
        margin-bottom: 15px;
        /* Limita a 2 linhas de texto */
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .btn-detalhes {
        margin-top: auto; /* Empurra o botão para o final do card */
        background-color: #6a1b9a; /* Roxo */
        color: white;
        padding: 8px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: bold;
        font-size: 0.85em;
        transition: background 0.2s;
    }

    .btn-detalhes:hover {
        background-color: #5a178a;
    }
</style>
</body>

</html>