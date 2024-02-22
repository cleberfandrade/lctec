<?php
namespace App\Models;

use Core\Model;

class Avisos extends Model
{ 
    private $tabela = 'tb_avisos';
    private $Model = '';
    private $codigo,$codEmpresa,$codUsuario, $descricao,$tipo;

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
    public function setCodUsuario($codUsuario)
    {
        $this->codUsuario = $codUsuario;
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
        $parametros = "A INNER JOIN tb_empresas E ON A.EMP_COD=E.EMP_COD WHERE A.EMP_COD={$this->codEmpresa} AND A.AVS_COD={$this->codigo}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarAvisoUsuario($ver = 0)
    {
        $parametros = "A INNER JOIN tb_empresas E ON A.EMP_COD=E.EMP_COD WHERE A.EMP_COD={$this->codEmpresa} AND A.AVS_USU_COD={$this->codUsuario}";
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
        $parametros = "A INNER JOIN tb_empresas E ON A.EMP_COD=E.EMP_COD WHERE A.EMP_COD={$this->codEmpresa} AND A.AVS_TIPO={$this->tipo} ORDER BY A.AVS_DT_CADASTRO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodos($ver = 0)
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} ORDER BY AVS_DT_CADASTRO";
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
    public function excluir(array $dados, $ver = 0)
    {
        $parametros = " WHERE EMP_COD={$this->codEmpresa} AND AVS_COD=";
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND AVS_DESCRICAO ='{$this->descricao}' AND AVS_STATUS=1";
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