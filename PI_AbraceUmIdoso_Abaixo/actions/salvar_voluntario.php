<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../class/ValidarEntradas.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/cadastro_voluntario.php');
    exit;
}

function redirecionarComErro(string $mensagem): void
{
    flash_set('error', $mensagem);
    header('Location: ../pages/cadastro_voluntario.php');
    exit;
}

/*
|--------------------------------------------------------------------------
| RECEBER E SANITIZAR DADOS
|--------------------------------------------------------------------------
*/
$nome = trim($_POST['nomePessoa'] ?? '');
$email = trim($_POST['email'] ?? '');
$cpf = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmarSenha = $_POST['confirmarSenha'] ?? '';

$dataNascimento = $_POST['dataNascimento'] ?? '';
$celular = preg_replace('/\D/', '', $_POST['celular'] ?? '');
$telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');

$cep = preg_replace('/\D/', '', $_POST['cep'] ?? '');
$estado = strtoupper(trim($_POST['estado'] ?? ''));
$cidade = trim($_POST['cidade'] ?? '');
$bairro = trim($_POST['bairro'] ?? '');
$numero = trim($_POST['numero'] ?? '');
$complemento = trim($_POST['complemento'] ?? '');
$nomeLogradouro = trim($_POST['nomeLogradouro'] ?? '');
$sobre = trim($_POST['sobre'] ?? '');

// como o novo formulário não tem mais tipoLogradouro:
$tipoLogradouro = '';

/*
|--------------------------------------------------------------------------
| VALIDAÇÕES
|--------------------------------------------------------------------------
*/
$validador = new ValidarEntradas();

$validador->obrigatorio('nome', $nome);
$validador->obrigatorio('email', $email);
$validador->obrigatorio('cpf', $cpf);
$validador->obrigatorio('senha', $senha);
$validador->obrigatorio('confirmarSenha', $confirmarSenha);
$validador->obrigatorio('dataNascimento', $dataNascimento);
$validador->obrigatorio('estado', $estado);
$validador->obrigatorio('cidade', $cidade);
$validador->obrigatorio('bairro', $bairro);
$validador->obrigatorio('numero', $numero);
$validador->obrigatorio('nomeLogradouro', $nomeLogradouro);

if ($nome !== '') {
    $validador->tamanhoMax('nome', $nome, 100);
    $validador->stringSemNumero('nome', $nome);
}

if ($email !== '') {
    $validador->email('email', $email);
    $validador->tamanhoMax('email', $email, 150);
}

if ($cpf !== '') {
    $validador->numero('cpf', $cpf);
    $validador->tamanhoExato('cpf', $cpf, 11);
}

if ($celular !== '') {
    $validador->numero('celular', $celular);
    if (!in_array(strlen($celular), [10, 11], true)) {
        $validador->numero('celular', 'abc'); // força mensagem padrão de número? avoid. Better custom impossible. can't access private add error.
    }
}

if ($telefone !== '') {
    $validador->numero('telefone', $telefone);
    if (!in_array(strlen($telefone), [10, 11], true)) {
        $validador->numero('telefone', 'abc');
    }
}

if ($cep !== '') {
    $validador->numero('cep', $cep);
    $validador->tamanhoExato('cep', $cep, 8);
}

if ($estado !== '') {
    $validador->tamanhoExato('estado', $estado, 2);
    $validador->stringSemNumero('estado', $estado);
}

if ($cidade !== '') {
    $validador->tamanhoMax('cidade', $cidade, 100);
}

if ($bairro !== '') {
    $validador->tamanhoMax('bairro', $bairro, 100);
}

if ($numero !== '') {
    $validador->tamanhoMax('numero', $numero, 10);
}

if ($complemento !== '') {
    $validador->tamanhoMax('complemento', $complemento, 100);
}

if ($nomeLogradouro !== '') {
    $validador->tamanhoMax('nomeLogradouro', $nomeLogradouro, 150);
}

if ($sobre !== '') {
    $validador->tamanhoMax('sobre', $sobre, 1000);
}

if ($dataNascimento !== '') {
    try {
        $validador->maiorDeIdade('dataNascimento', $dataNascimento);
    } catch (Throwable $e) {
        redirecionarComErro('Data de nascimento inválida.');
    }
}

$validador->senha($senha, $confirmarSenha);

if ($validador->temErros()) {
    $erros = $validador->getErros();
    redirecionarComErro(reset($erros));
}

/*
|--------------------------------------------------------------------------
| UPLOAD DA FOTO
|--------------------------------------------------------------------------
*/
$foto = 'assets/img/fotoPerfil.png'; // padrão

if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] !== UPLOAD_ERR_NO_FILE) {
    if ($_FILES['fotoPerfil']['error'] !== UPLOAD_ERR_OK) {
        redirecionarComErro('Erro ao enviar a foto.');
    }

    $tmpName = $_FILES['fotoPerfil']['tmp_name'];
    $nomeOriginal = $_FILES['fotoPerfil']['name'];
    $tamanho = $_FILES['fotoPerfil']['size'];

    $mime = mime_content_type($tmpName);
    $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp'];

    if (!in_array($mime, $tiposPermitidos, true)) {
        redirecionarComErro('Formato de imagem inválido. Use JPG, PNG ou WEBP.');
    }

    if ($tamanho > 5 * 1024 * 1024) {
        redirecionarComErro('A foto deve ter no máximo 5MB.');
    }

    $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));
    $nomeArquivo = uniqid('vol_', true) . '.' . $extensao;

    $pastaFisica = __DIR__ . '/../uploads/voluntarios/';
    if (!is_dir($pastaFisica)) {
        mkdir($pastaFisica, 0777, true);
    }

    $caminhoFisico = $pastaFisica . $nomeArquivo;
    $caminhoBanco = 'uploads/voluntarios/' . $nomeArquivo;

    if (!move_uploaded_file($tmpName, $caminhoFisico)) {
        redirecionarComErro('Não foi possível salvar a foto.');
    }

    $foto = $caminhoBanco;
}

/*
|--------------------------------------------------------------------------
| BANCO
|--------------------------------------------------------------------------
*/
$db = db();
$db->begin_transaction();

try {
    // Verificar se email já existe em contato
    $verContato = $db->prepare('SELECT idContato FROM contato WHERE email = ? LIMIT 1');
    if (!$verContato) {
        throw new Exception('Erro ao preparar verificação de e-mail.');
    }
    $verContato->bind_param('s', $email);
    $verContato->execute();
    $resContato = $verContato->get_result();

    if ($resContato->fetch_assoc()) {
        throw new Exception('Já existe um cadastro com este e-mail.');
    }

    // Verificar se CPF já existe em pessoa
    $verPessoa = $db->prepare('SELECT idPessoa FROM pessoa WHERE cpf = ? LIMIT 1');
    if (!$verPessoa) {
        throw new Exception('Erro ao preparar verificação de CPF.');
    }
    $verPessoa->bind_param('s', $cpf);
    $verPessoa->execute();
    $resPessoa = $verPessoa->get_result();

    if ($resPessoa->fetch_assoc()) {
        throw new Exception('Já existe um cadastro com este CPF.');
    }

    // contato
    $c = $db->prepare('INSERT INTO contato (email, celular, telefone) VALUES (?,?,?)');
    if (!$c) {
        throw new Exception('Erro ao preparar cadastro de contato.');
    }
    $c->bind_param('sss', $email, $celular, $telefone);
    $c->execute();
    $idContato = $db->insert_id;

    // endereco
    $e = $db->prepare('
        INSERT INTO endereco (
            cep, estado, cidade, bairro, numero, complemento, nomeLogradouro, tipoLogradouro
        ) VALUES (?,?,?,?,?,?,?,?)
    ');
    if (!$e) {
        throw new Exception('Erro ao preparar cadastro de endereço.');
    }
    $e->bind_param(
        'ssssssss',
        $cep,
        $estado,
        $cidade,
        $bairro,
        $numero,
        $complemento,
        $nomeLogradouro,
        $tipoLogradouro
    );
    $e->execute();
    $idEndereco = $db->insert_id;

    // pessoa
    $p = $db->prepare('
        INSERT INTO pessoa (nomePessoa, cpf, dataNascimento, fotoPerfil, sobre)
        VALUES (?,?,?,?,?)
    ');
    if (!$p) {
        throw new Exception('Erro ao preparar cadastro de pessoa.');
    }
    $p->bind_param('sssss', $nome, $cpf, $dataNascimento, $foto, $sobre);
    $p->execute();
    $idPessoa = $db->insert_id;

    // voluntario
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    $v = $db->prepare('
        INSERT INTO voluntario (senha, idContato, idEndereco, idPessoa)
        VALUES (?,?,?,?)
    ');
    if (!$v) {
        throw new Exception('Erro ao preparar cadastro de voluntário.');
    }
    $v->bind_param('siii', $senhaHash, $idContato, $idEndereco, $idPessoa);
    $v->execute();

    $db->commit();
    flash_set('success', 'Voluntário cadastrado com sucesso.');
} catch (Throwable $e) {
    $db->rollback();

    // se subiu foto e deu erro depois, remove
    if ($foto !== 'assets/img/fotoPerfil.png') {
        $arquivoRemover = __DIR__ . '/../' . $foto;
        if (file_exists($arquivoRemover)) {
            unlink($arquivoRemover);
        }
    }

    flash_set('error', 'Erro ao cadastrar voluntário: ' . $e->getMessage());
}

header('Location: ../pages/cadastro_voluntario.php');
exit;
?>