<?php
namespace App\Models;

use Core\Model;

class Producao extends Model
{ 
    private $tabela = '';
    private $Model = '';
    private $codigo,$codEmpresa,$codRegistro;

    public function __construct()
    {
        //$this->Model = new Model();
        //$this->Model->setTabela($this->tabela);
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }
    public function setCodEmpresa($codEmpresa)
    {
        $this->codEmpresa = $codEmpresa;
        return $this;   
    }
}