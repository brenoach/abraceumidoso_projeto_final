<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/ValidarEntradas.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/*
|------------------------------------------------------------------
| Funções auxiliares
|------------------------------------------------------------------
*/
function limpar(?string $valor): string
{
    return trim((string) $valor);
}

function somenteNumeros(?string $valor): string
{
    return preg_replace('/\D+/', '', (string) $valor) ?? '';
}

function voltarComErro(string $mensagem, array $dados = []): void
{
    $_SESSION['erro_cadastro_instituicao'] = $mensagem;
    $_SESSION['old_cadastro_instituicao'] = $dados;
    header('Location: ../pages/cadastro_instituicao.php');
    exit;
}

function voltarComSucesso(string $mensagem): void
{
    unset($_SESSION['old_cadastro_instituicao']);
    $_SESSION['sucesso_cadastro_instituicao'] = $mensagem;
    header('Location: ../pages/cadastro_instituicao.php');
    exit;
}

function salvarFotoInstituicao(): string
{
    $fotoPadrao = 'assets/img/logoPI.jpg';

    $fotoTexto = limpar($_POST['fotoInstituicao'] ?? '');
    if ($fotoTexto !== '') {
        return $fotoTexto;
    }

    if (
        !isset($_FILES['fotoInstituicao']) ||
        !is_array($_FILES['fotoInstituicao']) ||
        ($_FILES['fotoInstituicao']['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE
    ) {
        return $fotoPadrao;
    }

    $arquivo = $_FILES['fotoInstituicao'];

    if (($arquivo['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
        return $fotoPadrao;
    }

    $tmpName = $arquivo['tmp_name'] ?? '';
    if ($tmpName === '' || !is_uploaded_file($tmpName)) {
        return $fotoPadrao;
    }

    $extensao = strtolower(pathinfo($arquivo['name'] ?? '', PATHINFO_EXTENSION));
    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'webp'];

    if (!in_array($extensao, $extensoesPermitidas, true)) {
        return $fotoPadrao;
    }

    $diretorioFisico = __DIR__ . '/../uploads/instituicoes/';
    $diretorioBanco = 'uploads/instituicoes/';

    if (!is_dir($diretorioFisico) && !mkdir($diretorioFisico, 0777, true) && !is_dir($diretorioFisico)) {
        return $fotoPadrao;
    }

    $nomeArquivo = uniqid('instituicao_', true) . '.' . $extensao;
    $destinoFisico = $diretorioFisico . $nomeArquivo;

    if (!move_uploaded_file($tmpName, $destinoFisico)) {
        return $fotoPadrao;
    }

    return $diretorioBanco . $nomeArquivo;
}

/*
|------------------------------------------------------------------
| Aceita apenas POST
|------------------------------------------------------------------
*/
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/cadastro_instituicao.php');
    exit;
}

/*
|------------------------------------------------------------------
| Conexão
|------------------------------------------------------------------
*/
$db = db();

if (!$db || $db->connect_error) {
    voltarComErro('Erro ao conectar ao banco de dados.');
}

/*
|------------------------------------------------------------------
| Coleta dos dados do formulário
|------------------------------------------------------------------
*/
$nomeInstituicao = limpar($_POST['nomeInstituicao'] ?? '');
$cnpj            = somenteNumeros($_POST['cnpj'] ?? '');
$email           = limpar($_POST['email'] ?? '');
$senha           = (string) ($_POST['senha'] ?? '');
$confirmarSenha  = (string) ($_POST['confirmarSenha'] ?? '');
$celular         = somenteNumeros($_POST['celular'] ?? '');
$telefone        = somenteNumeros($_POST['telefone'] ?? '');
$cep             = somenteNumeros($_POST['cep'] ?? '');
$estado          = strtoupper(limpar($_POST['estado'] ?? ''));
$cidade          = limpar($_POST['cidade'] ?? '');
$bairro          = limpar($_POST['bairro'] ?? '');
$numero          = limpar($_POST['numero'] ?? '');
$complemento     = limpar($_POST['complemento'] ?? '');
$tipoLogradouro  = limpar($_POST['tipoLogradouro'] ?? '');
$nomeLogradouro  = limpar($_POST['nomeLogradouro'] ?? '');
$fotoInstituicao = salvarFotoInstituicao();

/*
|------------------------------------------------------------------
| Guarda valores para reaproveitar no formulário em caso de erro
|------------------------------------------------------------------
*/
$old = [
    'nomeInstituicao' => $nomeInstituicao,
    'cnpj' => $cnpj,
    'email' => $email,
    'celular' => $celular,
    'telefone' => $telefone,
    'cep' => $cep,
    'estado' => $estado,
    'cidade' => $cidade,
    'bairro' => $bairro,
    'numero' => $numero,
    'complemento' => $complemento,
    'tipoLogradouro' => $tipoLogradouro,
    'nomeLogradouro' => $nomeLogradouro,
    'fotoInstituicao' => $fotoInstituicao,
];

/*
|------------------------------------------------------------------
| Validações
|------------------------------------------------------------------
*/
$erros = [];

if ($nomeInstituicao === '') {
    $erros[] = 'Informe o nome da instituição.';
}

if ($cnpj === '' || strlen($cnpj) !== 14) {
    $erros[] = 'Informe um CNPJ com 14 dígitos.';
}

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Informe um e-mail válido.';
}

if ($senha === '' || strlen($senha) < 6) {
    $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
}

if ($confirmarSenha === '') {
    $erros[] = 'Confirme a senha.';
}

if ($senha !== $confirmarSenha) {
    $erros[] = 'As senhas não coincidem.';
}

if ($estado === '' || strlen($estado) !== 2) {
    $erros[] = 'Informe o estado com 2 letras.';
}

if ($cidade === '') {
    $erros[] = 'Informe a cidade.';
}

if ($bairro === '') {
    $erros[] = 'Informe o bairro.';
}

if ($numero === '') {
    $erros[] = 'Informe o número.';
}

if ($tipoLogradouro === '') {
    $erros[] = 'Informe o tipo do logradouro.';
}

if ($nomeLogradouro === '') {
    $erros[] = 'Informe o nome do logradouro.';
}

if ($cep !== '' && strlen($cep) !== 8) {
    $erros[] = 'O CEP deve ter 8 dígitos.';
}

if ($celular !== '' && strlen($celular) !== 11) {
    $erros[] = 'O celular deve ter 11 dígitos.';
}

if ($telefone !== '' && strlen($telefone) !== 10 && strlen($telefone) !== 11) {
    $erros[] = 'O telefone deve ter 10 ou 11 dígitos.';
}

if (!empty($erros)) {
    voltarComErro(implode(' ', $erros), $old);
}

/*
|------------------------------------------------------------------
| Persistência
|------------------------------------------------------------------
*/
try {
    $db->begin_transaction();

    // Verifica e-mail já cadastrado
    $stmt = $db->prepare('SELECT idContato FROM contato WHERE email = ? LIMIT 1');
    if (!$stmt) {
        throw new Exception('Erro ao verificar e-mail.');
    }

    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->fetch_assoc()) {
        $stmt->close();
        $db->rollback();
        voltarComErro('Já existe uma instituição ou usuário com este e-mail.', $old);
    }
    $stmt->close();

    // Verifica CNPJ já cadastrado
    $stmt = $db->prepare('SELECT idInstituicao FROM instituicao WHERE cnpj = ? LIMIT 1');
    if (!$stmt) {
        throw new Exception('Erro ao verificar CNPJ.');
    }

    $stmt->bind_param('s', $cnpj);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->fetch_assoc()) {
        $stmt->close();
        $db->rollback();
        voltarComErro('Já existe uma instituição cadastrada com este CNPJ.', $old);
    }
    $stmt->close();

    // 1) contato
    $stmt = $db->prepare('INSERT INTO contato (email, celular, telefone) VALUES (?, ?, ?)');
    if (!$stmt) {
        throw new Exception('Erro ao inserir contato.');
    }

    $celularParam = $celular !== '' ? $celular : null;
    $telefoneParam = $telefone !== '' ? $telefone : null;

    $stmt->bind_param('sss', $email, $celularParam, $telefoneParam);
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        throw new Exception('Falha ao salvar contato.');
    }

    $idContato = $db->insert_id;
    $stmt->close();

    // 2) endereco
    $stmt = $db->prepare(
        'INSERT INTO endereco (
            cep, estado, cidade, bairro, numero, nomeLogradouro, tipoLogradouro, complemento
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
    );

    if (!$stmt) {
        throw new Exception('Erro ao inserir endereço.');
    }

    $cepParam = $cep !== '' ? $cep : null;
    $complementoParam = $complemento !== '' ? $complemento : null;

    $stmt->bind_param(
        'ssssssss',
        $cepParam,
        $estado,
        $cidade,
        $bairro,
        $numero,
        $nomeLogradouro,
        $tipoLogradouro,
        $complementoParam
    );
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        throw new Exception('Falha ao salvar endereço.');
    }

    $idEndereco = $db->insert_id;
    $stmt->close();

    // 3) instituicao
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $stmt = $db->prepare(
        'INSERT INTO instituicao (
            fotoInstituicao, nomeInstituicao, cnpj, senha, idContato, idEndereco
        ) VALUES (?, ?, ?, ?, ?, ?)'
    );

    if (!$stmt) {
        throw new Exception('Erro ao inserir instituição.');
    }

    $stmt->bind_param(
        'ssssii',
        $fotoInstituicao,
        $nomeInstituicao,
        $cnpj,
        $senhaHash,
        $idContato,
        $idEndereco
    );
    $stmt->execute();

    if ($stmt->affected_rows <= 0) {
        throw new Exception('Falha ao salvar instituição.');
    }

    $stmt->close();

    $db->commit();
    voltarComSucesso('Instituição cadastrada com sucesso.');
} catch (Throwable $e) {
    $db->rollback();
    voltarComErro('Erro ao salvar a instituição: ' . $e->getMessage(), $old);
}