<?php
require_once dirname(__DIR__, 2) . '/connection/config.php';
class Pessoas{
    public string $nomePessoa, $cpf, $fotoPerfil, $sobre;
    public $dataNascimento;

    public function __construct($nomePessoa, $cpf, $fotoPerfil, $sobre, $dataNascimento){
        $this->nomePessoa = $nomePessoa;
        $this->cpf = $cpf;
        $this->fotoPerfil = $fotoPerfil;
        $this->sobre = $sobre;
        $this->dataNascimento = $dataNascimento;
    }
    public function __get($valor){
        return $this->$valor;
    }
    public function __set($valor, $campo){
        return  $this->$valor = $campo;
    }
}
?>