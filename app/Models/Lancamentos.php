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
        
        (isset($dados['LAN_TIPO']) && $dados['LAN_TIPO'] != 0) ? $tipo = " AND L.LAN_TIPO=".$dados['LAN_TIPO'].""  : $tipo = '';
        (isset($dados['LAN_RESULTADOS']) && $dados['LAN_RESULTADOS']  == 1)? $resultados = 'AND L.LAN_RESULTADOS="1"': (($dados['LAN_RESULTADOS']  == 2) ? $resultados = ' AND L.LAN_RESULTADOS="0"' : $resultados = '');
        (isset($dados['DATA']) ?  $resultados = ' AND L.LAN_DT_VENCIMENTO BETWEEN "'.$dados['LAN_DT_INICIAL'].'" AND "'.$dados['LAN_DT_FINAL'].'"' : ''); 
        
        (isset($dados['LAN_PAGINA']) && $dados['LAN_PAGINA'] != 0)? $pagina = $dados['LAN_PAGINA'] : $pagina = 0;
        (isset($dados['LAN_QTD']) && $dados['LAN_QTD'] != 0)? $qtd = $dados['LAN_QTD'] : $qtd = 10;

        $inicio = ($pagina * $qtd) - $pagina;
       // dump($qtd);

        $parametros = "L INNER JOIN tb_empresas E ON E.EMP_COD=L.EMP_COD WHERE L.EMP_COD={$this->codEmpresa}{$tipo}{$resultados} ORDER BY L.LAN_DT_CADASTRO LIMIT $inicio, $pagina";
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
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodosTipo($ver = 0)
    {
        $parametros = "L INNER JOIN tb_empresas E ON E.EMP_COD=L.EMP_COD WHERE L.EMP_COD={$this->codEmpresa} AND L.LAN_TIPO='{$this->tipo}' ORDER BY L.LAN_DT_VENCIMENTO DESC";
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