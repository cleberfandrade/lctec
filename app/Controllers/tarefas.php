<?php
namespace App\Controllers;


use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class tarefas extends View
{
    private $dados = [];
    private $link,$Check,$Usuarios,$UsuariosEmpresa;

    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'LC-TEC | MINHAS TAREFAS >>'; 
        $this->Check = new Check;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'TAREFAS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR TAREFAS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/tarefas', $this->dados);
    }
}