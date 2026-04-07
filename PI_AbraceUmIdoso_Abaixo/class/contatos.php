<?php
require_once dirname(__DIR__, 2) . '/connection/config.php';
class Contatos {
    private string $email, $celular, $telefone;

    public function __construct($email, $celular, $telefone) {
        $this->email = $email;
        $this->celular = $celular;
        $this->telefone = $telefone;
    }
    public function __get($valor){
        return $this->$valor;
    }
    public function __set($valor, $campo){
        return  $this->$valor = $campo;
    }
}


?>