<?php
namespace App\Controllers;

use App\Models\Classificacoes;
use App\Models\Colunas;
use App\Models\ColunasTarefas;
use App\Models\Tarefas as ModelsTarefas;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class tarefas extends View
{
    private $dados = [];
    private $link,$Check,$Usuarios,$UsuariosEmpresa, $Tarefas,$Classificacoes, $Colunas, $ColunasTarefas;

    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>'; 
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Tarefas = new ModelsTarefas;
        $this->Colunas = new Colunas;
        $this->Classificacoes = new Classificacoes;
        $this->ColunasTarefas = new ColunasTarefas;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);

        $this->dados['tarefas'] = $this->Tarefas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['colunas'] = $this->Colunas->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(1)->listarTodosPorTipo(0);
        //$this->dados['colunas_tarefas'] = $this->ColunasTarefas->setCodEmpresa($_SESSION['EMP_COD'])->listarColunasTarefas(0);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo('TAREFAS')->listarTodosPorTipo(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'tarefas','nome' => 'GERENCIAR TAREFAS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR TAREFAS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/tarefas/listar', $this->dados);
    }
    public function colunas()
    {
        $this->dados['title'] .= ' GERENCIAR COLUNAS TAREFAS';   
        $this->link[3] = ['link'=> 'cadastros/tarefas/colunas','nome' => 'CADASTRAR NOVA COLUNA'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/tarefas/colunas', $this->dados);
    }
    public function cadastro()
    {
        $this->dados['title'] .= ' CADASTRAR NOVA TAREFAS';
        $this->link[3] = ['link'=> 'cadastros/tarefas/cadastrar','nome' => 'CADASTRAR NOVA TAREFA'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/tarefas/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR SUAS TAREFAS';
        $this->link[3] = ['link'=> 'tarefas/cadastrar','nome' => 'CADASTRAR SUAS TAREFAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_TAREFA'])) {
            unset($dados['CADASTRAR_NOVA_TAREFA']);
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                //Verificar se já existe cadastro
                $this->Tarefas->setCodEmpresa($dados['EMP_COD'])->setDescricao($dados['TRF_DESCRICAO']);
                $cat = $this->Tarefas->checarDescricao();
                
                if(!$cat){

                    $dados += array(
                        'TRF_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'TRF_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                        'TRF_STATUS'=> 1
                    );

                    if($this->Tarefas->cadastrar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' TRF4- Erro ao cadastrar nova tarefa, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' TRF3- Cadastro já realizado!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' TRF2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' TRF1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['tarefas'] = $this->Tarefas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/tarefas/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/tarefas/cadastrar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR TAREFAS';
       
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {

            $this->link[3] = ['link'=> 'tarefas/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR TAREFAS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['TAREFA'] = $this->Tarefas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['TAREFA'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CAT22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CAT11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
            $this->render('admin/cadastros/tarefas/alterar', $this->dados);
        }else{
            $this->dados['tarefas'] = $this->Tarefas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/tarefas/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR TAREFAS';
        
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       

        if (isset($_POST) && isset($dados['ALTERAR_TAREFA'])) {

            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                $this->link[3] = ['link'=> 'tarefas/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['CAT_COD'],'nome' => 'ALTERAR TAREFAS'];

                unset($dados['ALTERAR_TAREFA']);
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                
                $this->Tarefas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CAT_COD']);
                
                $dados += array(
                    'TRF_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'TRF_STATUS'=> 1
                );
                if($this->Tarefas->alterar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' ERRO: TRF33- Erro ao alterar a tarefa, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: TRF22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: TRF21 - Acesso inválido(s)!','alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['tarefas'] = $this->Tarefas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/tarefas/listar', $this->dados);
        }else {
            $this->dados['tarefas'] = $this->Tarefas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['TRF_COD'])->listar(0);
            $this->render('admin/cadastros/tarefas/alterar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_TAREFA'])) {
            //Verifica se os campos foram todos preenchidos
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                unset($dados['STATUS_TAREFA']);
                $this->Tarefas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CAT_COD']);
                ($dados['TRF_STATUS'] == 1)? $dados['TRF_STATUS'] = 0: $dados['TRF_STATUS'] = 1;
                
                $db = array(
                    'USU_COD' => $_SESSION['USU_COD'],
                    'TRF_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'TRF_STATUS' => $dados['TRF_STATUS']
                );
                if($this->Tarefas->alterar($db,0)){
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status da TAREFA, entre em contato com o suporte!'
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
    public function cadastrar_colunas()
    {
        $this->dados['title'] .= ' CADASTRAR COLUNAS TAREFAS';
        $this->link[3] = ['link'=> 'cadastros/tarefas/colunas','nome' => 'CADASTRAR NOVA COLUNA'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_COLUNA_TAREFA'])) {
            unset($dados['CADASTRAR_NOVA_COLUNA_TAREFA']);
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
              
                //Verificar se já existe cadastro
                $this->Colunas->setCodEmpresa($dados['EMP_COD'])->setDescricao($dados['CLN_DESCRICAO'])->setTipo(1);
                $cln = $this->Colunas->checarDescricao();
                
                if(!$cln){

                    $dados += array(
                        'CLN_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'CLN_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                        'CLN_STATUS'=> 1
                    );

                    if($this->Colunas->cadastrar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' TRF4- Erro ao cadastrar nova coluna, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' TRF3- Cadastro já realizado!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' TRF2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' TRF1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['colunas'] = $this->Colunas->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(1)->listarTodosPorTipo(0);
            $this->render('admin/cadastros/tarefas/colunas', $this->dados);
        }else {
            $this->render('admin/cadastros/tarefas/colunas', $this->dados);
        }
    }
}