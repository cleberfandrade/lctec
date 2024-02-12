<?php
namespace App\Models;

use Core\Model;

class Produtos extends Model
{ 
    private $tabela = 'tb_produtos';
    private $Model = '';
    private $codigo,$codEstoque,$codCliente,$tipo, $codEmpresa,$codVenda, $codFornecedor,$nome,$status;

    public function __construct()
    {
        $this->Model = new Model();
        $this->Model->setTabela($this->tabela);
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
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }
    public function setCodEstoque($codEstoque)
    {
        $this->codEstoque = $codEstoque;
        return $this;
    }
    public function setCodFornecedor($codFornecedor)
    {
        $this->codFornecedor = $codFornecedor;
        return $this;
    }
    public function setCodEmpresa($codEmpresa)
    {
        $this->codEmpresa = $codEmpresa;
        return $this;
    }

    public function listar($ver)
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND EST_COD={$this->codEstoque} AND PRO_COD={$this->codigo}";
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
        $parametros = "P INNER JOIN tb_empresas E ON P.EMP_COD=E.EMP_COD INNER JOIN tb_estoques ET ON P.EST_COD=ET.EST_COD WHERE P.EMP_COD={$this->codEmpresa} AND P.EST_COD={$this->codEstoque} ORDER BY P.PRO_COD";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodosGeral($ver = 0)
    {
        $parametros = "P INNER JOIN tb_empresas E ON P.EMP_COD=E.EMP_COD INNER JOIN tb_estoques ET ON P.EST_COD=ET.EST_COD WHERE P.EMP_COD={$this->codEmpresa} AND P.PRO_STATUS={$this->status} ORDER BY P.PRO_COD";
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
        if ($this->status) {
            $parametros = "P INNER JOIN tb_empresas E ON P.EMP_COD=E.EMP_COD INNER JOIN tb_estoques ET ON P.EST_COD=ET.EST_COD WHERE P.EMP_COD={$this->codEmpresa} AND P.EST_COD={$this->codEstoque} AND P.PRO_TIPO={$this->tipo} AND P.PRO_STATUS={$this->status} ORDER BY P.PRO_COD";    
        }else {
            $parametros = "P INNER JOIN tb_empresas E ON P.EMP_COD=E.EMP_COD INNER JOIN tb_estoques ET ON P.EST_COD=ET.EST_COD WHERE P.EMP_COD={$this->codEmpresa} AND P.EST_COD={$this->codEstoque} AND P.PRO_TIPO={$this->tipo} ORDER BY P.PRO_COD";
        }
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
        $parametros = " WHERE PRO_COD=";
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
    public function excluir(array $dados, $ver = 0)
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND EST_COD={$this->codEstoque} AND PRO_COD=";
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
    public function checarNomeProduto()
    {
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND EST_COD={$this->codEstoque} AND PRO_NOME='{$this->nome}'";
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