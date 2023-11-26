<?php
namespace App\Controllers;

use Libraries\Sessao;
use Libraries\Url;
use App\Models\Usuarios;
use Core\View;

class site extends View
{
    private $dados = [];
    public function __construct()
    {
        $this->dados['mensagem'] = "LC-TEC";
    }
    public function index()
    { 
        $this->dados['title'] = 'LC-TEC | Acesso Administrativo';
        $this->render('site/login', $this->dados);
    }
    public function quem_somos()
    {
        $this->dados['title'] = 'LC-TEC | Quem Somos';
        //$Documentos = new documentosModel();  
        //$Documentos->setDiretorio('docs/ipb');
        //$this->dados['diretorio'] = 'ipb/';
        //$this->dados['documentos'] = $Documentos->lerDiretorio();
        $this->render('site/quem_somos', $this->dados);
    }
    public function termos()
    {
        //$this->dados['title'] = 'Termos e Privacidade | IPB de Santo Anastácio-SP';
        //$Termos = new termosModel;
        //$this->dados['termos'] = $Termos->listar(0);
        $this->render('site/termos', $this->dados);
    }
    public function cadastro()
    {
        $this->dados['title'] = 'LC-TECH | Cadastre-se';
        $this->render('site/cadastro', $this->dados);
    }
    public function sair()
    {
        unset($_SESSION['USU_COD']);
        session_destroy($_SESSION);
        Url::redirecionar('site/index');
    }
    public function finalizado()
    {

        //session_start();
        if (isset($_SESSION)) {
            session_start();
        }
        ob_start();
        
        unset($_SESSION['USU_COD']);
        //session_destroy($_SESSION);
        //dump('ok');
        //exit;
        $this->dados['title'] = 'LC-TEC | Acesse-se';
        $this->render('site/finalizado', $this->dados);
        //Url::redirecionar('site/bloqueado');
    }
    public function acesso()
    {
        $this->dados['title'] = 'ACESSO | LC-TEC';
        $this->render('site/acesso', $this->dados);
    }
    public function descadastramento()
    {
        /**$this->dados['title'] = 'Termos e Privacidade | IPB de Santo Anastácio-SP';
        //$Assinantes = new assinantesModel;
        $Check = new Check();
        $get = filter_input_array(INPUT_GET, FILTER_DEFAULT);
        if($Check::checarEmail($get['ASS_EMAIL'])){
            foreach ($get as $key => $value) {
                $dados[$key] = $value;
            }
            $dados += array(
                'ASS_STATUS' => 'DESATIVADO'
            );
            $Assinantes->setCodigo($dados['ASS_COD']);
            if($Assinantes->alterar($dados,0)){
                $this->render('site/descadastramento', $this->dados);
            }else{
                $this->render('site/home', $this->dados);
            }
        }else{
            $this->render('site/home', $this->dados);
        }
        **/
    }
    public function error()
    { 
        echo "error";
    }
    public function administrador()
    {
        Sessao::logado();
        $this->dados['title'] = 'ACESSO ADMINISTRATIVO | LC-TEC';
        $this->render('site/login_admin', $this->dados);
    }
}