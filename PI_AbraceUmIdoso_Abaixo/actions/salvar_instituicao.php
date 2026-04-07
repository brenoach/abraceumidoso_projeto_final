<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/ValidarEntradas.php';
// salvar_instituicao.php

declare(strict_types=1);

session_start();

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
    $_SESSION['sucesso_cadastro_instituicao'] = $mensagem;
    header('Location: ../pages/cadastro_instituicao.php');
    exit;
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
| Coleta dos dados do formulário novo
|------------------------------------------------------------------
*/
$nomeInstituicao = limpar($_POST['nomeInstituicao'] ?? '');
$cnpj            = somenteNumeros($_POST['cnpj'] ?? '');
$email           = limpar($_POST['email'] ?? '');
$senha           = (string) ($_POST['senha'] ?? '');
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
$fotoInstituicao = limpar($_POST['fotoInstituicao'] ?? '');

// Valor padrão, porque no formulário novo a foto vem como texto opcional
if ($fotoInstituicao === '') {
    $fotoInstituicao = 'assets/img/logoPI.jpg';
}

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
| Validações simples
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

if ($telefone !== '' && strlen($telefone) !== 11) {
    $erros[] = 'O telefone deve ter 11 dígitos.';
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
    // Verifica e-mail já cadastrado em contato
    $stmt = $pdo->prepare('SELECT idContato FROM contato WHERE email = :email LIMIT 1');
    $stmt->execute([':email' => $email]);

    if ($stmt->fetch()) {
        voltarComErro('Já existe uma instituição ou usuário com este e-mail.', $old);
    }

    // Verifica CNPJ já cadastrado
    $stmt = $pdo->prepare('SELECT idInstituicao FROM instituicao WHERE cnpj = :cnpj LIMIT 1');
    $stmt->execute([':cnpj' => $cnpj]);

    if ($stmt->fetch()) {
        voltarComErro('Já existe uma instituição cadastrada com este CNPJ.', $old);
    }

    $pdo->beginTransaction();

    // 1) contato
    $stmt = $pdo->prepare(
        'INSERT INTO contato (email, celular, telefone)
         VALUES (:email, :celular, :telefone)'
    );

    $stmt->execute([
        ':email' => $email,
        ':celular' => $celular !== '' ? $celular : null,
        ':telefone' => $telefone !== '' ? $telefone : null,
    ]);

    $idContato = (int) $pdo->lastInsertId();

    // 2) endereco
    $stmt = $pdo->prepare(
        'INSERT INTO endereco (
            cep, estado, cidade, bairro, numero, nomeLogradouro, tipoLogradouro, complemento
         ) VALUES (
            :cep, :estado, :cidade, :bairro, :numero, :nomeLogradouro, :tipoLogradouro, :complemento
         )'
    );

    $stmt->execute([
        ':cep' => $cep !== '' ? $cep : null,
        ':estado' => $estado,
        ':cidade' => $cidade,
        ':bairro' => $bairro,
        ':numero' => $numero,
        ':nomeLogradouro' => $nomeLogradouro,
        ':tipoLogradouro' => $tipoLogradouro,
        ':complemento' => $complemento !== '' ? $complemento : null,
    ]);

    $idEndereco = (int) $pdo->lastInsertId();

    // 3) instituicao
    $stmt = $pdo->prepare(
        'INSERT INTO instituicao (
            fotoInstituicao, nomeInstituicao, cnpj, senha, idContato, idEndereco
         ) VALUES (
            :fotoInstituicao, :nomeInstituicao, :cnpj, :senha, :idContato, :idEndereco
         )'
    );

    $stmt->execute([
        ':fotoInstituicao' => $fotoInstituicao,
        ':nomeInstituicao' => $nomeInstituicao,
        ':cnpj' => $cnpj,
        ':senha' => password_hash($senha, PASSWORD_DEFAULT),
        ':idContato' => $idContato,
        ':idEndereco' => $idEndereco,
    ]);

    $pdo->commit();

    voltarComSucesso('Instituição cadastrada com sucesso.');
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    voltarComErro('Erro ao salvar a instituição: ' . $e->getMessage(), $old);
}