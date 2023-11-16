<?php
namespace App\Models;

use Core\Model;

class Fornecedores extends Model
{ 
    private $tabela = 'tb_fornecedores';
    private $Model = '';
    private $codigo,$codUsuario,$codEmpresa,$codRegistro;

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
    public function setCodEmpresa($empresa)
    {
        $this->codEmpresa = $empresa;
        return $this;
    }
    public function setcodRegistro($codRegistro)
    {
        $this->codRegistro = $codRegistro;
        return $this;
    }
    public function listar($ver = 0)
    {
        $parametros = "F INNER JOIN tb_enderecos ED ON F.FOR_COD=ED.FOR_COD WHERE F.EMP_COD='{$this->codEmpresa}' AND F.FOR_COD='{$this->codigo}'";
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
        $parametros = "WHERE EMP_COD='{$this->codEmpresa}' AND FOR_STATUS='1'";
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
        $parametros = " WHERE EMP_COD='{$this->codEmpresa}' AND FOR_COD=";
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
    public function checarRegistro()
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND FOR_REGISTRO='{$this->codRegistro}' AND FOR_STATUS=1";
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