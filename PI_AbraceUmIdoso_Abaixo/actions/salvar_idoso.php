<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/ValidarEntradas.php';

require_login();

$validador = new ValidarEntradas();

// 🔹 Pegando dados
$nome = $_POST['nomePessoa'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$data = $_POST['dataNascimento'] ?? date('Y-m-d');
$idInstituicao = (int)($_POST['idInstituicao'] ?? 0);
$sobre = $_POST['sobre'] ?? '';
$visita = (int)($_POST['aceitaVisita'] ?? 1);
$carta = (int)($_POST['aceitaCarta'] ?? 1);

// 🔹 Validações
$validador->obrigatorio('nome', $nome);
$validador->obrigatorio('cpf', $cpf);
$validador->numero('cpf', $cpf);

if ($idInstituicao <= 0) {
    $validador->obrigatorio('instituicao', '');
}

// 🔹 Se tiver erro
if ($validador->temErros()) {
    flash_set('error', 'Preencha corretamente os dados.');
    header('Location: ../pages/cadastro_idoso.php');
    exit;
}

// 🔹 Limpeza dos dados
$nome = htmlspecialchars(trim($nome));
$cpf = preg_replace('/[^0-9]/', '', $cpf);
$sobre = htmlspecialchars(trim($sobre));

// 🔹 Upload da imagem
$foto = 'assets/img/fotoPerfil.png';

if (!empty($_FILES['foto']['name'])) {

    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nomeArquivo = time() . '.' . $ext;

    $pasta = __DIR__ . '/../uploads/';
    $caminho = $pasta . $nomeArquivo;

    if (!is_dir($pasta)) {
        mkdir($pasta, 0777, true);
    }

    if (move_uploaded_file($_FILES['foto']['tmp_name'], $caminho)) {
        $foto = 'uploads/' . $nomeArquivo;
    }
}

// 🔹 Banco
$db = db();
$db->begin_transaction();

try {

    // Inserir pessoa
    $p = $db->prepare('
        INSERT INTO pessoa (nomePessoa, cpf, dataNascimento, fotoPerfil, sobre) 
        VALUES (?, ?, ?, ?, ?)
    ');

    $p->bind_param('sssss', $nome, $cpf, $data, $foto, $sobre);
    $p->execute();

    $idPessoa = $db->insert_id;

    // Inserir idoso
    $i = $db->prepare('
        INSERT INTO idoso (idPessoa, idInstituicao, aceitaVisita, aceitaCarta) 
        VALUES (?, ?, ?, ?)
    ');

    $i->bind_param('iiii', $idPessoa, $idInstituicao, $visita, $carta);
    $i->execute();

    $db->commit();

    flash_set('success', 'Idoso cadastrado com sucesso!');

} catch (Throwable $e) {

    $db->rollback();
    flash_set('error', 'Erro: ' . $e->getMessage());

}

header('Location: ../pages/cadastro_idoso.php');
exit;