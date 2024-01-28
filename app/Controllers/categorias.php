<?php
namespace App\Controllers;

use App\Models\Categorias as ModelsCategorias;
use App\Models\Classificacoes;
use App\Models\Empresas;
use App\Models\Setores;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class categorias extends View
{
    private $dados = [];
    public $link,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Categorias,$Setores,$Classificacoes;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Empresa = new Empresas;
        $this->Categorias = new ModelsCategorias;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;
        $this->Setores = new Setores;
        $this->Classificacoes = new Classificacoes;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodosPorTipo(1);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodosPorTipo(1);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'categorias','nome' => 'GERENCIAR SUAS CATEGORIAS'];
    }
    public function index():void
    {
        $this->dados['title'] .= ' CATEGORIAS';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/categorias/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR SUAS CATEGORIAS';
        $this->link[3] = ['link'=> 'categorias/cadastrar','nome' => 'CADASTRAR SUAS CATEGORIAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/categorias/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR SUAS CATEGORIAS';
        $this->link[3] = ['link'=> 'categorias/cadastrar','nome' => 'CADASTRAR SUAS CATEGORIAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_CATEGORIA'])) {
            unset($dados['CADASTRAR_NOVA_CATEGORIA']);
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                
                //Verificar se já existe cadastro
                $this->Categorias->setCodEmpresa($dados['EMP_COD'])->setDescricao($dados['CAT_DESCRICAO']);
                $cat = $this->Categorias->checarDescricao();
                
                if(!$cat){

                    $dados += array(
                        'CAT_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'CAT_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                        'CAT_STATUS'=> 1
                    );

                    if($this->Categorias->cadastrar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' CAT4- Erro ao cadastrar nova categoria, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' CAT3- Cadastro já realizado!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' CAT2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' CAT1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/categorias/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/categorias/cadastrar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR CATEGORIAS';
       
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {

            $this->link[3] = ['link'=> 'categorias/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR CATEGORIAS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['categoria'] = $this->Categorias->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['categoria'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CAT22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CAT11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
            $this->render('admin/cadastros/categorias/alterar', $this->dados);
        }else{
            $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/categorias/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR CATEGORIAS';
        
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       

        if (isset($_POST) && isset($dados['ALTERAR_CATEGORIA'])) {

            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                $this->link[3] = ['link'=> 'categorias/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['CAT_COD'],'nome' => 'ALTERAR CATEGORIAS'];

                unset($dados['ALTERAR_CATEGORIA']);
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                
                $this->Categorias->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CAT_COD']);

                $cod = $dados['CAT_COD'];
                
                unset($dados['CAT_COD']);
                
                $dados += array(
                    'CAT_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'CAT_STATUS'=> 1
                );
                if($this->Categorias->alterar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' ERRO: CAT33- Erro ao alterar o categoria, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CAT22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CAT21 - Acesso inválido(s)!','alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/categorias/listar', $this->dados);
        }else {
            $this->dados['categoria'] = $this->Categorias->setCodEmpresa($dados['EMP_COD'])->setCodigo($cod)->listar(0);
            $this->render('admin/cadastros/categorias/alterar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_CATEGORIA'])) {
            //Verifica se os campos foram todos preenchidos
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                unset($dados['STATUS_CATEGORIA']);
                $this->Categorias->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CAT_COD']);
                ($dados['CAT_STATUS'] == 1)? $dados['CAT_STATUS'] = 0: $dados['CAT_STATUS'] = 1;
                
                $db = array(
                    'USU_COD' => $_SESSION['USU_COD'],
                    'CAT_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'CAT_STATUS' => $dados['CAT_STATUS']
                );
                if($this->Categorias->alterar($db,0)){
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status da categoria, entre em contato com o suporte!'
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
