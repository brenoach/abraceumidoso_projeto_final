<?php
/** Registra uma nova carta enviada pelo voluntário. */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/ValidarEntradas.php';

require_role('V');
$idVoluntario = (int)current_user()['id'];
$idIdoso = (int)($_POST['idIdoso'] ?? 0);
$texto = trim($_POST['textoCarta'] ?? '');
if ($idIdoso <= 0 || $texto === '') {
    flash_set('error', 'Selecione o destinatário e escreva a carta.');
    header('Location: ../pages/escrever_carta.php');
    exit;
}
$stmt = db()->prepare("INSERT INTO carta (textoCarta, dataCarta, statusCarta, idVoluntario, idIdoso) VALUES (?, NOW(), 'Enviada', ?, ?)");
$stmt->bind_param('sii', $texto, $idVoluntario, $idIdoso);
$stmt->execute();
flash_set('success', 'Carta enviada com sucesso.');
header('Location: ../pages/historico_cartas.php');
