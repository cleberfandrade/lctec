<?php
namespace App\Models;

use Core\Model;

class AberturaFechamentoCaixa extends Model
{ 
    private $tabela = 'tb_ab_caixas';
    private $Model = '';
    private $codigo,$codEmpresa,$codCaixa,$descricao,$tipo;

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
    public function setCodCaixa($codCaixa)
    {
        $this->codCaixa = $codCaixa;
        return $this;
    }
    public function setCodEmpresa($codEmpresa)
    {
        $this->codEmpresa = $codEmpresa;
        return $this;   
    }
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }
    public function listar($ver = 0)
    {
        $parametros = "AB INNER JOIN tb_empresas E ON AB.EMP_COD=E.EMP_COD INNER JOIN tb_caixas C ON AB.CXA_COD=C.CXA_COD WHERE AB.EMP_COD={$this->codEmpresa} AND AB.CXA_COD={$this->codCaixa} AND AB.ABF_COD={$this->codigo}";
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
        $parametros = "AB INNER JOIN tb_empresas E ON AB.EMP_COD=E.EMP_COD INNER JOIN tb_caixas C ON AB.CXA_COD=C.CXA_COD WHERE AB.EMP_COD={$this->codEmpresa} AND AB.CXA_COD={$this->codCaixa} ORDER BY AB.ABF_DATA DESC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodosPorTipo($ver = 0)
    {
        $parametros = "AB INNER JOIN tb_empresas E ON AB.EMP_COD=E.EMP_COD INNER JOIN tb_caixas C ON AB.CXA_COD=C.CXA_COD WHERE AB.EMP_COD={$this->codEmpresa} AND AB.CXA_COD={$this->codCaixa} ORDER BY AB.ABF_DATA DESC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function cadastrar(array $dados, $ver = 0)
    {
        $ok = $this->Model->cadastrar($dados, $ver);
        if ($ok) {
            return $ok;
        } else {
            return false;
        }
    }
    public function alterar(array $dados, $ver = 0)
    {
        $parametros = " WHERE EMP_COD={$this->codEmpresa} CXA_COD={$this->codCaixa} AND ABF_COD=";
        $this->Model->setParametros($parametros);
        $this->Model->setCodigo($this->codigo);
        $ok = false;
        $ok = $this->Model->alterar($dados, $ver);
        if ($ok) {
            return true;
        } else {
            return false;
        }
    }
}