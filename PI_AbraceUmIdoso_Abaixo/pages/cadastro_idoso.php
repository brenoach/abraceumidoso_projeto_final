<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_login();

$db = db();
$instituicoes = $db->query("SELECT idInstituicao, nomeInstituicao FROM instituicao ORDER BY nomeInstituicao");
?>

<style>
.form-box {
    background: #fff;
    padding: 30px;
    border-radius: 12px;
    max-width: 700px;
    margin: auto;
    box-shadow: 0 5px 20px rgba(0,0,0,0.1);
}

.section-title {
    text-align: center;
    margin-bottom: 20px;
}

/* FOTO */
.foto-usuario {
    width: 140px;
    height: 140px;
    border-radius: 50%;
    overflow: hidden;
    margin: 0 auto 10px;
    cursor: pointer;
    border: 3px solid #ccc;
    position: relative;
    transition: 0.3s;
}

.foto-usuario:hover {
    border-color: #4CAF50;
    transform: scale(1.05);
}

.foto-usuario::after {
    content: "📷";
    position: absolute;
    bottom: 5px;
    right: 10px;
    background: rgba(0,0,0,0.6);
    color: white;
    padding: 5px;
    border-radius: 50%;
}

.foto-usuario img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* GRID */
.form-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.form-grid input,
.form-grid select,
.form-grid textarea {
    width: 100%;
    padding: 10px;
    border-radius: 8px;
    border: 1px solid #ccc;
}

.actions-row {
    text-align: center;
    margin-top: 20px;
}

.actions-row button {
    background: #4CAF50;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 8px;
    cursor: pointer;
}

@media (max-width: 600px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<main class="page container">

<form class="form-box" action="../actions/salvar_idoso.php" method="POST" enctype="multipart/form-data">

<h1 class="section-title">Cadastrar idoso</h1>

<!-- FOTO -->
<div style="text-align:center;">
    <div class="foto-usuario" onclick="abrirUpload()">
        <img id="preview" src="../assets/img/user.png">
    </div>
    <small>Clique para adicionar foto</small>

    <input type="file" name="foto" id="inputFoto" accept="image/*" style="display:none" onchange="previewImagem(event)">
</div>

<div class="form-grid">

<div>
<label>Nome</label>
<input type="text" name="nomePessoa" required>
</div>

<div>
<label>CPF</label>
<input type="text" name="cpf" required>
</div>

<!-- DATA COMPLETA -->
<div style="grid-column:1/-1">
<label>Data de nascimento</label>

<div style="display:flex; gap:10px;">

<select name="dia" id="dia" required>
<option value="">Dia</option>
<?php for($i=1;$i<=31;$i++): ?>
<option value="<?= $i ?>"><?= $i ?></option>
<?php endfor; ?>
</select>

<select name="mes" id="mes" required>
<option value="">Mês</option>
<option value="01">Jan</option>
<option value="02">Fev</option>
<option value="03">Mar</option>
<option value="04">Abr</option>
<option value="05">Mai</option>
<option value="06">Jun</option>
<option value="07">Jul</option>
<option value="08">Ago</option>
<option value="09">Set</option>
<option value="10">Out</option>
<option value="11">Nov</option>
<option value="12">Dez</option>
</select>

<select name="ano" id="ano" required>
<option value="">Ano</option>
<?php for($i=date('Y');$i>=1900;$i--): ?>
<option value="<?= $i ?>"><?= $i ?></option>
<?php endfor; ?>
</select>

</div>

<small id="idadeTexto" style="color:#666;"></small>

</div>

<!-- INSTITUIÇÃO -->
<div>
<label>Instituição</label>

<?php if ($instituicoes->num_rows > 0): ?>
<select name="idInstituicao" required>
<option value="">Selecione</option>
<?php while($inst = $instituicoes->fetch_assoc()): ?>
<option value="<?= $inst['idInstituicao'] ?>">
<?= htmlspecialchars($inst['nomeInstituicao']) ?>
</option>
<?php endwhile; ?>
</select>
<?php else: ?>
<p style="color:red;">Nenhuma instituição cadastrada</p>
<?php endif; ?>

</div>

<div>
<label>Aceita visita?</label>
<select name="aceitaVisita">
<option value="1">Sim</option>
<option value="0">Não</option>
</select>
</div>

<div>
<label>Aceita carta?</label>
<select name="aceitaCarta">
<option value="1">Sim</option>
<option value="0">Não</option>
</select>
</div>

<div style="grid-column:1/-1">
<label>Sobre</label>
<textarea name="sobre"></textarea>
</div>

</div>

<div class="actions-row">
<button type="submit">Salvar idoso</button>
</div>

</form>
</main>

<script>
// FOTO
function abrirUpload() {
    document.getElementById('inputFoto').click();
}

function previewImagem(event) {
    const preview = document.getElementById('preview');
    const file = event.target.files[0];

    if (file) {
        preview.src = URL.createObjectURL(file);
    }
}

// DATA
document.getElementById('dia').addEventListener('change', validarData);
document.getElementById('mes').addEventListener('change', validarData);
document.getElementById('ano').addEventListener('change', validarData);

function validarData() {
    const dia = document.getElementById('dia').value;
    const mes = document.getElementById('mes').value;
    const ano = document.getElementById('ano').value;

    if (!dia || !mes || !ano) return;

    const data = new Date(ano, mes - 1, dia);

    if (data.getDate() != dia || data.getMonth() != mes - 1) {
        alert("Data inválida!");
        document.getElementById('dia').value = "";
        return;
    }

    const hoje = new Date();
    let idade = hoje.getFullYear() - ano;

    if (
        hoje.getMonth() < (mes - 1) || 
        (hoje.getMonth() == (mes - 1) && hoje.getDate() < dia)
    ) {
        idade--;
    }

    document.getElementById('idadeTexto').innerText = "Idade: " + idade + " anos";

    if (idade < 18) {
        alert("O idoso deve ter mais de 18 anos.");
        document.getElementById('dia').value = "";
        document.getElementById('mes').value = "";
        document.getElementById('ano').value = "";
        document.getElementById('idadeTexto').innerText = "";
    }
}
</script>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>