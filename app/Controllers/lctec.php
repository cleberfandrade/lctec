<?php 
namespace App\Controllers;

use App\Models\Empresas;
use App\Models\Financas;
use App\Models\Modulos;
use App\Models\ModulosEmpresa;
use App\Models\Suporte;
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
    private $link,$Financas,$Check,$Avisos,$Usuarios,$Empresas,$UsuariosEmpresa,$Modulos,$Suporte,$ModulosEmpresa,$Colaboradores,$CargosSalarios,$FolhasPagamento,$Divulgacoes,$Desligamentos,$Horarios,$Promocoes,$Recrutamentos,$Treinamentos,$Beneficios;
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

        $this->Suporte = new Suporte;
        
        $this->dados['suporte'] = $this->Suporte->setCodUsuario(0)->listarTodasMensagensSuporte(0);
        
        $this->link[0] = ['link'=> 'lctec','nome' => 'PAINEL GERENCIAL | LC/TEC'];
        //$this->link[1] = ['link'=> 'lctec','nome' => 'MÓDULO LC/TEC >>'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
    }
    public function index()
    {
        Sessao::logadoSistema();
        $this->dados['empresas'] = $this->Empresas->listarTodos();
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['title'] .= ' PAINEL GERENCIAL | LC/TEC';   
        
        $this->render('admin/lctec/painel', $this->dados);
    }
    
    public function avisos()
    {
        $this->dados['title'] .= ' AVISOS LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/painel', $this->dados);
    }
    public function cadastros()
    {
        $this->dados['title'] .= ' CADASTROS LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/cadastros', $this->dados);
    }
    public function empresas()
    {
        $this->dados['title'] .= ' EMPRESAS LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/empresas/empresas', $this->dados);
    }
    public function suporte()
    {
        $this->dados['title'] .= ' SUPORTE LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/suporte/listar', $this->dados);
    }
    public function links()
    {
        $this->dados['title'] .= ' CADASTRO DE LINKS LC/TEC';   
        Sessao::logadoSistema();
        $this->render('admin/lctec/cadastros/links', $this->dados);
    }
    public function modulos()
    {
        Sessao::logadoSistema();
        $this->dados['title'] .= ' LC/TEC >> MÓDULOS DO SISTEMA';   
        $this->link[1] = ['link'=> 'lctec','nome' => 'MÓDULOS LC/TEC >>'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/lctec/modulos/modulos', $this->dados);
    }
    public function modulos_alteracao()
    {
        Sessao::logadoSistema();
        $this->dados['title'] .= ' LC/TEC >> MÓDULOS DO SISTEMA';   
        $this->link[1] = ['link'=> 'lctec','nome' => 'MÓDULOS LC/TEC >>'];
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'modulos_alteracao' && isset($dados[2])) {
            
            if($_SESSION['USU_NIVEL'] >= 15){

                $this->dados['modulo'] = $this->Modulos->setCodigo($dados[2])->listar(0);
                if ($this->dados['modulo'] != 0) {
                    $ok = true;
                }else {
                    $ok = false;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CLI22 - Acesso nao permitido!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CLI11 - Dados inválido(s)!','alert alert-danger');
        } 
        if($ok){
            $this->render('admin/lctec/modulos/alterar', $this->dados);
        }else{
            $this->dados['modulos'] = $this->Modulos->listarTodos(0);
            $this->render('admin/lctec/modulos/modulos', $this->dados);
        }
    }
    public function modulos_status():void
    {
       //Recupera os dados enviados
       $this->link[1] = ['link'=> 'lctec','nome' => 'MÓDULOS LC/TEC >>'];
       //$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
       $ok = false;
       $dados = explode("/",$dados['url']);
       if (isset($dados[1]) && $dados[1] == 'modulos_status' && isset($dados[2]) && isset($dados[3])) {
       //if (isset($_POST) && isset($dados['STATUS_MODULO'])) {

          if($_SESSION['USU_NIVEL'] >= 20 && $_SESSION['USU_NIVEL'] <= 30){
              //Verifica se os campos foram todos preenchidos
              unset($dados['modulos_status']);
              $this->Modulos->setCodigo($dados[2]);
              ($dados[3] == 1)? $dados['MOD_STATUS'] = 0 : $dados['MOD_STATUS'] = 1;
              
              $db = array(
                  'MOD_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                  'MOD_STATUS' => $dados['MOD_STATUS']
              );
             
              if($this->Modulos->alterar($db,0)){
                $ok = true;
                Sessao::alert('OK','Status alterado com sucesso!','fs-4 alert alert-success');
              }else{
                Sessao::alert('ERRO',' ERRO: MOD33- Erro ao alterar status do módulo, entre em contato com a equipe de desenvolvimento!','fs-4 alert alert-danger');
              }
          }else {
            Sessao::alert('ERRO',' ERRO: MOD22 - Acesso inválido(s)!','alert alert-danger');
          }
      }else {
        Sessao::alert('ERRO',' ERRO: MOD22 - Dados inválido(s)!','alert alert-danger');
      }
     
      $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
      if ($ok) {
        $this->dados['modulos'] = $this->Modulos->listarTodos();
        $this->render('admin/lctec/modulos/modulos', $this->dados);
      }else {
          $this->render('admin/lctec/modulos/modulos', $this->dados);
      }
    }
}
