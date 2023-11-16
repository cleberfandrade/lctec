<?php
namespace App\Controllers;

use Core\View;

use Libraries\Util;
use Libraries\Sessao;
use App\Models\Usuarios;
use App\Models\Modulos;

class modulo extends View
{
    private $dados = [];
    private $link,$Modulos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;
        $this->Modulos = new Modulos;
    }
    public function index()
    {
        $Usuarios = new Usuarios;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        $this->Modulos = new Modulos;
        $this->render('admin/configuracoes/modulos', $this->dados);
    }
    public function alterar()
    {
        $Usuarios = new Usuarios;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        
        $Modulos = new Modulos;

        $Modulos->setCodigo(0);
        $post = filter_input_array(INPUT_POST, FILTER_DEFAULT);

        if (isset($_POST) && isset($post['ALTERAR_NIVEL'])) {
            unset($post['ALTERAR_NIVEL']);
            if (empty($post['NIV_COD'])) {
                Sessao::alert('ERRO',' 2- Houve um erro ao obter o código do Nível, entre em contato com o desenvolvedor!','text-uppercase fs-4 alert alert-danger');
            } else {
                foreach ($post as $key => $value) {
                    $dados[$key] = $value;
                }
                //unset($dados['NIV_COD']);
                //$Niveis->setCodigo($post['NIV_COD']);
                //if($Niveis->alterar($dados,0)){
                  //  Sessao::alert('OK',' Nível alterado com sucesso!','fs-4 alert alert-success');
                //}else{
                 //   Sessao::alert('ERRO',' 3- Erro ao alterar cadastro, entre em contato com o desenvolvedor!','fs-4 alert alert-danger');
                //}
            }
        }else{
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');
        }
        $this->render('admin/configuracoes/empresa', $this->dados);
    }
}