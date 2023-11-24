<?php
namespace App\Controllers;

use App\Models\classificacoes as ModelsClassificacoes;
use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class classificacoes extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Classificacoes;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Classificacoes = new ModelsClassificacoes;
        $this->Empresa = new Empresas;
        $this->Enderecos = new Enderecos;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'classificacoes','nome' => 'GERENCIAR SUAS CLASSIFICAÇÕES'];
    }
    public function index():void
    {
        $this->dados['title'] .= ' CLASSIFICAÇÕES';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/classificacoes/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR CLASSIFICAÇÕES';
        $this->link[3] = ['link'=> 'classificacoes/cadastrar','nome' => 'CADASTRAR SEUS SETORES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/classificacoes/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR CLASSIFICAÇÕES';
        $this->link[3] = ['link'=> 'classificacoes/cadastrar','nome' => 'CADASTRAR CLASSIFICAÇÕES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_CLASSIFICACAO'])) {
            unset($dados['CADASTRAR_NOVA_CLASSIFICACAO']);
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                
                //Verificar se já existe cadastro
                $cla = $this->Classificacoes->setCodEmpresa($dados['EMP_COD'])->setDescricao($dados['CLA_DESCRICAO'])->checarDescricao();
                if(!$cla){

                    $dados += array(
                        'CLA_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'CLA_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                        'CLA_STATUS'=> 1
                    );
                    //unset($dados['SET_TIPO']);
                    if($this->Classificacoes->cadastrar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' CLA4- Erro ao cadastrar nova classificação, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' CLA3- Cadastro já realizado!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' CLA2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' CLA1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/classificacoes/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/classificacoes/cadastrar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR CLASSIFICAÇÕES';
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {

            $this->link[3] = ['link'=> 'classificacoes/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR CLASSIFICAÇÕES'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['classificacao'] = $this->Classificacoes->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['classificacao'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CLA22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CLA11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
                $this->render('admin/cadastros/classificacoes/alterar', $this->dados);
        }else{
            $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/classificacoes/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR CLASSIFICAÇÕES';
        
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       

        if (isset($_POST) && isset($dados['ALTERAR_CLASSIFICACAO'])) {

            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                $this->link[3] = ['link'=> 'classificacoes/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['CLA_COD'],'nome' => 'ALTERAR CLASSIFICAÇÕES'];


                unset($dados['ALTERAR_CLASSIFICACAO']);
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                
                $this->Classificacoes->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CLA_COD']);
                
                $dados += array(
                    'CLA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'CLA_STATUS'=> 1
                );
                if($this->Classificacoes->alterar($dados,0)){
                        Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' ERRO: CLA33- Erro ao alterar a classificação, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CLA22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CLA21 - Acesso inválido(s)!','alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/classificacoes/listar', $this->dados);
        }else {
            $this->dados['classificacao'] = $this->Classificacoes->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CLA_COD'])->listar(0);
            $this->render('admin/cadastros/classificacoes/alterar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_CLASSIFICACAO'])) {
            //Verifica se os campos foram todos preenchidos
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                unset($dados['STATUS_CLASSIFICACAO']);
                $this->Classificacoes->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CLA_COD']);
                ($dados['CLA_STATUS'] == 1)? $dados['CLA_STATUS'] = 0: $dados['CLA_STATUS'] = 1;
                                
                $db = array(
                    'USU_COD' => $_SESSION['USU_COD'],
                    'CLA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'CLA_STATUS' => $dados['CLI_STATUS']
                );
                if($this->Classificacoes->alterar($db,0)){
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status da classificação, entre em contato com o suporte!'
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