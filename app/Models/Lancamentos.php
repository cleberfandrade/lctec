<?php
namespace App\Models;

use Core\Model;

class Lancamentos extends Model
{ 
    private $tabela = 'tb_lancamentos';
    private $Model = '';
    private $codigo, $descricao, $tipo, $codEmpresa, $codVenda, $codFornecedor, $dataVencimento,$status;

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
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    public function setCodEmpresa($codEmpresa)
    {
        $this->codEmpresa = $codEmpresa;
        return $this;
    }
    public function setDataVencimento($dataVencimento)
    {
        $this->dataVencimento = $dataVencimento;
        return $this;
    }
    public function setCodFornecedor($codFornecedor)
    {
        $this->codFornecedor = $codFornecedor;
        return $this;
    }
    public function listar($ver = 0)
    {
        $parametros = "L INNER JOIN tb_empresas E ON E.EMP_COD=L.EMP_COD WHERE L.EMP_COD={$this->codEmpresa} AND L.LAN_COD={$this->codigo}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarFiltro(array $dados, $ver = 0)
    {
        //dump($dados);
        (isset($dados['LAN_TIPO']) && $dados['LAN_TIPO'] != 0) ? $tipo = " AND L.LAN_TIPO=".$dados['LAN_TIPO'].""  : $tipo = '';
        (isset($dados['LAN_RESULTADOS']) && $dados['LAN_RESULTADOS'] != 2)? $resultados = " AND L.LAN_RESULTADOS=".$dados['LAN_RESULTADOS']."" : $resultados = '';
        (isset($dados['DATA']) ?  $data = ' AND L.LAN_DT_VENCIMENTO BETWEEN "'.$dados['LAN_DT_INICIAL'].'" AND "'.$dados['LAN_DT_FINAL'].'"' : $data = ''); 
        
        (isset($dados['LAN_PAGINA']) && $dados['LAN_PAGINA'] != 0)? $pagina = $dados['LAN_PAGINA'] : $pagina = 1;
        (isset($dados['LAN_QTD']) && $dados['LAN_QTD'] != 0 && $dados['LAN_QTD'] != 10)? $limit = $dados['LAN_QTD'] : $limit = 100;
        (isset($dados['LAN_STATUS']) && $dados['LAN_STATUS'] != 0 && $dados['LAN_STATUS'] != '') ? $status = " AND L.LAN_STATUS=".$dados['LAN_STATUS'].""  : $status = '';
        $qtd = (isset($dados['LAN_QTD_TOTAL'])? $dados['LAN_QTD_TOTAL'] : 0);

        $offset = ($pagina - 1) * $limit;
        //$n_pagina = ceil($dados['LAN_QTD_TOTAL']/$limit);
        //$offset = ($pagina * $qtd) - $limit;
        //dump($limit);

        //$parametros = "L INNER JOIN tb_empresas E ON E.EMP_COD=L.EMP_COD WHERE L.EMP_COD={$this->codEmpresa}{$tipo}{$resultados}{$data} ORDER BY L.LAN_DT_CADASTRO LIMIT {$limit} OFFSET {$offset}";
        $parametros = "L INNER JOIN tb_empresas E ON E.EMP_COD=L.EMP_COD WHERE L.EMP_COD={$this->codEmpresa}{$tipo}{$resultados}{$data}{$status} ORDER BY L.LAN_DT_CADASTRO LIMIT {$offset},{$limit}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        //dump($resultado);
        //exit;
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodos($ver = 0)
    {
        $parametros = "L INNER JOIN tb_empresas E ON E.EMP_COD=L.EMP_COD WHERE L.EMP_COD={$this->codEmpresa} ORDER BY L.LAN_DT_CADASTRO";
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
        $parametros = "L INNER JOIN tb_empresas E ON E.EMP_COD=L.EMP_COD WHERE L.EMP_COD={$this->codEmpresa} AND L.LAN_TIPO='{$this->tipo}' AND L.LAN_STATUS='{$this->status}' ORDER BY L.LAN_DT_VENCIMENTO DESC";
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND LAN_COD=";
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
        $parametros = "WHERE EMP_COD='{$this->codEmpresa}' AND LAN_COD=";
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
    public function checarDescricao()
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND LAN_DESCRICAO='{$this->descricao}' AND LAN_DT_VENCIMENTO='{$this->dataVencimento}'";
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