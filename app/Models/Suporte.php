<?php
namespace App\Models;

use Core\Model;

class Suporte extends Model
{ 
    private $tabela = 'tb_suporte';
    private $Model = '';
    private $codigo, $descricao, $tipo, $codEmpresa, $data,$codUsuario,$status,$codDestinatario;

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
    public function setCodEmpresa($codEmpresa)
    {
        $this->codEmpresa = $codEmpresa;
        return $this;
    }
    public function setCodUsuario($codUsuario)
    {
        $this->codUsuario = $codUsuario;
        return $this;
    }
    public function setCodDestinatario($codDestinatario)
    {
        $this->codDestinatario = $codDestinatario;
        return $this;
    }
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    
    public function listar($ver = 0)
    {
        $parametros = "S INNER JOIN tb_empresas E ON S.EMP_COD=E.EMP_COD WHERE S.EMP_COD={$this->codEmpresa} AND S.SUP_COD={$this->codigo}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function listarTodasMensagensRecebidas($ver = 0)
    {
        $parametros = "S WHERE S.USU_COD_DESTINATARIO={$this->codUsuario} AND S.SUP_STATUS=1 ORDER BY S.SUP_DT_CADASTRO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasMensagensEnviadasRecebidas($ver = 0)
    {
        $parametros = "S WHERE S.USU_COD={$this->codUsuario} or S.USU_COD_DESTINATARIO={$this->codUsuario} AND S.SUP_STATUS=1 OR S.SUP_STATUS=2 ORDER BY S.SUP_DT_CADASTRO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasMensagensUsuario($ver = 0)
    {
        $parametros = "S LEFT OUTER JOIN tb_usuarios U ON U.USU_COD=S.USU_COD WHERE S.USU_COD={$this->codUsuario} OR S.USU_COD_DESTINATARIO={$this->codUsuario} ORDER BY S.SUP_DT_CADASTRO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasMensagensSuporte($ver = 0)
    {
        $parametros = "S INNER JOIN tb_usuarios_empresa U ON U.USU_COD=S.USU_COD INNER JOIN tb_usuarios US ON US.USU_COD = U.USU_COD INNER JOIN tb_empresas E ON E.EMP_COD=U.EMP_COD WHERE S.USU_COD_DESTINATARIO={$this->codUsuario} AND S.SUP_STATUS=1 OR S.SUP_STATUS=2 ORDER BY S.SUP_DT_CADASTRO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasMensagensEnviadasRecebidasSuporte($ver = 0)
    {
        $parametros = "S INNER JOIN tb_empresas E ON E.EMP_COD=S.EMP_COD LEFT OUTER JOIN tb_usuarios_empresa U ON U.USU_COD=S.USU_COD OR U.USU_COD=S.USU_COD_DESTINATARIO LEFT OUTER JOIN tb_usuarios US ON US.USU_COD=U.USU_COD WHERE S.EMP_COD={$this->codEmpresa} ORDER BY S.SUP_DT_CADASTRO;";
        //$parametros = "S INNER JOIN tb_empresas E ON E.EMP_COD=S.EMP_COD INNER JOIN tb_usuarios U ON U.USU_COD=S.USU_COD_DESTINATARIO OR U.USU_COD=S.USU_COD WHERE S.EMP_COD={$this->codEmpresa} AND S.SUP_STATUS=1 OR S.SUP_STATUS=2 ORDER BY S.SUP_DT_CADASTRO";
        //$parametros = "S INNER JOIN tb_usuarios_empresa U ON U.USU_COD=S.USU_COD INNER JOIN tb_usuarios US ON US.USU_COD = U.USU_COD INNER JOIN tb_empresas E ON E.EMP_COD=U.EMP_COD WHERE S.EMP_COD={$this->codEmpresa} AND S.USU_COD={$this->codUsuario} or S.USU_COD_DESTINATARIO={$this->codUsuario} AND S.SUP_STATUS=1 ORDER BY S.SUP_DT_CADASTRO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasMensagensEmpresaUsuario($ver = 0)
    {
        $parametros = "S LEFT OUTER JOIN tb_usuarios_empresa US ON US.USU_COD=S.USU_COD LEFT OUTER JOIN tb_empresas E ON E.EMP_COD=US.EMP_COD  WHERE US.EMP_COD={$this->codEmpresa} AND S.USU_COD={$this->codUsuario} OR S.USU_COD_DESTINATARIO={$this->codUsuario} AND S.SUP_STATUS=1 ORDER BY S.SUP_DT_CADASTRO";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasMensagensEmpresa($ver = 0)
    {
        $parametros = "S INNER JOIN tb_empresas E ON E.EMP_COD=S.EMP_COD LEFT OUTER JOIN tb_usuarios US ON US.USU_COD = S.USU_COD WHERE S.EMP_COD={$this->codEmpresa} AND S.SUP_STATUS={$this->status}";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasMensagensRecebidasDaEmpresa($ver = 0)
    {
        $parametros = "S INNER JOIN tb_empresas E ON E.EMP_COD=S.EMP_COD LEFT OUTER JOIN tb_usuarios US ON US.USU_COD = S.USU_COD WHERE S.EMP_COD={$this->codEmpresa} AND S.SUP_STATUS={$this->status} AND S.USU_COD_DESTINATARIO={$this->codDestinatario} ORDER BY S.SUP_DT_CADASTRO";
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
        $parametros = "WHERE EMP_COD={$this->codEmpresa} AND SUP_COD=";
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
        $parametros = "WHERE EMP_COD='{$this->codEmpresa}' AND SUP_COD=";
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