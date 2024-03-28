<?php
namespace App\Models;

use Core\Model;

class Vendas extends Model
{ 
    private $tabela = 'tb_vendas';
    private $Model = '';
    private $codigo,$codEmpresa,$codProduto,$data,$dtInicial,$dtFinal,$mes,$ano;

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
    public function setCodEmpresa($codEmpresa)
    {
        $this->codEmpresa = $codEmpresa;
        return $this;
    }
    public function setCodProduto($codProduto)
    {
        $this->codProduto = $codProduto;
        return $this;
    }
    public function listar($ver = 0)
    {
        $parametros = "V INNER JOIN tb_empresas E ON E.EMP_COD=V.EMP_COD WHERE V.EMP_COD={$this->codEmpresa} AND V.VEN_COD={$this->codigo} ORDER BY V.VEN_COD";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function ultimaVenda()
    {
        $parametros = "V INNER JOIN tb_empresas E ON E.EMP_COD=V.EMP_COD WHERE V.EMP_COD={$this->codEmpresa} AND V.VEN_DATA={$this->data} ORDER BY V.VEN_COD DESC LIMIT 1";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function vendasUltimosSeteDias()
    {
        $parametros = "V INNER JOIN tb_empresas E ON E.EMP_COD=V.EMP_COD WHERE V.EMP_COD={$this->codEmpresa} AND V.VEN_DATA{$this->data} >= CURRENT_DATE - INTERVAL 7 DAY ORDER BY V.VEN_DATA DESC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodas($ver = 0)
    {
        $parametros = "V INNER JOIN tb_empresas E ON E.EMP_COD=V.EMP_COD WHERE V.EMP_COD={$this->codEmpresa} ORDER BY V.VEN_COD";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function qtdValorTotalVendas($ver = 0)
    {
        $parametros = "V INNER JOIN tb_empresas E ON E.EMP_COD=V.EMP_COD WHERE V.EMP_COD={$this->codEmpresa} ORDER BY V.VEN_COD";
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
        $parametros = " V INNER JOIN tb_empresas E ON E.EMP_COD=V.EMP_COD WHERE V.EMP_COD={$this->codEmpresa} V.VEN_COD=";
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
        $parametros = " V INNER JOIN tb_empresas E ON E.EMP_COD=V.EMP_COD WHERE V.EMP_COD={$this->codEmpresa} V.VEN_COD=";
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