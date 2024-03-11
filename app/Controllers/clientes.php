<?php
namespace App\Controllers;

use App\Models\Classificacoes;
use App\Models\Clientes as ModelsCliente;
use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Setores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class clientes extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Clientes,$Setores,$Classificacoes;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Clientes= new ModelsCliente;
        $this->Empresa = new Empresas;
        $this->Enderecos = new Enderecos;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;
        $this->Setores = new Setores;
        $this->Classificacoes = new Classificacoes;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'clientes','nome' => 'GERENCIAR SEUS CLIENTES'];
    }
    public function index()
    {
        $this->dados['title'] .= ' CLIENTES';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/clientes/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR SEUS CLIENTES';
        $this->link[3] = ['link'=> 'clientes/cadastrar','nome' => 'CADASTRAR SEUS CLIENTES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/clientes/cadastrar', $this->dados);
    }
    public function cadastro_simples():void
    {
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_CLIENTE'])) {
            unset($dados['CADASTRAR_NOVO_CLIENTE']);
            
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                if (!empty($dados['CLI_REGISTRO'])) {
                    $this->Clientes->setcodRegistro($dados['CLI_REGISTRO'])->setCodEmpresa($dados['EMP_COD']);
                   
                    //Verificar se já existe cadastro da empresa pelo REGISTRO: CPF ou CNPJ informado
                    $cli = $this->Clientes->checarRegistroCliente();

                    if(!$cli){
                       
                        $dados += array(
                            'CLI_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                            'CLI_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                            'CLI_STATUS'=> 1
                        );

                        $id = $this->Clientes->cadastrar($dados,0);
                        if($id){ 
                            $endr = $this->Enderecos->setCodCliente($id)->checarEnderecoCliente();
                            if(!$endr){
                                $db_endereco = array(
                                    'CLI_COD' => $id,
                                    'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                                    'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                                    'END_STATUS' => 1
                                );
                                if ($this->Enderecos->cadastrar($db_endereco,0)) {
                                    $respota = array(
                                        'COD'=>'OK',
                                        'MENSAGEM'=> 'ERRO COD6- CADASTRO EFETUADO COM SUCESSO!',
                                        'DADOS' => $dados
                                    );
                                }else {
                                    $respota = array(
                                        'COD'=>'OK',
                                        'MENSAGEM'=> 'ERRO COD6- CADASTRO EFETUADO COM SUCESSO, ENDERECO NAO CADASTRADO!'
                                    );
                                }
                            }else {
                                $respota = array(
                                    'COD'=>'OK',
                                    'MENSAGEM'=> 'ERRO COD6- CADASTRO EFETUADO COM SUCESSO, ENDERECO NAO CADASTRADO!'
                                );
                            }
                        }else{
                            $respota = array(
                                'COD'=>'ERRO',
                                'MENSAGEM'=> 'ERRO COD 5- ERRO AO CADASTRAR NOVO CLIENTE, CONTATE O SUPORTE!'
                            );
                        }
                    }else{
                        $respota = array(
                            'COD'=>'ERRO',
                            'MENSAGEM'=> 'ERRO COD 4- CLIENTE JÁ ESTÁ CADASTRADO!'
                        );
                    }
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO COD 3- Informe o CPF/CNPJ válido(s)!'
                    );
                }                
            }else{
                $respota = array(
                    'COD'=>'ERRO',
                    'MENSAGEM'=> 'ERRO COD 2- Dados inválido(s)!'
                );
            }
        }else{
            $respota = array(
                'COD'=>'ERRO',
                'MENSAGEM'=> 'ERRO COD 1- Acesso inválido!'
            );
        }
        echo json_encode($respota);
    }
    public function cadastrar():void
    {
        $this->dados['title'] .= ' CADASTRAR SEUS CLIENTES';
        $this->link[3] = ['link'=> 'clientes/cadastrar','nome' => 'CADASTRAR SEUS CLIENTES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_CLIENTE'])) {
            unset($dados['CADASTRAR_NOVO_CLIENTE']);
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                $this->Clientes->setcodRegistro($dados['CLI_REGISTRO'])->setCodEmpresa($dados['EMP_COD']);
                //Verificar se já existe cadastro da empresa pelo REGISTRO: CPF ou CNPJ informado
                $cli = $this->Clientes->checarRegistroCliente();
                if(!$cli){

                    $dados += array(
                        'CLI_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'CLI_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                        'CLI_STATUS'=> 1
                    );

                    $db_endereco = array(
                        'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                        'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                        'END_LOGRADOURO' =>  $dados['END_LOGRADOURO'],
                        'END_NUMERO' =>  $dados['END_NUMERO'],
                        'END_BAIRRO' =>  $dados['END_BAIRRO'],
                        'END_CIDADE' =>  $dados['END_CIDADE'],
                        'END_ESTADO' =>  $dados['END_ESTADO'],
                        'END_CEP'    =>  $dados['END_CEP'],
                        'END_STATUS' => 1
                    );

                    //REMOVENDO DADOS DE ENDERECO DA ATUALIZACAO DA EMPRESA
                    unset($dados["END_LOGRADOURO"]);
                    unset($dados["END_NUMERO"]);
                    unset($dados["END_BAIRRO"]);
                    unset($dados["END_CEP"]);
                    unset($dados["END_CIDADE"]);
                    unset($dados["END_ESTADO"]);

                    $id = $this->Clientes->cadastrar($dados,0);
                    if($id){ 
                        $endr = $this->Enderecos->setCodCliente($id)->checarEnderecoCliente();
                        if(!$endr){
                            $db_endereco['CLI_COD'] = $id;
                            if ($this->Enderecos->cadastrar($db_endereco,0)) {
                                $ok = true;
                                Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                            }else {
                                Sessao::alert('OK','Cadastro efetuado com sucesso, não foi possível cadastrar o endereço!','fs-4 alert alert-warning');
                            }
                        }else {
                            $ok = true;
                            Sessao::alert('OK','Cadastro efetuado com sucesso, endereço já cadastrado!','fs-4 alert alert-success');
                        }
                    }else{
                        Sessao::alert('ERRO',' CLI4- Erro ao cadastrar novo cliente, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    $ok = true;
                    //VERIFICAR NOVO ENDERECO
                    $endr = $this->Enderecos->setCodEmpresa($dados['EMP_COD'])->setCodCliente($cli['CLI_COD'])->checarEnderecoCliente();
                    if(!$endr){
                        //CADASTRAR ENDERECO DO CLIENTE
                        $db_endereco['CLI_COD'] = $cli['CLI_COD'];
                        $db_endereco = array(
                            'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                            'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                            'END_LOGRADOURO' =>  $dados['END_LOGRADOURO'],
                            'END_NUMERO' =>  $dados['END_NUMERO'],
                            'END_BAIRRO' =>  $dados['END_BAIRRO'],
                            'END_CIDADE' =>  $dados['END_CIDADE'],
                            'END_ESTADO' =>  $dados['END_ESTADO'],
                            'END_CEP'    =>  $dados['END_CEP'],
                            'END_STATUS' => 1
                        );
                        $this->Enderecos->cadastrar($db_endereco,0);
                        Sessao::alert('OK','Endereço cadastrado!','fs-4 alert alert-warning');
                    }else {
                        //ATUALIZAR ENDERECO DO CLIENTE
                        $db_endereco = array(
                            'END_DT_ATUALIZACAO' =>  date('Y-m-d H:i:s'),
                            'END_LOGRADOURO' =>  $dados['END_LOGRADOURO'],
                            'END_NUMERO' =>  $dados['END_NUMERO'],
                            'END_BAIRRO' =>  $dados['END_BAIRRO'],
                            'END_CIDADE' =>  $dados['END_CIDADE'],
                            'END_ESTADO' =>  $dados['END_ESTADO'],
                            'END_CEP'    =>  $dados['END_CEP'],
                            'END_STATUS' => 1
                        );
                        $this->Enderecos->setCodEmpresa($dados['EMP_COD'])->setCodCliente($endr[0]['CLI_COD'])->setCodigo($endr[0]['END_COD'])->alterarCliente($db_endereco,0);
                    }
                    Sessao::alert('ERRO',' CLI3- Cadastro já realizado!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' CLI2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' CLI1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/clientes/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/clientes/cadastrar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR CLIENTES';
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            $this->link[3] = ['link'=> 'cadastros/clientes/alterar/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR CLIENTES'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['cliente'] = $this->Clientes->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['cliente'] != 0) {
                    $ok = true;
                }else {
                    $ok = true;
                    //CLIENTE SEM ENDERECO CADASTRADO
                    $this->dados['cliente'] = $this->Clientes->listarSemEndereco(0);
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CLI22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CLI11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
                $this->render('admin/cadastros/clientes/alterar', $this->dados);
        }else{
            $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/clientes/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR CLIENTES';
    
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       

        if (isset($_POST) && isset($dados['ALTERAR_CLIENTE'])) {

            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                $this->link[3] = ['link'=> 'clientes/alteracao/'.$_SESSION['EMP_COD'].'/','nome' => 'ALTERAR CLIENTES'];
                $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();

                unset($dados['ALTERAR_CLIENTE']);
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
               
                $db_endereco = array(
                    'END_LOGRADOURO' => $dados['END_LOGRADOURO'],
                    'END_NUMERO' => $dados['END_NUMERO'],
                    'END_BAIRRO' => $dados['END_BAIRRO'],
                    'END_CIDADE' => $dados['END_CIDADE'],
                    'END_CEP'    => $dados['END_CEP'],
                    'END_ESTADO' => $dados['END_ESTADO'],
                    'END_STATUS' => 1
                );
                $this->Clientes->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CLI_COD']);
                $this->Enderecos->setCodEmpresa($dados['EMP_COD'])->setCodCliente($dados['CLI_COD'])->setCodigo($dados['END_COD']);

                $codEnd = $dados['END_COD'];
                $codCli = $dados['CLI_COD'];

                //REMOVE DADOS DE ENDERECO DO ARRAY DO CLIENTE
                unset($dados['END_LOGRADOURO']);
                unset($dados['END_NUMERO']);
                unset($dados['END_BAIRRO']);
                unset($dados['END_CIDADE']);
                unset($dados['END_CEP']);
                unset($dados['END_ESTADO']);

                unset($dados['CLI_COD']);
                unset($dados['EMP_COD']);
                unset($dados['END_COD']);

                $dados += array(
                    'CLI_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                );
                if($this->Clientes->alterar($dados,0)){
                   
                    if($codEnd != 0){
                        $ok = true;
                        $db_endereco += array(
                            'END_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                            'END_STATUS' => 1
                        );

                        if($this->Enderecos->alterarCliente($db_endereco,0)){
                            Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                        }else {
                            Sessao::alert('OK','Cadastro alterado, endereço não alterado!','fs-4 alert alert-warning');
                        }
                    }else {
                        $ok = true;
                        $db_endereco += array(
                            'CLI_COD' => $codCli,
                            'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                            'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                            'END_STATUS' => 1
                        );
                        //CADASTRAR O ENDERECO
                        if($this->Enderecos->cadastrar($db_endereco,0)){
                            Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                        }else {
                            Sessao::alert('OK','Cadastro alterado, endereço não alterado!','fs-4 alert alert-warning');
                        }
                    }
                }else{
                    Sessao::alert('ERRO',' 3- Erro ao alterar o cliente da empresa, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CLI22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CLI21 - Acesso inválido(s)!','alert alert-danger');
        }

        if ($ok) {
            $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/clientes/listar', $this->dados);
        }else {
            $this->dados['cliente'] = $this->Clientes->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CLI_COD'])->listar(0);
            $this->render('admin/cadastros/clientes/alterar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_CLIENTE'])) {
            //Verifica se os campos foram todos preenchidos
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                unset($dados['STATUS_CLIENTE']);
                $this->Clientes->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CLI_COD']);

                ($dados['CLI_STATUS'] == 1)? $dados['CLI_STATUS'] = 0 : $dados['CLI_STATUS'] = 1;
                
                $db = array(
                    'USU_COD' => $_SESSION['USU_COD'],
                    'CLI_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'CLI_STATUS' => $dados['CLI_STATUS']
                );
                if($this->Clientes->alterar($db,0)){
                    //DESATIVAR ENDERECO DO CLIENTE
                    $db_endereco = array(
                        'END_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                        'END_STATUS' => $dados['CLI_STATUS']
                    );
                    $endr = $this->Enderecos->setCodCliente($dados['CLI_COD'])->checarEnderecoCliente();
                    
                    $this->Enderecos->setCodigo($endr[0]['END_COD'])->setCodCliente($dados['CLI_COD'])->alterarCliente($db_endereco,0);
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do cliente, entre em contato com o suporte!'
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