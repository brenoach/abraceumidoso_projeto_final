<?php require_once __DIR__ . '/../includes/layout_top.php'; ?>

<main class="page">
  <div class="container">
    <form class="form-box fade-in" action="../actions/salvar_instituicao.php" method="POST" enctype="multipart/form-data">
      <h1 class="section-title">Cadastrar asilo / instituição</h1>
      <p class="muted">Preencha os dados da instituição parceira.</p>

      <div class="form-grid">
        <!-- Nome da instituição -->
        <div>
          <label for="nomeInstituicao">Nome da instituição</label>
          <input type="text" id="nomeInstituicao" name="nomeInstituicao" required>
        </div>

        <!-- CNPJ -->
        <div>
          <label for="cnpj">CNPJ</label>
          <input type="text" id="cnpj" name="cnpj" maxlength="14" required>
        </div>

        <!-- E-mail -->
        <div>
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" required>
        </div>

        <!-- Telefone -->
        <div>
          <label for="telefone">Telefone</label>
          <input type="text" id="telefone" name="telefone" maxlength="11" required>
        </div>

        <!-- Celular -->
        <div>
          <label for="celular">Celular</label>
          <input type="text" id="celular" name="celular">
        </div>

        <!-- CEP -->
        <div>
          <label for="cep">CEP</label>
          <input type="text" id="cep" name="cep" maxlength="8" required>
        </div>

        <!-- Estado -->
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
            <option value="MG">MG</option>
            <option value="MS">MS</option>
            <option value="MT">MT</option>
            <option value="PA">PA</option>
            <option value="PB">PB</option>
            <option value="PE">PE</option>
            <option value="PI">PI</option>
            <option value="PR">PR</option>
            <option value="RJ">RJ</option>
            <option value="RN">RN</option>
            <option value="RO">RO</option>
            <option value="RR">RR</option>
            <option value="RS">RS</option>
            <option value="SC">SC</option>
            <option value="SE">SE</option>
            <option value="SP">SP</option>
            <option value="TO">TO</option>
          </select>
        </div>

        <!-- Cidade -->
        <div>
          <label for="cidade">Cidade</label>
          <input type="text" id="cidade" name="cidade" required>
        </div>

        <!-- Bairro -->
        <div>
          <label for="bairro">Bairro</label>
          <input type="text" id="bairro" name="bairro" required>
        </div>

        <!-- Tipo do logradouro -->
        <div>
          <label for="tipoLogradouro">Tipo do logradouro</label>
          <input type="text" id="tipoLogradouro" name="tipoLogradouro" placeholder="Rua, Avenida, Travessa..." required>
        </div>

        <!-- Nome do logradouro -->
        <div>
          <label for="nomeLogradouro">Nome do logradouro</label>
          <input type="text" id="nomeLogradouro" name="nomeLogradouro" required>
        </div>

        <!-- Número -->
        <div>
          <label for="numero">Número</label>
          <input type="text" id="numero" name="numero" required>
        </div>

        <!-- Complemento -->
        <div>
          <label for="complemento">Complemento</label>
          <input type="text" id="complemento" name="complemento">
        </div>

        <!-- Senha -->
        <div>
          <label for="senha">Senha</label>
          <input type="password" id="senha" name="senha" required>
        </div>

        <!-- Confirmar senha -->
        <div>
          <label for="confirmarSenha">Confirmar senha</label>
          <input type="password" id="confirmarSenha" name="confirmarSenha" required>
        </div>

        <!-- Foto da instituição -->
        <div style="grid-column: 1 / -1;">
          <label for="fotoInstituicao">Foto da instituição</label>
          <input type="file" id="fotoInstituicao" name="fotoInstituicao" accept="image/*" required>
        </div>
      </div>

      <div class="actions-row">
        <button type="submit">Salvar instituição</button>
      </div>
    </form>
  </div>
</main>

<?php require_once __DIR__ . '/../includes/layout_bottom.php'; ?>