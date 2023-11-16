<?php
namespace App\Controllers;

use App\Models\CargosSalarios;
use App\Models\Colaboradores as ModelsColaboradores;
use App\Models\Empresas;
use App\Models\Setores;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class colaboradores extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Colaboradores,$CargosSalarios,$Setores;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->CargosSalarios= new CargosSalarios;
        $this->Setores= new Setores;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->Colaboradores= new ModelsColaboradores;
        $this->dados['colaboradores'] = $this->Colaboradores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['cargos_salarios'] = $this->CargosSalarios->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->Check = new Check;
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'colaboradores','nome' => 'GERENCIAR COLABORADORES'];
    }
    public function index():void
    {
        $this->dados['title'] .= ' COLABORADORES';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/colaboradores/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR COLABORADORES';
        $this->link[3] = ['link'=> 'colaboradores/cadastrar','nome' => 'CADASTRO DE COLABORADORES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/colaboradores/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR COLABORADORES';
        $this->link[3] = ['link'=> 'colaboradores/cadastrar','nome' => 'CADASTRO DE COLABORADORES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_COLABORADOR'])) {
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['CADASTRAR_NOVO_COLABORADOR']);
                    $dados += array(
                        'COL_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'COL_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),             
                        'COL_STATUS'=> 1
                    );

                    if($this->Colaboradores->cadastrar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' CGS3 - Erro ao cadastrar novo colaborador(a), entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
            }else{
                Sessao::alert('ERRO',' CGS2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' CGS1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['colaboradores'] = $this->Colaboradores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/colaboradores/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/colaboradores/cadastrar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR COLABORADORES';
       
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);

        $dados = explode("/",$dados['url']);
        $ok = false;
        
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            
            
            $this->link[3] = ['link'=> 'colaboradores/alteracao'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR DE COLABORADORES'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){

                $this->dados['colaborador'] = $this->Colaboradores->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['colaborador'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: COL22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: COL11 - Acesso inválido(s)!','alert alert-danger');
        }      
       if($ok){
            $this->render('admin/cadastros/colaboradores/alterar', $this->dados);
       }else{
            $this->dados['colaboradores'] = $this->Colaboradores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/colaboradores/listar', $this->dados);
       }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR COLABORADORES';
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       
        if (isset($_POST) && isset($dados['ALTERAR_COLABORADOR'])) {
           
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['ALTERAR_COLABORADOR']);

                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $this->dados['colaborador'] = $this->Colaboradores->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['COL_COD']);

                if ($this->dados['colaborador'] != 0) {

                    $this->link[3] = ['link'=> 'fornecedores/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['FOR_COD'],'nome' => 'ALTERAR FORNECEDORES'];
        
                    unset($dados['COL_COD']);
                    unset($dados['EMP_COD']);
                    $dados += array(
                        'COL_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                    );
                    if($dados['COL_RESET_SENHA']== "SIM") {
                        $dados['COL_SENHA'] = $this->Check->codificarSenha('123456');
                    }

                    unset($dados['COL_RESET_SENHA']);
                    
                    if($this->Colaboradores->alterar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' ERRO: COL24 - Erro ao alterar colaborador(a), entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' COL23- Colaborador não foi encontrado!, verifque os dados informados, ou entre em contato com o Suporte','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: COL22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: COL21 - Acesso inválido(s)!','alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['colaboradores'] = $this->Colaboradores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/colaboradores/listar', $this->dados);
        }else {
            $this->dados['colaborador'] = $this->Colaboradores->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['COL_COD'])->listar(0);
            $this->render('admin/cadastros/colaboradores/alterar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_COLABORADOR'])) {
 
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['STATUS_COLABORADOR']);
                $this->Colaboradores->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['COL_COD']);
                ($dados['COL_STATUS'] == 1)? $dados['COL_STATUS'] = 0 : $dados['COL_STATUS'] = 1;
                
                $db = array(
                    'COL_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'COL_STATUS' => $dados['COL_STATUS']
                );

                if($this->Colaboradores->alterar($db,0)){
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do cargo, entre em contato com o suporte!'
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

