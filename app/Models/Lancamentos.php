<?php
namespace App\Models;

use Core\Model;

class Lancamentos extends Model
{ 
    private $tabela = 'tb_lancamentos';
    private $Model = '';
    private $codigo, $descricao, $tipo, $codEmpresa, $codVenda, $codFornecedor, $dataVencimento;

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
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarFiltro(array $dados, $ver = 0)
    {
        $parametros = "L INNER JOIN tb_empresas E ON E.EMP_COD=L.EMP_COD WHERE L.EMP_COD={$this->codEmpresa} ORDER BY L.LAN_DT_CADASTRO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
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
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodosTipo($ver = 0)
    {
        $parametros = "L INNER JOIN tb_empresas E ON E.EMP_COD=L.EMP_COD WHERE L.EMP_COD={$this->codEmpresa} AND P.LAN_TIPO='{$this->tipo}' ORDER BY L.LAN_DT_VENCIMENTO DESC";
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