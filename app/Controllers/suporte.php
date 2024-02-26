<?php
namespace App\Controllers;

use App\Models\Chat;
use App\Models\Classificacoes;
use App\Models\Empresas;
use App\Models\Suporte as ModelsSuporte;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class suporte extends View
{
    private $dados = [];
    private $link,$Check,$Empresa, $Usuarios,$UsuariosEmpresa,$Suporte,$Chat;

    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'HELP!! SUPORTE AO SISTEMA >>';
        $this->Empresa = new Empresas;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;

        $this->Suporte = new ModelsSuporte;

        $this->dados['suporte_usuario'] = $this->Suporte->setCodUsuario($_SESSION['USU_COD'])->listarTodasMensagensEnviadasRecebidas(0);
    
        $this->Chat = new Chat;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'SUPORTE AO SISTEMA'];
    }
    public function index()
    {
        $this->dados['title'] .= ' HELP!! SUPORTE AO SISTEMA';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/suporte', $this->dados);
    }
    public function cadastrar()
    { 
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_MENSAGEM'])) {
            unset($dados['CADASTRAR_NOVA_MENSAGEM']);
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $dados += array(
                    'SUP_DT_CADASTRO'=> date('Y-m-d H:i:s'),         
                    'SUP_STATUS'=> 1
                );
                $id = $this->Suporte->cadastrar($dados,0);
                if($id){
                        $ok = true;
                        $respota = array(
                            'COD'=>'OK',
                            'MENSAGEM' => 'mensagem enviada! em breve entraremos em contato'
                        );
                }else{
                    
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 3- ERRO AO CRIAR MENSAGEM, FALE COM O SUPORTE POR TELEFONE(s)!'
                    );
                }
            }else {
                if($dados['USU_COD'] == 0 && $dados['USU_COD_DESTINATARIO'] !=0 && $dados['EMP_COD'] !=0){
                    foreach ($dados as $key => $value) {
                        $dados[$key] = $this->Check->checarString($value);
                    }
                    $dados += array(
                        'SUP_DT_CADASTRO'=> date('Y-m-d H:i:s'),         
                        'SUP_STATUS'=> 1
                    );
                    $id = $this->Suporte->cadastrar($dados,0);
                    if($id){
                            $ok = true;
                            $respota = array(
                                'COD'=>'OK',
                                'MENSAGEM' => 'mensagem enviada! em breve entraremos em contato'
                            );
                    }else{
                        
                        $respota = array(
                            'COD'=>'ERRO',
                            'MENSAGEM'=> 'ERRO 3- ERRO AO CRIAR MENSAGEM, FALE COM O SUPORTE POR TELEFONE(s)!'
                        );
                    }
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- DADOS INVÁLIDO(S)!'
                    );
                }
            }
        }else {
            $respota = array(
                'COD'=>'ERRO',
                'MENSAGEM'=> 'ERRO 1- ACESSO INVÁLIDO(S)!'
            );
        }
        echo json_encode($respota);
    }
    public function detalhar()
    {
        $this->dados['title'] .= 'HELP!! SUPORTE AO SISTEMA';   
        $this->link[1] = ['link'=> 'suporte/detalhar','nome' => 'DETAL AO MENSA'];
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        
        if(isset($dados[2]) && $_SESSION['USU_NIVEL'] >= 15){

            $this->dados['empresa'] = $this->Empresa->setCodigo($dados[2])->listar(0);
            $this->dados['suporte'] = $this->Suporte->setCodEmpresa($dados[2])->listarTodasMensagensEmpresa(0);
            $ok = true;
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if($ok){
            $this->render('admin/lctec/suporte/detalhar', $this->dados);
        }else{
            $this->dados['suporte'] = $this->Suporte->setCodUsuario(0)->listarTodasMensagensSuporte(0);
            $this->render('admin/lctec/suporte/listar', $this->dados);
        }

    }
}