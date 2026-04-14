<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/layout_top.php';
require_login();

$db = db();

// 🔹 Buscar instituições
$instituicoes = $db->query("SELECT idInstituicao, nomeInstituicao FROM instituicao ORDER BY nomeInstituicao");

if (!$instituicoes) {
    die("Erro na query: " . $db->error);
}
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
    font-size: 14px;
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

    <small style="color:#777;">Clique para adicionar foto</small>

    <input type="file" 
           name="foto" 
           id="inputFoto" 
           accept="image/*" 
           style="display:none"
           onchange="previewImagem(event)">
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

<div>
<label>Data de nascimento</label>
<input type="date" name="dataNascimento" required>
</div>

<!-- INSTITUIÇÃO -->
<div>
<label>Instituição</label>

<?php if ($instituicoes->num_rows > 0): ?>

<select name="idInstituicao" required>
    <option value="">Selecione uma instituição</option>

    <?php while($inst = $instituicoes->fetch_assoc()): ?>
        <option value="<?= $inst['idInstituicao'] ?>">
            <?= htmlspecialchars($inst['nomeInstituicao']) ?>
        </option>
    <?php endwhile; ?>

</select>

<?php else: ?>

<p style="color:red;">Nenhuma instituição cadastrada.</p>

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
</script>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>