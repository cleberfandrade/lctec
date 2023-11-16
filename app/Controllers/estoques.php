<?php
namespace App\Controllers;

use App\Models\Clientes;
use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Estoques as ModelsEstoques;
use App\Models\Financas;
use App\Models\Fornecedores;
use App\Models\ModulosEmpresa;
use App\Models\Produtos;
use Libraries\Util;
use Core\View;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Core\Controller;
use Libraries\Check;
use Libraries\Sessao;

class estoques extends View
{
    private $dados = [];
    private $link,$Enderecos,$Clientes,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$CargosSalarios,$ModulosEmpresa,$Financas,$Estoques;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | ESTOQUES >>';
        $this->Clientes= new Clientes;
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Enderecos = new Enderecos;
        $this->Check = new Check;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Estoques = new ModelsEstoques;
        $this->Financas = new Financas;
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'estoques','nome' => 'ESTOQUES'];
    }
    public function index()
    {
        $this->dados['title'] .= 'ACESSAR';
        $Usuarios = new Usuarios;
        $Empresa = new Empresas;
        $Estoques = new ModelsEstoques;
        $UsuariosEmpresa = new UsuariosEmpresa;
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
        }
        $Estoques->setCodEmpresa($_SESSION['EMP_COD']);
        $this->dados['estoques'] = $Estoques->listarTodos(0);           
        $Check->setLink($this->link);
        $this->dados['breadcrumb'] = $Check->breadcrumb();
        $this->render('admin/estoques/estoques', $this->dados);
    }
    public function gerenciar()
    {
        $this->dados['title'] .= 'GERENCIAR ESTOQUE';
        $Usuarios = new Usuarios;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        $Estoques = new ModelsEstoques;
        $UsuariosEmpresa = new UsuariosEmpresa;
        $Empresa = new Empresas;
        $Produtos = new Produtos;
        $Check = new Check;

        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);

        $dados = filter_input_array(INPUT_GET, FILTER_DEFAULT);
        $dados = explode("/",$dados['url']);

        $UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuarios_empresa'] = $UsuariosEmpresa->checarUsuario();
        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
            $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
            $Empresa->setCodigo($_SESSION['EMP_COD']);
            $this->dados['empresa'] = $Empresa->listar(0);
            $UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['usuarios'] = $UsuariosEmpresa->listarTodos(0);
        }

        if (isset($dados[0]) && $dados[0] == 'estoques' && isset($dados[1]) && $dados[1] == 'gerenciar') {
            
            if (isset($dados[2]) && $dados[2] != '' && isset($dados[3]) && $dados[3] != '') {

                if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] == $dados[2]){
                    $Estoques->setCodEmpresa($dados[2]);
                    $Estoques->setCodigo($dados[3]);
                    $this->dados['estoque'] = $Estoques->listar(0);

                    $Produtos->setCodEstoque($dados[3]);
                    $Produtos->setCodEmpresa($dados[2]);
                    $this->dados['produtos'] = $Estoques->listarProdutosEstoque(0);
                    $this->link[2] = ['link'=> 'estoques/gerenciar/'.$dados[2].'/'.$dados[3],'nome' => 'GERENCIAR ESTOQUE'];
                    $Check->setLink($this->link);
                    $this->dados['breadcrumb'] = $Check->breadcrumb();

                    $this->render('admin/estoques/gerenciar', $this->dados);

                }else {
                    
                    Sessao::alert('ERRO',' 2- Acesso inválido!','fs-4 alert alert-danger');
                    $Estoques->setCodEmpresa($_SESSION['EMP_COD']);
                    $this->dados['estoques'] = $Estoques->listarTodos(0);

                    $Produtos->setCodEstoque($dados[3]);
                    $Produtos->setCodEmpresa($_SESSION['EMP_COD']);
                    $this->dados['produtos'] = $Estoques->listarProdutosEstoque(0);
                    $Check->setLink($this->link);
                    $this->dados['breadcrumb'] = $Check->breadcrumb();
                    $this->render('admin/estoques/estoques', $this->dados);
                }
            }else {
                Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');

                $Estoques->setCodEmpresa($_SESSION['EMP_COD']);
                $this->dados['estoques'] = $Estoques->listarTodos(0);
                $this->dados['produtos'] = 0;
                $Check->setLink($this->link);
                $this->dados['breadcrumb'] = $Check->breadcrumb();
                $this->render('admin/estoques/estoques', $this->dados);
            }
        }else {
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');
            $Estoques->setCodEmpresa($_SESSION['EMP_COD']);
            $this->dados['estoques'] = $Estoques->listarTodos(0);
            $this->dados['produtos'] = 0;
            $Check->setLink($this->link);
            $this->dados['breadcrumb'] = $Check->breadcrumb();
            $this->render('admin/estoques/estoques', $this->dados);
        }
    }
   
    public function servicos()
    {
        $Usuarios = new usuarios;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        $this->render('admin/estoques/servicos', $this->dados);
    }
}