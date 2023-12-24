<?php
namespace App\Controllers;

use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;

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
        $this->dados['title'] = 'MÓDULO | CADASTROS >>'; 
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'tarefas','nome' => 'GERENCIAR TAREFAS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR TAREFAS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/tarefas/listar', $this->dados);
    }
    public function colunas()
    {
        $this->dados['title'] .= ' GERENCIAR COLUNAS TAREFAS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/tarefas/colunas', $this->dados);
    }
}