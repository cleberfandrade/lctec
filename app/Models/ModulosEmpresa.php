<?php
namespace App\Models;

use Core\Model;

class ModulosEmpresa extends Model
{ 
    private $tabela = 'tb_modulos_empresa';
    private $Model = '';
    private $Informacoes = '';
    private $codigo,$codModulo, $codUsuario, $codEmpresa;

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
    public function setCodModulo($codModulo)
    {
        $this->codModulo = $codModulo;
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
    public function listar($ver = 0)
    {
        $parametros = " ME INNER JOIN tb_modulos M ON M.MOD_COD=ME.MOD_COD WHERE ME.EMP_COD={$this->codEmpresa} ORDER BY M.MOD_NOME";
        //ativar quando não quiser que apareça até mesmo no menu lateral SOMENTE com o módulo desativado, não irá aparecer
        //$parametros = " ME INNER JOIN tb_modulos M ON M.MOD_COD=ME.MOD_COD WHERE ME.EMP_COD={$this->codEmpresa} AND M.MOD_STATUS=1 ORDER BY M.MOD_NOME";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarModuloEmpresa($ver = 0)
    {
        $parametros = " ME INNER JOIN tb_modulos M ON M.MOD_COD=ME.MOD_COD WHERE ME.EMP_COD={$this->codEmpresa} AND ME.MOD_COD={$this->codigo} ORDER BY M.MOD_NOME";
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
        $parametros = " ME INNER JOIN tb_modulos M ON M.MOD_COD=ME.MOD_COD ORDER BY ME.EMP_COD";
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND MOD_EMP_COD=";
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
    public function excluir($ver = 0)
    {
        $parametros = "WHERE EMP_COD='{$this->codEmpresa}' AND MOD_COD='{$this->codModulo}' AND MLE_COD=";
        $this->Model->setParametros($parametros);
        $this->Model->setCodigo($this->codigo);
        $ok = false;
        $ok = $this->Model->deletar($ver);
        if ($ok) {
            return true;
        } else {
            return false;
        }
    }
    public function checarRegistroModuloEmpresa($ver = 0)
    {
        $parametros = "WHERE EMP_COD='{$this->codEmpresa}' AND MOD_COD='{$this->codModulo}'";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            //Já existe
            return $resultado[0];
        } else {
            //Nao existe
            return false;
        }
    }
}