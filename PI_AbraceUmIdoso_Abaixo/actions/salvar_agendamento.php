<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

require_login();
require_role('V');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/agendar_visita.php');
    exit;
}

$db = db();
$user = current_user();
$idIdoso = isset($_POST['idIdoso']) ? (int) $_POST['idIdoso'] : 0;
$dataVisita = trim($_POST['data_visita'] ?? '');
$horaVisita = trim($_POST['hora_visita'] ?? '');

if ($idIdoso <= 0 || $dataVisita === '' || $horaVisita === '') {
    flash_set('error', 'Preencha todos os dados da visita.');
    header('Location: ../pages/agendar_visita.php' . ($idIdoso > 0 ? '?id=' . $idIdoso : ''));
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataVisita) || !preg_match('/^\d{2}:\d{2}$/', $horaVisita)) {
    flash_set('error', 'Data ou horário inválidos.');
    header('Location: ../pages/agendar_visita.php' . ($idIdoso > 0 ? '?id=' . $idIdoso : ''));
    exit;
}

if ($dataVisita < date('Y-m-d')) {
    flash_set('error', 'Escolha uma data de hoje em diante.');
    header('Location: ../pages/agendar_visita.php?id=' . $idIdoso);
    exit;
}

$stmtIdoso = $db->prepare("SELECT i.idIdoso, i.aceitaVisita, p.nomePessoa
                           FROM idoso i
                           INNER JOIN pessoa p ON p.idPessoa = i.idPessoa
                           WHERE i.idIdoso = ?");
$stmtIdoso->bind_param('i', $idIdoso);
$stmtIdoso->execute();
$idoso = $stmtIdoso->get_result()->fetch_assoc();
$stmtIdoso->close();

if (!$idoso || (int) $idoso['aceitaVisita'] !== 1) {
    flash_set('error', 'Este idoso não está disponível para novas visitas.');
    header('Location: ../pages/agendar_visita.php');
    exit;
}

$diaSemanaMap = [
    'Monday' => 'Segunda-feira',
    'Tuesday' => 'Terça-feira',
    'Wednesday' => 'Quarta-feira',
    'Thursday' => 'Quinta-feira',
    'Friday' => 'Sexta-feira',
    'Saturday' => 'Sábado',
    'Sunday' => 'Domingo',
];

$diaSemanaIngles = date('l', strtotime($dataVisita));
$diaSemana = $diaSemanaMap[$diaSemanaIngles] ?? null;

if ($diaSemana !== null) {
    $sqlDispo = "SELECT idDisponibilidade
                 FROM disponibilidade
                 WHERE idoso_idIdoso = ?
                   AND dia_semana = ?
                   AND ? >= hora_inicio
                   AND ? <= hora_fim
                 LIMIT 1";
    $stmtDispo = $db->prepare($sqlDispo);
    $horaCompleta = $horaVisita . ':00';
    $stmtDispo->bind_param('isss', $idIdoso, $diaSemana, $horaCompleta, $horaCompleta);
    $stmtDispo->execute();
    $horarioPermitido = $stmtDispo->get_result()->fetch_assoc();
    $stmtDispo->close();

    $stmtTemAgenda = $db->prepare('SELECT COUNT(*) AS total FROM disponibilidade WHERE idoso_idIdoso = ?');
    $stmtTemAgenda->bind_param('i', $idIdoso);
    $stmtTemAgenda->execute();
    $temAgenda = (int) ($stmtTemAgenda->get_result()->fetch_assoc()['total'] ?? 0);
    $stmtTemAgenda->close();

    if ($temAgenda > 0 && !$horarioPermitido) {
        flash_set('error', 'O horário escolhido não está dentro da disponibilidade cadastrada para este idoso.');
        header('Location: ../pages/agendar_visita.php?id=' . $idIdoso);
        exit;
    }
}

$stmtDuplicado = $db->prepare('SELECT idAgendamento FROM agendamento WHERE idIdoso = ? AND dataAgendamento = ? AND horaAgendamento = ? AND status IN (\'Pendente\', \'Aprovado\') LIMIT 1');
$horaCompleta = $horaVisita . ':00';
$stmtDuplicado->bind_param('iss', $idIdoso, $dataVisita, $horaCompleta);
$stmtDuplicado->execute();
$duplicado = $stmtDuplicado->get_result()->fetch_assoc();
$stmtDuplicado->close();

if ($duplicado) {
    flash_set('error', 'Já existe uma visita pendente ou aprovada para este idoso nesse mesmo dia e horário.');
    header('Location: ../pages/agendar_visita.php?id=' . $idIdoso);
    exit;
}

$stmt = $db->prepare('INSERT INTO agendamento (dataAgendamento, horaAgendamento, idVoluntario, idIdoso, status) VALUES (?, ?, ?, ?, ?)');
$status = 'Pendente';
$idVoluntario = (int) $user['id'];
$stmt->bind_param('ssiis', $dataVisita, $horaCompleta, $idVoluntario, $idIdoso, $status);
$stmt->execute();
$stmt->close();

flash_set('success', 'Visita solicitada com sucesso. Agora ela aguarda aprovação.');
header('Location: ../pages/agenda.php');
exit;
