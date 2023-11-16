<?php
namespace App\Controllers;

use App\Models\Empresas;
use App\Models\Enderecos;
use App\Models\Usuarios as ModelsUsuarios;
use App\Models\UsuariosEmpresa;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;

class usuarios extends View
{
    private $dados = [];
    public $link,$Enderecos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | CADASTROS >>';
        $this->Empresa = new Empresas;
        $this->Enderecos = new Enderecos;
        $this->Usuarios = new ModelsUsuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuarios'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS'];
        $this->link[2] = ['link'=> 'usuarios','nome' => 'GERENCIAR USUÁRIOS'];
    }
    public function index():void
    {
        $this->dados['title'] .= ' USUÁRIOS';
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/usuarios/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR SEUS USUÁRIOS';
        $this->link[3] = ['link'=> 'usuarios/cadastrar','nome' => 'CADASTRAR SEUS USUÁRIOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/usuarios/cadastrar', $this->dados);
    }
    public function cadastrar():void
    {
        $this->dados['title'] .= ' CADASTRAR SEUS USUÁRIOS';
        $this->link[3] = ['link'=> 'usuarios/cadastrar','nome' => 'CADASTRAR SEUS USUÁRIOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        //Recupera os dados enviados
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_USUARIO'])) {

            unset($dados['CADASTRAR_NOVO_USUARIO']);
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                if($this->Check->checarEmail($dados['USU_EMAIL'])){
                    //$Usuarios->setCodEmpresa($dados['EMP_COD']);
                    $$this->Usuarios->setEmailUsuario($dados['USU_EMAIL']);
                    if(!$this->Usuarios->checarEmailUsuario()){

                        $db = array(
                            'EMP_COD' => 0,
                            'USU_DT_CADASTRO'   => date('Y-m-d H:i:s'),
                            'USU_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),
                            'USU_NOME'      => $dados['USU_NOME'],
                            'USU_SOBRENOME' => $dados['USU_SOBRENOME'],
                            'USU_SEXO'  => $dados['USU_SEXO'],
                            'USU_EMAIL' => $dados['USU_EMAIL'],
                            'USU_SENHA' => $this->Check->codificarSenha('123456'),
                            'USU_NIVEL' => $dados['USU_NIVEL'],
                            'USU_STATUS'=> 1
                        );
                        $id = $this->Usuarios->cadastrar($db,0);
                        if($id){
                            $ok = true;
                            $endr = $this->Enderecos->setCodUsuario($id)->checarEnderecoUsuario();
                            

                            if(!$endr){

                                $db_endereco = array(
                                    'USU_COD' => $id,
                                    'END_DT_CADASTRO' => date('Y-m-d H:i:s'),
                                    'END_DT_ATUALIZACAO' => date('0000-00-00 00:00:00'),
                                    'END_STATUS' => 1
                                );
                        
                                if ($this->Enderecos->cadastrar($db_endereco,0)) {
                                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                                }else {
                                    Sessao::alert('OK','Cadastro efetuado com sucesso, atualize o endereço após acesso','fs-4 alert alert-warning');
                                }
                            }else {
                                $this->Enderecos->setCodigo($endr[0]['END_COD']);
                                $db_endereco = array(
                                    'END_DT_ATUALIZACAO' => date('Y-m-d H:i:s'),
                                    'END_STATUS' => 1
                                );
                                if ($this->Enderecos->alterar($db_endereco,0)) {
                                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                                }else {
                                    Sessao::alert('OK','Cadastro efetuado com sucesso, atualize o endereço após acesso','fs-4 alert alert-warning');
                                }
                            }
                        }else{
                            Sessao::alert('ERRO',' USU5- Erro ao cadastrar novo usuário, contate o suporte!','fs-4 alert alert-danger');
                        }
                    }else {
                        Sessao::alert('ERRO',' USU4- Email do usuário ja utilizado, altere o cadastro, ou entre em contato com o suporte!','fs-4 alert alert-warning');
                    }
                }else{
                    Sessao::alert('ERRO',' USU3- Email informado é inválido, informe um email válido!','alert alert-danger');
                }
            }else {
                Sessao::alert('ERRO',' USU2- Dados inválido(s)!','fs-4 alert alert-danger');
            }
        }else {
            Sessao::alert('ERRO',' USU1- Acesso inválido!','fs-4 alert alert-danger');
        }
        if ($ok) {
            $this->dados['usuarios'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/cadastros/usuarios/listar', $this->dados);
        }else {
            $this->render('admin/cadastros/usuarios/cadastrar', $this->dados);
        }
    }
}
