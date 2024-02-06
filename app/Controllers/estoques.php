<?php
namespace App\Controllers;

use App\Models\Categorias;
use App\Models\Classificacoes;
use App\Models\Clientes;
use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Estoques as ModelsEstoques;
use App\Models\Financas;
use App\Models\Fornecedores;
use App\Models\ModulosEmpresa;
use App\Models\Movimentacoes;
use App\Models\Produtos;
use App\Models\Setores;
use Libraries\Util;
use Core\View;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Core\Controller;
use Libraries\Check;
use Libraries\Sessao;

class estoques extends View
{
    private $dados = [];
    private $link,$Enderecos,$Clientes,
    $Usuarios,$Produtos,$Empresa,$UsuariosEmpresa,
    $Check,$CargosSalarios,$ModulosEmpresa,$Financas,
    $Estoques,$Setores,$Categorias,$Classificacoes,$Movimentacoes;
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
        $this->Estoques = new ModelsEstoques;
        $this->Financas = new Financas;
        $this->Setores = new Setores;
        $this->Categorias = new Categorias;
        $this->Produtos = new Produtos;
        $this->Classificacoes = new Classificacoes;
        $this->Movimentacoes = new Movimentacoes;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        
        $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0); 
        
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0); 
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0);

        $this->dados['movimentacoes'] = $this->Movimentacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0); 

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'estoques','nome' => 'MÓDULO DE ESTOQUES'];
        $this->link[2] = ['link'=> 'cadastros/estoques','nome' => 'GERENCIAR ESTOQUES'];
    }
    public function index()
    {
        $this->dados['title'] .= 'ACESSAR';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/estoques/estoques', $this->dados);
    }
    public function cadastro()
    {
        $this->dados['title'] .= ' CADASTRAR NOVO ESTOQUE';
        $this->link[3] = ['link'=> 'cadastros/estoques/cadastrar','nome' => 'CADASTRAR NOVO ESTOQUE'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/estoques/cadastrar', $this->dados);
    }
    public function movimentacao()
    {
        $this->dados['title'] .= ' MOVIMENTAÇÃO DO ESTOQUE';
        $this->link[2] = ['link'=> 'estoques/movimentacao','nome' => 'MOVIMENTAÇÃO DO ESTOQUE'];

        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/estoques/movimentacao', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR NOVO ESTOQUE';
        $this->link[3] = ['link'=> 'cadastros/estoques','nome' => 'CADASTRAR NOVO ESTOQUE'];

        $ok = false;      
        //Recupera os dados enviados
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                
        if (isset($_POST) && isset($dados['CRIAR_NOVO_ESTOQUE'])) {
            unset($dados['CRIAR_NOVO_ESTOQUE']);
            $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $dados += array(
                    'EST_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                    'EST_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),             
                    'EST_STATUS'=> 1
                );

                if($this->Estoques->cadastrar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' EST13- Erro ao cadastrar novo estoque, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' EST12 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' EST11- Dados inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/cadastros/estoques/listar', $this->dados);
        }else {

            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/cadastros/estoques/cadastrar', $this->dados);
        }
    }
    public function produtos()
    {
        $this->dados['title'] .= 'GERENCIAR ESTOQUE';
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(8)->listarTodosPorTipo(0);
        $dados = filter_input_array(INPUT_GET, FILTER_DEFAULT);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[0]) && $dados[0] == 'estoques' && isset($dados[1]) && $dados[1] == 'produtos') {
            
            if (isset($dados[2]) && $dados[2] != '' && isset($dados[3]) && $dados[3] != '') {

                if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] == $dados[2]){
                    
                    $this->dados['estoque'] = $this->Estoques->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);

                    if ($this->dados['estoque'] != 0) {
                        
                        //$this->link[2] = ['link'=> 'estoques/'.$dados[2],'nome' => 'GERENCIAR ESTOQUE'];
                        $this->link[2] = ['link'=> 'estoques/produtos/'.$dados[2].'/'.$dados[3],'nome' => 'GERENCIAR PRODUTOS'];
                        $this->dados['produtos'] = $this->Estoques->listarProdutosEstoque(0);

                        $ok = true;       
                    }
                    //$this->Produtos->setCodEmpresa($dados[2])->setCodEstoque($dados[3]);
                }else {
                    Sessao::alert('ERRO',' 3- Acesso inválido!','fs-4 alert alert-danger');
                }
            }else {
                Sessao::alert('ERRO',' 2- Dados inválido(s)!','fs-4 alert alert-danger');
            }
        }else {
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');
        }
        if ($ok) {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/produtos/listar', $this->dados);
        } else {
            $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->dados['produtos'] = 0;
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/estoques', $this->dados);
        }
    }
    public function servicos()
    {
        $Usuarios = new usuarios;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        $this->render('admin/estoques/servicos', $this->dados);
    }
    public function status():void
    {
       //Recupera os dados enviados
       $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       if (isset($_POST) && isset($dados['STATUS_ESTOQUE'])) {

          if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
              //Verifica se os campos foram todos preenchidos
              unset($dados['STATUS_ESTOQUE']);
              $this->Estoques->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['EST_COD']);
              ($dados['EST_STATUS'] == 1)? $dados['EST_STATUS'] = 0 : $dados['EST_STATUS'] = 1;
              
              $db = array(
                  'EST_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                  'EST_STATUS' => $dados['EST_STATUS']
              );

              if($this->Estoques->alterar($db,0)){
                  $respota = array(
                      'COD'=>'OK',
                      'MENSAGEM' => 'Status alterado com sucesso!'
                  );
              }else{
                  $respota = array(
                      'COD'=>'ERRO',
                      'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do estoque, entre em contato com o suporte!'
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
    public function listar_produtos()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['PRODUTOS_ESTOQUE'])) {
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                $this->dados['produtos'] = $this->Estoques->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['EST_COD'])->listarProdutosEstoque(0);
                //dump($this->dados['produtos'JSON_UNESCAPED_UNICODE]);
                echo json_encode($this->dados['produtos']);
            }
        }
    }
    public function cadastrar_movimentacao()
    {
        $this->dados['title'] .= ' MOVIMENTAÇÃO DO ESTOQUE';
        $this->link[3] = ['link'=> 'cadastros/estoques/gerenciar','nome' => 'MOVIMENTAÇÃO DO ESTOQUE'];

        $ok = false;      
        //Recupera os dados enviados
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                
        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_MOVIMENTACAO_ESTOQUE'])) {
            unset($dados['CADASTRAR_NOVA_MOVIMENTACAO_ESTOQUE']);

            $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
           
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $dados += array(
                    'MOV_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                    'MOV_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),             
                    'MOV_STATUS'=> 1
                );

                if($this->Movimentacoes->cadastrar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' EST13- Erro ao cadastrar nova movimentação, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            
            }else{
                Sessao::alert('ERRO',' EST12 - Acesso inválido(s)!','alert alert-danger');
            }

        }else{
            Sessao::alert('ERRO',' EST11- Dados inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['movimentacoes'] = $this->Movimentacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/movimentacao', $this->dados);
        }else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/estoques/movimentacao', $this->dados);
        }
    }

}