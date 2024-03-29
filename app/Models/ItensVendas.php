<?php
namespace App\Models;

use Core\Model;

class ItensVendas extends Model
{ 
    private $tabela = 'tb_itens_vendas';
    private $Model = '';
    private $codigo,$codEmpresa,$codProduto,$codVenda,$FormasPagamentos,$codCaixa,$codCliente;

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
    public function listar($ver = 0)
    {
        $parametros = "IV INNER JOIN tb_empresas E ON E.EMP_COD=IV.EMP_COD WHERE IV.EMP_COD={$this->codEmpresa} AND IV.VEN_COD={$this->codVenda} ORDER BY IV.ITS_COD";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarTodas($ver = 0)
    {
        $parametros = "IV INNER JOIN tb_empresas E ON E.EMP_COD=IV.EMP_COD WHERE IV.EMP_COD={$this->codEmpresa} ORDER BY IV.ITS_COD";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
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
        $parametros = " IV INNER JOIN tb_empresas E ON E.EMP_COD=IV.EMP_COD WHERE IV.EMP_COD={$this->codEmpresa} AND IV.VEN_COD=";
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
    public function excluirItem(array $dados, $ver = 0)
    {
        $parametros = " IV INNER JOIN tb_empresas E ON E.EMP_COD=IV.EMP_COD WHERE IV.EMP_COD={$this->codEmpresa} AND IV.ITS_COD=";
        $this->Model->setParametros($parametros);
        $this->Model->setCodigo($this->codigo);
        $ok = false;
        $ok = $this->Model->deletar($dados, $ver);
        if ($ok) {
            return true;
        } else {
            return false;
        }
    }
    public function excluirTodosItens(array $dados, $ver = 0)
    {
        $parametros = " IV INNER JOIN tb_empresas E ON E.EMP_COD=IV.EMP_COD WHERE IV.EMP_COD={$this->codEmpresa} AND IV.VEN_COD=";
        $this->Model->setParametros($parametros);
        $this->Model->setCodigo($this->codigo);
        $ok = false;
        $ok = $this->Model->deletar($dados, $ver);
        if ($ok) {
            return true;
        } else {
            return false;
        }
    }
}