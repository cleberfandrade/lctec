<?php 
namespace App\Controllers;

use App\Models\Empresas;
use App\Models\Financas;
use App\Models\Modulos;
use App\Models\ModulosEmpresa;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Vendedores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class lctec extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Avisos,$Usuarios,$Empresas,$UsuariosEmpresa,$Modulos,$ModulosEmpresa,$Colaboradores,$CargosSalarios,$FolhasPagamento,$Divulgacoes,$Desligamentos,$Horarios,$Promocoes,$Recrutamentos,$Treinamentos,$Beneficios;
    public function __construct()
    {
        Sessao::naoLogadoSistema();
        $this->dados['title'] = 'MÓDULO | LCTEC >>'; 

        $this->Financas = new Financas;  
        $this->Check = new Check;
        
        $this->Empresas = new Empresas;
        $this->dados['empresas'] = $this->Empresas->listarTodos();

        $this->Usuarios = new Usuarios;
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);

        $this->Modulos = new Modulos;
        $this->dados['modulos'] = $this->Modulos->listarTodos();
        $this->ModulosEmpresa = new ModulosEmpresa;

        
        $this->link[0] = ['link'=> 'lctec','nome' => 'PAINEL GERENCIAL | LC/TEC'];
        //$this->link[1] = ['link'=> 'lctec','nome' => 'MÓDULO LC/TEC >>'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
    }
    public function index()
    {
        Sessao::logadoSistema();
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['title'] .= ' PAINEL GERENCIAL | LC/TEC';   
        $this->render('admin/lctec/painel', $this->dados);
    }
    public function modulos()
    {
        Sessao::logadoSistema();
        $this->dados['title'] .= ' LC/TEC >> MÓDULOS DO SISTEMA';   
        $this->link[1] = ['link'=> 'lctec','nome' => 'MÓDULOS LC/TEC >>'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/lctec/modulos/modulos', $this->dados);
    }
    public function avisos()
    {
        $this->dados['title'] .= ' AVISOS LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/painel', $this->dados);
    }
    public function empresas()
    {
        $this->dados['title'] .= ' EMPRESAS LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/painel', $this->dados);
    }
    public function suporte()
    {
        $this->dados['title'] .= ' SUPORTE LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/suporte/listar', $this->dados);
    }
    public function modulos_status():void
    {
       //Recupera os dados enviados
       $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       if (isset($_POST) && isset($dados['STATUS_MODULO'])) {

          if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $_SESSION['USU_NIVEL'] >= 15){
              //Verifica se os campos foram todos preenchidos
              unset($dados['STATUS_MODULO']);
              $this->Modulos->setCodigo($dados['MOD_COD']);
              ($dados['MOD_STATUS'] == 1)? $dados['MOD_STATUS'] = 0 : $dados['MOD_STATUS'] = 1;
              
              $db = array(
                  'MOD_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
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
                      'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do módulo, entre em contato com o suporte!'
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
