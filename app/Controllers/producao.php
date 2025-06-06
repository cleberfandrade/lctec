<?php 
namespace App\Controllers;

use App\Models\Categorias;
use App\Models\Empresas;
use App\Models\Financas;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\ModulosEmpresa;
use App\Models\Vendedores;
use App\Models\Produtos;
use App\Models\Fornecedores;
use App\Models\Estoques;
use App\Models\Classificacoes;
use App\Models\Clientes;
use App\Models\Colaboradores;
use App\Models\Setores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class producao extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa,$ModulosEmpresa, $Categorias,$Colaboradores,$Setores,$FolhasPagamento,$Produtos,$Desligamentos,$Horarios,$Promocoes,$Recrutamentos,$Treinamentos,$Beneficios;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | PRODUÇÃO >>'; 
        $this->Financas = new Financas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Categorias = new Categorias;
        $this->Produtos = new Produtos;
        $this->Setores = new Setores;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        //MÓDULO PRODUÇÃO --- 07
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodModulo(7)->checarRegistroModuloEmpresa(0);
        
        if ($this->dados['modulos_empresa'] == 0) {
            Sessao::alert('OK',' MÓDULO NÃO DISPONÍVEL!','alert alert-danger');
            Url::redirecionar('admin/painel');
        }else {
            $this->dados['modulo'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo(7)->listarModuloEmpresa(0);
        }
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'producao','nome' => 'MÓDULO DE PRODUÇÃO >>'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR PRODUÇÃO';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/producao/producao', $this->dados);
    }
    public function insumos()
    {
        $this->dados['title'] .= ' GERENCIAR PRODUÇÃO';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/producao/insumos/listar', $this->dados);
    }
}
