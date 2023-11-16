<?php
namespace App\Models;

use Core\Model;

class Clientes extends Model
{ 
    private $tabela = 'tb_clientes';
    private $Model = '';
    private $codigo,$codEmpresa,$codRegistro;

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
    public function setcodRegistro($codRegistro)
    {
        $this->codRegistro = $codRegistro;
        return $this;
    }
    public function listar($ver = 0)
    {
        $parametros = "C INNER JOIN tb_enderecos ED ON C.CLI_COD=ED.CLI_COD WHERE C.EMP_COD={$this->codEmpresa} AND C.CLI_COD={$this->codigo}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarSemEndereco($ver = 0)
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND CLI_COD={$this->codigo}";
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} ORDER BY CLI_NOME";
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
        $parametros = " WHERE EMP_COD={$this->codEmpresa} AND CLI_COD=";
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
        $parametros = " WHERE EMP_COD={$this->codEmpresa} AND CLI_COD=";
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
    public function checarRegistroCliente()
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND CLI_REGISTRO='{$this->codRegistro}' AND CLI_STATUS=1";
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