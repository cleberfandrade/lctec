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

class financeiro extends View
{
    private $dados = [];
    private $link,$Financas;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Financas = new Financas;  
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO DE FINANÇAS'];
    }
    public function index()
    {
        $Usuarios = new Usuarios;
        $Empresa = new Empresas;
        $UsuariosEmpresa = new UsuariosEmpresa;
        $Financas = new Financas;
        $Check = new Check;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        
        $UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuarios_empresa'] = $UsuariosEmpresa->checarUsuario();
        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
            $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
            $Empresa->setCodigo($_SESSION['EMP_COD']);
            $this->dados['empresa'] = $Empresa->listar(0);
            $UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['usuarios'] = $UsuariosEmpresa->listarTodos(0);
            $Financas->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['contas'] = $Financas->listarTodas();
        }
        $Check->setLink($this->link);
        $this->dados['breadcrumb'] = $Check->breadcrumb();
        $this->render('admin/financeiro/financeiro', $this->dados);
    }
    public function contas()
    {
        $this->dados['title'] .= 'GERENCIAR CONTAS';   
        $Usuarios = new Usuarios;
        $Empresa = new Empresas;
        $UsuariosEmpresa = new UsuariosEmpresa;
        $Financas = new Financas;
        $Check = new Check;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);

        $UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuarios_empresa'] = $UsuariosEmpresa->checarUsuario();
        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
            $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
            $Empresa->setCodigo($_SESSION['EMP_COD']);
            $this->dados['empresa'] = $Empresa->listar(0);
            $UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['usuarios'] = $UsuariosEmpresa->listarTodos(0);
            $Financas->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['contas'] = $Financas->listarTodas();
        }
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'LISTAGEM DE CONTAS'];
        $Check->setLink($this->link);
        $this->dados['breadcrumb'] = $Check->breadcrumb();
        $this->render('admin/financeiro/contas/listar', $this->dados);
    }
    public function detalhar_contas()
    {
        $this->dados['title'] .= 'DETALHAR CONTA'; 
        $UsuariosEmpresa = new UsuariosEmpresa;
        $Financas = new Financas;
        $Check = new Check;
        $Empresa = new Empresas;
        $Usuarios = new Usuarios;
        $dados = filter_input_array(INPUT_GET, FILTER_DEFAULT);
        $dados = explode("/",$dados);
        
        $this->dados['CTA_COD'] = $dados[3];

        $this->render('admin/financeiro/contas/detalhar', $this->dados);
    }
    public function alteracao()
    {
        $this->dados['title'] .= 'ALTERAR CONTA'; 

        $this->render('admin/financeiro/contas/alterar', $this->dados);
    }
    public function alterar()
    {
        $this->dados['title'] .= 'ALTERAR CONTA'; 

        $this->render('admin/financeiro/contas/alterar', $this->dados);
    }
    public function cadastro()
    {
        $this->dados['title'] .= 'GERENCIAR CONTA DA EMPRESA/NEGÓCIO';   
        $this->render('admin/financeiro/contas/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= 'GERENCIAR CONTA DA EMPRESA/NEGÓCIO';   
        $this->render('admin/financeiro/contas/cadastrar', $this->dados);
    }
    public function frente_caixa()
    {
        $this->dados['title'] .= 'GERENCIAR CAIXA EMPRESA/NEGÓCIO';   
        $this->render('admin/financeiro/caixa/gerenciar', $this->dados);
    }

    public function alterar_caixa()
    {
        $this->dados['title'] .= 'GERENCIAR CAIXA EMPRESA/NEGÓCIO'; 


        $this->render('admin/financeiro/caixa/status', $this->dados);
    }
    public function movimentacao()
    {
        
    }
   
    public function pdv()
    {
        $this->dados['title'] .= 'PDV';   

        $Usuarios = new Usuarios;
        $Empresa = new Empresas;
        $UsuariosEmpresa = new UsuariosEmpresa;
        $Financas = new Financas;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        $UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuarios_empresa'] = $UsuariosEmpresa->checarUsuario();
        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
            $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
            $Empresa->setCodigo($_SESSION['EMP_COD']);
            $this->dados['empresa'] = $Empresa->listar(0);
            $UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['usuarios'] = $UsuariosEmpresa->listarTodos(0);
            $Financas->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['contas'] = $Financas->listarTodas();
        }

        $this->render('admin/financeiro/pdv', $this->dados);
    }
}
