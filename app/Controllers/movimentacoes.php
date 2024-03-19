<?php

namespace App\Controllers;

header("Content-Type: text/html; charset=UTF-8",true);

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

class movimentacoes extends View
{
    private $dados = [];
    private $link,$Enderecos,$Clientes,
    $Usuarios,$Produtos,$Empresa,$UsuariosEmpresa,
    $Check,$CargosSalarios,$ModulosEmpresa,$Financas,
    $Estoques,$Setores,$Categorias,$Classificacoes,
    $Movimentacoes,$Transacoes,$FormasPagamentos,$Vendas,$Caixas,$ItensVendas;
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
        $this->Setores = new Setores;
        $this->Categorias = new Categorias;
        $this->Produtos = new Produtos;
        $this->Classificacoes = new Classificacoes;
        $this->FormasPagamentos = new FormasPagamentos;
        $this->Movimentacoes = new MovimentacoesModels;
        $this->Transacoes = new Transacoes;
        $this->Vendas = new Vendas;
        $this->Caixas = new Caixas;
        $this->ItensVendas = new ItensVendas;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        
        $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0); 
        
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0); 
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0);
        
        $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodosAtivos(0);
        $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodasAtivas(0);
        $this->dados['movimentacoes'] = $this->Movimentacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0); 

        $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'estoques','nome' => 'MÓDULO DE ESTOQUES'];
        $this->link[2] = ['link'=> 'cadastros/estoques','nome' => 'GERENCIAR ESTOQUES'];
    }
    public function index()
    {
        $this->dados['title'] .= ' MOVIMENTAÇÃO DO ESTOQUE';
        $this->link[2] = ['link'=> 'estoques/movimentacao','nome' => 'MOVIMENTAÇÃO DO ESTOQUE'];

        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/estoques/movimentacoes/movimentacoes', $this->dados);
    }
    public function listar()
    {
        $this->dados['title'] .= ' MOVIMENTAÇÃO DO ESTOQUE';
        $this->link[2] = ['link'=> 'estoques/movimentacao','nome' => 'MOVIMENTAÇÃO DO ESTOQUE'];

        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/estoques/movimentacoes/listar', $this->dados);
    }

    public function produtos()
    {
        $this->dados['title'] .= 'GERENCIAR ESTOQUE';
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(8)->listarTodosPorTipo(0);
        $dados = filter_input_array(INPUT_GET, FILTER_DEFAULT);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[0]) && $dados[0] == 'estoques' && isset($dados[1]) && $dados[1] == 'produtos') {
            
            if (isset($dados[2]) && $dados[2] != '' && isset($dados[3]) && $dados[3] != '') {

                if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] == $dados[2]){
                    
                    $this->dados['estoque'] = $this->Estoques->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);

                    if ($this->dados['estoque'] != 0) {
                        
                        //$this->link[2] = ['link'=> 'estoques/'.$dados[2],'nome' => 'GERENCIAR ESTOQUE'];
                        $this->link[2] = ['link'=> 'estoques/produtos/'.$dados[2].'/'.$dados[3],'nome' => 'GERENCIAR PRODUTOS'];
                        $this->dados['produtos'] = $this->Estoques->listarProdutosEstoque(0);

                        $ok = true;       
                    }
                    //$this->Produtos->setCodEmpresa($dados[2])->setCodEstoque($dados[3]);
                }else {
                    Sessao::alert('ERRO',' 3- Acesso inválido!','fs-4 alert alert-danger');
                }
            }else {
                Sessao::alert('ERRO',' 2- Dados inválido(s)!','fs-4 alert alert-danger');
            }
        }else {
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');
        }
        if ($ok) {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/produtos/listar', $this->dados);
        } else {
            $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->dados['produtos'] = 0;
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/estoques', $this->dados);
        }
    }
    public function listar_produtos()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['PRODUTOS_ESTOQUE'])) {
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                $this->dados['produtos'] = $this->Estoques->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['EST_COD'])->listarProdutosEstoque(0);
                //$this->dados['produtos'] += array(json_encode($this->dados['produtos']['PRO_NOME']));
                //dump($this->dados['produtos']);
                //$string = htmlentities(string $string [, int $flags = ENT_COMPAT | ENT_HTML401 [, string $encoding = ini_get("default_charset") [, bool $double_encode = true ]]] );
                //$this->dados['produtos']= utf8_encode($this->dados['produtos']);
                echo json_encode($this->dados['produtos'],JSON_UNESCAPED_UNICODE);
                //print_r($string);
            }
        }
    }
    public function listar_produto()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($_POST) && isset($dados['PRODUTOS'])) {
           
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                $this->dados['produto'] = $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD'])->listar(0);
                echo json_encode($this->dados['produto'],JSON_UNESCAPED_UNICODE);
                //print($string);
            }
        }
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' MOVIMENTAÇÃO DO ESTOQUE';
        $this->link[2] = ['link'=> 'movimentacoes','nome' => 'MOVIMENTAÇÃO DO ESTOQUE'];

        $ok = false;      
        //Recupera os dados enviados
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                
        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_MOVIMENTACAO_ESTOQUE'])) {
            unset($dados['CADASTRAR_NOVA_MOVIMENTACAO_ESTOQUE']);

            $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
           
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $dados += array(
                    'MOV_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                    'MOV_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),             
                    'MOV_STATUS'=> 1
                );
                $dados['MOV_DT_MOVIMENTACAO'] = $dados['MOV_DT_MOVIMENTACAO'].' '.date('H:i:s');   
                $this->dados['produto'] = $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD'])->listar(0);
                $liberado = false;
                $motivo = false;
                if (!empty($dados['MOV_TIPO'])) {

                    if ($dados['MOV_TIPO'] == 1) {
                        $liberado = true;
                       
                        $this->dados['produto']['PRO_QUANTIDADE']+= $dados['MOV_QUANTIDADE'];  
                    }else {
                       
                        if ($this->dados['produto']['PRO_QUANTIDADE'] >=0 && ($this->dados['produto']['PRO_QUANTIDADE']-$dados['MOV_QUANTIDADE'])>=0) {
                            
                            $this->dados['produto']['PRO_QUANTIDADE']-= $dados['MOV_QUANTIDADE'];
                            
                            //REGISTRAR VENDA - MOTIVO 2 => VENDA
                            /* if($dados['MOV_MOTIVO']  == 2){
                                $db_transacao = array(
                                    'VEN_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                    'VEN_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),   
                                    'TRS_TIPO' => 2,
                                    'ITS_COD' => $this->dados['produto']['PRO_COD'],
                                    'ITS_QUANTIDADE'=> $dados['MOV_QUANTIDADE'],
                                    'VEN_TOKEN' => $this->Check->token(10,'',true),  
                                    'VEN_ORDEM' => $this->Check->token(10,'',true),          
                                    'VEN_STATUS'=> 1
                                );
                            }else {
                                # code...
                            }*/
    
                            $liberado = true;
                        }
                    }
                } else {
                   $motivo = true;
                }
               
                if ($liberado) {
                    if($this->Movimentacoes->cadastrar($dados,0)){
                        $db = array(
                            'PRO_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                            'PRO_QUANTIDADE' => $this->dados['produto']['PRO_QUANTIDADE']
                        );
                        $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD']);
                        if($this->Produtos->alterar($db,0)){
                            $ok = true;
                            Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                        }
                    }else{
                        Sessao::alert('ERRO',' MOV15- Erro ao cadastrar nova movimentação, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    if($motivo){
                        Sessao::alert('ERRO',' MOVI14 - Informe o motivo da movimentação do produto!','alert alert-danger');
                    }else{
                        Sessao::alert('ERRO',' MOVI13 - A movimentação foi recusada porque o estoque do produto ficará negativo!','alert alert-danger');
                    }
                }
            }else{
                Sessao::alert('ERRO',' MOV12 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' MOV11- Dados inválido(s)!','alert alert-danger');
        }

        if ($ok) {
            header("Location:".DIRPAGE."movimentacoes");
            //$this->dados['movimentacoes'] = $this->Movimentacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
            //$this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //$this->render('admin/estoques/movimentacao', $this->dados);
        }else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/movimentacoes/movimentacoes', $this->dados);
        }
    }
    public function registrar(){
        $this->dados['title'] .= ' MOVIMENTAÇÃO DO ESTOQUE';
        $this->link[2] = ['link'=> 'movimentacoes','nome' => 'MOVIMENTAÇÃO DO ESTOQUE'];

        $ok = false;      
        //Recupera os dados enviados
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_MOVIMENTACAO_ESTOQUE'])) {
            
            unset($dados['CADASTRAR_NOVA_MOVIMENTACAO_ESTOQUE']);
         
            $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
           
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
               
               
                $liberado = false;
                $motivo = false;
                $its = 0;
                $qtd = (is_array($dados['PRO_COD'])? count($dados['PRO_COD']) : 0);

                if (!empty($dados['MOV_TIPO'])) {
                    //CHECANDO TIPO DE MOVIMENTACAO
                    if ($dados['MOV_TIPO'] == 1) {
                        //1 = ENTRADA

                        //CRIAR A MOVIMENTAÇÃO
                        for ($i=0; $i < $qtd; $i++) { 
                            
                            $this->dados['produto'] = $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD'][$i])->listar(0);
                            $dados_movimentacao = array(
                                'EMP_COD' => $_SESSION['EMP_COD'],
                                'USU_COD' => $_SESSION['USU_COD'],
                                'EST_COD' => $dados['EST_COD'],
                                'PRO_COD' => $dados['PRO_COD'][$i],
                                'MOV_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                'MOV_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),        
                                'MOV_DT_MOVIMENTACAO'=>  $dados['MOV_DT_MOVIMENTACAO'].' '.date('H:i:s'),     
                                'MOV_TIPO' => $dados['MOV_TIPO'],
                                'MOV_MOTIVO' => $dados['MOV_MOTIVO'],
                                'MOV_QUANTIDADE' => $dados['MOV_QUANTIDADE'][$i],
                                'MOV_DESCRICAO' => $dados['MOV_DESCRICAO'],
                                'MOV_STATUS'=> 1
                            );
                            //CADASTRAR MOVIMENTAÇÃO
                            if($this->Movimentacoes->cadastrar($dados_movimentacao,0)){
                                $this->dados['produto']['PRO_QUANTIDADE']+= $dados['MOV_QUANTIDADE'][$i];
                                $db = array(
                                    'PRO_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                                    'PRO_QUANTIDADE' => $this->dados['produto']['PRO_QUANTIDADE']
                                );
                                //ALTERAR A QUANTIDADE DO PRODUTO
                                $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD'][$i]);
                                if($this->Produtos->alterar($db,0)){
                                    $ok = true;
                                    $its++;
                                    
                                }
                            }
                            //REGISTRA OS ITENS DA MOVIMENTAÇÃO
                            //'TRS_TOKEN' => $this->Check->token(10,'',true), 

                            //REGISTRA SALDO NO CAIXA
                        }
                    } else {
                        //2 - SAIDA
                        //CHECAR MOTIVO DA MOVIMENTAÇÃO
                        if($dados['MOV_MOTIVO'] == 2){
                            //2 = VENDA
                            //REGISTRAR VENDA - MOTIVO 2 => VENDA
                            $ordem = 0;
                            $dados['ultima_venda'] = $this->Vendas->setCodEmpresa($dados['EMP_COD'])->setData(date('Y-m-d'))->ultimaVenda(0);
                            if ($dados['ultima_venda'] !=0) {
                               $ordem = $dados['ultima_venda']['VEN_ORDEM']+1;
                            }else {
                                $ordem = 1;
                            }
                            $dados_venda = array(
                                'EMP_COD' => $_SESSION['EMP_COD'],
                                'USU_COD' => $_SESSION['USU_COD'],
                                'CLI_COD' => $dados['CLI_COD'],
                                'CXA_COD' => $dados['CXA_COD'],
                                'FPG_COD' => $dados['FPG_COD'],
                                'VEN_ORDEM' => $ordem,
                                'VEN_TOKEN' => $this->Check->token(5,'',true),
                                'VEN_CODE' => '',
                                'VEN_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                'VEN_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),  
                                'VEN_VALOR_SUBTOTAL' => $dados['VEN_VALOR_SUBTOTAL'],
                                'VEN_VALOR_DESCONTO' => $dados['VEN_VALOR_DESCONTO'],
                                'VEN_VALOR_TOTAL' => $dados['VEN_VALOR_TOTAL'],
                                'VEN_STATUS'=> 1
                            );

                            $idVenda = $this->Vendas->cadastrar($dados_venda,0);

                            for ($i=0; $i < $qtd; $i++) { 
                                
                                $this->dados['produto'] = $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD'][$i])->listar(0);
                                
                                if ($this->dados['produto']['PRO_QUANTIDADE'] >=0 && ($this->dados['produto']['PRO_QUANTIDADE']-$dados['MOV_QUANTIDADE'][$i])>=0) {
                                    //CADASTRAR MOVIMENTAÇÃO
                                    $dados_movimentacao = array(
                                        'EMP_COD' => $_SESSION['EMP_COD'],
                                        'USU_COD' => $_SESSION['USU_COD'],
                                        'EST_COD' => $dados['EST_COD'],
                                        'PRO_COD' => $dados['PRO_COD'][$i],
                                        'MOV_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                        'MOV_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),        
                                        'MOV_DT_MOVIMENTACAO'=>  $dados['MOV_DT_MOVIMENTACAO'].' '.date('H:i:s'),     
                                        'MOV_TIPO' => $dados['MOV_TIPO'],
                                        'MOV_MOTIVO' => $dados['MOV_MOTIVO'],
                                        'MOV_QUANTIDADE' => $dados['MOV_QUANTIDADE'][$i],
                                        'MOV_DESCRICAO' => $dados['MOV_DESCRICAO'],
                                        'MOV_STATUS'=> 1
                                    );
                                    $idMovimentacao = $this->Movimentacoes->cadastrar($dados_movimentacao,0);
                                    if($idMovimentacao){
                                        $this->dados['produto']['PRO_QUANTIDADE']-= $dados['MOV_QUANTIDADE'][$i];
                                        $db = array(
                                            'PRO_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                                            'PRO_QUANTIDADE' => $this->dados['produto']['PRO_QUANTIDADE']
                                        );
                                        //ALTERAR A QUANTIDADE DO PRODUTO
                                        $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD'][$i]);
                                        if($this->Produtos->alterar($db,0)){
                                            $ok = true;
                                        }

                                        $dados_itens = array(
                                            'EMP_COD' => $_SESSION['EMP_COD'],
                                            'USU_COD' => $_SESSION['USU_COD'],
                                            'VEN_COD' => $idVenda,
                                            'PRO_COD' => $dados['PRO_COD'][$i],
                                            'MOV_COD' => $idMovimentacao,
                                            'ITS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                            'ITS_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'), 
                                            'ITS_QUANTIDADE' => $dados['MOV_QUANTIDADE'][$i],
                                            'ITS_VALOR_DESCONTO' => '0.00',
                                            'ITS_VALOR_TOTAL' => $dados['PRO_PRECO_VENDA'][$i],
                                            'ITS_STATUS'=> 1
                                        );
                                        //CADASTRAR ITENS
                                        if($this->ItensVendas->cadastrar($dados_itens,0)){    
                                            $ok = true;
                                            $its++;
                                        }
                                   
                                   
                                    }
                                   
                                }else {
                                    //ESTOQUE FICARÁ NEGATIVO
                                }

                            }
                        }elseif($dados['MOV_MOTIVO'] >= 4) {
                            //5 = DEVOLUÇÃO AO FORNECEDOR
                            for ($i=0; $i < $qtd; $i++) { 
                                
                                $this->dados['produto'] = $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD'][$i])->listar(0);
                                $dados_movimentacao = array(
                                    'EMP_COD' => $_SESSION['EMP_COD'],
                                    'USU_COD' => $_SESSION['USU_COD'],
                                    'EST_COD' => $dados['EST_COD'],
                                    'PRO_COD' => $dados['PRO_COD'][$i],
                                    'MOV_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                    'MOV_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),        
                                    'MOV_DT_MOVIMENTACAO'=>  $dados['MOV_DT_MOVIMENTACAO'].' '.date('H:i:s'),     
                                    'MOV_TIPO' => $dados['MOV_TIPO'],
                                    'MOV_MOTIVO' => $dados['MOV_MOTIVO'],
                                    'MOV_QUANTIDADE' => $dados['MOV_QUANTIDADE'][$i],
                                    'MOV_DESCRICAO' => $dados['MOV_DESCRICAO'],
                                    'MOV_STATUS'=> 1
                                );
                                //CADASTRAR MOVIMENTAÇÃO
                                if($this->Movimentacoes->cadastrar($dados_movimentacao,0)){
                                    if ($this->dados['produto']['PRO_QUANTIDADE'] >=0 && ($this->dados['produto']['PRO_QUANTIDADE']-$dados['MOV_QUANTIDADE'][$i])>=0) {
                                        $this->dados['produto']['PRO_QUANTIDADE']-= $dados['MOV_QUANTIDADE'][$i];
                                        $db = array(
                                            'PRO_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                                            'PRO_QUANTIDADE' => $this->dados['produto']['PRO_QUANTIDADE']
                                        );
                                        //ALTERAR A QUANTIDADE DO PRODUTO
                                        $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD'][$i]);
                                        if($this->Produtos->alterar($db,0)){
                                            $ok = true;
                                            $its++;
                                        }
                                    }
                                }
                            }
                            
                           
                            
                        }else {
                              // OUTRO MOTIVO
                              Sessao::alert('ERRO',' MOV3 - Informe um motivo para a movimentação!','alert alert-danger');
                        }

                    }     
                }else {
                    Sessao::alert('ERRO',' MOV3 - Informe um motivo para a movimentação!','alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' MOV2 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' MOV1- Dados inválido(s)!','alert alert-danger');
        }


        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($its == $qtd) {
            Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
            header("Location:".DIRPAGE."movimentacoes/listar");
        }else {

            $this->render('admin/estoques/movimentacoes/movimentacoes', $this->dados);
        }  
        
    }
    public function reverter_movimentacao():void
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $ok = false; 
        if (isset($_POST) && isset($dados['EXCLUIR_MOVIMENTACAO_PRODUTO'])) {

            unset($dados['EXCLUIR_MOVIMENTACAO_PRODUTO']);
            unset($dados['MOV_STATUS']);
            $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
            
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }

                $dados += array(
                    'MOV_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),             
                    'MOV_STATUS'=> 0
                );
                //dump($dados);
                $this->Movimentacoes->setCodEmpresa($_SESSION['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodProduto($dados['PRO_COD'])->setCodigo($dados['MOV_COD']);

                //BUSCANDO OS DADOS DA MOVIMENTACAO
                $this->dados['movimentacao'] = $this->Movimentacoes->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($dados['MOV_COD'])->listar(0);
                //BUSCANDO OS DADOS DO PRODUTO
                $this->dados['produto'] = $this->Produtos->setCodEmpresa($_SESSION['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD'])->listar(0);

                $liberado = false;
                //VERIFICANDO SE A MOVIMENTACAO FOI DE ENTRADA OU SAÍDA
                if ($this->dados['movimentacao']['MOV_TIPO'] == 1) {
                    //REVERTENDO A MOVIMENTACAO DIMINUINDO NO ESTOQUE DO PRODUTO
                    $this->dados['produto']['PRO_QUANTIDADE']-= $this->dados['movimentacao']['MOV_QUANTIDADE'];  
                    $liberado = true;
                }else {
                    //REVERTENDO A MOVIMENTACAO ACRESCENTANDO NO ESTOQUE DO PRODUTO
                    //if ($this->dados['produto']['PRO_QUANTIDADE'] >=0 && ($this->dados['produto']['PRO_QUANTIDADE']-= $dados['MOV_QUANTIDADE'])>=0) {
                    $this->dados['produto']['PRO_QUANTIDADE']+= $this->dados['movimentacao']['MOV_QUANTIDADE'];
                    $liberado = true;
                    //}
                }

                if ($liberado) {

                    if($this->Movimentacoes->checarRegistro(0)){

                        if($this->Movimentacoes->alterar($dados,0)){
                            $db = array(
                                'PRO_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                                'PRO_QUANTIDADE' => $this->dados['produto']['PRO_QUANTIDADE']
                            );
                            $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD']);
                            if($this->Produtos->alterar($db,0)){
                                $ok = true;
                                $respota = array(
                                    'COD'=>'OK',
                                    'MENSAGEM' => 'Reversão efetuada com sucesso!'
                                );
                            }
                        }else{
                            $respota = array(
                                'COD'=>'ERRO',
                                'MENSAGEM'=> 'ERRO 5- Erro ao reveter sua movimentação de estoque, entre em contato com o suporte!'
                            );
                        }
                    }else {
                        $respota = array(
                            'COD'=>'ERRO',
                            'MENSAGEM'=> 'ERRO 4- Alteração já foi realizada!'
                        );
                    }
                }else {
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 3- Alteração na movimentação foi recusada porque o estoque do produto ficará negativo!'
                    );
                }
            }else{
                $respota = array(
                    'COD'=>'ERRO',
                    'MENSAGEM'=> 'ERRO 2- Dados inválido(s)!'
                );
            }
      
        }else{
            $respota = array(
                'COD'=>'ERRO',
                'MENSAGEM'=> 'ERRO 1- Acesso inválido!'
            );
        }
        echo json_encode($respota);
    }
}