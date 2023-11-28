<?php
namespace App\Models;

use Core\Model;

class FormasPagamentos extends Model
{ 
    private $tabela = 'tb_formas_pagamentos';
    private $Model = '';
    private $codigo,$codUsuario,$codConta,$codEmpresa,$registro,$descricao;

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
    public function setRegistro($registro)
    {
        $this->registro = $registro;
        return $this;
    }
    public function setDescricao($descricao)
    {
        $this->descricao = $descricao;
        return $this;
    }
    public function listar($ver = 0)
    {
        $parametros = "FP INNER JOIN tb_empresas E ON E.EMP_COD=FP.EMP_COD WHERE FP.FPG_COD={$this->codigo} AND FP.EMP_COD={$this->codEmpresa}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarTodas($ver = 0)
    {
        $parametros = "FP INNER JOIN tb_empresas E ON E.EMP_COD=FP.EMP_COD WHERE FP.EMP_COD={$this->codEmpresa}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasSistema($ver = 0)
    {
        $parametros = " WHERE EMP_COD=0 AND FPG_STATUS=1";
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
        $parametros = " WHERE EMP_COD={$this->codEmpresa} AND FPG_COD=";
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND FPG_DESCRICAO='{$this->registro}'";
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
    public function checarDescricao($ver = 0)
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND FPG_DESCRICAO='{$this->descricao}' AND FPG_STATUS=1";
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