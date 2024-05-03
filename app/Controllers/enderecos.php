<?php
namespace App\Controllers;

use App\Models\Classificacoes;
use App\Models\Clientes;
use App\Models\Empresas;
use App\Models\Enderecos as ModelsEnderecos;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Setores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class enderecos extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Clientes,$Setores,$Classificacoes;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Clientes= new Clientes;
        $this->Empresa = new Empresas;
        $this->Enderecos = new ModelsEnderecos;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;
        $this->Setores = new Setores;
        $this->Classificacoes = new Classificacoes;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['enderecos'] = $this->Enderecos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'clientes','nome' => 'GERENCIAR ENDEREÇOS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' ENDEREÇOS';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/enderecos/listar', $this->dados);
    }
    public function alteracao()
    {
        $this->dados['title'] .= 'ALTERAR ENDEREÇOS';
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {
            $this->link[3] = ['link'=> 'cadastros/enderecos/alterar/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR ENDEREÇOS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['endereco'] = $this->Enderecos->setCodigo($dados[3])->listar(0);
                if ($this->dados['endereco'] != 0) {
                    $ok = true;
                }else{
                    Sessao::alert('ERRO',' ERRO: USU32 - Endereço não encontrado!, contate o suporte','alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: USU22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: USU11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
            $this->render('admin/cadastros/enderecos/alterar', $this->dados);
        }else{
            $this->dados['enderecos'] = $this->Enderecos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/enderecos/listar', $this->dados);
        }
    }
}