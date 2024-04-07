<?php 
namespace App\Models;

use App\Models\Usuarios;
use Core\Model;


class UsuariosEmpresa extends Model
{
    private $tabela = 'tb_usuarios_empresa';
    private $Model = '';
    private $email, $codigo,$codUsuario,$codEmpresa,$status,$Usuarios;
    public function __construct()
    {
        $this->Usuarios = new Usuarios;
        $this->Model = new Model();
        $this->Model->setTabela($this->tabela);
    }
    public function setCodigo($codigo)
    {
        $this->codigo = $codigo;
        return $this;
    }
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }
    public function setSenha($senha)
    {
        $this->senha = $senha;
        return $this;
    }
    public function setCodUsuario($codUsuario)
    {
        $this->codUsuario = $codUsuario;
        return $this;
    }
    public function setCodEmpresa($empresa)
    {
        $this->codEmpresa = $empresa;
        return $this;
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
        $parametros = " WHERE EMP_COD='{$this->codEmpresa}' AND USU_COD=";
        $this->Model->setParametros($parametros);
        $this->Model->setCodigo($this->codUsuario);
        $ok = false;
        $ok = $this->Model->alterar($dados, $ver);
        if ($ok) {
            return true;
        } else {
            return false;
        }
    }
    public function listarUsuariosEmpresa($ver = 0)
    {
        $parametros = " UMP INNER JOIN tb_usuarios USU ON UMP.USU_COD=USU.USU_COD 
                            INNER JOIN tb_empresas EMP ON UMP.EMP_COD=EMP.EMP_COD 
                            INNER JOIN tb_enderecos EN ON EMP.EMP_COD=EN.EMP_COD 
                            WHERE UMP.EMP_COD='{$this->codEmpresa}' AND UMP.UMP_STATUS='{$this->status}' ORDER BY USU.USU_NOME ASC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodasEmpresasUsuario($ver = 0)
    {
        $parametros = " UMP INNER JOIN tb_usuarios USU ON UMP.USU_COD=USU.USU_COD 
                            INNER JOIN tb_empresas EMP ON UMP.EMP_COD=EMP.EMP_COD 
                            INNER JOIN tb_enderecos EN ON EMP.EMP_COD=EN.EMP_COD 
                            WHERE UMP.USU_COD='{$this->codUsuario}' ORDER BY EMP.EMP_NOME_FANTASIA ASC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listarTodos($ver = 0)
    {
        $parametros = "UE INNER JOIN tb_usuarios U ON U.USU_COD=UE.USU_COD WHERE UE.EMP_COD='{$this->codEmpresa}' AND UE.USU_COD<>'{$this->codUsuario}' ORDER BY U.USU_NOME ASC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado;
        } else {
            return false;
        }
    }
    public function listar($ver = 0)
    {
        $parametros = "UE 
        INNER JOIN tb_usuarios U ON UE.USU_COD=U.USU_COD 
        INNER JOIN tb_empresas EMP ON UE.EMP_COD=EMP.EMP_COD 
        WHERE UE.EMP_COD='{$this->codEmpresa}'AND UE.USU_COD='{$this->codUsuario}'";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function checarUsuarioEmpresa()
    {
        $parametros = "WHERE USU_COD='{$this->codUsuario}' AND EMP_COD='{$this->codEmpresa}'";
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
    public function checarUsuario()
    {
        $parametros = "WHERE USU_COD='{$this->codUsuario}'";
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
    public function checarAcessoUsuarioEmpresa($ver = 0)
    {
        $parametros = "UE INNER JOIN tb_usuarios U ON UE.USU_COD=U.USU_COD INNER JOIN tb_empresas EMP ON UE.EMP_COD=EMP.EMP_COD WHERE UE.EMP_COD={$this->codEmpresa} AND U.USU_EMAIL='{$this->email}'";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            //Já existe
            if($this->Usuarios->checarSenhaAcesso($this->senha, $resultado[0]['USU_SENHA'])){
                  //Acesso correto
                return $resultado[0];
            }else{
                return false;
            }
        } else {
            //Nao existe
            return false;
        }
    }
}