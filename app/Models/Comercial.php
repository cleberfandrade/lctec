<?php
namespace App\Models;

use Core\Model;

class Colunas extends Model
{ 
    private $tabela = 'tb_comercial';
    private $Model = '';
    private $codigo, $descricao, $tipo, $codEmpresa, $data;

    public function __construct()
    {
        $this->Model = new Model();
        $this->Model->setTabela($this->tabela);
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }
    public function setCodEmpresa($codEmpresa)
    {
        $this->codEmpresa = $codEmpresa;
        return $this;
    }
    public function listar($ver = 0)
    {
        $parametros = "C INNER JOIN tb_empresas E ON C.EMP_COD=E.EMP_COD WHERE C.EMP_COD={$this->codEmpresa} AND C.CLN_COD={$this->codigo}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarTodos($ver = 0)
    {
        $parametros = "C INNER JOIN tb_empresas E ON E.EMP_COD=C.EMP_COD WHERE C.EMP_COD={$this->codEmpresa} ORDER BY C.CLN_DT_CADASTRO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
}