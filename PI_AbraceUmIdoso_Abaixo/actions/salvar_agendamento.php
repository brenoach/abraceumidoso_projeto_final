<?php
/** Salva um novo agendamento de visita. */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/ValidarEntradas.php';

require_role('V');
$idVoluntario = (int)current_user()['id'];
$idIdoso = (int)($_POST['idIdoso'] ?? 0);
$data = $_POST['dataAgendamento'] ?? '';
$hora = $_POST['horaAgendamento'] ?? '';

if ($idIdoso <= 0 || $data === '' || $hora === '') {
    flash_set('error', 'Preencha os dados do agendamento.');
    header('Location: ../pages/agendar_visita.php');
    exit;
}

$stmt = db()->prepare("INSERT INTO agendamento (dataAgendamento, horaAgendamento, idVoluntario, idIdoso, status) VALUES (?, ?, ?, ?, 'Pendente')");
$stmt->bind_param('ssii', $data, $hora, $idVoluntario, $idIdoso);
$stmt->execute();
flash_set('success', 'Agendamento salvo com sucesso.');
header('Location: ../pages/agenda.php?data=' . urlencode($data));
