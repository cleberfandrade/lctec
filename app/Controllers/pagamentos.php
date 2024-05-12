<?php 
namespace App\Controllers;

use App\Models\Contas;
use App\Models\FormasPagamentos;
use App\Models\Lancamentos;
use App\Models\ModulosEmpresa;
use App\Models\Movimentacoes;
use App\Models\PagamentosRecebimentos;
use App\Models\Transacoes;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class pagamentos extends View
{
    private $dados = [];
    private $link,$Check,$Usuarios,$UsuariosEmpresa,$Lancamentos,$Contas,$Movimentacoes, $FormasPagamentos, $ModulosEmpresa,$PagamentosRecebimentos,$Transacoes;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Check = new Check;
        $this->Contas = new Contas;
        $this->FormasPagamentos = new FormasPagamentos;
        $this->Lancamentos = new Lancamentos;  
        $this->Movimentacoes = new Movimentacoes;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->PagamentosRecebimentos = new PagamentosRecebimentos;
        $this->Transacoes = new Transacoes;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);

        $this->dados['pagamentos'] = $this->PagamentosRecebimentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(1)->listarTodosTipo(0);

        $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodasAtivas(0);
        $this->dados['modulo'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo(3)->listarModuloEmpresa(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO FINANCEIRO'];
        $this->link[2] = ['link'=> 'pagamentos','nome' => 'GERENCIAR PAGAMENTOS/RECEBIMENTOS'];
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
        $this->dados['lancamentos_pagar'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(1)->setStatus(1)->listarTodosTipo(0);
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
                    (isset($dados['PAG_TOTAL']) && $dados['PAG_TOTAL'] == "on")? $dados['PAG_TOTAL'] = 1: $dados['PAG_TOTAL'] = 0;
                    $token = $this->Check->token(10,'',true);
                    $db = array(
                        'EMP_COD' => $_SESSION['EMP_COD'],
                        'USU_COD' => $_SESSION['USU_COD'],
                        'CTA_COD' => $dados['CTA_COD'],
                        'LAN_COD' => $dados['LAN_COD'],
                        'FPG_COD' => $dados['FPG_COD'],
                        'PAG_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'PAG_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'), 
                        'PAG_DT_PAGAMENTO' => $dados['PAG_DT_PAGAMENTO'],
                        'PAG_TOTAL'=> $dados['PAG_TOTAL'],
                        'PAG_TIPO' => 2,
                        'PAG_TOKEN' => $token,
                        'PAG_VALOR' => $dados['PAG_VALOR'],
                        'PAG_OBSERVACAO' => $dados['PAG_OBSERVACAO'],
                        'PAG_STATUS'=> 1
                    );
                    
                    if ($dados['PAG_TOTAL'] == 1) {
                        //PAGAMENTO FOI EFETUADO TOTALMENTE
                        //RESGISTRANDO PAGAMENTO
                        $idPagamento = $this->PagamentosRecebimentos->cadastrar($db,0);
                        if(isset($idPagamento) && !empty($idPagamento)){
                            
                            //ATUALIZANDO O STATUS DO LANÇAMENTO
                            $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($dados['LAN_COD']);
                            $novo = 0;
                            ($dados['PAG_VALOR'] != $this->dados['lancamento']['LAN_VALOR'])? $novo = $dados['PAG_VALOR'] : $novo = $this->dados['lancamento']['LAN_VALOR'];
                            $dados_lancamentos = array(
                                'LAN_VALOR' => $novo,
                                'LAN_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                                'LAN_STATUS'=> 3
                            );
                            $desconto = $acrescimo = 0;
                            ($dados['PAG_VALOR'] > $this->dados['lancamento']['LAN_VALOR'])? $acrescimo = ($dados['PAG_VALOR']-$this->dados['lancamento']['LAN_VALOR']) : $desconto = ($this->dados['lancamento']['LAN_VALOR']-$dados['PAG_VALOR']); 
                            if($this->Lancamentos->alterar($dados_lancamentos,0)){
                                $ok = true;
                                //ATUALIZANDO O SALDO EM CONTA DA EMPRESA
                                Sessao::alert('OK','Pagamento registrado com sucesso!','fs-4 alert alert-success');
                                $this->dados['conta'] = $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD'])->listar(0);
                                if ($this->dados['conta'] != 0) {
                                    $db_transacao = array(
                                        'EMP_COD' => $_SESSION['EMP_COD'],
                                        'USU_COD' => $_SESSION['USU_COD'],
                                        'CTA_COD' => $dados['CTA_COD'],
                                        'CLA_COD' => 0,
                                        'FPG_COD' => $dados['FPG_COD'],
                                        'PAG_COD' => $idPagamento,
                                        'TRS_TIPO' => 2,
                                        'TRS_DESCONTO' => $desconto,
                                        'TRS_ACRESCIMO' => $acrescimo,
                                        'TRS_VALOR_TOTAL' => $dados['PAG_VALOR'],
                                        'TRS_DATA' => $dados['PAG_DT_PAGAMENTO'],
                                        'TRS_DESCRICAO'=> 'PAGAMENTO: '.$this->dados['lancamento']['LAN_DESCRICAO'],
                                        'TRS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                        'TRS_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),   
                                        'TRS_TOKEN' => $token,          
                                        'TRS_STATUS'=> 1
                                    );
                                    //dump($db_transacao);
                                    if($this->Transacoes->cadastrar($db_transacao,0)){
                                        $saldo = 0;
                                        $saldo = ($this->dados['conta']['CTA_SALDO'] - $dados['PAG_VALOR']);
                                        $db_conta = array(
                                            'CTA_SALDO' => $saldo,
                                            'CTA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                                        );
                                    
                                        if($this->Contas->alterar($db_conta,0)){
                                            $ok = true;
                                            Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                                        }else{
                                            Sessao::alert('ERRO',' ERRO: CTA24 - Erro ao alterar conta, entre em contato com o suporte!','fs-4 alert alert-danger');
                                        }
                                    }else{
                                        Sessao::alert('ERRO',' CTA3 - Erro ao cadastrar nova transação, entre em contato com o suporte!','fs-4 alert alert-danger');
                                    }
                                }
    
                            }else{
                                Sessao::alert('ERRO',' ERRO: LAN33- Erro ao registrar atualização no lançamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                            }
                           
                        }else {
                            Sessao::alert('ERRO',' PAG12 - Errro ao registrar seu pagamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                        }
                    } else {
                       //PAGAMENTO FOI EFETUADO PARCIALMENTE

                       //REGISTRAR SOBRA DO PAGAMENTO PARA O PROXIMO MÊS SE EXISTIR SOLICITAÇÃO
                       (isset($dados['PROXIMO_MES']) && $dados['PROXIMO_MES'] == "on")? $dados['PROXIMO_MES'] = 1: $dados['PROXIMO_MES'] = 0;

                       if ($dados['PROXIMO_MES'] == 1) {
                            $saldo_lancamento = 0;
                           ($dados['PAG_VALOR'] < $this->dados['lancamento']['LAN_VALOR'])? $saldo_lancamento = ($this->dados['lancamento']['LAN_VALOR']-$dados['PAG_VALOR']) : $saldo_lancamento = $this->dados['lancamento']['LAN_VALOR']; 
                            $novo_vencimento = date('Y-m-d', strtotime($this->dados['lancamento']['LAN_DT_VENCIMENTO']. ' + 30 days')); 
                            $db_lancamento = array(
                                'EMP_COD' => $_SESSION['EMP_COD'],
                                'USU_COD' => $_SESSION['USU_COD'],
                                'CTA_COD' => $dados['CTA_COD'],
                                'CAT_COD' => 0,
                                'CLA_COD' => 0,
                                'CLI_COD' => 0,
                                'FOR_COD' => 0,
                                'LAN_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                'LAN_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'), 
                                'LAN_DT_VENCIMENTO'=> $novo_vencimento, 
                                'LAN_TIPO' => 1,
                                'LAN_DESCRICAO' => 'SALDO RESTANTE: '.$this->dados['lancamento']['LAN_DESCRICAO'],
                                'LAN_OBSERVACAO' => '',
                                'LAN_RESULTADOS' => 1,
                                'LAN_VALOR' => $saldo_lancamento,
                                'LAN_PARCELA'=> 1,      
                                'LAN_STATUS'=> 1
                            );
                            $this->Lancamentos->cadastrar($db_lancamento,0);
                            
                            $idPagamento = $this->PagamentosRecebimentos->cadastrar($db,0);
                            if(isset($idPagamento) && !empty($idPagamento)){
                                
                                //ATUALIZANDO O STATUS DO LANÇAMENTO
                                $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($dados['LAN_COD']);
                                $novo = 0;
                                ($dados['PAG_VALOR'] != $this->dados['lancamento']['LAN_VALOR'])? $novo = $dados['PAG_VALOR'] : $novo = $this->dados['lancamento']['LAN_VALOR'];
                                /*
                                *LAN_TIPO = 1 A PAGAR
                                *LAN_TIPO = 2 A RECEBER
                                *LAN_STATUS = 3 PAGO
                                *LAN_STATUS = 4 RECEBIDO
                                */
                                $dados_lancamentos = array(
                                    'LAN_VALOR' => $novo,
                                    'LAN_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                                    'LAN_STATUS'=> 3
                                );
                                $desconto = $acrescimo = 0;
                                ($dados['PAG_VALOR'] > $this->dados['lancamento']['LAN_VALOR'])? $acrescimo = ($dados['PAG_VALOR']-$this->dados['lancamento']['LAN_VALOR']) : $desconto = ($this->dados['lancamento']['LAN_VALOR']-$dados['PAG_VALOR']); 
                                if($this->Lancamentos->alterar($dados_lancamentos,0)){
                                    $ok = true;
                                    //ATUALIZANDO O SALDO EM CONTA DA EMPRESA
                                    Sessao::alert('OK','Pagamento registrado com sucesso!','fs-4 alert alert-success');
                                    $this->dados['conta'] = $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD'])->listar(0);
                                    if ($this->dados['conta'] != 0) {
                                        $db_transacao = array(
                                            'EMP_COD' => $_SESSION['EMP_COD'],
                                            'USU_COD' => $_SESSION['USU_COD'],
                                            'CTA_COD' => $dados['CTA_COD'],
                                            'CLA_COD' => 0,
                                            'FPG_COD' => $dados['FPG_COD'],
                                            'PAG_COD' => $idPagamento,
                                            'TRS_TIPO' => 2,
                                            'TRS_DESCONTO' => $desconto,
                                            'TRS_ACRESCIMO' => $acrescimo,
                                            'TRS_VALOR_TOTAL' => $dados['PAG_VALOR'],
                                            'TRS_DATA' => $dados['PAG_DT_PAGAMENTO'],
                                            'TRS_DESCRICAO'=> 'PAGAMENTO: '.$this->dados['lancamento']['LAN_DESCRICAO'],
                                            'TRS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                            'TRS_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),   
                                            'TRS_TOKEN' => $token,          
                                            'TRS_STATUS'=> 1
                                        );
                                        //dump($db_transacao);
                                        if($this->Transacoes->cadastrar($db_transacao,0)){
                                            $saldo = 0;
                                            $saldo = ($this->dados['conta']['CTA_SALDO'] - $dados['PAG_VALOR']);
                                            $db_conta = array(
                                                'CTA_SALDO' => $saldo,
                                                'CTA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                                            );
                                        
                                            if($this->Contas->alterar($db_conta,0)){
                                                $ok = true;
                                                Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                                            }else{
                                                Sessao::alert('ERRO',' ERRO: CTA24 - Erro ao alterar conta, entre em contato com o suporte!','fs-4 alert alert-danger');
                                            }
                                        }else{
                                            Sessao::alert('ERRO',' CTA3 - Erro ao cadastrar nova transação, entre em contato com o suporte!','fs-4 alert alert-danger');
                                        }
                                    }
        
                                }else{
                                    Sessao::alert('ERRO',' ERRO: LAN33- Erro ao registrar atualização no lançamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                                }
                            }else {
                                Sessao::alert('ERRO',' PAG12 - Errro ao registrar seu pagamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                            }
                       }else{
                            //dump($dados);


                            Sessao::alert('ERRO',' PAG12 - Erro ao registrar saldo restante do pagamento, contate o suporte','fs-4 alert alert-danger');
                       }
                    }
                }else {
                    Sessao::alert('ERRO',' PAG13 - Lançamento da empresa não encontrado!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' PAG12 - Acesso inválido(s)!','fs-4 alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' PAG11- Dados inválido(s)!','fs-4 alert alert-danger');
        }

        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->dados['lancamentos_pagar'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(1)->setStatus(1)->listarTodosTipo(0);
        if ($ok) {
           
            $this->render('admin/financeiro/pagamentos/pagar', $this->dados);
        }else {
            $this->render('admin/financeiro/pagamentos/pagar', $this->dados);
        }
    } 
    public function receber()
    {
        $this->dados['title'] .= ' GERENCIAR RECEBIMENTOS';   
        $this->dados['lancamentos_receber'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(2)->setStatus(1)->listarTodosTipo();
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/pagamentos/receber', $this->dados);
    }
    public function receber_lançamento()
    {
        $this->dados['title'] .= ' GERENCIAR RECEBIMENTOS';   
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $ok = false;
        if (isset($_POST) && isset($dados['CADASTRAR_RECEBIMENTO'])) {
            unset($dados['CADASTRAR_RECEBIMENTO']);
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
               
                $this->dados['lancamento'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($dados['LAN_COD'])->listar(0);
                if ($this->dados['lancamento'] != 0) {
                    (isset($dados['PAG_TOTAL']) && $dados['PAG_TOTAL'] == "on")? $dados['PAG_TOTAL'] = 1: $dados['PAG_TOTAL'] = 0;
                    $token = $this->Check->token(10,'',true);
                    $db = array(
                        'EMP_COD' => $_SESSION['EMP_COD'],
                        'USU_COD' => $_SESSION['USU_COD'],
                        'CTA_COD' => $dados['CTA_COD'],
                        'LAN_COD' => $dados['LAN_COD'],
                        'FPG_COD' => $dados['FPG_COD'],
                        'PAG_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'PAG_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'), 
                        'PAG_DT_PAGAMENTO' => $dados['PAG_DT_PAGAMENTO'],
                        'PAG_TOTAL'=> $dados['PAG_TOTAL'],
                        'PAG_TIPO' => 1,
                        'PAG_TOKEN' => $token,
                        'PAG_VALOR' => $dados['PAG_VALOR'],
                        'PAG_OBSERVACAO' => $dados['PAG_OBSERVACAO'],
                        'PAG_STATUS'=> 1
                    );

                    if ($dados['PAG_TOTAL'] == 1) {
                        //PAGAMENTO FOI EFETUADO TOTALMENTE
                        //RESGISTRANDO PAGAMENTO
                        $idPagamento = $this->PagamentosRecebimentos->cadastrar($db,0);
                        if(isset($idPagamento) && !empty($idPagamento)){
                            
                            //ATUALIZANDO O STATUS DO LANÇAMENTO
                            $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($dados['LAN_COD']);
                            $novo = 0;
                            ($dados['PAG_VALOR'] != $this->dados['lancamento']['LAN_VALOR'])? $novo = $dados['PAG_VALOR'] : $novo = $this->dados['lancamento']['LAN_VALOR'];
                           /*
                            *LAN_TIPO = 1 A PAGAR
                            *LAN_TIPO = 2 A RECEBER
                            *LAN_STATUS = 3 PAGO
                            *LAN_STATUS = 4 RECEBIDO
                            */
                            $dados_lancamentos = array(
                                'LAN_VALOR' => $novo,
                                'LAN_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                                'LAN_STATUS'=> 4
                            );
                            $desconto = $acrescimo = 0;
                            ($dados['PAG_VALOR'] > $this->dados['lancamento']['LAN_VALOR'])? $acrescimo = ($dados['PAG_VALOR']-$this->dados['lancamento']['LAN_VALOR']) : $desconto = ($this->dados['lancamento']['LAN_VALOR']-$dados['PAG_VALOR']); 
                            if($this->Lancamentos->alterar($dados_lancamentos,0)){
                                $ok = true;
                                //ATUALIZANDO O SALDO EM CONTA DA EMPRESA
                                Sessao::alert('OK','Pagamento registrado com sucesso!','fs-4 alert alert-success');
                                $this->dados['conta'] = $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD'])->listar(0);
                                if ($this->dados['conta'] != 0) {
                                    $db_transacao = array(
                                        'EMP_COD' => $_SESSION['EMP_COD'],
                                        'USU_COD' => $_SESSION['USU_COD'],
                                        'CTA_COD' => $dados['CTA_COD'],
                                        'CLA_COD' => 0,
                                        'FPG_COD' => $dados['FPG_COD'],
                                        'PAG_COD' => $idPagamento,
                                        'TRS_TIPO' => 1,
                                        'TRS_DESCONTO' => $desconto,
                                        'TRS_ACRESCIMO' => $acrescimo,
                                        'TRS_VALOR_TOTAL' => $dados['PAG_VALOR'],
                                        'TRS_DATA' => $dados['PAG_DT_PAGAMENTO'],
                                        'TRS_DESCRICAO'=> 'RECEBIMENTO: '.$this->dados['lancamento']['LAN_DESCRICAO'],
                                        'TRS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                        'TRS_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),   
                                        'TRS_TOKEN' => $token,          
                                        'TRS_STATUS'=> 1
                                    );
                                    //dump($db_transacao);
                                    if($this->Transacoes->cadastrar($db_transacao,0)){
                                        $saldo = 0;
                                        $saldo = ($this->dados['conta']['CTA_SALDO'] + $dados['PAG_VALOR']);
                                        $db_conta = array(
                                            'CTA_SALDO' => $saldo,
                                            'CTA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                                        );
                                    
                                        if($this->Contas->alterar($db_conta,0)){
                                            $ok = true;
                                            Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                                        }else{
                                            Sessao::alert('ERRO',' ERRO: CTA24 - Erro ao alterar conta, entre em contato com o suporte!','fs-4 alert alert-danger');
                                        }
                                    }else{
                                        Sessao::alert('ERRO',' CTA3 - Erro ao cadastrar nova transação, entre em contato com o suporte!','fs-4 alert alert-danger');
                                    }
                                }
    
                            }else{
                                Sessao::alert('ERRO',' ERRO: LAN33- Erro ao registrar atualização no lançamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                            }
                           
                        }else {
                            Sessao::alert('ERRO',' PAG12 - Errro ao registrar seu pagamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                        }
                    } else {
                       //PAGAMENTO FOI EFETUADO PARCIALMENTE

                       //REGISTRAR SOBRA DO PAGAMENTO PARA O PROXIMO MÊS SE EXISTIR SOLICITAÇÃO
                       (isset($dados['PROXIMO_MES']) && $dados['PROXIMO_MES'] == "on")? $dados['PROXIMO_MES'] = 1: $dados['PROXIMO_MES'] = 0;

                       if ($dados['PROXIMO_MES'] == 1) {
                            $saldo_lancamento = 0;
                           ($dados['PAG_VALOR'] < $this->dados['lancamento']['LAN_VALOR'])? $saldo_lancamento = ($this->dados['lancamento']['LAN_VALOR']-$dados['PAG_VALOR']) : $saldo_lancamento = $this->dados['lancamento']['LAN_VALOR']; 
                            $novo_vencimento = date('Y-m-d', strtotime($this->dados['lancamento']['LAN_DT_VENCIMENTO']. ' + 30 days')); 
                            $db_lancamento = array(
                                'EMP_COD' => $_SESSION['EMP_COD'],
                                'USU_COD' => $_SESSION['USU_COD'],
                                'CTA_COD' => $dados['CTA_COD'],
                                'CAT_COD' => 0,
                                'CLA_COD' => 0,
                                'CLI_COD' => 0,
                                'FOR_COD' => 0,
                                'LAN_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                'LAN_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'), 
                                'LAN_DT_VENCIMENTO'=> $novo_vencimento, 
                                'LAN_TIPO' => 2,
                                'LAN_DESCRICAO' => 'SALDO RESTANTE: '.$this->dados['lancamento']['LAN_DESCRICAO'],
                                'LAN_OBSERVACAO' => '',
                                'LAN_RESULTADOS' => 1,
                                'LAN_VALOR' => $saldo_lancamento,
                                'LAN_PARCELA'=> 1,      
                                'LAN_STATUS'=> 1
                            );
                            $this->Lancamentos->cadastrar($db_lancamento,0);
                            
                            $idPagamento = $this->PagamentosRecebimentos->cadastrar($db,0);
                            if(isset($idPagamento) && !empty($idPagamento)){
                                
                                //ATUALIZANDO O STATUS DO LANÇAMENTO
                                $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($dados['LAN_COD']);
                                $novo = 0;
                                ($dados['PAG_VALOR'] != $this->dados['lancamento']['LAN_VALOR'])? $novo = $dados['PAG_VALOR'] : $novo = $this->dados['lancamento']['LAN_VALOR'];
                                $dados_lancamentos = array(
                                    'LAN_VALOR' => $novo,
                                    'LAN_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                                    'LAN_STATUS'=> 3
                                );
                                $desconto = $acrescimo = 0;
                                ($dados['PAG_VALOR'] > $this->dados['lancamento']['LAN_VALOR'])? $acrescimo = ($dados['PAG_VALOR']-$this->dados['lancamento']['LAN_VALOR']) : $desconto = ($this->dados['lancamento']['LAN_VALOR']-$dados['PAG_VALOR']); 
                                if($this->Lancamentos->alterar($dados_lancamentos,0)){
                                    $ok = true;
                                    //ATUALIZANDO O SALDO EM CONTA DA EMPRESA
                                    Sessao::alert('OK','Pagamento registrado com sucesso!','fs-4 alert alert-success');
                                    $this->dados['conta'] = $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD'])->listar(0);
                                    if ($this->dados['conta'] != 0) {
                                        $db_transacao = array(
                                            'EMP_COD' => $_SESSION['EMP_COD'],
                                            'USU_COD' => $_SESSION['USU_COD'],
                                            'CTA_COD' => $dados['CTA_COD'],
                                            'CLA_COD' => 0,
                                            'FPG_COD' => $dados['FPG_COD'],
                                            'PAG_COD' => $idPagamento,
                                            'TRS_TIPO' => 1,
                                            'TRS_DESCONTO' => $desconto,
                                            'TRS_ACRESCIMO' => $acrescimo,
                                            'TRS_VALOR_TOTAL' => $dados['PAG_VALOR'],
                                            'TRS_DATA' => $dados['PAG_DT_PAGAMENTO'],
                                            'TRS_DESCRICAO'=> 'RECEBIMENTO: '.$this->dados['lancamento']['LAN_DESCRICAO'],
                                            'TRS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                            'TRS_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),   
                                            'TRS_TOKEN' => $token,          
                                            'TRS_STATUS'=> 1
                                        );
                                        
                                        if($this->Transacoes->cadastrar($db_transacao,0)){
                                            $saldo = 0;
                                            $saldo = ($this->dados['conta']['CTA_SALDO'] - $dados['PAG_VALOR']);
                                            $db_conta = array(
                                                'CTA_SALDO' => $saldo,
                                                'CTA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                                            );
                                        
                                            if($this->Contas->alterar($db_conta,0)){
                                                $ok = true;
                                                Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                                            }else{
                                                Sessao::alert('ERRO',' ERRO: CTA24 - Erro ao alterar conta, entre em contato com o suporte!','fs-4 alert alert-danger');
                                            }
                                        }else{
                                            Sessao::alert('ERRO',' CTA3 - Erro ao cadastrar nova transação, entre em contato com o suporte!','fs-4 alert alert-danger');
                                        }
                                    }
        
                                }else{
                                    Sessao::alert('ERRO',' ERRO: LAN33- Erro ao registrar atualização no lançamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                                }
                            }else {
                                Sessao::alert('ERRO',' PAG12 - Errro ao registrar seu pagamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                            }
                       }else{
                            //dump($dados);
                            Sessao::alert('ERRO',' PAG12 - Erro ao registrar saldo restante do pagamento, contate o suporte','fs-4 alert alert-danger');
                       }
                    }
                }else{
                    Sessao::alert('ERRO',' PAG13 - Lançamento da empresa não encontrado!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' PAG12 - Acesso inválido(s)!','fs-4 alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' PAG11- Dados inválido(s)!','fs-4 alert alert-danger');
        }

        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->dados['lancamentos_receber'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(2)->setStatus(1)->listarTodosTipo(0);
        if ($ok) {
           
            $this->render('admin/financeiro/pagamentos/receber', $this->dados);
        }else {
            $this->render('admin/financeiro/pagamentos/receber', $this->dados);
        }
    }
}