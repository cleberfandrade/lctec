<?php
namespace App\Models;

use Core\Model;

class Empresas extends Model
{ 
    private $tabela = 'tb_empresas';
    private $Model = '';
    private $Informacoes = '';
    private $codigo,$codUsuario,$codRegistro;

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
    public function setCodUsuario($usuario)
    {
        $this->codUsuario = $usuario;
        return $this;
    }
    public function setcodRegistro($codRegistro)
    {
        $this->codRegistro = $codRegistro;
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
    public function listarEmpresaUsuario($ver = 0)
    {
        $parametros = " E INNER JOIN tb_usuarios_empresa UE ON UE.EMP_COD=E.EMP_COD INNER JOIN tb_usuarios U ON U.USU_COD=UE.USU_COD WHERE UE.EMP_COD={$this->codigo} AND UE.USU_COD={$this->codUsuario}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listar($ver = 0)
    {
        $parametros = "WHERE EMP_COD={$this->codigo}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarTodos($ver = 0)
    {
        $parametros = "ORDER BY EMP_DT_CADASTRO,EMP_NOME_FANTASIA";
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
        $parametros = " WHERE EMP_COD=";
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
    public function checarRegistroEmpresa()
    {
        $parametros = "WHERE EMP_REGISTRO='{$this->codRegistro}'";
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