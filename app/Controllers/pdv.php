<?php
namespace App\Controllers;

use App\Models\Caixas;
use App\Models\Categorias;
use App\Models\Classificacoes;
use App\Models\Clientes;
use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Estoques as ModelsEstoques;
use App\Models\Financas;
use App\Models\FormasPagamentos;
use App\Models\Fornecedores;
use App\Models\ItensVendas;
use App\Models\ModulosEmpresa;
use App\Models\Movimentacoes as MovimentacoesModels;
use App\Models\Produtos;
use App\Models\Setores;
use App\Models\Transacoes;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Vendas;
use Libraries\Util;

use Core\View;
use Core\Controller;

use Libraries\Check;
use Libraries\Sessao;

class pdv extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresas,$Vendedores,$Empresa,$UsuariosEmpresa,$Check,$Clientes,$Produtos,$Colaboradores,$Caixas,$Contas,$FormasPagamentos,$Estoques,$Movimentacoes,$ItensVendas,$Vendas;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | PDV >>';   
        $this->Check = new Check;
        $this->Clientes= new Clientes;
        $this->Produtos = new Produtos;
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Enderecos = new Enderecos;
        $this->Estoques = new Estoques;
        $this->Contas = new Contas;
        $this->FormasPagamentos = new FormasPagamentos;
        $this->Movimentacoes = new Movimentacoes;
        $this->Vendas = new Vendas;
        $this->Caixas = new Caixas;
        $this->Colaboradores = new Colaboradores;
        $this->ItensVendas = new ItensVendas;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodosAtivos(0);
        $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodasAtivas(0);
        $this->dados['movimentacoes'] = $this->Movimentacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0); 
        $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodosAtivos(0);
      
        $this->link[0] = ['link'=> 'admin/financeiro','nome' => 'FINANCEIRO'];
        $this->link[1] = ['link'=> 'pdv','nome' => 'PDV'];
    }
    public function index()
    {
        $this->dados['title'] = 'VENDAS | LC/TEC';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/pdv/pdv', $this->dados);
    }
    public function vendas()
    {
        $this->dados['title'] = 'VENDAS | LC/TEC';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/pdv/vendas', $this->dados);
    }
    public function caixas()
    {
        $this->dados['title'] = 'GERENCIAR CAIXAS';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/pdv/caixas', $this->dados);
    }
    /*public function auth()
    {
        $Check = new Check;
        //$Vendedores = new Vendedores;
        $Url = new Url;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['acesso'])) {
            
            if (!empty($dados['VDD_EMAIL']) && !empty($dados['VDD_SENHA'])) {
                //Validar Dados
                $dados['VDD_EMAIL'] = $Check->checarString($dados['VDD_EMAIL']);
                $dados['VDD_SENHA'] = $Check->checarString($dados['VDD_SENHA']);
                if($Check->checarEmail($dados['VDD_EMAIL'])){
                    //$Vendedores->setEmail($dados['VDD_EMAIL']);
                    //$Vendedores->setSenha($dados['VDD_SENHA']);
                    //$this->dados['vendedor'] = $Vendedores->acessarPDV(0);
                    //checar se retornou algum usuario
                    $qtd = (is_array($this->dados['vendedor']) ? count($this->dados['vendedor']) : 0);
                    if(!empty($qtd) && $qtd != 0){
                        //Checar se o status do usuario == 1: ativado/desativado
                        if($this->dados['vendedor']['VDD_STATUS'] == 1){
                           
                            //Criando sessao para acessar o PDV da EMPRESA/NEGÓCIO
                            if(Sessao::criarSessao($this->dados['vendedor'])){
                                Sessao::alert('OK',' Bem vindo(a) '.$_SESSION['VDD_NOME'].'','m-0 fs-4 alert alert-success');
                                //Sessao::alert('OK',' Acesso efetuado com sucesso!','m-0 fs-4 alert alert-success');
                                //Redirecionando o usuário para a página painel do sistema admin/painel
                                header("Location:".DIRPAGE."financeiro/pdv");
                            }else{
                                Sessao::alert('ERRO',' 6- O sistema encontroiu um erro interno, contate o suporte','alert alert-danger');
                            }
                        }else {
                            Sessao::alert('ERRO',' 5- Vendedor(a) desativado(a), contate o suporte','alert alert-danger');
                        }
                    }else{
                        Sessao::alert('ERRO',' 4- Vendedor(a) ou senha inválido(s)!','alert alert-danger');
                    }
                }else{
                    Sessao::alert('ERRO',' 3- Vendedor(a) ou senha inválido(s)!','alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' 2- Vendedor(a) ou senha inválido(s)!','alert alert-danger');
            }           
        }else{
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','alert alert-danger');
        }
        $this->render('site/acesso', $this->dados);
    }*/
    public function pdv()
    {
        Sessao::naoLogado();
        $this->dados['empresa'] = $this->Colaboradores->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($_SESSION['COL_COD'])->listar(0);

        $this->dados['title'] .= $this->dados['empresa']['EMP_NOME_FANTASIA'];
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/pdv', $this->dados);
    }
    public function produtos()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['PESQUISA_PRODUTO'])) {
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

            }
        }
    }
}
