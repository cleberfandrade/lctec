<?php
namespace App\Controllers;

use App\Models\Empresas;
use Core\View;

use Libraries\Util;
use Libraries\Sessao;
use App\Models\Usuarios;
use App\Models\Modulos as ModelsModulos;
use App\Models\ModulosEmpresa;
use App\Models\UsuariosEmpresa;
use Libraries\Check;

class modulo_link extends View
{ 
    private $dados = [];
    private $link,$Modulos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Util,$ModulosEmpresa;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;
        $this->Modulos = new ModelsModulos;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Util = new Util;
        $this->dados['title'] = 'MÓDULO | LCTEC >>'; 
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listar(0);
        $this->dados['modulos'] = $this->Modulos->listarTodos(0);

        $this->link[0] = ['link'=> 'lctec','nome' => 'PAINEL DESENVOLVIMENTO'];
        $this->link[1] = ['link'=> 'lctec/modulos','nome' => 'MÓDULO DE CADASTROS >> '];
        $this->link[2] = ['link'=> 'lctec/modulos/links','nome' => 'GERENCIAR LINKS DOS MÓDULOS'];
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
    }
    public function index()
    {
        $this->dados['title'] .= ' LINKS DOS MÓDULOS';
        $this->render('admin/cadastros/lctec/modulos/links', $this->dados);
    }
}