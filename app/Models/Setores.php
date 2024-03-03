<?php
namespace App\Models;

use Core\Model;

class Setores extends Model
{ 
    private $tabela = 'tb_setores';
    private $Model = '';
    private $codigo,$codEmpresa,$descricao,$tipo;

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
    public function setCodEmpresa($codEmpresa)
    {
        $this->codEmpresa = $codEmpresa;
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
    public function listar($ver = 0)
    {
        $parametros = "S INNER JOIN tb_empresas E ON S.EMP_COD=E.EMP_COD WHERE S.EMP_COD={$this->codEmpresa} AND S.SET_COD={$this->codigo}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarTodosPorTipo($ver = 0)
    {
        $parametros = "S INNER JOIN tb_empresas E ON S.EMP_COD=E.EMP_COD WHERE S.EMP_COD={$this->codEmpresa} AND S.SET_TIPO='{$this->tipo}' ORDER BY S.SET_DESCRICAO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodos($ver = 0)
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} ORDER BY SET_DESCRICAO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
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
        $parametros = " WHERE EMP_COD={$this->codEmpresa} AND SET_COD=";
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
    public function excluir(array $dados, $ver = 0)
    {
        $parametros = " WHERE EMP_COD={$this->codEmpresa} AND SET_COD=";
        $this->Model->setParametros($parametros);
        $this->Model->setCodigo($this->codigo);
        $ok = false;
        $ok = $this->Model->deletar($dados, $ver);
        if ($ok) {
            return true;
        } else {
            return false;
        }
    }
    public function checarDescricao()
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND SET_DESCRICAO ='{$this->descricao}' AND SET_STATUS=1";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            //Já existe
            return $resultado[0];
        } else {
            //Nao existe
            return false;
        }
    }
}