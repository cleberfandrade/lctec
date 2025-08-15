<?php 
namespace App\Controllers;

use App\Models\Categorias;
use App\Models\Classificacoes;
use App\Models\Empresas;
use App\Models\Financas;
use App\Models\FormasPagamentos;
use App\Models\Lancamentos;
use App\Models\ModulosEmpresa;
use App\Models\PagamentosRecebimentos;
use App\Models\Transacoes;
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
    private $link,$Financas,$Check,$Usuarios,
    $UsuariosEmpresa,$ModulosEmpresa, $Categorias,
    $Classificacoes,$Lancamentos,$PagamentosRecebimentos,
    $Transacoes,$FormasPagamentos;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Financas = new Financas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Categorias = new Categorias;
        $this->Classificacoes = new Classificacoes;
        $this->Lancamentos = new Lancamentos;
        $this->PagamentosRecebimentos = new PagamentosRecebimentos;
        $this->Transacoes = new Transacoes;
        $this->FormasPagamentos = new FormasPagamentos;
        
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['contas'] = $this->Financas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas();
        $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(6)->listarTodosPorTipo(0);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(6)->listarTodosPorTipo(0);
        $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodModulo(3)->checarRegistroModuloEmpresa(0);

        if ($this->dados['modulos_empresa'] == 0) {
            Sessao::alert('OK',' MÓDULO NÃO DISPONÍVEL!','alert alert-danger');
            Url::redirecionar('admin/painel');
        }else {
            $this->dados['modulo'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo(3)->listarModuloEmpresa(0);
        }

        $this->dados['lancamentos_pagar'] = [];
        $this->dados['lancamentos_receber'] = [];
        $this->dados['lancamentos'] = [];

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO FINANCEIRO >>'];
    }
    public function index()
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' GERENCIAR CONTAS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb($this->dados['modulo'][0]['MOD_COR_TEXTO']);
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['filtrar'])) {
           
            if (isset($dados['LAN_DT_INICIAL']) && isset($dados['LAN_DT_FINAL'])) {
                
                if (strtotime($dados['LAN_DT_FINAL']) > strtotime($dados['LAN_DT_INICIAL'])) {
                    $dados['DATA'] = 1;
                    $db = array(
                        'QTD' => 5,
                        'DATA_INICIAL' => $dados['LAN_DT_INICIAL'],
                        'DATA_FINAL' => $dados['LAN_DT_FINAL']
                    );
                    
                } else {
                    Sessao::alert('ERRO',' Datas inválidas!','alert alert-danger');
                }
            }else {
                $db = array(
                    'QTD' => 5,
                    'DATA_INICIAL' => date('Y-m-1'),
                    'DATA_FINAL' => date('Y-m-t')
                );
            }
        }else{
            $db = array(
                'QTD' => 5,
                'DATA_INICIAL' => date('Y-m-1'),
                'DATA_FINAL' => date('Y-m-t')
            );
            $dados = array(
                'DATA' => 1,
                'LAN_QTD' => 5,
                'LAN_DT_INICIAL'=> date('Y-m-1'),
                'LAN_DT_FINAL' => date('Y-m-t')
            ); 
        }

        $this->dados['transacoes'] = $this->Transacoes->setCodEmpresa($_SESSION['EMP_COD'])->filtrarTodasTransacoes($db,0);
        $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarFiltro($dados,0);
        
        $qtdLA = (is_array($this->dados['lancamentos']) ? count( $this->dados['lancamentos']) : 0);
        for ($i = 0; $i < $qtdLA; $i++) { 
            if ($this->dados['lancamentos'][$i]['LAN_STATUS'] == 1) {
                /*
                *LAN_TIPO = 1 A PAGAR
                *LAN_TIPO = 2 A RECEBER
                */
                if ($this->dados['lancamentos'][$i]['LAN_TIPO'] == 1) {
                    $this->dados['lancamentos_pagar'][] = $this->dados['lancamentos'][$i];
                } else {
                    $this->dados['lancamentos_receber'][] = $this->dados['lancamentos'][$i];
                }
            }
        }


        $this->render('admin/financeiro/financeiro', $this->dados);
    }
    public function contas()
    {
        $this->dados['title'] .= ' GERENCIAR CONTAS';   
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/contas/listar', $this->dados);
    }
    public function detalhar_contas()
    {
        $this->dados['title'] .= 'DETALHAR CONTA'; 
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false; 
        
        if (isset($dados[2]) && isset($dados[3])) {
            
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['conta'] = $this->Financas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['conta'] != 0) {
                    //$this->dados['CTA_COD'] = $dados[3];
                    $this->link[3] = ['link'=> 'financeiro/detalhar_contas/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'DETALHAR CONTA >> '.$this->dados['conta']['CTA_DESCRICAO']];
                    $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
                    $ok = true;
                }
               
            }else{
                Sessao::alert('ERRO',' ERRO:CTA22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CTA11 - Acesso inválido(s)!','alert alert-danger');
        }      

        if ($ok) {
            $this->render('admin/financeiro/contas/detalhar', $this->dados);
        } else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/financeiro/contas/listar', $this->dados);
        }        
    }
    public function cadastro()
    {
        $this->dados['title'] .= ' GERENCIAR CONTA DA EMPRESA/NEGÓCIO';   
        $this->render('admin/financeiro/contas/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' GERENCIAR CONTA DA EMPRESA/NEGÓCIO';   
        $this->render('admin/financeiro/contas/cadastrar', $this->dados);
    }
    public function frente_caixa()
    {
        $this->dados['title'] .= ' GERENCIAR CAIXA EMPRESA/NEGÓCIO';   
        $this->render('admin/financeiro/caixa/gerenciar', $this->dados);
    }
    public function contas_pagar()
    {
        $this->dados['title'] .= 'GERENCIAR CAIXA EMPRESA/NEGÓCIO'; 


        $this->render('admin/financeiro/pagamentos/pagar', $this->dados);
    }public function contas_receber()
    {
        $this->dados['title'] .= 'GERENCIAR CAIXA EMPRESA/NEGÓCIO'; 
        $this->render('admin/financeiro/pagamentos/receber', $this->dados);
    }
    public function alterar_caixa()
    {
        $this->dados['title'] .= 'GERENCIAR CAIXA EMPRESA/NEGÓCIO'; 

        $this->render('admin/financeiro/caixa/status', $this->dados);
    }
    public function movimentacao()
    {
        $this->dados['title'] .= 'MOVIMENTAÇÃO';
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
