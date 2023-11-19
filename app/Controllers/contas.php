<?php 
namespace App\Controllers;

use App\Models\Empresas;
use App\Models\Contas as ModelsContas;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Vendedores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class contas extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Contas = new ModelsContas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['contas'] = $this->Financas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas();

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO DE FINANÇAS >>'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR CONTAS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/financeiro', $this->dados);
    }
    public function contas()
    {
        $this->dados['title'] .= ' GERENCIAR CONTAS';   
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/contas/listar', $this->dados);
    }
    public function detalhar_contas()
    {
        $this->dados['title'] .= 'DETALHAR CONTA'; 
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false; 
        
        if (isset($dados[2]) && isset($dados[3])) {
            
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['conta'] = $this->Financas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['conta'] != 0) {
                    //$this->dados['CTA_COD'] = $dados[3];
                    $this->link[3] = ['link'=> 'financeiro/detalhar_contas/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'DETALHAR CONTA >> '.$this->dados['conta']['CTA_DESCRICAO']];
                    $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
                    $ok = true;
                }
               
            }else{
                Sessao::alert('ERRO',' ERRO:CTA22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CTA11 - Acesso inválido(s)!','alert alert-danger');
        }      

        if ($ok) {
            $this->render('admin/financeiro/contas/detalhar', $this->dados);
        } else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/financeiro/contas/listar', $this->dados);
        }        
    }
    public function alteracao()
    {
        $this->dados['title'] .= ' ALTERAR CONTA'; 
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['conta'] = $this->Financas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['conta'] != 0) {
                    $this->link[3] = ['link'=> 'financeiro/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR CONTA'];
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CTA22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CTA11 - Acesso inválido(s)!','alert alert-danger');
        }  
        if($ok){
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/financeiro/contas/alterar', $this->dados);
        }else {
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            $this->render('admin/financeiro/contas/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR CONTA'; 
        $this->link[2] = ['link'=> 'financeiro/contas','nome' => 'GERENCIAR CONTAS'];
        $this->render('admin/financeiro/contas/alterar', $this->dados);
    }
    public function cadastro()
    {
        $this->dados['title'] .= ' GERENCIAR CONTA DA EMPRESA/NEGÓCIO';   
        $this->render('admin/financeiro/contas/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' GERENCIAR CONTA DA EMPRESA/NEGÓCIO';   
        $this->render('admin/financeiro/contas/cadastrar', $this->dados);
    }
}