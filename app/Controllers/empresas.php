<?php
namespace App\Controllers;

use App\Models\Clientes as ModelsCliente;
use App\Models\Empresas as ModelsEmpresa;
use App\Models\Enderecos;
use App\Models\Financas;
use App\Models\ModulosEmpresa;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class empresas extends View
{
    private $dados = [];
    private $link,$Enderecos,$Clientes,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$CargosSalarios,$ModulosEmpresa,$Financas;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Clientes= new ModelsCliente;
        $this->Usuarios = new Usuarios;
        $this->Empresa = new ModelsEmpresa;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Enderecos = new Enderecos;
        $this->Check = new Check;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Financas = new Financas;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['empresas'] = $this->UsuariosEmpresa->listarTodasEmpresasUsuario(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        
        if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] != 0) {
            $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
            $this->link[2] = ['link'=> 'empresas','nome' => 'GERENCIAR SUA EMPRESA/NEGÓCIO'];
        }
    }
    public function index()
    { 
        $this->dados['title'] .= ' EMPRESA/NEGÓCIO';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/empresas/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR NOVA EMPRESA/NEGÓCIO';
        if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] != 0) {
            $this->link[3] = ['link'=> 'empresas/cadastrar','nome' => 'CADASTRAR NOVA EMPRESA/NEGÓCIO'];
        }else{
            $this->link[1] = ['link'=> 'empresas/cadastrar','nome' => 'CADASTRAR NOVA EMPRESA/NEGÓCIO'];
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/empresas/cadastrar', $this->dados);
    }
    public function cadastrar():void
    {
        $this->dados['title'] .= ' CADASTRAR SEUS CLIENTES';
        if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] != 0) {
            $this->link[3] = ['link'=> 'empresas/cadastrar','nome' => 'CADASTRAR NOVA EMPRESA/NEGÓCIO'];
        }else{
            $this->link[1] = ['link'=> 'empresas/cadastrar','nome' => 'CADASTRAR NOVA EMPRESA/NEGÓCIO'];
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        if (isset($_POST) && isset($dados['CADASTRAR_EMPRESA'])) {
            //Verifica se os campos foram todos preenchidos
            unset($dados['CADASTRAR_EMPRESA']);
                
            $this->Empresa->setcodRegistro($dados['EMP_REGISTRO']);
            //Verificar se já existe cadastro da empresa pelo REGISTRO: CPF ou CNPJ informado
            $emp = $this->Empresa->checarRegistroEmpresa();
            if(!$emp){

                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }

                //Iniciar o cadastro da nova empresa
                $db_empresa = array(
                    'EMP_TIPO' => $dados['EMP_TIPO'],
                    'EMP_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                    'EMP_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),
                    'EMP_NOME_FANTASIA'   => $dados['EMP_NOME_FANTASIA'],
                    'EMP_RAZAO_SOCIAL'    => $dados['EMP_RAZAO_SOCIAL'],
                    'EMP_REGULAMENTACAO'  => $dados['EMP_REGULAMENTACAO'],
                    'EMP_PORTE' => $dados['EMP_PORTE'],
                    'EMP_REGISTRO' => $dados['EMP_REGISTRO'],
                    'EMP_INSCRICAO_ESTADUAL' => $dados['EMP_INSCRICAO_ESTADUAL'],
                    'EMP_DT_FUNDACAO'=> $dados['EMP_DT_FUNDACAO'],
                    'EMP_DESCRICAO'=> $dados['EMP_DESCRICAO'],
                    'EMP_TEL_1'=> $dados['EMP_TEL_1'],
                    'EMP_TEL_2'=> $dados['EMP_TEL_2'],
                    'EMP_CEL_1'=> $dados['EMP_CEL_1'],
                    'EMP_CEL_2'=> $dados['EMP_CEL_2'],
                    'EMP_EMAIL_1'=> $dados['EMP_EMAIL_1'],
                    'EMP_EMAIL_2'=> $dados['EMP_EMAIL_2'],
                    'EMP_DESCRICAO'=> $dados['EMP_DESCRICAO'],                        
                    'EMP_STATUS'=> 1
                );
                //CADASTRAR NOVA EMPRESA
                $id = $this->Empresa->cadastrar($db_empresa,0);
                if($id){
                    Sessao::criarSessao($db_empresa);                        
                    $ok = true;
                }else{
                    $ok = false;
                    Sessao::alert('ERRO',' 3- Erro ao cadastrar nova empresa, contate o suporte!','fs-4 alert alert-danger');
                }
            }else {
                $ok = true;
                $id = $emp['EMP_COD'];
            }
            //CHECAR CADASTRO DO ENDEREÇO DA EMPRESA
            $endr = $this->Enderecos->setCodEmpresa($id)->checarEnderecoEmpresa();
            if(!$endr){
                $db_endereco = array(
                    'USU_COD' => 0,
                    'EMP_COD' => $id,
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
                //CADASTRAR O ENDERECO DA EMPRESA
                if($this->Enderecos->cadastrar($db_endereco,0)){
                    $ok = true;
                    
                }else {
                    $ok = true;
                }
            }
           
            if ($ok) {
               
                $ok2 = true;
                $this->UsuariosEmpresa->setCodEmpresa($id)->setCodUsuario($dados['USU_COD']);
                $usu_emp = $this->UsuariosEmpresa->checarUsuarioEmpresa();
                //VERIFICAR SE O USUARIO ESTA VINCULADO A NOVA EMPRESA
                if(!$usu_emp){
                  
                    $db_usuario_empresa = array(
                        'EMP_COD' => $id,
                        'USU_COD' => $dados['USU_COD'],
                        'UMP_DT_CADASTRO' => date('Y-m-d H:i:s'),
                        'UMP_STATUS' => 1
                    );
                    //CADASTRAR O USUARIO NA EMPRESA
                    if($this->UsuariosEmpresa->cadastrar($db_usuario_empresa,0)){
                        $ok2 = true;
                       
                    }else {
                        $ok2 = false;
                        Sessao::alert('ERRO',' 2- Erro ao vincular nova empresa ao usuário, contate o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                   
                    $db_usuario_empresa = array(
                        'EMP_COD' => $id,
                        'USU_COD' => $dados['USU_COD'],
                        'UMP_DT_CADASTRO' => date('Y-m-d H:i:s'),
                        'UMP_STATUS' => 1
                    );
                    //$UsuariosEmpresa->setCodUsuario($dados['USU_COD']);
                    //$UsuariosEmpresa->setCodEmpresa($id);
                    //$UsuariosEmpresa->alterar($db_usuario_empresa,0);
                    $ok2 = true;
                }

                //VERIFICAR SE USUARIO FOI VINCULADO OU NAO HA NOVA EMPRESA
                if ($ok2) {
                    $db_modulos_empresa = [];
                    $m = 0;
                    //LIBERAR MODULOS PARA EMPRESA
                    for ($i = 1; $i <= 3; $i++) {           
                       
                        $this->ModulosEmpresa->setCodModulo($i);
                        $this->ModulosEmpresa->setCodEmpresa($id);
                        if(!$this->ModulosEmpresa->checarRegistroModuloEmpresa()){
                            $db_modulos_empresa = array(
                                'EMP_COD' => $id,
                                'MOD_COD' => $i,
                                'MLE_DT_CADASTRO' => date('Y-m-d H:i:s'),
                                'MLE_STATUS' => 1
                            );
                            if($this->ModulosEmpresa->cadastrar($db_modulos_empresa,0)){
                                $m++;
                                unset($db_modulos_empresa);
                            }
                        }else {
                            $m++;
                        }
                       
                    } 
                    //VERIFICAR SE FOI LIBERADO NO MINIMO 2 MÓDULOS A EMPRESA DO USUARIO
                    if ($m >= 2) {

                        $db_conta_empresa = array(
                            'EMP_COD'  => $id,
                            'CTA_TIPO' => 'PJ',
                            'CTA_DT_CADASTRO'    => date('Y-m-d H:i:s'),
                            'CTA_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                            'CTA_NOME'     => 'PRINCIPAL',
                            'CTA_DESCRICAO'=> 'CONTA PRINCIPAL DA EMPRESA',   
                            'CTA_SALDO'  => 0,
                            'CTA_STATUS' => 1
                        );
                        
                        $this->Financas->setCodEmpresa($id);
                        if(!$this->Financas->checarRegistroContaEmpresa()){
                            if ($this->Financas->cadastrar($db_conta_empresa,0)) {
                                Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');

                            }else {
                                Sessao::alert('OK','Cadastro efetuado com sucesso, crie uma conta para gerenciar sua movimentação','fs-4 alert alert-success');
                            }
                        }else {
                            Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                        }
                        $db_usuario = array(
                            'EMP_COD'  => $id,
                            'USU_DT_ATUALIZACAO' => date('Y-m-d H:i:s')
                        );
                        $this->Usuarios->setCodigo($dados['USU_COD'])->alterar($db_usuario,0);

                    }else {
                        Sessao::alert('ERRO',' 2- Módulos não foram liberados, contate o suporte!','alert alert-danger');
                    }
                }
            }
        }else{
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','alert alert-danger');
        }
        if($ok){
            $this->render('admin/cadastros/empresas/cadastrar', $this->dados);
        }else{
            $this->dados['empresas'] = $this->UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD'])->listarTodasEmpresasUsuario(0);
            $this->render('admin/cadastros/empresas/listar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR SUA EMPRESA/NEGÓCIO';
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            $this->link[3] = ['link'=> 'cadastros/empresas/alterar/'.$_SESSION['EMP_COD'].'/'.$_SESSION['USU_COD'],'nome' => 'ALTERAR SUA EMPRESA/NEGÓCIO'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($dados[2])->setCodUsuario($dados[3])->listar(0);
                $this->Enderecos->setCodEmpresa($dados[2]);
                $id = $this->Enderecos->checarEnderecoEmpresa(0);
                unset($id[0]['USU_COD']);
                $this->dados['empresa'] += $id[0];
                if ($this->dados['empresa'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: EMP22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: EMP11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
                $this->render('admin/cadastros/empresas/alterar', $this->dados);
        }else{
            $this->dados['empresas'] = $this->UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD'])->listarTodasEmpresasUsuario(0);
            $this->render('admin/cadastros/empresas/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' CADASTRAR EMPRESA/NEGÓCIO';
        $this->link[3] = ['link'=> 'clientes/alterar','nome' => 'CADASTRO DE EMPRESA/NEGÓCIO'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       

        if (isset($_POST) && isset($dados['ALTERAR_EMPRESA'])) {
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                unset($dados['ALTERAR_EMPRESA']);
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $this->UsuariosEmpresa->setCodUsuario($dados['USU_COD'])->setCodEmpresa($dados['EMP_COD']);
                
                $dados += array(
                    'EMP_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                );

                unset($dados['USU_COD']);
                
                $this->Empresa->setCodigo($dados['EMP_COD']);
                    $db_endereco = array(
                        'EMP_COD' => $dados['EMP_COD'],
                        'END_DT_ATUALIZACAO' => date('Y-m-d H:i:s'),
                        'END_LOGRADOURO' =>  $dados['END_LOGRADOURO'],
                        'END_NUMERO' =>  $dados['END_NUMERO'],
                        'END_BAIRRO' =>  $dados['END_BAIRRO'],
                        'END_CEP'    =>  $dados['END_CEP'],
                        'END_CIDADE' =>  $dados['END_CIDADE'],
                        'END_ESTADO' =>  $dados['END_ESTADO']
                    );
                    
                    //REMOVENDO DADOS DE ENDERECO DA ATUALIZACAO DA EMPRESA
                    unset($dados["END_LOGRADOURO"]);
                    unset($dados["END_NUMERO"]);
                    unset($dados["END_BAIRRO"]);
                    unset($dados["END_CEP"]);
                    unset($dados["END_CIDADE"]);
                    unset($dados["END_ESTADO"]);

                    if($this->Empresa->alterar($dados,0)){

                        $ok = true;
                        $this->Enderecos->setCodEmpresa($dados['EMP_COD']);
                        $id = $this->Enderecos->checarEnderecoEmpresa();
                        $this->Enderecos->setCodigo($id[0]['END_COD']);
                       
                        if($this->Enderecos->alterarEmpresa($db_endereco,0)){
                            Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                        }else {
                            Sessao::alert('ERRO','Cadastro alterado, houve um erro ao alterar o endereço!','fs-4 alert alert-warning');
                        }
                    }else{
                        Sessao::alert('ERRO',' 3- Erro ao alterar sua empresa, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }

            }else{
                Sessao::alert('ERRO',' ERRO: EMP22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: EMP21 - Acesso inválido(s)!','alert alert-danger');
        }

        if ($ok) {
            $this->dados['empresas'] = $this->UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD'])->listarTodasEmpresasUsuario(0);
            $this->render('admin/cadastros/empresas/listar', $this->dados);
        }else {
            $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
            $this->Enderecos->setCodEmpresa($_SESSION['EMP_COD']);
            $id = $this->Enderecos->checarEnderecoEmpresa(0);
            unset($id[0]['USU_COD']);
            $this->dados['empresa'] += $id[0];
            $this->render('admin/cadastros/empresas/alterar', $this->dados);
        }

    }
    public function exclusao():void
    {
        $this->dados['title'] .= ' EXCLUSÃO DE EMPRESA/NEGÓCIO';
        $this->link[3] = ['link'=> 'empresas/exclusao','nome' => 'EXCLUSÃO DE EMPRESA/NEGÓCIO'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/empresas/exclusao', $this->dados);
    }
    public function excluir():void
    {
        
    }
}