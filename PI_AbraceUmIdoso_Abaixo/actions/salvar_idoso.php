<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/ValidarEntradas.php';

require_login();
$nome = ValidarEntradas::texto($_POST['nomePessoa'] ?? '');
$cpf = ValidarEntradas::numeros($_POST['cpf'] ?? '');
$idInstituicao = (int)($_POST['idInstituicao'] ?? 0);
if ($nome === '' || $cpf === '' || $idInstituicao <= 0) {
    flash_set('error', 'Preencha os dados do idoso.');
    header('Location: ../pages/cadastro_idoso.php');
    exit;
}
$db = db();
$db->begin_transaction();
try {
  $p = $db->prepare('INSERT INTO pessoa (nomePessoa, cpf, dataNascimento, fotoPerfil, sobre) VALUES (?,?,?,?,?)');
  $data = $_POST['dataNascimento'] ?? date('Y-m-d');
  $foto = 'assets/img/fotoPerfil.png';
  $sobre = ValidarEntradas::texto($_POST['sobre'] ?? '');
  $p->bind_param('sssss', $nome, $cpf, $data, $foto, $sobre);
  $p->execute();
  $idPessoa = $db->insert_id;
  $i = $db->prepare('INSERT INTO idoso (idPessoa, idInstituicao, aceitaVisita, aceitaCarta) VALUES (?,?,?,?)');
  $visita = (int)($_POST['aceitaVisita'] ?? 1);
  $carta = (int)($_POST['aceitaCarta'] ?? 1);
  $i->bind_param('iiii', $idPessoa, $idInstituicao, $visita, $carta);
  $i->execute();
  $db->commit();
  flash_set('success', 'Idoso cadastrado com sucesso.');
} catch (Throwable $e) {
  $db->rollback();
  flash_set('error', 'Erro ao cadastrar idoso: ' . $e->getMessage());
}
header('Location: ../pages/cadastro_idoso.php');
