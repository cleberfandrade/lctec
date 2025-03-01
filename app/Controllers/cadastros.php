<?php
namespace App\Controllers;

use App\Models\CargosSalarios;
use App\Models\Classificacoes;
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
use App\Models\Colaboradores;
use Libraries\Check;
use Libraries\Url;
use Libraries\Sessao;
use App\Models\Setores;

class cadastros extends View
{
    private $dados = [];
    private $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$CargosSalarios,$Estoques, $Setores,$Limites,$Classificacoes,$ModulosEmpresa;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->CargosSalarios= new CargosSalarios;
        $this->Check = new Check;
        $this->Estoques = new Estoques;
        $Usuarios = new Usuarios;
        $Empresa = new Empresas;
        $UsuariosEmpresa = new UsuariosEmpresa;
        $ModulosEmpresa = new ModulosEmpresa;
        
        $this->CargosSalarios = new CargosSalarios;
        $this->Check = new Check;
        $this->Limites = new Check;
        $this->Setores = new Setores;
        $this->Classificacoes = new Classificacoes;
        $this->ModulosEmpresa = new ModulosEmpresa;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['empresas'] = $this->UsuariosEmpresa->listarTodasEmpresasUsuario(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuarios_empresa'] = $UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD'])->checarUsuario();
        $this->dados['modulos_empresa'] = $ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listar();
        $this->dados['modulo'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo(1)->listarModuloEmpresa(0);
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0); 
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);

        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
            $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
            $this->dados['empresa'] = $Empresa->setCodigo($_SESSION['EMP_COD'])->listar(0); 
        }else {
            $this->dados['modulos_empresa'] = false;
            $this->dados['empresa'] = false;
        }

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        if (isset($_SESSION['EMP_COD']) && $_SESSION['EMP_COD'] != 0) {
            $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        }
    }
    public function index()
    {
        $Usuarios = new Usuarios;
        $Check = new Check;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb($this->dados['modulo'][0]['MOD_COR_TEXTO']);
        $this->render('admin/cadastros/cadastros', $this->dados);
    }
    public function limite_acesso()
    {
        $this->dados['title'] .= ' LIMITE DE ACESSO';
        $this->link[2] = ['link'=> 'cadastros/limite_acesso','nome' => 'GERENCIAR LIMITE DE ACESSO'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/limite_acesso', $this->dados);
    }
    public function alterar_limite_acesso()
    {
        $this->dados['title'] .= ' LIMITE DE ACESSO';
        $this->link[2] = ['link'=> 'cadastros/limite_acesso','nome' => 'GERENCIAR LIMITE DE ACESSO'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        if (isset($_POST) && isset($dados['TEMPO_LIMITE'])) {
            
            unset($dados['TEMPO_LIMITE']);
            
            if($_SESSION['USU_COD'] == $dados['USU_COD'] && $_SESSION['EMP_COD'] == $dados['EMP_COD']){

                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }

                $dados += array(
                   'LMA_DT_ATUALIZACAO' => date('Y-m-d H:i:s'),
                   'LMA_STATUS' => 1
                );
                if($this->Estoques->alterar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' LMA3- Erro ao alterar o limite de acesso, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' LMA2- Acesso inválido!','fs-4 alert alert-danger');
            }
        }else {
            Sessao::alert('ERRO',' LMA1- Dados inválido(s)!','fs-4 alert alert-danger');
        }

        if($ok) {
            $this->dados['estoque'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
           
        }
        $this->render('admin/cadastros/limite_acesso', $this->dados);
    }
    //CADASTRO - ESTOQUES
    public function estoques()
    {
        $this->dados['title'] .= ' ESTOQUES';
        $this->link[2] = ['link'=> 'cadastros/estoques','nome' => 'GERENCIAR ESTOQUES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0); 
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0);
        
        $this->Estoques->setCodEmpresa($_SESSION['EMP_COD']);
        $this->dados['estoques'] = $this->Estoques->listarTodos(0);

        $this->render('admin/cadastros/estoques/listar', $this->dados);
    }
    public function cadastro_estoques()
    {
        $this->dados['title'] .= ' ESTOQUES';
        $this->link[2] = ['link'=> 'cadastros/estoques','nome' => 'GERENCIAR ESTOQUES'];
        $this->link[3] = ['link'=> 'cadastros/estoques/cadastrar','nome' => 'CRIAR NOVO ESTOQUE'];
        
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(4); 
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0);
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/estoques/cadastrar', $this->dados);
    }
    public function cadastrar_estoques()
    {
        $this->dados['title'] .= ' ESTOQUES';
        $this->link[2] = ['link'=> 'cadastros/estoques','nome' => 'GERENCIAR ESTOQUES'];
        $this->link[3] = ['link'=> 'cadastros/estoques','nome' => 'CRIAR NOVO ESTOQUE'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
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
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0); 
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0);

        if ($ok) {
            $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/estoques/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/estoques/cadastrar', $this->dados);
        }
    }
    public function alteracao_estoques()
    {
        $this->dados['title'] .= ' ESTOQUES';
        $this->link[2] = ['link'=> 'cadastros/estoques','nome' => 'GERENCIAR ESTOQUES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao_estoques' && isset($dados[2]) && isset($dados[3])) {
            
            $this->link[3] = ['link'=> 'cadastros/estoques/alteracao_estoques/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR ESTOQUES'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
                
                $this->dados['estoque'] = $this->Estoques->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['estoque'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: EST22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: EST21 - Acesso inválido(s)!','alert alert-danger');
        }      
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0); 
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0);
        if($ok){
                $this->render('admin/cadastros/estoques/alterar', $this->dados);
        }else{
            $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/estoques/listar', $this->dados);
        }
    }
    public function alterar_estoques()
    {
        $this->dados['title'] .= ' ESTOQUES';
        $this->link[2] = ['link'=> 'cadastros/estoques','nome' => 'GERENCIAR ESTOQUES'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $ok = false;
        
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        if (isset($_POST) && isset($dados['ALTERAR_ESTOQUE'])) {

            $this->link[3] = ['link'=> 'cadastros/estoques/alteracao_estoques/'.$_SESSION['EMP_COD'].'/'.$dados['EST_COD'],'nome' => 'ALTERAR ESTOQUES'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            
           
            
            if($_SESSION['USU_COD'] == $dados['USU_COD'] && $_SESSION['EMP_COD'] == $dados['EMP_COD']){

                //Verifica se tem algum valor proibido
                //foreach ($dados as $key => $value) {
                  //  $dados[$key] = $this->Check->checarString($value);
                //}
                unset($dados['ALTERAR_ESTOQUE']);
                $this->Estoques->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['EST_COD']);
                $codEstoque = $dados['EST_COD'];
                $codEmpresa = $dados['EMP_COD'];
                unset($dados['EST_COD']);
                unset($dados['EMP_COD']);

                //$dados += array(
                //    'EST_DT_ATUALIZACAO' => date('Y-m-d H:i:s'),
                 //   'EST_STATUS' => 1
                //);
                if($this->Estoques->alterar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' EST33- Erro ao alterar o estoque da empresa, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' EST32- Acesso inválido!','fs-4 alert alert-danger');
            }
        }else {
            Sessao::alert('ERRO',' EST31- Dados inválido(s)!','fs-4 alert alert-danger');
        }
        $this->dados['setores'] = $this->Setores->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0); 
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(4)->listarTodosPorTipo(0);
        if($ok) {
            $this->dados['estoque'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo($codEstoque)->listar(0);
            ///$this->render('admin/cadastros/estoques/listar', $this->dados);
            $this->render('admin/cadastros/estoques/alterar', $this->dados);
            //$this->render('admin/cadastros/estoques/alterar/'.$_SESSION['EMP_COD'].'/'.$codEstoque, $this->dados);
        }else{
            $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/estoques/listar', $this->dados);
        }

    }
}