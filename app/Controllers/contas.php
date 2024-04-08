<?php 
namespace App\Controllers;

use App\Models\Caixas;
use App\Models\Classificacoes;
use App\Models\Empresas;
use App\Models\Contas as ModelsContas;
use App\Models\FormasPagamentos;
use App\Models\ModulosEmpresa;
use App\Models\Movimentacoes;
use App\Models\Transacoes;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Vendedores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class contas extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa,$Contas,$Movimentacoes,$FormasPagamentos,$Caixas,$Classificacoes,$ModulosEmpresa,$Transacoes;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Contas = new ModelsContas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Movimentacoes = new Movimentacoes;
        $this->Caixas = new Caixas;
        $this->Classificacoes = new Classificacoes;
        $this->FormasPagamentos = new FormasPagamentos;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Transacoes = new Transacoes;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['contas'] = $this->Contas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas();
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(11)->listarTodosPorTipo(0);
        $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodasAtivas(0);
        $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['modulo'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo(3)->listarModuloEmpresa(0);
        $this->dados['transacoes'] = $this->Transacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodasTransacoes(0);
        
        if ($this->dados['modulo'] == 0) {
            Sessao::alert('OK',' MÓDULO NÃO DISPONÍVEL!','alert alert-danger');
            Url::redirecionar('admin/painel');
        }

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO DE FINANÇAS >>'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR CONTAS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/contas/listar', $this->dados);
    }
    public function contas()
    {
        $this->dados['title'] .= ' GERENCIAR CONTAS';   
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/contas/listar', $this->dados);
    }
    public function detalhar()
    {
        $this->dados['title'] .= 'DETALHAR CONTA'; 
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false; 
        
        if (isset($dados[2]) && isset($dados[3])) {
            
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['conta'] = $this->Contas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['conta'] != 0) {
                    //$this->dados['CTA_COD'] = $dados[3];
                    $this->link[3] = ['link'=> 'financeiro/detalhar_contas/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'DETALHAR CONTA >> '.$this->dados['conta']['CTA_DESCRICAO']];
                    $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
                    $ok = true;
                    $this->dados['transacoes_conta'] = $this->Transacoes->setCodEmpresa($_SESSION['EMP_COD'])->setCodConta($this->dados['conta']['CTA_COD'])->listarTodasTransacoesConta(0);
                    $this->dados['transacoes_conta_entradas'] = $this->Transacoes->setCodEmpresa($_SESSION['EMP_COD'])->setCodConta($this->dados['conta']['CTA_COD'])->setTipo(1)->listarTodasTransacoesContaPorTipo(0);
                    $this->dados['transacoes_conta_saidas'] = $this->Transacoes->setCodEmpresa($_SESSION['EMP_COD'])->setCodConta($this->dados['conta']['CTA_COD'])->setTipo(2)->listarTodasTransacoesContaPorTipo(0);
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
        $this->dados['title'] .= ' CADASTRAR CONTA DA EMPRESA/NEGÓCIO';   
        $this->link[3] = ['link'=> 'contas/cadastro','nome' => 'CADASTRO DE CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/contas/cadastrar', $this->dados);
    }
    public function saques()
    {
        $this->dados['title'] .= ' SACAR DA CONTA DA EMPRESA/NEGÓCIO';   
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();

        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
            $this->dados['conta'] = $this->Contas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
            if ($this->dados['conta'] != 0) {
                $this->link[3] = ['link'=> 'financeiro/detalhar_contas/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'DETALHAR CONTA >> '.$this->dados['conta']['CTA_DESCRICAO']];
                $this->link[4] = ['link'=> 'contas/saques/'.$this->dados['conta']['EMP_COD'].'/'.$this->dados['conta']['CTA_COD'],'nome' => 'SAQUE DA CONTA'];
                $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
                $ok = true;
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CTA22 - Acesso inválido(s)!','alert alert-danger');
        }
        if($ok){
            $this->render('admin/financeiro/contas/saques', $this->dados);
        }else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/financeiro/contas', $this->dados);
        }
    }
    public function depositos()
    {
        $this->dados['title'] .= ' DEPOSITAR NA CONTA DA EMPRESA/NEGÓCIO';   
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();

        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
            $this->dados['conta'] = $this->Contas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
            if ($this->dados['conta'] != 0) {
                $this->link[3] = ['link'=> 'financeiro/detalhar_contas/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'DETALHAR CONTA >> '.$this->dados['conta']['CTA_DESCRICAO']];
                $this->link[4] = ['link'=> 'contas/depositos/'.$this->dados['conta']['EMP_COD'].'/'.$this->dados['conta']['CTA_COD'],'nome' => 'DEPÓSITO NA CONTA'];
                $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
                $ok = true;
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CTA22 - Acesso inválido(s)!','alert alert-danger');
        }
        if($ok){
            $this->render('admin/financeiro/contas/depositos', $this->dados);
        }else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/financeiro/contas', $this->dados);
        }
    }
    public function depositar()
    {
        $this->dados['title'] .= ' CADASTRAR TRANSAÇÃO DA CONTA';   
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_TRANSACAO'])) {
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['CADASTRAR_NOVA_TRANSACAO']);

                $this->dados['conta'] = $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD'])->listar(0);
                if ($this->dados['conta'] != 0) {

                    $this->link[3] = ['link'=> 'financeiro/detalhar_contas/'.$_SESSION['EMP_COD'].'/'.$dados['CTA_COD'],'nome' => 'DETALHAR CONTA >> '.$this->dados['conta']['CTA_DESCRICAO']];
                    $this->link[4] = ['link'=> 'contas/depositos/'.$this->dados['conta']['EMP_COD'].'/'.$this->dados['conta']['CTA_COD'],'nome' => 'DEPÓSITO NA CONTA'];
                    $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
                      
                    
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
    
                        $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD']);

                        $db = array(
                            'CTA_SALDO'=> $dados['TRS_VALOR_TOTAL'],
                            'CTA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                        );
                    
                        if($this->Contas->alterar($db,0)){
                            $ok = true;
                            Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                        }else{
                            Sessao::alert('ERRO',' ERRO: CTA24 - Erro ao alterar conta, entre em contato com o suporte!','fs-4 alert alert-danger');
                        }
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
            $this->render('admin/financeiro/contas/depositos', $this->dados);
        }else {
            $this->render('admin/financeiro/contas', $this->dados);
        }
    }
    public function transferencias()
    {
        $this->dados['title'] .= ' TRANSFERÊNCIA DA CONTA DA EMPRESA/NEGÓCIO';   
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();

        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
            $this->dados['conta'] = $this->Contas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
            if ($this->dados['conta'] != 0) {
                $this->link[3] = ['link'=> 'financeiro/detalhar_contas/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'DETALHAR CONTA >> '.$this->dados['conta']['CTA_DESCRICAO']];
                $this->link[4] = ['link'=> 'contas/transferencias/'.$this->dados['conta']['EMP_COD'].'/'.$this->dados['conta']['CTA_COD'],'nome' => 'TRANSFERÊNCIA DA CONTA'];
                $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
                $ok = true;
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CTA22 - Acesso inválido(s)!','alert alert-danger');
        }
        if($ok){
            $this->render('admin/financeiro/contas/transferencias', $this->dados);
        }else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/financeiro/contas', $this->dados);
        }
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR CONTA DA EMPRESA/NEGÓCIO';   
        $this->link[3] = ['link'=> 'contas/cadastro','nome' => 'CADASTRO DE CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_CONTA'])) {
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['CADASTRAR_NOVA_CONTA']);
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $dados += array(
                    'CTA_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                    'CTA_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),             
                    'CTA_STATUS'=> 1
                );

                if($this->Contas->cadastrar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' CTA3 - Erro ao cadastrar nova conta, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' CTA2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' CTA1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['contas'] = $this->Contas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
            $this->render('admin/financeiro/contas/listar', $this->dados);
        }else {
            $this->render('admin/financeiro/contas/cadastrar', $this->dados);
        }
    }
    public function alteracao()
    {
        $this->dados['title'] .= ' ALTERAR CONTA'; 
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['conta'] = $this->Contas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['conta'] != 0) {
                    $this->link[3] = ['link'=> 'financeiro/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR CONTA'];
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CTA22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CTA11 - Acesso inválido(s)!','alert alert-danger');
        }  
        if($ok){
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/financeiro/contas/alterar', $this->dados);
        }else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/financeiro/contas/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR CONTA'; 
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['ALTERAR_CONTA'])) {
           
            if($_SESSION['USU_COD'] == $dados['USU_COD_ATUALIZACAO'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['ALTERAR_CONTA']);

                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }

                $this->dados['conta'] = $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD']);

                if ($this->dados['conta'] != 0) {

                    $this->link[3] = ['link'=> 'contas/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['CTA_COD'],'nome' => 'ALTERAR CONTAS'];
        
                    $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD']);
                   
                    unset($dados['CTA_COD']);
                    unset($dados['EMP_COD']);

                    $dados += array(
                        'CTA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                        'CTA_STATUS' => 1
                    );
                   
                    if($this->Contas->alterar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' ERRO: CTA24 - Erro ao alterar conta, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' CTA23- Conta não foi encontrada!, verifque os dados informados, ou entre em contato com o Suporte','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CTA22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CTA21 - Acesso inválido(s)!','alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['contas'] = $this->Contas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
            $this->render('admin/financeiro/contas/listar', $this->dados);
        }else {
            $this->dados['conta'] = $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD'])->listar(0);
            $this->render('admin/financeiro/contas/alterar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_CONTA'])) {
 
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['STATUS_CONTA']);
                $this->Contas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CTA_COD']);
                ($dados['CTA_STATUS'] == 1)? $dados['CTA_STATUS'] = 0 : $dados['CTA_STATUS'] = 1;
                
                $db = array(
                    'COL_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'CTA_STATUS' => $dados['CTA_STATUS']
                );

                if($this->Contas->alterar($db,0)){
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status da conta da empresa/negócio, entre em contato com o suporte!'
                    );
                }
            }else {
                $respota = array(
                    'COD'=>'ERRO',
                    'MENSAGEM'=> 'ERRO 2- Dados inválido(s)!'
                );
            }
        }else {
            $respota = array(
                'COD'=>'ERRO',
                'MENSAGEM'=> 'ERRO 1- Acesso inválido!'
            );
        }
        echo json_encode($respota);
    }
}