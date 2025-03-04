<?php
namespace App\Controllers;

use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Setores;
use App\Models\Usuarios as ModelsUsuarios;
use App\Models\UsuariosEmpresa;
use App\Models\UsuariosSetores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class usuarios extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check, $Setores,$UsuariosSetores;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Empresa = new Empresas;
        $this->Enderecos = new Enderecos;
        $this->Usuarios = new ModelsUsuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Setores = new Setores;
        $this->UsuariosSetores = new UsuariosSetores;
        $this->Check = new Check;

        if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] != 0) {
            $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
            $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
            $this->dados['usuarios'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(7)->listarTodosPorTipo(0);
            $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
            $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
            $this->link[2] = ['link'=> 'usuarios','nome' => 'GERENCIAR USUÁRIOS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        }else {
            header("Location:".DIRPAGE."admin/painel");
        }
    }
    public function index():void
    {
        $this->dados['title'] .= ' USUÁRIOS';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/usuarios/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR SEUS USUÁRIOS';
        $this->link[3] = ['link'=> 'usuarios/cadastrar','nome' => 'CADASTRAR SEUS USUÁRIOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/usuarios/cadastrar', $this->dados);
    }
    public function cadastrar():void
    {
        $this->dados['title'] .= ' CADASTRAR SEUS USUÁRIOS';
        $this->link[3] = ['link'=> 'usuarios/cadastrar','nome' => 'CADASTRAR SEUS USUÁRIOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        //Recupera os dados enviados
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_USUARIO'])) {

            unset($dados['CADASTRAR_NOVO_USUARIO']);
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }

                if($this->Check->checarEmail($dados['USU_EMAIL'])){

                    //$Usuarios->setCodEmpresa($dados['EMP_COD']);
                    $user = $this->Usuarios->setEmailUsuario($dados['USU_EMAIL'])->checarEmailUsuario();
                    if(!$user){

                        $db = array(
                            'USU_DT_CADASTRO'   => date('Y-m-d H:i:s'),
                            'USU_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),
                            'USU_NOME'      => $dados['USU_NOME'],
                            'USU_SOBRENOME' => $dados['USU_SOBRENOME'],
                            'USU_SEXO'  => $dados['USU_SEXO'],
                            'USU_EMAIL' => $dados['USU_EMAIL'],
                            'USU_SENHA' => $this->Check->codificarSenha('123456'),
                            'USU_NIVEL' => $dados['USU_NIVEL'],
                            'USU_STATUS'=> 1
                        );


                        $id = $this->Usuarios->cadastrar($db,0);

                        if($id){
                            $ump =  $this->UsuariosEmpresa->setCodEmpresa($dados['EMP_COD'])->setCodUsuario($id)->checarUsuarioEmpresa(0);
                            if(!$ump){

                                $db_ump = array(
                                    'USU_COD' => $id,
                                    'EMP_COD' => $dados['EMP_COD'],
                                    'SET_COD' => $dados['SET_COD'],
                                    'UMP_DT_CADASTRO' => date('Y-m-d H:i:s'),
                                    'UMP_STATUS' => 1
                                );

                                $this->UsuariosEmpresa->cadastrar($db_ump,0);
                            }



                            //$this->UsuariosSetores->setCodEmpresa($dados['EMP_COD'])->setCodUsuario($id)->checarUsuarioSetor(0);
                            /*$db_ust = array(
                                'EMP_COD' => $dados['EMP_COD'],
                                'USU_COD' => $id,
                                'SET_COD' => $dados['SET_COD'],
                                'UST_DT_CADASTRO' => date('Y-m-d H:i:s'),
                                'UST_STATUS' => 1
                            );*/


                            $ok = true;
                            $endr = $this->Enderecos->setCodUsuario($id)->checarEnderecoUsuario();

                            if(!$endr){

                                $db_endereco = array(
                                    'USU_COD' => $id,
                                    'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                                    'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                                    'END_STATUS' => 1
                                );
                        
                                if ($this->Enderecos->cadastrar($db_endereco,0)) {
                                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                                }else {
                                    Sessao::alert('OK','Cadastro efetuado com sucesso, atualize o endereço após acesso','fs-4 alert alert-warning');
                                }
                            }else {
                                $this->Enderecos->setCodigo($endr[0]['END_COD']);
                                $db_endereco = array(
                                    'END_DT_ATUALIZACAO' => date('Y-m-d H:i:s'),
                                    'END_STATUS' => 1
                                );
                                if ($this->Enderecos->alterar($db_endereco,0)) {
                                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                                }else {
                                    Sessao::alert('OK','Cadastro efetuado com sucesso, atualize o endereço após acesso','fs-4 alert alert-warning');
                                }
                            }
                        }else{
                            Sessao::alert('ERRO',' USU5- Erro ao cadastrar novo usuário, contate o suporte!','fs-4 alert alert-danger');
                        }
                    }else {
                        Sessao::alert('ERRO',' USU4- Email do usuário ja utilizado, altere o cadastro, ou entre em contato com o suporte!','fs-4 alert alert-warning');
                    }
                }else{
                    Sessao::alert('ERRO',' USU3- Email informado é inválido, informe um email válido!','alert alert-danger');
                }
            }else {
                Sessao::alert('ERRO',' USU2- Dados inválido(s)!','fs-4 alert alert-danger');
            }
        }else {
            Sessao::alert('ERRO',' USU1- Acesso inválido!','fs-4 alert alert-danger');
        }
        
        if ($ok) {
            $this->dados['usuarios'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listarTodos(0);
            $this->render('admin/cadastros/usuarios/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/usuarios/cadastrar', $this->dados);
        }
    }
    public function alteracao()
    {
        $this->dados['title'] .= 'ALTERAR USUÁRIOS';
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            $this->link[3] = ['link'=> 'cadastros/usuarios/alterar/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR USUÁRIOS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['usuario'] = $this->UsuariosEmpresa->setCodEmpresa($dados[2])->setCodUsuario($dados[3])->listar(0);
                if ($this->dados['usuario'] != 0) {
                    $ok = true;
                }else{
                    Sessao::alert('ERRO',' ERRO: USU32 - Usuário não encontrado!, contate o suporte','alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: USU22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: USU11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
            $this->render('admin/cadastros/usuarios/alterar', $this->dados);
        }else{
            $this->dados['usuarios'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listarTodos(0);
            $this->render('admin/cadastros/usuarios/listar', $this->dados);
        }
    }
    public function alterar()
    {   
        $this->dados['title'] .= 'ALTERAR DADOS DE USUÁRIO';
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $codUsuario = $dados['USU_COD'];
        $codEmpresa = $dados['EMP_COD'];
       
        if (isset($_POST) && isset($dados['ALTERAR_USUARIO'])) {
            unset($dados['ALTERAR_USUARIO']);
          
            if($_SESSION['USU_COD'] <> $dados['USU_COD'] && $_SESSION['EMP_COD'] == $dados['EMP_COD']){

                $this->Usuarios->setCodigo($codUsuario);

                $dados += array(
                    'USU_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')             
                );
                if($dados['USU_RESET_SENHA']== "SIM") {
                    $dados['USU_SENHA'] = $this->Check->codificarSenha('123456');
                }  
                
                unset($dados['EMP_COD']);
                unset($dados['USU_COD']);
                unset($dados['USU_RESET_SENHA']);
                if(isset($dados['SET_COD']) && !empty($dados['SET_COD'])){

                    $ump = $this->UsuariosEmpresa->setCodEmpresa($codEmpresa)->setCodUsuario($codUsuario)->checarUsuarioEmpresa(0);
                 
                    if(!empty($ump)){
                       
                        if($dados['SET_COD'] <> $ump['SET_COD']){
                           
                            $db_ump = array(
                                'SET_COD' => $dados['SET_COD']
                            );
                            if($this->UsuariosEmpresa->setCodEmpresa($codEmpresa)->setCodUsuario($codUsuario)->alterar($db_ump,0)){
                                
                            }
                        }
                    }else {
                        Sessao::alert('ERRO',' 4- Erro ao alterar o usuário da empresa, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }
                unset($dados['SET_COD']);
                if($this->Usuarios->alterar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' 3- Erro ao alterar o usuário da empresa, entre em contato com o suporte!','fs-4 alert alert-danger');
                }

            }else{
                Sessao::alert('ERRO',' 2- Acesso inválido!','fs-4 alert alert-danger');
             }
        }else {
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');   
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();

        if($ok){

            $this->dados['usuarios'] = $this->UsuariosEmpresa->setCodEmpresa($codEmpresa)->setCodUsuario($_SESSION['USU_COD'])->listarTodos(0);
            $this->render('admin/cadastros/usuarios/listar', $this->dados);
        }else{
            $this->dados['usuario'] = $this->UsuariosEmpresa->setCodEmpresa($codEmpresa)->setCodigo($codUsuario)->listar(0);
            $this->render('admin/cadastros/usuarios/alterar', $this->dados);
        }
    }
    public function alterar_meus_dados()
    {   
        $this->dados['title'] .= 'ALTERAR DADOS DE USUÁRIO';
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $codUsuario = $dados['USU_COD'];
        $codEmpresa = $dados['EMP_COD'];
        
        if (isset($_POST) && isset($dados['ALTERAR_USUARIO'])) {
            unset($dados['ALTERAR_USUARIO']);
          
            if($_SESSION['USU_COD'] == $dados['USU_COD'] && $_SESSION['EMP_COD'] == $dados['EMP_COD']){

                $this->Usuarios->setCodigo($codUsuario);
                
                $dados += array(
                    'USU_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')             
                );

                if(isset($dados['USU_RESET_SENHA']) && !empty($dados['USU_RESET_SENHA']) && $dados['USU_RESET_SENHA'] == "SIM") {
                    $dados['USU_SENHA'] = $this->Check->codificarSenha('123456');
                }  
                if(!empty($dados['USU_SENHA']) && !empty($dados['USU_CONF_SENHA'])){
                    if($dados['USU_SENHA'] == $dados['USU_CONF_SENHA']){
                        $dados['USU_SENHA'] = $this->Check->codificarSenha($dados['USU_SENHA']);
                        unset($dados['USU_CONF_SENHA']);
                    }else{
                        unset($dados['USU_SENHA']);
                        unset($dados['USU_CONF_SENHA']);
                    }
                }else{
                    unset($dados['USU_SENHA']);
                    unset($dados['USU_CONF_SENHA']);
                }
                
                if(isset($dados['SET_COD']) && !empty($dados['SET_COD'])){
                   
                    $ump = $this->UsuariosEmpresa->setCodEmpresa($codEmpresa)->setCodUsuario($codUsuario)->checarUsuarioEmpresa(0);
                   
                    if(!empty($ump)){
                     
                        if($dados['SET_COD'] <> $ump['SET_COD']){
                          
                            $db_ump = array(
                                'SET_COD' => $dados['SET_COD']
                            );
                            
                            $this->UsuariosEmpresa->setCodEmpresa($codEmpresa)->setCodUsuario($codUsuario)->alterar($db_ump,0);
                        }
                    }else {
                        Sessao::alert('ERRO',' 4- Erro ao alterar o usuário da empresa, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }

                $dados_endereco = array(
                    'END_LOGRADOURO' => $dados['END_LOGRADOURO'],
                    'END_NUMERO' =>  $dados['END_NUMERO'],
                    'END_BAIRRO' =>  $dados['END_BAIRRO'],
                    'END_CIDADE' =>  $dados['END_CIDADE'],
                    'END_ESTADO' =>  $dados['END_ESTADO']
                );
                
                unset($dados['END_LOGRADOURO']);
                unset($dados['END_NUMERO']);
                unset($dados['END_BAIRRO']);
                unset($dados['END_CIDADE']);
                unset($dados['END_ESTADO']);

                unset($dados['EMP_COD']);
                unset($dados['USU_COD']);
                unset($dados['SET_COD']);
                unset($dados['USU_RESET_SENHA']);

                if($this->Usuarios->alterar($dados,0)){

                    $db_endereco = array(
                        'USU_COD' => $codUsuario,
                        'END_DT_ATUALIZACAO' => date('Y-m-d H:i:s'),
                        'END_STATUS' => 1
                    );

                    $db_endereco += $dados_endereco;
                    $endr = $this->Enderecos->setCodUsuario($codUsuario)->checarEnderecoUsuario();
                    if($endr){
                        $this->Enderecos->setCodigo($endr[0]['END_COD'])->setCodUsuario($codUsuario);
                        
                        if ($this->Enderecos->alterar($db_endereco,0)) {
                            $ok = true;
                            Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                        } else {
                            Sessao::alert('OK','Cadastro alterado com sucesso, erro ao alterar seu endereço, entre em contato com o suporte','fs-4 alert alert-warning'); 
                        }
                    }else{
                        //Endereço Não Encontrado
                        //Cadastrar endereço de usuário
                        $db_endereco2 = array(
                            'USU_COD' => $codUsuario,
                            'EMP_COD' => 0,
                            'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                            'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                            'END_LOGRADOURO' =>  $dados_endereco['END_LOGRADOURO'],
                            'END_NUMERO' =>  $dados_endereco['END_NUMERO'],
                            'END_BAIRRO' =>  $dados_endereco['END_BAIRRO'],
                            'END_CIDADE' =>  $dados_endereco['END_CIDADE'],
                            'END_ESTADO' =>  $dados_endereco['END_ESTADO'],
                            'END_STATUS' => 1
                        );
                        if ($this->Enderecos->cadastrar($db_endereco2,0)) {
                            $ok = true;
                            Sessao::alert('OK','Cadastro de endereço efetuado com sucesso, Usuário alterado!','fs-4 alert alert-success');
                        }else {
                            Sessao::alert('OK','Cadastro alterado com sucesso, erro ao cadastrar seu endereço, entre em contato com o suporte','fs-4 alert alert-warning');
                        }
                    }
                }else{
                    Sessao::alert('ERRO',' 3- Erro ao alterar o usuário da empresa, entre em contato com o suporte!','fs-4 alert alert-danger');
                }

            }else{
                Sessao::alert('ERRO',' 2- Acesso inválido!','fs-4 alert alert-danger');
             }
        }else {
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');   
        }

        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'usuarios','nome' => 'GERENCIAR USUÁRIOS'];

        if($ok){
            $this->link[3] = ['link'=> 'cadastros/meus_dados','nome' => 'ALTERAR MEUS DADOS DE USUÁRIO'];
            $this->dados['usuarios'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listarTodos(0);
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/cadastros/usuarios/meus_dados', $this->dados);
        }else{
            $this->dados['usuario'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($codUsuario)->listar(0);
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/cadastros/usuarios/listar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_USUARIO'])) {
            //Verifica se os campos foram todos preenchidos
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                unset($dados['STATUS_USUARIO']);
                $this->Usuarios->setCodigo($dados['USU_COD']);
               // dump($dados['USU_STATUS']);
                ($dados['USU_STATUS'] == 1)? $dados['USU_STATUS'] = 0 : $dados['USU_STATUS'] = 1;
                //dump($dados['USU_STATUS']);
                $db = array(
                    'USU_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'USU_STATUS' => $dados['USU_STATUS']
                );
                if($this->Usuarios->alterar($db,0)){
                    //DESATIVAR ENDERECO DO CLIENTE
                    $db_endereco = array(
                        'END_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                        'END_STATUS' => $dados['USU_STATUS']
                    );
                    $endr = $this->Enderecos->setCodUsuario($dados['USU_COD'])->checarEnderecoUsuario();
                    
                    $this->Enderecos->setCodigo($endr[0]['END_COD'])->setCodUsuario($dados['USU_COD'])->alterar($db_endereco,0);
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do usuário, entre em contato com o suporte!'
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
    public function meus_dados()
    {
        $this->dados['title'] .= 'MEUS DADOS DE USUÁRIO';
        $this->link[2] = ['link'=> 'usuarios','nome' => 'GERENCIAR USUÁRIOS'];
        //$this->link[2] = ['link'=> 'cadastros/usuarios','nome' => 'LISTAGEM DE USUÁRIOS'];
        $this->link[3] = ['link'=> 'cadastros/meus_dados','nome' => 'ALTERAR MEUS DADOS DE USUÁRIO'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        
        $this->dados['usuarios_empresa'] = $this->UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD'])->checarUsuario();

        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
            $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
            $this->dados['empresa'] = $this->Empresa->setCodigo($_SESSION['EMP_COD'])->listar(0);
            $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
            $this->render('admin/cadastros/usuarios/meus_dados', $this->dados);
        }else {
            Sessao::alert('ERRO',' 2- Acesso inválido!','fs-4 alert alert-danger');
            $this->render('admin/cadastros/usuarios', $this->dados);
        }
    }
    public function excluir()
    {
        $ok = false;      
        //Recupera os dados enviados
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['EXCLUIR_USUARIO'])) {
            unset($dados['EXCLUIR_USUARIO']);
    
            $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
            $this->dados['usuario'] = $this->Usuarios->setCodUsuario($dados['USU_COD'])->listar(0);
            if($this->dados['usuario'] != 0){
                /*
                $endr = $this->Enderecos->setCodUsuario($dados['USU_COD'])->checarEnderecoUsuario();
                if(isset($endr) && $endr != 0){
                    $this->Enderecos->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($endr[0]['END_COD'])->excluir(0);
                }
               
                if(!$this->Usuarios->excluir(0)){
                    $ok = true;
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Exclusão efetuada com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 5- Erro ao excluir seu usuário, entre em contato com o suporte!'
                    );
                }
                */
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
