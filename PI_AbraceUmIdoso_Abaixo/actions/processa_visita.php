<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

require_login();

$user = current_user();
if (!in_array($user['tipo'] ?? '', ['I', 'F'], true)) {
    http_response_code(403);
    exit('Acesso negado.');
}

$idAgendamento = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$acao = $_GET['acao'] ?? '';
$novoStatus = null;

if ($acao === 'aprovar') {
    $novoStatus = 'Aprovado';
} elseif ($acao === 'recusar') {
    $novoStatus = 'Recusado';
}

if ($idAgendamento <= 0 || $novoStatus === null) {
    flash_set('error', 'Solicitação inválida para processamento da visita.');
    header('Location: ../pages/agenda.php');
    exit;
}

$db = db();

$sql = "SELECT a.idAgendamento, a.idIdoso, a.dataAgendamento, a.horaAgendamento, a.status, i.aceitaVisita, i.idInstituicao
        FROM agendamento a
        INNER JOIN idoso i ON i.idIdoso = a.idIdoso
        WHERE a.idAgendamento = ?
        LIMIT 1";
$stmt = $db->prepare($sql);
$stmt->bind_param('i', $idAgendamento);
$stmt->execute();
$agendamento = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$agendamento) {
    flash_set('error', 'Agendamento não encontrado.');
    header('Location: ../pages/agenda.php');
    exit;
}

if (($user['tipo'] === 'I' || $user['tipo'] === 'F') && (int) $agendamento['idInstituicao'] !== (int) ($user['idInstituicao'] ?? $user['id'] ?? 0)) {
    flash_set('error', 'Você não pode processar visitas de outra instituição.');
    header('Location: ../pages/agenda.php');
    exit;
}

if ($novoStatus === 'Aprovado' && (int) $agendamento['aceitaVisita'] !== 1) {
    flash_set('error', 'Este idoso não está disponível para receber visitas no momento.');
    header('Location: ../pages/agenda.php');
    exit;
}

if ($novoStatus === 'Aprovado') {
    $stmtConflito = $db->prepare("SELECT idAgendamento
                                 FROM agendamento
                                 WHERE idIdoso = ?
                                   AND dataAgendamento = ?
                                   AND horaAgendamento = ?
                                   AND status = 'Aprovado'
                                   AND idAgendamento <> ?
                                 LIMIT 1");
    $stmtConflito->bind_param('issi', $agendamento['idIdoso'], $agendamento['dataAgendamento'], $agendamento['horaAgendamento'], $idAgendamento);
    $stmtConflito->execute();
    $conflito = $stmtConflito->get_result()->fetch_assoc();
    $stmtConflito->close();

    if ($conflito) {
        flash_set('error', 'Já existe outra visita aprovada para este idoso nesse mesmo horário.');
        header('Location: ../pages/agenda.php');
        exit;
    }
}

$stmtUpdate = $db->prepare('UPDATE agendamento SET status = ? WHERE idAgendamento = ?');
$stmtUpdate->bind_param('si', $novoStatus, $idAgendamento);
$stmtUpdate->execute();
$stmtUpdate->close();

flash_set('success', 'Status da visita atualizado para ' . strtolower($novoStatus) . '.');
header('Location: ../pages/agenda.php?data=' . urlencode($agendamento['dataAgendamento']));
exit;
