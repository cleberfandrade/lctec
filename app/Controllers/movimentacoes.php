<?php
namespace App\Controllers;

use App\Models\Categorias;
use App\Models\Classificacoes;
use App\Models\Clientes;
use App\Models\Contas;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;
use App\Models\Empresas;
use App\Models\Financas;
use App\Models\Fornecedores;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Lancamentos;
use App\Models\Movimentacoes as ModelsMovimentacoes;

class movimentacoes extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa,$Lancamentos,$Categorias,$Contas,$Classificacoes,$Clientes,$Fornecedores, $Movimentacoes;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Financas = new Financas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Lancamentos = new Lancamentos;
        $this->Categorias = new Categorias;
        $this->Contas = new Contas;
        $this->Classificacoes = new Classificacoes;
        $this->Fornecedores = new Fornecedores;
        $this->Clientes= new Clientes;
        $this->Movimentacoes = new ModelsMovimentacoes;
        
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['contas'] = $this->Contas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO FINANCEIRO'];
        $this->link[2] = ['link'=> 'lancamentos','nome' => 'GERENCIAR MOVIMENTAÇÕES'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR MOVIMENTAÇÕES A PARGAR E RECEBER';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/movimentacoes/listar', $this->dados);
    }
}