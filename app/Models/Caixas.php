<?php
namespace App\Models;

use Core\Model;

/*
 CREATE TABLE `tb_caixas` (
	`CXA_COD` INT NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT NOT NULL,
	`USU_COD` INT NOT NULL,
	`CTA_COD` INT NOT NULL,
	`CXA_DT_CADASTRO` DATETIME NOT NULL,
	`CXA_DT_ATUALIZACAO` DATETIME NULL,
	`CXA_DESCRICAO` VARCHAR(150) NOT NULL,
	`CXA_SALDO` DECIMAL(20,2) NULL DEFAULT NULL,
	`CXA_STATUS` INT NOT NULL,
	PRIMARY KEY (`CXA_COD`),
	INDEX `EMP_COD` (`EMP_COD`),
	INDEX `USU_COD` (`USU_COD`),
	INDEX `CTA_COD` (`CTA_COD`)
)
COLLATE='utf8_general_ci'
; */

class Caixas extends Model
{ 
    private $tabela = 'tb_caixas';
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
        $parametros = "C INNER JOIN tb_empresas E ON C.EMP_COD=E.EMP_COD WHERE C.EMP_COD={$this->codEmpresa} AND C.CXA_COD={$this->codigo}";
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
        $parametros = "C INNER JOIN tb_empresas E ON C.EMP_COD=E.EMP_COD INNER JOIN tb_contas CT ON CT.CTA_COD=C.CTA_COD WHERE C.EMP_COD={$this->codEmpresa} ORDER BY C.CXA_DESCRICAO";
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
        $parametros = " WHERE EMP_COD={$this->codEmpresa} AND CXA_COD=";
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
    public function checarDescricao()
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND CXA_DESCRICAO ='{$this->descricao}' AND CXA_STATUS=1";
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