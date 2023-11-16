<?php
namespace App\Models;

use Core\Model;

class Servicos extends Model
{ 
    private $tabela = 'tb_servicos';
    private $Model = '';
    private $Informacoes = '';
    private $codInformacoes = 1;

    public function __construct()
    {
        $this->Model = new Model();
        $this->Model->setTabela($this->tabela);
    }
    public function setCodInformacoes($codInformacoes)
    {
        $this->codInformacoes = $codInformacoes;
        return $this;
    }
   
    public function listar($ver = 0)
    {
        $parametros = "";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function alterar(array $dados, $ver = 0)
    {
        $parametros = " WHERE INF_COD=";
        $this->Model->setParametros($parametros);
        $this->Model->setCodigo($this->codInformacoes);
        $ok = false;
        $ok = $this->Model->alterar($dados, $ver);
        if ($ok) {
            return true;
        } else {
            return false;
        }
    }
}