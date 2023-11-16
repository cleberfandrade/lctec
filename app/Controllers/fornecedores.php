<?php
namespace App\Controllers;

use App\Models\Clientes as ModelsCliente;
use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Fornecedores as ModelsFornecedores;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class fornecedores extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Clientes, $Fornecedores;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Clientes= new ModelsCliente;
        $this->Empresa = new Empresas;
        $this->Enderecos = new Enderecos;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Fornecedores = new ModelsFornecedores;
        $this->Check = new Check;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['fornecedores'] = $this->Fornecedores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'fornecedores','nome' => 'GERENCIAR FORNECEDORES'];
    }
    public function index()
    {
        $this->dados['title'] .= ' FORNECEDORES';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/fornecedores/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR SEUS FORNECEDORES';
        $this->link[3] = ['link'=> 'fornecedores/cadastrar','nome' => 'CADASTRAR SEUS FORNECEDORES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/fornecedores/cadastrar', $this->dados);
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR FORNECEDORES';
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            $this->link[3] = ['link'=> 'fornecedores/alterar/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR FORNECEDORES'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['fornecedor'] = $this->Fornecedores->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                $this->Enderecos->setCodFornecedor($dados[3]);
                $id = $this->Enderecos->checarEnderecoFornecedor(0);
                unset($id[0]['FOR_COD']);
                $this->dados['fornecedor'] += $id[0];
                if ($this->dados['fornecedor'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: EMP22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: EMP11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
                $this->render('admin/cadastros/fornecedores/alterar', $this->dados);
        }else{
            $this->dados['fornecedores'] = $this->Fornecedores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/fornecedores/listar', $this->dados);
        }
    }
    public function alterar():void
    {
        $this->dados['title'] .= ' ALTERAR FORNECEDORES';
        $this->link[3] = ['link'=> 'fornecedores/alteracao','nome' => 'CADASTRAR SEUS FORNECEDORES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       
       
        $this->Fornecedores->setCodEmpresa($_SESSION['EMP_COD']);
        $this->dados['fornecedores'] = $this->Fornecedores->listarTodos(0);
        if (isset($_POST) && isset($dados['ALTERAR_CLIENTE'])) {
            
            if($_SESSION['EMP_COD'] == $dados[2] && isset($dados[3]) && $dados[3] !=''){
                $this->Fornecedores->setCodEmpresa($dados[2]);
                $this->Fornecedores->setCodigo($dados[3]);
                $this->Enderecos->setCodFornecedor($dados[3]);
                $this->dados['fornecedor'] = $this->Fornecedores->listar(0);
    
                if ($this->dados['fornecedor'] != 0) {
                    $this->link[3] = ['link'=> 'fornecedores/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['FOR_COD'],'nome' => 'ALTERAR FORNECEDORES'];
                    $this->dados['breadcrumb'] = $Check->setLink($this->link)->breadcrumb();
                    $this->render('admin/cadastros/fornecedores/alterar', $this->dados);
                }else {
                    $Check->setLink($this->link);
                    $this->dados['breadcrumb'] = $Check->breadcrumb();
                    Sessao::alert('ERRO',' 3- Fornecedor não foi encontrado!','fs-4 alert alert-danger');
                    $this->render('admin/cadastros/fornecedores/listar', $this->dados);
                }
                
            }else{
                $Check->setLink($this->link);
                $this->dados['breadcrumb'] = $Check->breadcrumb();
                Sessao::alert('ERRO',' 2- Acesso inválido(s)!','fs-4 alert alert-danger');
                $this->render('admin/cadastros/clientes/listar', $this->dados);
            }
        }else {
            $Check->setLink($this->link);
            $this->dados['breadcrumb'] = $Check->breadcrumb();
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');
            $this->render('admin/cadastros/clientes/listar', $this->dados);
        }

        $this->render('admin/cadastros/fornecedores/cadastrar', $this->dados);
    }
    public function status():void
    {
       //Recupera os dados enviados
       $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       if (isset($_POST) && isset($dados['STATUS_CARGO'])) {

          if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
              //Verifica se os campos foram todos preenchidos
              unset($dados['STATUS_FORNECEDOR']);
              $this->Fornecedores->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['FOR_COD']);
              ($dados['FOR_STATUS'] == 1)? $dados['FOR_STATUS'] = 0 : $dados['FOR_STATUS'] = 1;
              
              $db = array(
                  'FOR_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                  'FOR_STATUS' => $dados['FOR_STATUS']
              );

              if($this->Fornecedores->alterar($db,0)){
                  $respota = array(
                      'COD'=>'OK',
                      'MENSAGEM' => 'Status alterado com sucesso!'
                  );
              }else{
                  $respota = array(
                      'COD'=>'ERRO',
                      'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do fornecedor, entre em contato com o suporte!'
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
