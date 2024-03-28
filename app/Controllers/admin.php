<?php
namespace App\Controllers;

use App\Models\Avisos;
use App\Models\Empresas;
use App\Models\Estoques;
use App\Models\ModulosEmpresa;
use App\Models\Movimentacoes;
use App\Models\Produtos;
use App\Models\Tarefas;
use Core\View;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Vendas;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Util;

class admin extends View
{
    private $dados = [];
    private $link,$Util,$Check,$Empresa,$Usuarios,$Estoques,$UsuariosEmpresa,$ModulosEmpresa, $Tarefas,$Produtos,$Movimentacoes,$Avisos,$Vendas;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'PAINEL | LC/TEC';
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->Estoques = new Estoques;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Tarefas = new Tarefas;
        $this->Util = new Util;
        $this->Check = new Check;
        $this->Produtos = new Produtos;
        $this->Movimentacoes = new Movimentacoes;
        $this->Avisos = new Avisos;
        $this->Vendas = new Vendas;

       
        /*
        $motivos = array(
            0 => '---',
            1 =>'COMPRA',
            2 =>'VENDA',
            3 =>'AJUSTE',
            4 =>'DEVOLUÇÃO CLIENTE',
            5=>'DEVOLUÇÃO FORNECEDOR',
            6=>'PERDA',
            7=>'OUTROS'
        );
        */
        $this->dados['usuarios_empresa'] = $this->UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD'])->checarUsuario();
        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
          $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
        }
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listar();
        $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['produtos'] = $this->Produtos->setCodEmpresa($_SESSION['EMP_COD'])->setStatus(1)->listarTodosGeral(0);

        $this->dados['vendas'] = $this->Vendas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
        
        $this->dados['ultimos_sete_dias'] = $this->Vendas->setCodEmpresa($_SESSION['EMP_COD'])->vendasUltimosSeteDias(0);

        $this->dados['custo_produtos'] = $this->Produtos->setCodEmpresa($_SESSION['EMP_COD'])->qtdTotalCustoProdutos(0);
        
        $this->dados['usuarios'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setStatus(1)->listarUsuariosEmpresa(0);
        
        $this->dados['movimentacoes'] = $this->Movimentacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(2)->setStatus(1)->listarTodasPorTipo(0); 

        $this->dados['tarefas'] = $this->Tarefas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);

        $this->dados['avisos'] = $this->Avisos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        
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
    public function gerencial()
    {
        $this->dados['title'] = 'ACESSO GERENCIAL | LC-TEC';
        Sessao::naoLogado();
        $this->render('site/login_admin', $this->dados);
    }
    public function lctec()
    {
        $this->dados['title'] = 'ACESSO ADMINISTRATIVO - GERENCIAL | LC-TEC';
        Sessao::naoLogado();
        $this->render('admin/lctec/painel', $this->dados);
    }
    public function mes()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'PAINEL | LC-TEC';
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