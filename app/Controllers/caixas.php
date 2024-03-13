<?php 
namespace App\Controllers;

use App\Models\Caixas as ModelsCaixa;
use App\Models\Empresas;
use App\Models\Contas;
use App\Models\Movimentacoes;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Vendedores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class caixas extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa,$Contas,$Movimentacoes,$Caixas;
    public function __construct()
    {

        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Contas = new Contas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Movimentacoes = new Movimentacoes;
        $this->Caixas = new ModelsCaixa;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['contas'] = $this->Contas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas();

        $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO DE FINANÇAS'];
        $this->link[2] = ['link'=> 'caixas','nome' => 'GERENCIAR SEUS CAIXAS'];

    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR CAIXAS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/caixas/listar', $this->dados);
    }
    public function cadastro()
    {
        $this->dados['title'] .= ' CADASTRAR CAIXA DA EMPRESA/NEGÓCIO';   
        $this->link[3] = ['link'=> 'caixas/cadastro','nome' => 'CADASTRO DE CAIXAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/caixas/cadastrar', $this->dados);
    }
}