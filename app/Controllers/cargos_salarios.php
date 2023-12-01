<?php
namespace App\Controllers;

use App\Models\CargosSalarios;
use App\Models\Empresas;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class cargos_salarios extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$CargosSalarios;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | RECURSOS HUMANOS >>';
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->CargosSalarios= new CargosSalarios;
        $this->dados['cargos_salarios'] = $this->CargosSalarios->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->Check = new Check;
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'recursos_humanos','nome' => 'MÓDULO DE RECURSOS HUMANOS'];
        $this->link[2] = ['link'=> 'cargos_salarios','nome' => 'GERENCIAR CARGOS E SALÁRIOS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' CARGOS E SALÁRIOS';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/recursos_humanos/cargos_salarios/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR CARGOS E SALÁRIOS';
        $this->link[3] = ['link'=> 'cargos_salarios/cadastrar','nome' => 'CADASTRO DE CARGOS E SALÁRIOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/recursos_humanos/cargos_salarios/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR CARGOS E SALÁRIOS';
        $this->link[3] = ['link'=> 'cargos_salarios/cadastrar','nome' => 'CADASTRO DE CARGOS E SALÁRIOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_CARGO_SALARIO'])) {
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['CADASTRAR_NOVO_CARGO_SALARIO']);
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $dados['CGS_SALARIO'] = number_format(str_replace(",",".",str_replace(".","",$dados['CGS_SALARIO'])), 2, '.', '');
                $dados += array(
                    'CGS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                    'CGS_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),             
                    'CGS_STATUS'=> 1
                );

                if($this->CargosSalarios->cadastrar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' CGS3 - Erro ao cadastrar novo cargo e salário, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' CGS2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' CGS1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['cargos_salarios'] = $this->CargosSalarios->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/recursos_humanos/cargos_salarios/listar', $this->dados);
        }else {
            $this->render('admin/recursos_humanos/cargos_salarios/cadastrar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR CARGOS E SALÁRIOS';
       
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            $this->link[3] = ['link'=> 'cargos_salarios/alteracao','nome' => 'ALTERAR DE CARGOS E SALÁRIOS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['cargo_salario'] = $this->CargosSalarios->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['cargo_salario'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CGS22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CGS11 - Acesso inválido(s)!','alert alert-danger');
        }      
       if($ok){
            $this->render('admin/recursos_humanos/cargos_salarios/alterar', $this->dados);
       }else{
            $this->dados['cargos_salarios'] = $this->CargosSalarios->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/recursos_humanos/cargos_salarios/listar', $this->dados);
       }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' CADASTRAR CARGOS E SALÁRIOS';
        $this->link[3] = ['link'=> 'cargos_salarios/alterar','nome' => 'CADASTRO DE CARGOS E SALÁRIOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       
        if (isset($_POST) && isset($dados['ALTERAR_CARGO_SALARIO'])) {
           
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['ALTERAR_CARGO_SALARIO']);
                $this->CargosSalarios->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CGS_COD']);
                unset($dados['CGS_COD']);
                unset($dados['EMP_COD']);
                //$dados['CGS_SALARIO'] = str_replace(",","",$dados['CGS_SALARIO']);
                //$dados['CGS_SALARIO'] = str_replace(".","",$dados['CGS_SALARIO']);
                $dados['CGS_SALARIO'] = number_format(str_replace(",",".",str_replace(".","",$dados['CGS_SALARIO'])), 2, '.', '');
                $dados += array(
                    'CGS_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                );
                if($this->CargosSalarios->alterar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' ERRO: CGS33 - Erro ao alterar cargo e salário, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CGS22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CGS21 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['cargos_salarios'] = $this->CargosSalarios->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/recursos_humanos/cargos_salarios/listar', $this->dados);
        }else {
            $this->dados['cargo_salario'] = $this->CargosSalarios->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CGS_COD'])->listar(0);
            $this->render('admin/recursos_humanos/cargos_salarios/alterar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_CARGO'])) {
 
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se os campos foram todos preenchidos
                unset($dados['STATUS_CARGO']);
                $this->CargosSalarios->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CGS_COD']);
                ($dados['CGS_STATUS'] == 1)? $dados['CGS_STATUS'] = 0 : $dados['CGS_STATUS'] = 1;
                
                $db = array(
                    'CGS_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'CGS_STATUS' => $dados['CGS_STATUS']
                );

                if($this->CargosSalarios->alterar($db,0)){
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do cargo, entre em contato com o suporte!'
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

