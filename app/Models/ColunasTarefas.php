<?php
namespace App\Models;

use Core\Model;

class ColunasTarefas extends Model
{ 
    private $tabela = 'tb_colunas_tarefas';
    private $Model = '';
    private $codigo, $codEmpresa, $codigoTarefa;

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
    public function listar($ver = 0)
    {
        $parametros = " WHERE CLT_COD='{$this->codigo}'";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarColunasTarefas($ver = 0)
    {
        $parametros = "C INNER JOIN tb_empresas E ON E.EMP_COD=C.EMP_COD INNER JOIN tb_colunas CLN ON CLN.CLN_COD=C.CLN_COD INNER JOIN tb_tarefas T ON T.TRF_COD=C.TRF_COD WHERE C.EMP_COD={$this->codEmpresa} ORDER BY C.CLT_DT_CADASTRO";
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
    public function excluir($ver = 0)
    {
        $parametros = "WHERE EMP_COD='{$this->codEmpresa}' AND CLN_COD=";
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
}