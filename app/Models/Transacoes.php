<?php
namespace App\Models;

use Core\Model;

class Transacoes extends Model
{ 
    private $tabela = 'tb_transacoes';
    private $Model = '';
    private $codigo,$codUsuario,$codProduto, $codEstoque,$codEmpresa,$tipo,$status,$codConta,$data;

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
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }
    public function setCodConta($codConta)
    {
        $this->codConta = $codConta;
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
    public function setCodEstoque($codEstoque)
    {
        $this->codEstoque = $codEstoque;
        return $this;
    }
    public function setCodProduto($codProduto)
    {
        $this->codProduto = $codProduto;
        return $this;
    }
    public function listar($ver = 0)
    {
        $parametros = "T INNER JOIN tb_empresas E ON T.EMP_COD=E.EMP_COD WHERE T.EMP_COD={$this->codEmpresa} AND T.TRS_COD={$this->codigo}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarTodasTransacoes($ver = 0)
    {
        $parametros = "T INNER JOIN tb_empresas E ON E.EMP_COD=T.EMP_COD OUTER LEFT JOIN tb_contas C ON C.CTA_COD=T.CTA_COD WHERE T.EMP_COD={$this->codEmpresa} ORDER BY T.TRS_DT_CADASTRO DESC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasTransacoesContaPorTipo($ver = 0)
    {
        $parametros = "T INNER JOIN tb_empresas E ON E.EMP_COD=T.EMP_COD INNER JOIN tb_contas C ON C.CTA_COD=T.CTA_COD WHERE T.CTA_COD={$this->codConta} AND T.EMP_COD={$this->codEmpresa} AND T.TRS_TIPO={$this->tipo} ORDER BY T.TRS_DT_CADASTRO DESC";
        $campos = "SUM(T.TRS_VALOR_TOTAL) AS TOTAL,*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasTransacoesConta($ver = 0)
    {
        $parametros = "T INNER JOIN tb_empresas E ON E.EMP_COD=T.EMP_COD INNER JOIN tb_contas C ON C.CTA_COD=T.CTA_COD WHERE T.CTA_COD={$this->codConta} AND T.EMP_COD={$this->codEmpresa} ";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasPorTipo($ver = 0)
    {
        if ($this->status) {
            $parametros = "M INNER JOIN tb_empresas E ON E.EMP_COD=M.EMP_COD INNER JOIN tb_estoques ES ON ES.EST_COD=M.EST_COD WHERE M.EMP_COD={$this->codEmpresa} AND M.MOV_TIPO={$this->tipo} AND M.MOV_STATUS={$this->status} ORDER BY M.MOV_COD DESC";
        } else {
            $parametros = "M INNER JOIN tb_empresas E ON E.EMP_COD=M.EMP_COD INNER JOIN tb_estoques ES ON ES.EST_COD=M.EST_COD WHERE M.EMP_COD={$this->codEmpresa} AND M.MOV_TIPO={$this->tipo} ORDER BY M.MOV_COD DESC";
        }
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND CTA_COD={$this->codConta} AND TRS_COD=";
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
    public function checarRegistro($ver = 0)
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND CTA_COD={$this->codConta} AND TRS_DATA='{$this->data}'";
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