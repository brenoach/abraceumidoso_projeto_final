
<?php
class ValidarEntradas{
    private array $erros = [];
 
    public function getErros()
    {
        return $this->erros;
    }
 
    public function temErros(){
        return !empty($this->erros);}
 
    private function adicionarErro(string $campo, string $mensagem){
        $this->erros[$campo] = $mensagem;
    }
 
    /* ========================= VALIDAÇÕES ========================= */


    // ---------------------------------------- Checar se os campos estão preenchidos (todos os campos)----------------------------------------
    public function obrigatorio($campo, $valor){
        if (empty($valor)) {
            $this->adicionarErro($campo, "O campo $campo é obrigatório.");
        }
    }
    // ---------------------------------------- Checar se o campo é numérico (cep,numero,telefone,celular,cpf)----------------------------------------
    public function numero($campo, $valor){
        if (!is_numeric($valor)) {
            $this->adicionarErro($campo, "O campo $campo deve ser numérico.");
        }
    }
    // ---------------------------------------- Checar E-mail (email) ----------------------------------------
    public function email($campo, $valor){
        if (!filter_var($valor, FILTER_VALIDATE_EMAIL)) {
            $this->adicionarErro($campo, "Email inválido.");
        }
    }
    // ---------------------------------------- Checar se o damanho esta certo (cep,telefone,celular,cpf,estado)----------------------------------------
    public function tamanhoExato($campo, $valor,$tamanho){
        if (strlen($valor) !== $tamanho) {
            $this->adicionarErro($campo, "O campo $campo deve ter $tamanho caracteres.");
        }
    }
    // ---------------------------------------- Checar tamanho maximo (todos)----------------------------------------
    public function tamanhoMax($campo, $valor,$max){
        if (strlen($valor) > $max) {
            $this->adicionarErro($campo, "O campo $campo deve ter no máximo $max caracteres.");
        }
    }
    // ---------------------------------------- Checar string sem número (estado, nomePessoa)----------------------------------------
    public function stringSemNumero($campo, $valor){
        if (preg_match('/\d/', $valor)) {
            $this->adicionarErro($campo, "O campo $campo não pode conter números.");
        }
    }
    // ---------------------------------------- Checar Data de nascimento (dataNascimento) ----------------------------------------
    public function maiorDeIdade($campo, $dataNascimento){
 
        $nascimento = new DateTime($dataNascimento);
        $hoje = new DateTime();
        $idade = $hoje->diff($nascimento)->y;
 
        if ($idade < 18) {
            $this->adicionarErro($campo, "É necessário ser maior de 18 anos.");
        }
    }
// ---------------------------------------- Checar Senha (senha,confirmarSenha) ----------------------------------------
    public function senha($senha,$confirmarSenha){
        $padrao = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,45}$/';
 
        if ($senha !== $confirmarSenha) {
            $this->adicionarErro('senha', "As senhas não coincidem.");
            return;
        }
 
        if (!preg_match($padrao, $senha)) {
            $this->adicionarErro(
                'senha',
                "A senha deve ter pelo menos 8 caracteres, incluindo maiúscula, minúscula, número e símbolo."
            );
        }
    }
}


?>