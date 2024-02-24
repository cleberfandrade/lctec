<?php 
namespace App\Controllers;

use App\Models\Empresas;
use App\Models\Financas;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Vendedores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class lctec extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa,$Colaboradores,$CargosSalarios,$FolhasPagamento,$Divulgacoes,$Desligamentos,$Horarios,$Promocoes,$Recrutamentos,$Treinamentos,$Beneficios;
    public function __construct()
    {
        Sessao::naoLogadoSistema();
        $this->dados['title'] = 'MÓDULO | LCTEC >>'; 
        $this->Financas = new Financas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        
        $this->link[0] = ['link'=> 'lctec','nome' => 'PAINEL GERENCIAL | LC/TEC'];
        //$this->link[1] = ['link'=> 'lctec','nome' => 'MÓDULO LC/TEC >>'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
    }
    public function index()
    {
        Sessao::logadoSistema();
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['title'] .= ' PAINEL GERENCIAL | LC/TEC';   
        $this->render('admin/lctec/painel', $this->dados);
    }
    public function modulos()
    {
        Sessao::logadoSistema();
        $this->dados['title'] .= ' LC/TEC >> MÓDULOS DO SISTEMA';   
        $this->link[1] = ['link'=> 'lctec','nome' => 'MÓDULOS LC/TEC >>'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/lctec/modulos/modulos', $this->dados);
    }
    public function avisos()
    {
        $this->dados['title'] .= ' AVISOS LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/painel', $this->dados);
    }
    public function empresas()
    {
        $this->dados['title'] .= ' EMPRESAS LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/painel', $this->dados);
    }
    public function suporte()
    {
        $this->dados['title'] .= ' SUPORTE LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/suporte/listar', $this->dados);
    }
}
