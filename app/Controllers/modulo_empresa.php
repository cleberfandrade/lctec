<?php
namespace App\Controllers;

use Core\View;

use Libraries\Util;
use Libraries\Sessao;
use App\Models\Usuarios;
use App\Models\Modulos as ModelsModulos;
use App\Models\ModulosEmpresa;
use App\Models\UsuariosEmpresa;
use Libraries\Check;

class modulo_empresa extends View
{ 
    private $dados = [];
    private $link,$Modulos,$Usuarios,$Empresa,$UsuariosEmpresa,$Check,$Util,$ModulosEmpresa;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Check = new Check;
        $this->Modulos = new ModelsModulos;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Util = new Util;

        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listar(0);
        $this->dados['modulos'] = $this->Modulos->listarTodos(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS >> '];
        $this->link[2] = ['link'=> 'usuarios','nome' => 'GERENCIAR MÓDULOS DA EMPRESA'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
    }
    public function index()
    {
        $this->dados['title'] .= ' MÓDULOS';
        $this->render('admin/cadastros/modulos/listar', $this->dados);
    }
    public function alterar()
    {
        $Usuarios = new Usuarios;
        $Usuarios->setCodUsuario($_SESSION['USU_COD']);
        $this->dados['usuario'] = $Usuarios->listar(0);
        
        $Modulos = new Modulos;

        //$Modulos->setCodigo(0);
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
    public function status():void
    {
       //Recupera os dados enviados
       $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       if (isset($_POST) && isset($dados['STATUS_MODULO'])) {

          if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
              //Verifica se os campos foram todos preenchidos
              unset($dados['STATUS_MODULO']);
              $this->ModulosEmpresa->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['FOR_COD']);
              ($dados['MOD_STATUS'] == 1)? $dados['MOD_STATUS'] = 0 : $dados['MOD_STATUS'] = 1;
              
              $db = array(
                  'MDE_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                  'MOD_STATUS' => $dados['MOD_STATUS']
              );

              if($this->ModulosEmpresa->alterar($db,0)){
                  $respota = array(
                      'COD'=>'OK',
                      'MENSAGEM' => 'Status alterado com sucesso!'
                  );
              }else{
                  $respota = array(
                      'COD'=>'ERRO',
                      'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do módulo da empresa, entre em contato com o suporte!'
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