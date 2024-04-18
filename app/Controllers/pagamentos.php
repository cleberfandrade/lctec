<?php 
namespace App\Controllers;

use App\Models\FormasPagamentos;
use App\Models\Lancamentos;
use App\Models\ModulosEmpresa;
use App\Models\Movimentacoes;
use App\Models\PagamentosRecebimentos;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class pagamentos extends View
{
    private $dados = [];
    private $link,$Check,$Usuarios,$UsuariosEmpresa,$Lancamentos,$Contas,$Movimentacoes, $FormasPagamentos, $ModulosEmpresa,$PagamentosRecebimentos;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Check = new Check;
       
        $this->FormasPagamentos = new FormasPagamentos;
        $this->Lancamentos = new Lancamentos;  
        $this->Movimentacoes = new Movimentacoes;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->PagamentosRecebimentos = new PagamentosRecebimentos;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);

        $this->dados['pagamentos'] = $this->PagamentosRecebimentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(1)->listarTodosTipo(0);

        $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodasAtivas(0);
        $this->dados['modulo'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo(3)->listarModuloEmpresa(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO FINANCEIRO'];
        $this->link[2] = ['link'=> 'pagamentos','nome' => 'GERENCIAR PAGAMENTOS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR PAGAMENTOS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/pagamentos', $this->dados);
    }
    public function pagar()
    {
        $this->dados['title'] .= ' GERENCIAR PAGAMENTOS';   
        $this->dados['lancamentos_pagar'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(1)->listarTodosTipo();
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/pagamentos/pagar', $this->dados);
    }
    public function pagar_lançamento()
    {
        $this->dados['title'] .= ' GERENCIAR PAGAMENTOS';   
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $ok = false;
        if (isset($_POST) && isset($dados['CADASTRAR_PAGAMENTO'])) {
            unset($dados['CADASTRAR_PAGAMENTO']);
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
               
                $this->dados['lancamento'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($dados['LAN_COD'])->listar(0);
            
                if ($this->dados['lancamento'] != 0) {

                    $db = array(
                        'EMP_COD' => $_SESSION['EMP_COD'],
                        'USU_COD' => $_SESSION['USU_COD'],
                        'CTA_COD' => $dados['CTA_COD'],
                        'LAN_COD' => $dados['LAN_COD'],
                        'FPG_COD' => $dados['FPG_COD'],
                        'PAG_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'PAG_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'), 
                        'PAG_DT_PAGAMENTO' => $dados['PAG_DT_PAGAMENTO'],
                        'PAG_TIPO' => 2,
                        'PAG_TOKEN' => $this->Check->token(10,'',true),
                        'PAG_VALOR' => $dados['PAG_VALOR'],
                        'PAG_OBSERVACAO' => $dados['PAG_OBSERVACAO'],
                        'PAG_STATUS'=> 1
                    );
                    //$this->PagamentosRecebimentos
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' PAG12 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' PAG11- Dados inválido(s)!','alert alert-danger');
        }

        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->dados['lancamentos_pagar'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(1)->listarTodosTipo(0);
        if ($ok) {
           
            $this->render('admin/financeiro/pagamentos/pagar', $this->dados);
        }else {
            $this->render('admin/financeiro/pagamentos/pagar', $this->dados);
        }
    }
    
    public function receber()
    {
        $this->dados['title'] .= ' GERENCIAR PAGAMENTOS';   
        $this->dados['lancamentos_receber'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(2)->listarTodosTipo();
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/pagamentos/receber', $this->dados);
    }
}