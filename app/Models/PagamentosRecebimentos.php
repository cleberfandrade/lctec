<?php
namespace App\Models;

use Core\Model;

class PagamentosRecebimentos extends Model
{ 
    private $tabela = 'tb_pagamentos_recebimentos';
    private $Model = '';
    private $codigo,$codUsuario,$codProduto, $codEstoque,$codEmpresa,$tipo,$status;

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
    public function setTipo($tipo)
    {
        $this->tipo = $tipo;
        return $this;
    }
    public function setStatus($status)
    {
        $this->status = $status;
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
        $parametros = "P INNER JOIN tb_empresas E ON E.EMP_COD=P.EMP_COD INNER JOIN tb_contas C ON C.CTA_COD=P.CTA_COD INNER JOIN tb_lancamentos L ON L.LAN_COD=P.LAN_COD INNER JOIN tb_formas_pagamentos FP ON FP.FPG_COD=P.FPG_COD WHERE P.PAG_COD={$this->codigo} AND P.EMP_COD={$this->codEmpresa}";
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
        $parametros = "P INNER JOIN tb_empresas E ON E.EMP_COD=P.EMP_COD INNER JOIN tb_contas C ON C.CTA_COD=P.CTA_COD INNER JOIN tb_lancamentos L ON L.LAN_COD=P.LAN_COD INNER JOIN tb_formas_pagamentos FP ON FP.FPG_COD=P.FPG_COD WHERE P.EMP_COD={$this->codEmpresa} ORDER BY P.PAG_DT_PAGAMENTO DESC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function filtrarTodosPagamentosRecebimentos(array $dados,$ver = 0)
    {
        (isset($dados['TIPO']) && $dados['TIPO'] != 0) ? $tipo = " AND P.PAG_TIPO=".$dados['TIPO'].""  : $tipo = '';
        (isset($dados['DATA_INICIAL']) && isset($dados['DATA_FINAL']) ?  $data = ' AND P.PAG_DT_PAGAMENTO BETWEEN "'.$dados['DATA_INICIAL'].'" AND "'.$dados['DATA_FINAL'].'"' : $data = ''); 
        (isset($dados['QTD']) && $dados['QTD'] != 0)? $limit = $dados['QTD'] : $limit = 100;
        $parametros = "P INNER JOIN tb_empresas E ON E.EMP_COD=P.EMP_COD LEFT OUTER JOIN tb_contas C ON C.CTA_COD=P.CTA_COD WHERE P.EMP_COD={$this->codEmpresa} {$tipo} {$data} ORDER BY P.PAG_DT_CADASTRO DESC LIMIT {$limit}";
        
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodosTipo($ver = 0)
    {
        $parametros = "P INNER JOIN tb_empresas E ON E.EMP_COD=P.EMP_COD WHERE P.EMP_COD={$this->codEmpresa} AND P.PAG_TIPO='{$this->tipo}' ORDER BY P.PAG_DT_PAGAMENTO DESC";
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND PAG_COD=";
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
        $parametros = "WHERE EMP_COD='{$this->codEmpresa}' AND PAG_COD=";
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
    public function checarPagamento()
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND PAG_COD={$this->codigo}";
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