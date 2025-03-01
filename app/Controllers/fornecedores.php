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
    public function cadastrar():void
    {
        $this->dados['title'] .= ' CADASTRAR SEUS FORNECEDORES';
        $this->link[3] = ['link'=> 'fornecedores/cadastrar','nome' => 'CADASTRAR SEUS FORNECEDORES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_FORNECEDOR'])) {
            unset($dados["CADASTRAR_NOVO_FORNECEDOR"]);
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $this->Fornecedores->setCodEmpresa($dados['EMP_COD'])->setcodRegistro($dados['FOR_REGISTRO']);
                //Verificar se já existe cadastro da empresa pelo REGISTRO: CPF ou CNPJ informado
                $forn = $this->Fornecedores->checarRegistro();
                if(!$forn){
                    $dados += array(
                        'FOR_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'FOR_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                        'FOR_STATUS'=> 1
                    );
                    $db_endereco = array(
                        'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                        'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                        'END_LOGRADOURO' => $dados['END_LOGRADOURO'],
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
                    $id = $this->Fornecedores->cadastrar($dados,0);
                    if($id){ 
                        $ok = true;
                        $endr = $this->Enderecos->setCodEmpresa($dados['EMP_COD'])->setCodFornecedor($id)->checarEnderecoFornecedor();
                        if(!$endr){
                            $db_endereco['FOR_COD'] = $id;
                            if ($this->Enderecos->cadastrar($dados,0)) {
                                Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                            }else {
                                Sessao::alert('OK','Cadastro efetuado com sucesso, não foi possível cadastrar o endereço!','fs-4 alert alert-warning');
                            }
                        }else {
                            Sessao::alert('OK','Cadastro efetuado com sucesso, endereço já cadastrado!','fs-4 alert alert-success');
                        }
                    }else{
                        Sessao::alert('ERRO',' FOR4- Erro ao cadastrar novo fornecedor, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else{
                    Sessao::alert('ERRO',' FOR3- Fornecedor já cadastrado, altere o cadastro, ou entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' FOR2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' FOR1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['fornecedores'] = $this->Fornecedores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/fornecedores/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/fornecedores/cadastrar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR FORNECEDORES';
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            $this->link[3] = ['link'=> 'fornecedores/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR FORNECEDORES'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
               
                $this->dados['fornecedor'] = $this->Fornecedores->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                dump($this->dados['fornecedor']);
                $id = $this->Enderecos->setCodFornecedor($dados[3])->checarEnderecoFornecedor(0);
                
                exit;
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
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        $this->dados['fornecedores'] = $this->Fornecedores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        if (isset($_POST) && isset($dados['ALTERAR_FORNECEDOR'])) {
            
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                unset($dados['ALTERAR_FORNECEDOR']);
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $this->dados['fornecedor'] = $this->Fornecedores->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['FOR_COD'])->listar(0);
    
                if ($this->dados['fornecedor'] != 0) {
                    
                    $this->link[3] = ['link'=> 'fornecedores/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['FOR_COD'],'nome' => 'ALTERAR FORNECEDORES'];
                   
                    $codEnd = $dados['END_COD'];
                    $codFor = $dados['FOR_COD'];
                    $this->Fornecedores->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['FOR_COD']);

                    $this->Enderecos->setCodFornecedor($dados['FOR_COD'])->setCodigo($dados['END_COD']);

                    $db_endereco = array(
                        'END_LOGRADOURO' => $dados['END_LOGRADOURO'],
                        'END_NUMERO' => $dados['END_NUMERO'],
                        'END_BAIRRO' => $dados['END_BAIRRO'],
                        'END_CIDADE' => $dados['END_CIDADE'],
                        'END_CEP'    => $dados['END_CEP'],
                        'END_ESTADO' => $dados['END_ESTADO'],
                        'END_STATUS' => 1
                    );
                    //REMOVENDO DADOS DO ARRAY FORNECEDOR
                    unset($dados['END_LOGRADOURO']);
                    unset($dados['END_NUMERO']);
                    unset($dados['END_BAIRRO']);
                    unset($dados['END_CIDADE']);
                    unset($dados['END_CEP']);
                    unset($dados['END_ESTADO']);

                    unset($dados['FOR_COD']);
                    unset($dados['EMP_COD']);
                    unset($dados['END_COD']);
                    
                    $dados += array(
                        'FOR_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                    );

                    if($this->Fornecedores->alterar($dados,0)){
                      
                        if($codEnd != 0){
                        
                            $db_endereco += array(
                                'END_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                            );

                            if($this->Enderecos->alterarFornecedor($db_endereco,0)){
                                $ok = true;
                                Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                            }else {
                                Sessao::alert('OK','Cadastro alterado, endereço não alterado!','fs-4 alert alert-warning');
                            }
                        }else {

                            $db_endereco += array(
                                'FOR_COD' => $codFor,
                                'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                                'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                                'END_STATUS' => 1
                            );
                            //CADASTRAR O ENDERECO
                            if($this->Enderecos->cadastrar($db_endereco,0)){
                                Sessao::alert('OK','Cadastro alterado com sucesso, endereço cadastrado!','fs-4 alert alert-success');
                            }else {
                                Sessao::alert('OK','Cadastro alterado, endereço não alterado!','fs-4 alert alert-warning');
                            }
                        }
                    }else{
                        Sessao::alert('ERRO',' FOR24- Erro ao alterar o cliente da empresa, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' FOR23- Fornecedor não foi encontrado!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' FOR22- Acesso inválido(s)!','fs-4 alert alert-danger');
            }
        }else {
            Sessao::alert('ERRO',' FOR21- Dados inválido(s)!','fs-4 alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['fornecedores'] = $this->Fornecedores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/fornecedores/listar', $this->dados);
        }else {
            $this->dados['fornecedor'] = $this->Fornecedores->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['FOR_COD'])->listar(0);
            $this->render('admin/cadastros/fornecedores/alterar', $this->dados);
        }
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
