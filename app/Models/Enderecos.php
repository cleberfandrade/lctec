<?php 
namespace App\Models;

use Core\Model;

class Enderecos extends Model
{
    private $tabela = 'tb_enderecos';
    private $Model = '';
    private $codigo,$codEmpresa,$codUsuario,$codFornecedor,$codCliente,$codColaborador;
    public function __construct()
    {
        $this->Model = new Model();
        $this->Model->setTabela($this->tabela);
    }
    public function setCodigo($cod)
    {
        $this->codigo = $cod;
        return $this;
    }
    public function setCodUsuario($cod)
    {
        $this->codUsuario = $cod;
        return $this;
    }
    public function setCodCliente($cod)
    {
        $this->codCliente = $cod;
        return $this;
    }
    public function setCodFornecedor($cod)
    {
        $this->codFornecedor = $cod;
        return $this;
    }
    public function setCodEmpresa($cod)
    {
        $this->codEmpresa = $cod;
        return $this;
    }

    public function setCodColaborador($cod)
    {
        $this->codColaborador = $cod;
        return $this;
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
    public function listar($ver = 0)
    {
        $parametros = " WHERE END_COD='{$this->codigo}'";
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
        $parametros = " WHERE EMP_COD={$this->codEmpresa}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function checarEnderecoUsuario($ver = 0)
    {
        $parametros = " WHERE USU_COD='{$this->codUsuario}'";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            //Já existe
            return $resultado;
        } else {
            //Nao existe
            return false;
        }
    }
    public function checarEnderecoCliente($ver = 0)
    {
        $parametros = " WHERE CLI_COD='{$this->codCliente}'";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            //Já existe
            return $resultado;
        } else {
            //Nao existe
            return false;
        }
    }
    public function checarEnderecoFornecedor($ver = 0)
    {
        $parametros = "WHERE FOR_COD={$this->codFornecedor}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            //Já existe
            return $resultado;
        } else {
            //Nao existe
            return false;
        }
    }
    public function checarEnderecoEmpresa($ver = 0)
    {
        $parametros = " WHERE EMP_COD={$this->codEmpresa}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            //Já existe
            return $resultado;
        } else {
            //Nao existe
            return false;
        }
    }
    public function checarEnderecoColaborador($ver = 0)
    {
        $parametros = " WHERE COL_COD={$this->codColaborador}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            //Já existe
            return $resultado;
        } else {
            //Nao existe
            return false;
        }
    }
    public function alterar(array $dados, $ver = 0)
    {
        $parametros = " WHERE USU_COD='{$this->codUsuario}' AND END_COD=";
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
    public function alterarCliente(array $dados, $ver = 0)
    {
        $parametros = " WHERE CLI_COD='{$this->codCliente}' AND END_COD=";
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
    public function alterarFornecedor(array $dados, $ver = 0)
    {
        $parametros = " WHERE FOR_COD={$this->codFornecedor} AND END_COD=";
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
    public function alterarEmpresa(array $dados, $ver = 0)
    {
        $parametros = " WHERE EMP_COD={$this->codEmpresa} AND END_COD=";
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
    public function alterarColaborador(array $dados, $ver = 0)
    {
        $parametros = " WHERE COL_COD={$this->codColaborador} AND END_COD=";
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