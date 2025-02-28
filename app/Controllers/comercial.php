<?php
namespace App\Controllers;

use App\Models\Classificacoes;
use App\Models\Clientes;
use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Setores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class comercial extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Clientes,$Setores,$Classificacoes;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | COMERCIAL >>';
        $this->Clientes= new ModelsCliente;
        $this->Empresa = new Empresas;
        $this->Enderecos = new Enderecos;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;
        $this->Setores = new Setores;
        $this->Classificacoes = new Classificacoes;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'comercial','nome' => 'MÓDULO COMERCIAL'];
        $this->link[2] = ['link'=> 'listar','nome' => 'GERENCIAR SUAS VENDAS E PEDIDOS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' COMERCIAL';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/comercial/comercial', $this->dados);
    }
}