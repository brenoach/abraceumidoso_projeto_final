<?php require_once __DIR__ . '/../includes/layout_top.php'; ?>

<main class="page container">
  <form class="form-box fade-in" action="../actions/salvar_voluntario.php" method="POST" enctype="multipart/form-data">
    <h1 class="section-title">Cadastrar voluntário</h1>

    <?php if (function_exists('flash_get')): ?>
      <?php if ($mensagemErro = flash_get('error')): ?>
        <div class="alert alert-error" style="margin-bottom:16px; padding:12px; border-radius:8px; background:#ffe5e5; color:#8a1f1f;">
          <?php echo htmlspecialchars($mensagemErro, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <?php if ($mensagemSucesso = flash_get('success')): ?>
        <div class="alert alert-success" style="margin-bottom:16px; padding:12px; border-radius:8px; background:#e6ffed; color:#1f6b37;">
          <?php echo htmlspecialchars($mensagemSucesso, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>

    <div class="form-group" style="text-align:center;">
      <label for="fotoPerfil">
        <img src="../assets/img/fotoPerfil.png" id="preview-img" alt="Foto de perfil" style="width:120px; height:120px; object-fit:cover; border-radius:50%; cursor:pointer;">
      </label>
      <input type="file" id="fotoPerfil" name="fotoPerfil" accept=".jpg,.jpeg,.png,.webp,image/*" hidden>
      <p style="margin-top:8px; font-size:14px;">Clique na imagem para selecionar uma foto.</p>
    </div>

    <script>
      const inputFoto = document.getElementById('fotoPerfil');
      const previewImg = document.getElementById('preview-img');

      inputFoto.addEventListener('change', function () {
        const arquivo = this.files[0];
        if (arquivo) {
          const leitor = new FileReader();
          leitor.onload = function (e) {
            previewImg.src = e.target.result;
          };
          leitor.readAsDataURL(arquivo);
        }
      });
    </script>

    <div class="form-grid">
      <div>
        <label for="nomePessoa">Nome</label>
        <input type="text" id="nomePessoa" name="nomePessoa" maxlength="100" required>
      </div>

      <div>
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" maxlength="150" required>
      </div>

      <div>
        <label for="dataNascimento">Data de nascimento</label>
        <input type="date" id="dataNascimento" name="dataNascimento" required>
      </div>

      <div>
        <label for="cpf">CPF</label>
        <input type="text" id="cpf" name="cpf" maxlength="14" required>
      </div>

      <div>
        <label for="celular">Celular</label>
        <input type="tel" id="celular" name="celular" maxlength="15" >
      </div>

      <div>
        <label for="telefone">Telefone</label>
        <input type="tel" id="telefone" name="telefone" maxlength="14" placeholder="(00) 0000-0000">
      </div>

      <div>
        <label for="cep">CEP</label>
        <input type="text" id="cep" name="cep" maxlength="9" placeholder="00000-000">
      </div>

      <div>
        <label for="cidade">Cidade</label>
        <input type="text" id="cidade" name="cidade" maxlength="100" required>
      </div>

      <div>
        <label for="bairro">Bairro</label>
        <input type="text" id="bairro" name="bairro" maxlength="100" required>
      </div>

      <div>
        <label for="estado">Estado</label>
        <select id="estado" name="estado" required>
          <option value="">Selecione</option>
          <option value="AC">AC</option>
          <option value="AL">AL</option>
          <option value="AP">AP</option>
          <option value="AM">AM</option>
          <option value="BA">BA</option>
          <option value="CE">CE</option>
          <option value="DF">DF</option>
          <option value="ES">ES</option>
          <option value="GO">GO</option>
          <option value="MA">MA</option>
          <option value="MT">MT</option>
          <option value="MS">MS</option>
          <option value="MG">MG</option>
          <option value="PA">PA</option>
          <option value="PB">PB</option>
          <option value="PR">PR</option>
          <option value="PE">PE</option>
          <option value="PI">PI</option>
          <option value="RJ">RJ</option>
          <option value="RN">RN</option>
          <option value="RS">RS</option>
          <option value="RO">RO</option>
          <option value="RR">RR</option>
          <option value="SC">SC</option>
          <option value="SP">SP</option>
          <option value="SE">SE</option>
          <option value="TO">TO</option>
        </select>
      </div>

      <div>
        <label for="nomeLogradouro">Rua</label>
        <input type="text" id="nomeLogradouro" name="nomeLogradouro" maxlength="150" required>
      </div>

      <div>
        <label for="numero">Número</label>
        <input type="text" id="numero" name="numero" maxlength="10" required>
      </div>

      <div>
        <label for="complemento">Complemento</label>
        <input type="text" id="complemento" name="complemento" maxlength="100">
      </div>

      <div>
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" minlength="8" maxlength="45" required>
      </div>

      <div>
        <label for="confirmarSenha">Confirmar senha</label>
        <input type="password" id="confirmarSenha" name="confirmarSenha" minlength="8" maxlength="45" required>
      </div>

      <div style="grid-column:1/-1">
        <label for="sobre">Sobre</label>
        <textarea id="sobre" name="sobre" maxlength="1000"></textarea>
      </div>
    </div>

    <div class="actions-row">
      <button type="submit">Cadastrar</button>
    </div>
  </form>
</main>

<script>
    
    /*
  function aplicarMascaraCPF(valor) {
    valor = valor.replace(/\D/g, '').slice(0, 11);
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d)/, '$1.$2');
    valor = valor.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    return valor;
  }

  function aplicarMascaraCEP(valor) {
    valor = valor.replace(/\D/g, '').slice(0, 8);
    valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
    return valor;
  }

  function aplicarMascaraTelefone(valor, celular = false) {
    valor = valor.replace(/\D/g, '').slice(0, celular ? 11 : 10);

    if (celular) {
      valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
      valor = valor.replace(/(\d{5})(\d)/, '$1-$2');
      return valor;
    }

    valor = valor.replace(/(\d{2})(\d)/, '($1) $2');
    valor = valor.replace(/(\d{4})(\d)/, '$1-$2');
    return valor;
  }

  document.getElementById('cpf').addEventListener('input', function () {
    this.value = aplicarMascaraCPF(this.value);
  });

  document.getElementById('cep').addEventListener('input', function () {
    this.value = aplicarMascaraCEP(this.value);
  });

  document.getElementById('celular').addEventListener('input', function () {
    this.value = aplicarMascaraTelefone(this.value, true);
  });

  document.getElementById('telefone').addEventListener('input', function () {
    this.value = aplicarMascaraTelefone(this.value, false);
  });
  */
</script>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>
?>