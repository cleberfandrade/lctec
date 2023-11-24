<?php
namespace App\Controllers;

use App\Models\Categorias;
use App\Models\Clientes;
use Core\View;
use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Estoques;
use App\Models\Financas;
use App\Models\Fornecedores;
use App\Models\ModulosEmpresa;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\FuncionariosVendedores;
use Libraries\Check;
use Libraries\Url;
use Libraries\Sessao;
use App\Models\Produtos as ModelsProdutos;

class produtos extends View
{
    private $dados = [];
    private $link,$Enderecos,$Clientes,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$ModulosEmpresa,$Financas,$Estoques,$Produtos,$Categorias,$Fornecedores;
    
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | ESTOQUES >>';
        $this->Clientes= new Clientes;
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Enderecos = new Enderecos;
        $this->Check = new Check;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Estoques = new Estoques;
        $this->Financas = new Financas;
        $this->Produtos = new ModelsProdutos;
        $this->Categorias = new Categorias;
        $this->Fornecedores = new Fornecedores;
        $this->dados['empresas'] = $this->UsuariosEmpresa->listarTodasEmpresasUsuario(0);
        $this->dados['usuarios_empresa'] = $this->UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD'])->checarUsuario();
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['produtos'] = $this->Produtos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['fornecedores'] = $this->Fornecedores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'estoques','nome' => 'MODÚLO DE ESTOQUE'];
       
    }
    public function index()
    {
        $this->dados['title'] .= 'GERENCIAR PRODUTOS';
        $ok = false;
        $dados = filter_input_array(INPUT_GET, FILTER_DEFAULT);
        $dados = explode("/",$dados['url']);
        
        if (isset($dados[2]) && $dados[2] != '' && isset($dados[3]) && $dados[3] != '') {
            
            $this->link[2] = ['link'=> 'estoques/gerenciar/'.$dados[2].'/'.$dados[3],'nome' => 'GERENCIAR ESTOQUE'];

            if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] == $dados[2]){
                
                //Verificando a existencia do estoque informado
                $this->dados['estoque'] = $this->Estoques->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                //Buscando produtos deste estoque
                if ($this->dados['estoque'] != 0) {
                    $this->dados['produtos'] = $this->Produtos->setCodEmpresa($this->dados['estoque']['EMP_COD'])->setCodEstoque($this->dados['estoque']['EST_COD'])->listarTodos(0);
                    if ($this->dados['produtos'] != 0) {
                        $this->link[3] = ['link'=> 'estoques/produtos/'.$dados[2].'/'.$dados[3],'nome' => 'GERENCIAR PRODUTOS'];
                        $ok = true;
                    }
                }
                
            }else {
                Sessao::alert('ERRO',' 2- Acesso inválido!','fs-4 alert alert-danger');
            }
        }else {
            Sessao::alert('ERRO',' 1- Acesso inválido(s)!','fs-4 alert alert-danger');
        }
        if ($ok) {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/produtos/listar', $this->dados);
        } else {
            $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/estoques', $this->dados);
        }
        
    }
    public function cadastro()
    {
        $this->dados['title'] .= 'CADASTRAR PRODUTOS';
        $dados = filter_input_array(INPUT_GET, FILTER_DEFAULT);
        $dados = explode("/",$dados['url']);
        
        if (isset($dados[2]) && $dados[2] != '' && isset($dados[3]) && $dados[3] != '') {

            if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] == $dados[2]){
        
                //Verificando a existencia do estoque informado
                $this->dados['estoque'] = $this->Estoques->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                $this->dados['fornecedores'] = $this->Fornecedores->setCodEmpresa($dados[2])->listarTodos(0);
                
                $this->link[2] = ['link'=> 'estoques/gerenciar/'.$dados[2].'/'.$dados[3],'nome' => 'GERENCIAR ESTOQUE'];
                $this->link[3] = ['link'=> 'estoques/produtos/'.$dados[2].'/'.$dados[3],'nome' => 'GERENCIAR PRODUTOS'];
                $this->link[4] = ['link'=> 'estoques/produtos/cadastro/'.$dados[2].'/'.$dados[3],'nome' => 'CADASTRAR PRODUTOS'];

                $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
                $this->render('admin/estoques/produtos/cadastrar', $this->dados);
            }else {
                Sessao::alert('ERRO',' 2- Dados inválido(s)!','fs-4 alert alert-danger');
                $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
                $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
                $this->render('admin/estoques/estoques', $this->dados);
            }
        }else {
            Sessao::alert('ERRO',' 1- Acesso inválido(s)!','fs-4 alert alert-danger');
            $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/estoques', $this->dados);
        }
    }
    public function cadastrar()
    {
        $this->dados['title'] .= 'CADASTRAR PRODUTOS';
        //Recupera os dados enviados
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $ok = false;
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_PRODUTO'])) {

            unset($dados['CADASTRAR_NOVO_PRODUTO']);
            if($_SESSION['USU_COD'] == $dados['USU_COD'] && isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] == $dados['EMP_COD']){

                //Checar duplicidade de cadastro
                $pro = $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->checarNomeProduto();
                if (!$pro) {
                    $this->dados['estoque'] = $this->Estoques->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['EST_COD'])->listar(0);
                    
                    $this->link[2] = ['link'=> 'estoques/gerenciar/'.$_SESSION['EMP_COD'].'/'.$dados['EST_COD'],'nome' => 'GERENCIAR ESTOQUE'];
                    $this->link[3] = ['link'=> 'estoques/produtos/'.$_SESSION['EMP_COD'].'/'.$dados['EST_COD'],'nome' => 'GERENCIAR PRODUTOS'];
                    $this->link[4] = ['link'=> 'estoques/produtos/cadastro/'.$_SESSION['EMP_COD'].'/'.$dados['EST_COD'],'nome' => 'CADASTRAR PRODUTOS'];
                    
                    $dados += array(
                        'PRO_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'PRO_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                        'PRO_STATUS'=> 1
                    );
                
                    if ($this->Produtos->cadastrar($dados,0)) {
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else {
                        Sessao::alert('ERRO',' 4- Erro ao cadastrar novo produto, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' 3- Nome já utilizado por outro produto no sistema, digite outro nome!','fs-4 alert alert-danger');
                }
            }else {
                Sessao::alert('ERRO',' 2- Dados inválido(s)!','fs-4 alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' 1- Acesso inválido(s)!','fs-4 alert alert-danger');
        }
        if ($ok) {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/produtos/listar', $this->dados);
        } else {
            $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/produtos/cadastrar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR PRODUTOS';
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3]) && isset($dados[4])) {
            
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['produto'] = $this->Produtos->setCodEmpresa($dados[2])->setCodEstoque($dados[3])->setCodigo($dados[4])->listar(0);
                if ($this->dados['produto'] != 0) {

                    $this->link[2] = ['link'=> 'estoques/gerenciar/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'GERENCIAR ESTOQUE'];
                    $this->link[3] = ['link'=> 'estoques/produtos/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'GERENCIAR PRODUTOS'];
                    $this->link[4] = ['link'=> 'estoques/produtos/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3].'/'.$dados[4],'nome' => 'ALTERAR PRODUTOS'];   
                    
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: PRO22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: PRO11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/produtos/alterar', $this->dados);
        }else{
            $this->dados['produtos'] = $this->Produtos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/estoques/produtos/listar', $this->dados);
        }
    }
    public function status():void
    {
       //Recupera os dados enviados
       $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       if (isset($_POST) && isset($dados['STATUS_PRODUTO'])) {

          if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
              //Verifica se os campos foram todos preenchidos
              unset($dados['STATUS_PRODUTO']);
              $this->Produtos->setCodEmpresa($dados['EMP_COD'])->setCodEstoque($dados['EST_COD'])->setCodigo($dados['PRO_COD']);
              ($dados['PRO_STATUS'] == 1)? $dados['PRO_STATUS'] = 0 : $dados['PRO_STATUS'] = 1;
              
              $db = array(
                  'PRO_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                  'PRO_STATUS' => $dados['PRO_STATUS']
              );

              if($this->Produtos->alterar($db,0)){
                  $respota = array(
                      'COD'=>'OK',
                      'MENSAGEM' => 'Status alterado com sucesso!'
                  );
              }else{
                  $respota = array(
                      'COD'=>'ERRO',
                      'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do produto, entre em contato com o suporte!'
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