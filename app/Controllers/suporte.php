<?php
namespace App\Controllers;

use App\Models\Classificacoes;
use App\Models\Empresas;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class suporte extends View
{
    private $dados = [];
    private $link,$Check,$Empresa, $Usuarios,$UsuariosEmpresa;

    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'HELP!! SUPORTE AO SISTEMA >>';
        $this->Empresa = new Empresas;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'SUPORTE AO SISTEMA'];
    }
    public function index()
    {
        $this->dados['title'] .= ' SUPORTE AO SISTEMA';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/suporte', $this->dados);
    }
}