<?php
namespace App\Models;

use Core\Model;

class Movimentacoes extends Model
{ 
    private $tabela = 'tb_movimentacoes';
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
        $parametros = "M INNER JOIN tb_empresas E ON E.EMP_COD=M.EMP_COD INNER JOIN tb_estoques ES ON ES.EST_COD=M.EST_COD INNER JOIN tb_produtos P ON P.PRO_COD=M.PRO_COD WHERE M.MOV_COD={$this->codigo} AND M.EMP_COD={$this->codEmpresa} ";
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
        $parametros = "M INNER JOIN tb_empresas E ON E.EMP_COD=M.EMP_COD INNER JOIN tb_estoques ES ON ES.EST_COD=M.EST_COD WHERE M.EMP_COD={$this->codEmpresa} ORDER BY M.MOV_COD DESC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasPorTipo($ver = 0)
    {
        if ($this->status) {
            $parametros = "M INNER JOIN tb_empresas E ON E.EMP_COD=M.EMP_COD INNER JOIN tb_estoques ES ON ES.EST_COD=M.EST_COD WHERE M.EMP_COD={$this->codEmpresa} M.MOV_TIPO={$this->tipo} AND M.MOV_STATUS={$this->status} ORDER BY M.MOV_COD DESC";
        } else {
            $parametros = "M INNER JOIN tb_empresas E ON E.EMP_COD=M.EMP_COD INNER JOIN tb_estoques ES ON ES.EST_COD=M.EST_COD WHERE M.EMP_COD={$this->codEmpresa} M.MOV_TIPO={$this->tipo} ORDER BY M.MOV_COD DESC";
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND EST_COD={$this->codEstoque} AND MOV_COD=";
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND EST_COD={$this->codEstoque} AND  MOV_COD='{$this->codigo}'";
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