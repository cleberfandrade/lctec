<?php
namespace App\Controllers;

use Core\View;
use Libraries\Check;
use Libraries\Url;
use Libraries\Sessao;
use App\Models\Usuarios;
use App\Models\Enderecos;
use App\Models\Recuperacoes;
use PHPMailer\PHPMailer;

class login extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Clientes, $Fornecedores, $Recuperacoes;
    public function __construct()
    {
        Sessao::logado();
        $this->dados['title'] = 'LC/TEC | Acesso Administrativo';

        $this->Check = new Check; 
        $this->Usuarios = new Usuarios;
        $this->Enderecos = new Enderecos;
        $this->Recuperacoes = new Recuperacoes;
    }
    public function index()
    { 
        $this->render('site/login', $this->dados);
    }
    public function auth()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['acesso'])) {
            
            if (!empty($dados['email_usuario']) && !empty($dados['senha_usuario'])) {
                //Validar Dados
                $dados['email_usuario'] = $this->Check->checarString($dados['email_usuario']);
                $dados['senha_usuario'] = $this->Check->checarString($dados['senha_usuario']);
                if($this->Check->checarEmail($dados['email_usuario'])){
                    $this->Usuarios->setEmailUsuario($dados['email_usuario']);
                    //$senha = $Check->codificarSenha($dados['senha_usuario']);
                    $user = $this->Usuarios->setSenhaUsuario($dados['senha_usuario'])->Acessar(0);
                    //checar se retornou algum usuario
                    if(!empty($user) && $user != 0){
                        //Checar se o status do usuario == 1: ativado/desativado
                        if($user['USU_STATUS'] == 1){
                           
                            //Criando sessao para acessar a area administrativa
                            if(Sessao::criarSessao($user)){
                                Sessao::alert('OK',' Bem vindo(a) '.$_SESSION['USU_NOME'].'','m-0 fs-4 alert alert-success');
                                //Sessao::alert('OK',' Acesso efetuado com sucesso!','m-0 fs-4 alert alert-success');
                                //Redirecionando o usuário para a página painel do sistema admin/painel
                                if($user['USU_NIVEL'] >= 5){
                                    header("Location:".DIRPAGE."admin/painel");
                                }else {
                                    header("Location:".DIRPAGE."site/acesso");
                                }
                                
                                //$Url->redirecionar('admin/painel');
                                //$this->render('admin/painel', $this->dados);
                            }else{
                                Sessao::alert('ERRO',' 6- O sistema encontrou um erro interno, contate o administrador','alert alert-danger');
                            }
                        }else {
                            Sessao::alert('ERRO',' 5- Usuário desativado, contate o administrador','alert alert-danger');
                        }
                    }else{
                        Sessao::alert('ERRO',' 4- Usuário ou senha inválido(s)!','alert alert-danger');
                    }
                }else{
                    Sessao::alert('ERRO',' 3- Usuário ou senha inválido(s)!','alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' 2- Usuário ou senha inválido(s)!','alert alert-danger');
            }           
        }else{
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','alert alert-danger');
        }
        $this->render('site/login', $this->dados);
    }
    public function sair()
    {
        $this->dados['title'] = 'LC/TEC | Acesso Administrativo';
        Sessao::logado();
        $this->Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $dados = array(
            'USU_DT_ATUALIZACAO' => date('Y-m-d H:i:s')
        );
        if($this->Usuarios->alterar($dados,0)){
            Sessao::alert('OK','Acesso encerrado com sucesso!','fs-4 alert alert-success');
        }else{
            Sessao::alert('OK','Acesso encerrado!','fs-4 alert alert-success');
        }
        unset($_SESSION['USU_COD']);
        session_destroy();
        Url::redirecionar('site/index');
    }
    public function lembrar()
    {
      
        //$info = New informacoesModel;
        //$informacoes = $info->listar();
        //foreach ($informacoes as $key => $value) {
          //  $this->dados[$key] = $value;
       // }
        $this->dados['title'] = 'LC/TEC | SOLICITAÇÃO DE NOVA SENHA';

        Sessao::logado();
        
        session_destroy();
        
        $this->render('site/lembrar', $this->dados);
    }
   public function novo_cadastro()
   {
        $this->dados['title'] = 'LC/TEC | CADASTRE=SE';
        //Recupera os dados enviados
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['cadastro'])) {
            //Verifica se os campos foram todos preenchidos
            unset($dados['cadastro']);
            if (!empty($dados['nome_usuario']) && !empty($dados['sobrenome_usuario']) && !empty($dados['logradouro_usuario']) && !empty($dados['numero_usuario']) && !empty($dados['bairro_usuario']) && !empty($dados['cidade_usuario']) && !empty($dados['sexo_usuario']) && !empty($dados['email_usuario']) && !empty($dados['senha_usuario']) && !empty($dados['conf_senha_usuario'])){
                 //Validar Dados
                 $dados_usuario['nome_usuario'] = $this->Check->checarString($dados['nome_usuario']);
                 $dados_usuario['sobrenome_usuario'] = $this->Check->checarString($dados['sobrenome_usuario']);
                 $dados_usuario['email_usuario'] = $this->Check->checarString($dados['email_usuario']);
                 $dados_usuario['sexo_usuario'] = $this->Check->checarString($dados['sexo_usuario']);
 
                 $dados_usuario['senha_usuario'] = $this->Check->checarString($dados['senha_usuario']);
                 $dados_usuario['conf_senha_usuario'] = $this->Check->checarString($dados['conf_senha_usuario']);
 
                 $dados_endereco['logradouro_usuario'] = $this->Check->checarString($dados['logradouro_usuario']);
                 $dados_endereco['numero_usuario'] = $this->Check->checarString($dados['numero_usuario']);
                 $dados_endereco['bairro_usuario'] = $this->Check->checarString($dados['bairro_usuario']);
                 $dados_endereco['cidade_usuario'] = $this->Check->checarString($dados['cidade_usuario']);
                 //Verificar se é um email no formato válido
                 if($this->Check->checarEmail($dados_usuario['email_usuario'])){
                    
                    //Checar se o email já está cadastrado no sistema 
                    $this->Usuarios->setEmailUsuario($dados_usuario['email_usuario']);
                    if(!$this->Usuarios->checarEmailUsuario()){
        
                        if($dados_usuario['senha_usuario'] == $dados_usuario['conf_senha_usuario']){
                            $db = array(
                                'USU_DT_CADASTRO'   => date('Y-m-d H:i:s'),
                                'USU_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),
                                'USU_NOME'      => $dados_usuario['nome_usuario'],
                                'USU_SOBRENOME' => $dados_usuario['sobrenome_usuario'],
                                'USU_SEXO'  => $dados_usuario['sexo_usuario'],
                                'USU_EMAIL' => $dados_usuario['email_usuario'],
                                'USU_NIVEL' => 10,
                                'USU_STATUS'=> 1
                            );
                            
                            $db['USU_SENHA'] = $this->Check->codificarSenha($dados_usuario['senha_usuario']);
    
                            $id = $this->Usuarios->cadastrar($db,0);
                            if($id){
                                $db_endereco = array(
                                    'USU_COD' => $id,
                                    'EMP_COD' => 0,
                                    'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                                    'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                                    'END_LOGRADOURO' =>  $dados_endereco['logradouro_usuario'],
                                    'END_NUMERO' =>  $dados_endereco['numero_usuario'],
                                    'END_BAIRRO' =>  $dados_endereco['bairro_usuario'],
                                    'END_CIDADE' =>  $dados_endereco['cidade_usuario'],
                                    'END_STATUS' => 1
                                );
                                $this->Enderecos->setCodUsuario($id);
                                if(!$this->Enderecos->checarEnderecoUsuario()){
                                    $this->Enderecos->cadastrar($db_endereco,0);
                                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                                }else {
                                    Sessao::alert('OK','Cadastro efetuado com sucesso, atualize seu endereço','fs-4 alert alert-success');
                                }
                            }else{
                                Sessao::alert('ERRO',' 6- Erro ao cadastrar novo usuário, contate o suporte!','fs-4 alert alert-danger');
                            }
                        }else {
                             Sessao::alert('ERRO',' 5- Senha não confere com a confirmação de senha, digite novamente!','fs-4 alert alert-danger');
                        }
                    }else {
                        Sessao::alert('ERRO',' 4- Email já cadastrado, tente recuperar a senha, ou entre em contato conosco!','alert alert-danger');
                    }
                }else{
                    Sessao::alert('ERRO',' 3- Email informado é inválido, informe um email válido!','alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' 2- Preencha todos os campos!','alert alert-danger');
            }  
        }else{
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','alert alert-danger');
        } 

        $this->render('site/cadastro', $this->dados);
   }
    public function recover()
    {
        $this->dados['title'] = 'LC/TEC / SOLICITAÇÃO DE NOVA SENHA';
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         
        $mail = new PHPMailer();
        //$Emails = new emailsModel();
        //$info = New informacoesModel;
        
        //$Recuperacoes = New recuperacoesModel;
        //$informacoes = $info->listar();      

        if (isset($_POST) && isset($dados['ENVIAR_EMAIL'])) {

            if($this->Check->checarEmail($dados['EML_EMAIL'])){
               
                $this->Usuarios->setEmailUsuario($dados['EML_EMAIL']);

                if(!$this->Usuarios->checarEmailUsuario()){
                   
                    $token = bin2hex(random_bytes(50));
    
                    $data = date('Y-m-d H:i:s', strtotime('+24 Hours'));
            
                    $dadosRecuperacao = [
                        'REC_EMAIL' => $dados['EML_EMAIL'],
                        'REC_TOKEN' => $token,
                        'REC_DT_CADASTRO' => $dados['lembrar'],
                        'REC_DT_EXPIRACAO' => $data
                    ];

                    $destinatario = $dados['EML_EMAIL'];
                    //$remetente = $informacoes['INF_EMAIL_2'];
                    
                    $mail->IsSMTP();
                    $mail->SMTPSecure = 'ssl';
                    $mail->Host = "smtp.hostinger.com"; 
                    $mail->Port = 465;
                    $mail->IsHTML(true); 
                    $mail->SMTPAuth = true; 
                    //$mail->Username = $informacoes['INF_EMAIL_2']; 
                    $mail->Password = 'Cf@10100801';

                    //$mail->setFrom($remetente, "Igreja Presbiteriana do Brasil Em Santo Anastácio/SP");
                    $mail->FromName = 'CONTATO DO SISTEMA LC/TEC'; 
                    $mail->Subject = "Solicitação de Nova Senha";
                    $link = DIRPAGE . 'login/token/' . $token;
                    $mensagem = "Olá, você solicitou o reset da sua senha de acesso em nosso sistema";
                    $mensagem .= "<p>Para criar uma nova senha, clique no link abaixo</p>";
                    $mensagem .= "<p><a href=" . $link . " target='_blanck' title='Clique aqui'>Clique aqui</a> para criar uma nova senha de acesso</p>";
                    //$mensagem .= "<p><hr><img style='width:90px;' src='" . DIRIMG . "logo.png'></p>";
                    //$mensagem .= "<p style='font-size:10px;'>Tel: (18) 99107-7297</p>";
                    $mail->Body = $mensagem;
                    //$mail->AltBody = 'Use um visualizador de e-mail com suporte a HTML';
                    //$mail->addAttachment('storage/public/images/logo.png');
                    //$mail->addAddress($informacoes['INF_EMAIL_1'],'Contato do Site');
                    $mail->addAddress($destinatario,'Recuperação de Senha de acesso');
                    
                    $ver = 0;
                    $ok = false;
                    $excluirTokenAnterior = false;
                    $checagem = $this->Recuperacoes->setEmail($destinatario)->Recuperacoes->checarSolicitacoesAnterioes();
                    if($checagem){
                        $oke = $this->Recuperacoes->setCodigo($checagem['REC_COD'])->Recuperacoes->excluir(0);
                        if($oke){
                            $excluirTokenAnterior = true;
                        }
                    }else{
                        $excluirTokenAnterior = true;
                    }
                    
                    if($excluirTokenAnterior){
                        $ok = $this->Recuperacoes->cadastrar($dadosRecuperacao,0);
                        if (!$ok) {
                            Sessao::alert('ERRO','ERRO 6: Encontramos um problema ao cadastrar sua solicitação, por favor tente mais tarde!','fs-4 alert alert-danger');
                        } else {
                           
                            if ($mail->Send()) {
                                Sessao::alert('OK', 'Email Enviado com sucesso, aguarde o recebimento do link','fs-4 alert alert-success');
                            } else {
                                Sessao::alert('ERRO', 'ERRO 5: Erro ao enviar email' . $mail->ErrorInfo,'fs-4 alert alert-danger');
                            }
                        }
                    }else{
                        Sessao::alert('ERRO','ERRO 4: Houve um problema ao excluir sua solicitação anterior, por favor, entre em contato com o administrador','fs-4 alert alert-danger');
                    }
                }else{
                    Sessao::alert('ERRO',' 3- Email de usuário não encontrado, entre em contato com o administrador!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' 2- Informe um email válido!','fs-4 alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');
        }
        $this->render('site/lembrar',$this->dados);
    }
    public function auth_admin()
    {
        $Check = new Check();
        $Usuarios = new Usuarios();
        $Url = new Url();
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['acesso'])) {
            
            if (!empty($dados['email_usuario']) && !empty($dados['senha_usuario'])) {
                //Validar Dados
                $dados['email_usuario'] = $this->Check->checarString($dados['email_usuario']);
                $dados['senha_usuario'] = $this->Check->checarString($dados['senha_usuario']);
                if($this->Check->checarEmail($dados['email_usuario'])){
                    //$senha = $Check->codificarSenha($dados['senha_usuario']);
                    $user =$this->Usuarios->setEmailUsuario($dados['email_usuario'])->setSenhaUsuario($dados['senha_usuario'])->Acessar(0);
                    //checar se retornou algum usuario
                    if(!empty($user) && $user != 0){
                        //Checar se o status do usuario == 1: ativado/desativado
                        if($user['USU_STATUS'] == 1){
                           
                            //Criando sessao para acessar a area administrativa
                            if(Sessao::criarSessao($user)){
                                Sessao::alert('OK',' Bem vindo(a) '.$_SESSION['USU_NOME'].'','m-0 fs-4 alert alert-success');
                                //Sessao::alert('OK',' Acesso efetuado com sucesso!','m-0 fs-4 alert alert-success');
                                //Redirecionando o usuário para a página painel do sistema admin/painel
                                if($user['USU_NIVEL'] >= 10){
                                    header("Location:".DIRPAGE."lctec");
                                }else{
                                    header("Location:".DIRPAGE."site/administrador");
                                }
                                
                                //$Url->redirecionar('admin/painel');
                                //$this->render('admin/painel', $this->dados);
                            }else{
                                Sessao::alert('ERRO',' 6- O sistema encontroiu um erro interno, contate o administrador','alert alert-danger');
                            }
                        }else {
                            Sessao::alert('ERRO',' 5- Usuário desativado, contate o administrador','alert alert-danger');
                        }
                    }else{
                        Sessao::alert('ERRO',' 4- Usuário ou senha inválido(s)!','alert alert-danger');
                    }
                }else{
                    Sessao::alert('ERRO',' 3- Usuário ou senha inválido(s)!','alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' 2- Usuário ou senha inválido(s)!','alert alert-danger');
            }           
        }else{
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','alert alert-danger');
        }
    }
    /*
    //LINK DO EMAIL PARA CHECAR SOLICITACAO DE MUDANCA DE SENHA
    public function token()
    {
        Sessao::logado();
        $pag = filter_input(INPUT_GET,'url', FILTER_DEFAULT);
        $pag = explode('/',  $pag);
        $token_url = isset($pag[2]) ? $pag[2] : 1;

        if ($token_url != '') {
            $Recuperacoes = new recuperacoesModel;
            $Recuperacoes->setToken($token_url);
            $this->dados['recuperacoes'] = array();
            $this->dados['recuperacoes'] = $Recuperacoes->checarToken();
           
            if (!empty($this->dados['recuperacoes'])) {
                if ($this->dados['recuperacoes']['REC_TOKEN'] == $token_url) {

                    $dataAtual = date('Y-m-d H:i:s');

                    if ($dataAtual <= $this->dados['recuperacoes']['REC_DT_EXPIRACAO']) {

                        $this->render('site/nova_senha', $this->dados);
                    } else {
                        Sessao::alert('ERRO','ERRO 3: Link expirado, faça uma nova solicitação','fs-4 alert alert-danger');
                        
                        $Recuperacoes->setToken($this->dados['recuperacoes']['REC_TOKEN']);
                        $token_usuario = checarToken(0);
                        $Recuperacoes->setCodigo($token_usuario['REC_COD']);
                        $Recuperacoes->excluir(0);
                        $this->render('site/lembrar', $this->dados);
                    }
                } else {
                    Sessao::alert('ERRO','ERRO 2: Token inválido, faça uma nova solicitação','fs-4 alert alert-danger');
                    $this->render('site/lembrar', $this->dados);
                }
            } else {
               
                Sessao::alert('ERRO','ERRO 1: Ocorreu um erro ao localizar o token cadastrado, faça uma nova solicitação','fs-4 alert alert-danger');
                
                $this->render('site/lembrar', $this->dados);
            }
        }else {
            $this->render('site/lembrar', $this->dados);
        }
    }
    public function nova_senha()
    {
        $info = New informacoesModel;
        $Recuperacoes = new recuperacoesModel;
        $informacoes = $info->listar();
        foreach ($informacoes as $key => $value) {
            $this->dados[$key] = $value;
        }
        $this->dados['title'] = 'Nova senha de acesso | IPB/Santo Anastácio-SP';
        Sessao::logado();
        $pag = filter_input(INPUT_GET,'url', FILTER_DEFAULT);
        $pag = explode('/',  $pag);
        $token_url = isset($pag[2]) ? $pag[2] : 1;
        if ($token_url != '') {
            $Recuperacoes->setToken($token_url);
            $token_usuario = checarToken(0);
            $Recuperacoes->setCodigo($token_usuario['REC_COD']);
            $Recuperacoes->excluir(0);
            
            $this->render('site/nova_senha', $this->dados);
        }else {
            $this->render('site/lembrar', $this->dados);
        }
    }
    */
}