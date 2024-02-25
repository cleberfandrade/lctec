<?php
namespace App\Controllers;

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
    private $link,$Check,$Empresa, $Usuarios,$UsuariosEmpresa,$Suporte;

    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'HELP!! SUPORTE AO SISTEMA >>';
        $this->Empresa = new Empresas;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;

        $this->Suporte = new ModelsSuporte;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'SUPORTE AO SISTEMA'];
    }
    public function index()
    {
        $this->dados['title'] .= ' SUPORTE AO SISTEMA';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/suporte', $this->dados);
    }
    public function cadastrar()
    { 
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($_POST) && isset($dados['CADASTRAR_NOVA_MENSAGEM'])) {
            unset($dados['CADASTRAR_NOVA_MENSAGEM']);
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                $dados += array(
                    'SUP_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                    'USU_COD_DESTINATARIO' => 'SUPORTE',
                    'SUP_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                    'SUP_STATUS'=> 1
                );

                if($this->Suporte->cadastrar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' SET4- Erro ao cadastrar, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }
        }
    }
}