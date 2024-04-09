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
use App\Models\FormasPagamentos;
use App\Models\Fornecedores;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Lancamentos;
use App\Models\ModulosEmpresa;
use App\Models\Movimentacoes as ModelsMovimentacoes;
use App\Models\Transacoes as ModelsTransacoes;

class transacoes extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa,$Lancamentos,$Categorias,$Contas,$Classificacoes,$Clientes,$Fornecedores, $Movimentacoes, $FormasPagamentos,$Transacoes,$ModulosEmpresa;
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
        $this->FormasPagamentos = new FormasPagamentos;
        $this->Transacoes = new ModelsTransacoes;
        $this->ModulosEmpresa = new ModulosEmpresa; 

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['contas'] = $this->Contas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
        $this->dados['modulo'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo(5)->listarModuloEmpresa(0);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        //$this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(6)->listarTodosPorTipo(0);
        $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(6)->listarTodosPorTipo(0);
        $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
        $this->dados['transacoes'] = $this->Transacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodasTransacoes(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO FINANCEIRO'];
        $this->link[2] = ['link'=> 'transacoes','nome' => 'GERENCIAR TRANSAÇÕES'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR TRANSAÇÕES';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/transacoes/listar', $this->dados);
    }
    public function cadastro()
    {
        $this->dados['title'] .= ' CADASTRAR TRANSAÇÃO DA EMPRESA/NEGÓCIO';   
        $this->link[3] = ['link'=> 'movimentacoes/cadastro','nome' => 'CADASTRO DE TRANSAÇÕES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/movimentacoes/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR TRANSAÇÃO DA EMPRESA/NEGÓCIO';   
        $this->link[3] = ['link'=> 'transacoes/cadastro','nome' => 'CADASTRO DE TRANSAÇÕES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_TRANSACAO'])) {
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['CADASTRAR_NOVA_TRANSACAO']);

                $this->dados['conta'] = $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD'])->listar(0);
                if ($this->dados['conta'] != 0) {

                    foreach ($dados as $key => $value) {
                        $dados[$key] = $this->Check->checarString($value);
                    }
                    //$dados['MOV_VALOR'] = number_format($dados['MOV_VALOR'],2, '.', ',');
                    //$dados['MOV_VALOR'] = str_replace(',', '.', str_replace('.', '', $dados['MOV_VALOR']));
                    //$dados['MOV_VALOR'] = $this->Check->onlyNumbers($dados['MOV_VALOR']);
                    //$dados['MOV_VALOR'] = $this->Check->formatMoneyDb($dados['MOV_VALOR']);
                    $dados += array(
                        'TRS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'TRS_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),   
                        'TRS_TOKEN' => $this->Check->token(10,'',true),          
                        'TRS_STATUS'=> 1
                    );

                    if($this->Transacoes->cadastrar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' CTA3 - Erro ao cadastrar nova transação, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else{
                    Sessao::alert('ERRO',' CTA2 - Dados inválido(s)!','alert alert-danger');
                } 
            }else{
                Sessao::alert('ERRO',' TRAS2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' TRAS1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['transacoes'] = $this->Transacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodasTransacoes(0);
            $this->render('admin/financeiro/transacoes/listar', $this->dados);
        }else {
            $this->render('admin/financeiro/transacoes/cadastrar', $this->dados);
        }
    }
}