<?php
namespace App\Models;

use Core\Model;
/*
CREATE TABLE `tb_af_caixas` (
	`AFC_COD` INT NOT NULL AUTO_INCREMENT,
	`EMP_COD` INT NOT NULL,
	`CXA_COD` INT NOT NULL,
	`MVC_COD` INT NOT NULL,
	`USU_COD_ABERTURA` INT NOT NULL,
	`USU_COD_FECHAMENTO` INT NOT NULL,
	`AFC_DT_CADASTRO` DATETIME NOT NULL,
	`AFC_DT_ATUALIZACAO` DATETIME NULL,
	`AFC_DT_ABERTURA` DATETIME NOT NULL,
	`AFC_DT_FECHAMENTO` DATETIME NULL,
	`AFC_SALDO_INICIAL` DECIMAL(20,2) NOT NULL,
	`AFC_SALDO_FINAL` DECIMAL(20,2) NOT NULL,
	`AFC_STATUS` INT(11) NOT NULL,
	PRIMARY KEY (`AFC_COD`),
	INDEX `EMP_COD` (`EMP_COD`),
	INDEX `USU_COD` (`USU_COD_ABERTURA`),
	INDEX `CXA_COD` (`CXA_COD`),
	INDEX `USU_COD_FECHAMENTO` (`USU_COD_FECHAMENTO`),
	INDEX `MVC_COD` (`MVC_COD`)
)
COLLATE='utf8_general_ci'
;
*/
class AberturaFechamentoCaixas extends Model
{ 
    private $tabela = 'tb_af_caixas';
    private $Model = '';
    private $codigo,$codEmpresa,$codCaixa,$descricao,$tipo,$data;

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
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    public function checarStatusCaixa($ver = 0)
    {
        $parametros = "AF INNER JOIN tb_empresas E ON AF.EMP_COD=E.EMP_COD INNER JOIN tb_caixas C ON AF.CXA_COD=C.CXA_COD WHERE AF.EMP_COD={$this->codEmpresa} AND AF.CXA_COD={$this->codCaixa} AND AF.AFC_DT_ABERTURA='{$this->data}' DESC LIMIT 1";
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
    public function listar($ver = 0)
    {
        $parametros = "AF INNER JOIN tb_empresas E ON AF.EMP_COD=E.EMP_COD INNER JOIN tb_caixas C ON AF.CXA_COD=C.CXA_COD WHERE AF.EMP_COD={$this->codEmpresa} AND AF.CXA_COD={$this->codCaixa} AND AF.AFC_COD={$this->codigo}";
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
        $parametros = "AB INNER JOIN tb_empresas E ON AB.EMP_COD=E.EMP_COD INNER JOIN tb_caixas C ON AB.CXA_COD=C.CXA_COD WHERE AB.EMP_COD={$this->codEmpresa} ORDER BY AB.AFC_DT_ABERTURA DESC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function checarAFCaixa($ver = 0)
    {
        $parametros = "AB INNER JOIN tb_empresas E ON AB.EMP_COD=E.EMP_COD INNER JOIN tb_caixas C ON AB.CXA_COD=C.CXA_COD WHERE AB.EMP_COD={$this->codEmpresa} AND AB.CXA_COD={$this->codCaixa} ORDER BY AB.ABF_DATA DESC LIMIT 1";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
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