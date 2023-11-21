<?php
namespace App\Controllers;

use App\Models\Empresas;
use App\Models\Estoques;
use App\Models\ModulosEmpresa;
use Core\View;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Libraries\Sessao;
use Libraries\Util;

class admin extends View
{
    private $dados = [];
    private $link,$Util,$Empresa,$Usuarios,$Estoques,$UsuariosEmpresa,$ModulosEmpresa;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'PAINEL | LC-TECH';
        $this->Usuarios = new Usuarios;
        $Empresa = new Empresas;
        $Estoques = new Estoques;
        $UsuariosEmpresa = new UsuariosEmpresa;
        $ModulosEmpresa = new ModulosEmpresa;
        $this->Util = new Util;
        
        $this->dados['usuarios_empresa'] = $this->UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD'])->checarUsuario();
        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
            $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
        }
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listar();
        $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
    }
    public function index()
    {   
        Sessao::naoLogado();
        $this->render('admin/painel', $this->dados);
    }
    public function painel()
    {   
        Sessao::naoLogado();
        $this->render('admin/painel', $this->dados);
    }
    public function acesso()
    {
        $this->dados['title'] = 'ACESSO | LC-TEC';
        $this->render('site/acesso', $this->dados);
    }
    public function administrador()
    {
        $this->dados['title'] = 'ACESSO ADMINISTRATIVO | LC-TEC';
        Sessao::naoLogado();
        $this->render('site/login_admin', $this->dados);
    }
    public function mes()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'PAINEL | LC-TECH';
        /*
        $Usuarios = new Usuarios;
        $Empresa = new Empresas;
        $Estoques = new Estoques;
        $UsuariosEmpresa = new UsuariosEmpresa;
        $ModulosEmpresa = new ModulosEmpresa;
        
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        
        $UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuarios_empresa'] = $UsuariosEmpresa->checarUsuario();
        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
            $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
            $Empresa->setCodigo($_SESSION['EMP_COD']);
            $this->dados['empresa'] = $Empresa->listar(0);
            $ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['modulos_empresa'] = $ModulosEmpresa->listar();
            $Estoques->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['estoques'] = $Estoques->listarTodos(0);
        }else {
            $this->dados['modulos_empresa'] = false;
            $this->dados['empresa'] = false;
            $this->dados['estoques'] = false;
        }
        */
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $mes = explode("/",$dados['url']);
        
        if(isset($mes[2])){
            $this->Util->setData($mes[2]);
            $this->dados['m'] = $this->Util->getMesAtual();           
        }
        $this->render('admin/painel', $this->dados);
    }
}