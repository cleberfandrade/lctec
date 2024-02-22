<?php 
namespace App\Models;

use Core\Model;

class Usuarios extends Model
{
    private $tabela = 'tb_usuarios';
    private $Model = '';
    private $email, $codigo,$senha,$sexo;
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
    public function setEmailUsuario($email)
    {
        $this->email = $email;
        return $this;
    }
    public function setSenhaUsuario($senha)
    {
        $this->senha = $senha;
        return $this;
    }
    public function setSexoUsuario($sexo)
    {
        $this->sexo = $sexo;
        return $this;
    }
    public function setCodUsuario($codUsuario)
    {
        $this->codigo = $codUsuario;
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
        $parametros = " WHERE USU_COD=";
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
    public function Acessar($ver = 0)
    {
        $parametros = " U LEFT OUTER JOIN tb_enderecos E ON E.USU_COD=U.USU_COD WHERE U.USU_EMAIL='{$this->email}'";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver, $id = false);
        if ($resultado) {
            if(self::checarSenhaAcesso($this->senha, $resultado[0]['USU_SENHA'])){
                return $resultado[0];
            }else{
                return false;
            }
        } else {
            return false;
        }
    }
    public function listarTodos($ver = 0)
    {
        $parametros = " U INNER JOIN tb_enderecos E ON E.USU_COD=U.USU_COD WHERE U.USU_COD<>'{$this->codigo}' ORDER BY U.USU_NOME ASC";
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
        $parametros = " U INNER JOIN tb_enderecos E ON E.USU_COD=U.USU_COD WHERE U.USU_COD='{$this->codigo}'  ORDER BY U.USU_NOME ASC";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function checarEmailUsuario()
    {
        $parametros = "WHERE USU_EMAIL='{$this->email}'";
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
    private static function checarSenhaAcesso($senhaUsuario, $senhaDB)
    {
        if (password_verify($senhaUsuario, $senhaDB)) {
            return true;
        } else {
            return false;
        }
    }
}