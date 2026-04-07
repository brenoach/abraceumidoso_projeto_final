<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/ValidarEntradas.php';

require_login();
$user = current_user();
$nome = ValidarEntradas::texto($_POST['nome'] ?? '');
$email = ValidarEntradas::email($_POST['email'] ?? '');
$sobre = ValidarEntradas::texto($_POST['sobre'] ?? '');
$db = db();
try {
 if ($user['tipo'] === 'V') {
  $stmt = $db->prepare('UPDATE pessoa p JOIN voluntario v ON v.idPessoa = p.idPessoa JOIN contato c ON c.idContato = v.idContato SET p.nomePessoa = ?, p.sobre = ?, c.email = ? WHERE v.idVoluntario = ?');
  $id = (int)$user['id'];
  $stmt->bind_param('sssi', $nome, $sobre, $email, $id);
 } elseif ($user['tipo'] === 'I') {
  $stmt = $db->prepare('UPDATE instituicao i JOIN contato c ON c.idContato = i.idContato SET i.nomeInstituicao = ?, c.email = ? WHERE i.idInstituicao = ?');
  $id = (int)$user['id'];
  $stmt->bind_param('ssi', $nome, $email, $id);
 } else {
  $stmt = $db->prepare('UPDATE pessoa p JOIN funcionario f ON f.idPessoa = p.idPessoa JOIN contato c ON c.idContato = f.idContato SET p.nomePessoa = ?, f.cargo = ?, c.email = ? WHERE f.idFuncionario = ?');
  $id = (int)$user['id'];
  $stmt->bind_param('sssi', $nome, $sobre, $email, $id);
 }
 $stmt->execute();
 $_SESSION['user']['nome'] = $nome;
 $_SESSION['user']['email'] = $email;
 flash_set('success', 'Dados atualizados com sucesso.');
} catch (Throwable $e) {
 flash_set('error', 'Erro ao atualizar dados: ' . $e->getMessage());
}
header('Location: ../pages/editar_perfil.php');
