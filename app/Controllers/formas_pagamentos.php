<?php
namespace App\Controllers;



use App\Models\Empresas;
use App\Models\FormasPagamentos;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;

use Libraries\Check;
use Libraries\Sessao;

use Core\View;

class formas_pagamentos extends View
{
    private $dados = [];
    private $link,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$FormasPagamentos;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Check = new Check;
        $this->FormasPagamentos = new FormasPagamentos;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'formas_pagamentos','nome' => 'GERENCIAR FORMAS DE PAGAMENTOS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' FORMAS DE PAGAMENTOS';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/formas_pagamentos/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR SUAS FORMAS DE PAGAMENTOS';
        $this->link[3] = ['link'=> 'formas_pagamentos/cadastrar','nome' => 'CADASTRAR SUAS FORMAS DE PAGAMENTOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/formas_pagamentos/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR SUAS FORMAS DE PAGAMENTOS';   
        $this->link[3] = ['link'=> 'contas/cadastro','nome' => 'CADASTRAR FORMAS DE PAGAMENTOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_FORMA_PAGAMENTO'])) {
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['CADASTRAR_NOVA_FORMA_PAGAMENTO']);
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                //Verificar se já existe cadastro
                $fpg = $this->FormasPagamentos->setCodEmpresa($dados['EMP_COD'])->setDescricao($dados['FPG_DESCRICAO'])->checarDescricao();
                if(!$fpg){

                    $dados += array(
                        'FPG_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'FPG_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),             
                        'FPG_STATUS'=> 1
                    );

                    if($this->FormasPagamentos->cadastrar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' FPG4 - Erro ao cadastrar nova forma de pagamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' FPG3- Cadastro com essa descrição já realizada!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' FPG2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' FPG1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
            $this->render('admin/cadastros/formas_pagamentos/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/formas_pagamentos/cadastrar', $this->dados);
        }
    }
    public function alteracao()
    {
        $this->dados['title'] .= ' ALTERAR SUAS FORMAS DE PAGAMENTOS'; 
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['forma_pagamento'] = $this->FormasPagamentos->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['forma_pagamento'] != 0) {
                    $this->link[3] = ['link'=> 'formas_pagamentos/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR FORMA DE PAGAMENTO'];
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: FPG22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: FPG11 - Acesso inválido(s)!','alert alert-danger');
        }  
        if($ok){
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/cadastros/formas_pagamentos/alterar', $this->dados);
        }else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/cadastros/formas_pagamentos/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR FORMAS DE PAGAMENTOS';
        
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       

        if (isset($_POST) && isset($dados['ALTERAR_FORMA_PAGAMENTO'])) {

            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                $this->link[3] = ['link'=> 'formas_pagamentos/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['FPG_COD'],'nome' => 'ALTERAR FORMAS DE PAGAMENTOS'];

                unset($dados['ALTERAR_FORMA_PAGAMENTO']);
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                
                $this->FormasPagamentos->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['FPG_COD']);
                //Verificar se já existe cadastro
                $fpg = $this->FormasPagamentos->setCodEmpresa($dados['EMP_COD'])->setDescricao($dados['FPG_DESCRICAO'])->checarDescricao(0);

                if(isset($fpg['FPG_COD']) && $fpg['FPG_COD'] == $dados['FPG_COD']){
                    $alterar = true;
                }elseif(!$fpg) {
                    $alterar = true;
                }
                
                if($alterar){

                    $dados += array(
                        'FPG_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                        'FPG_STATUS'=> 1
                    );
                    if($this->FormasPagamentos->alterar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' ERRO: CAT33- Erro ao alterar o forma pagamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' FPGG3- Exite um cadastro com essa descrição, utilize outra!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CAT22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CAT21 - Acesso inválido(s)!','alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['formas_pagamentos'] = $this->FormasPagamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
            $this->render('admin/cadastros/formas_pagamentos/listar', $this->dados);
        }else {
            $this->dados['forma_pagamento'] = $this->FormasPagamentos->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CAT_COD'])->listar(0);
            $this->render('admin/cadastros/formas_pagamentos/alterar', $this->dados);
        }
    }
    public function status():void
    {
       //Recupera os dados enviados
       $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       if (isset($_POST) && isset($dados['STATUS_FORMA_PAGAMENTO'])) {

          if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
              //Verifica se os campos foram todos preenchidos
              unset($dados['STATUS_FORMA_PAGAMENTO']);
              $this->FormasPagamentos->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['FOR_COD']);
              ($dados['FPG_STATUS'] == 1)? $dados['FPG_STATUS'] = 0 : $dados['FPG_STATUS'] = 1;
              
              $db = array(
                  'FOR_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                  'FPG_STATUS' => $dados['FPG_STATUS']
              );

              if($this->FormasPagamentos->alterar($db,0)){
                  $respota = array(
                      'COD'=>'OK',
                      'MENSAGEM' => 'Status alterado com sucesso!'
                  );
              }else{
                  $respota = array(
                      'COD'=>'ERRO',
                      'MENSAGEM'=> 'ERRO 2- Erro ao mudar status da forma de pagamento, entre em contato com o suporte!'
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