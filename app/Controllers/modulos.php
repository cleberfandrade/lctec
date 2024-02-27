<?php
namespace App\Controllers;

use Core\View;

use Libraries\Util;
use Libraries\Sessao;
use App\Models\Empresas;
use App\Models\Usuarios;
use App\Models\Modulos as ModelsModulos;
use App\Models\ModulosEmpresa;
use App\Models\UsuariosEmpresa;
use Libraries\Check;

class modulos extends View
{
    private $dados = [];
    private $link,$Modulos,$Usuarios,$Empresas,$UsuariosEmpresa,$Check,$Util,$ModulosEmpresa;
    public function __construct()
    {
        Sessao::logadoSistema();
        $this->Empresas = new Empresas;
        $this->Check = new Check;
        $this->Util = new Util;

        $this->Usuarios = new Usuarios;
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        
        $this->Modulos = new ModelsModulos;
        $this->dados['modulos'] = $this->Modulos->listarTodos();

        $this->ModulosEmpresa = new ModulosEmpresa;

        $this->dados['title'] = 'MÓDULOS LC/TEC >> ';

        $this->link[0] = ['link'=> 'lctec','nome' => 'PAINEL GERENCIAL | LC/TEC'];
        $this->link[1] = ['link'=> 'lctec/modulos','nome' => 'MÓDULOS LC/TEC >>'];
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
    }
    public function index()
    {
        Sessao::logadoSistema();
        $this->dados['title'] .= ' LC/TEC >> MÓDULOS DO SISTEMA';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/lctec/modulos/modulos', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR MÓDULOS';
        $this->link[2] = ['link'=> 'modulos/cadastro','nome' => 'CADASTRAR MÓDULOS'];
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/lctec/modulos/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR MÓDULOS';
        $this->link[2] = ['link'=> 'modulos/cadastro','nome' => 'CADASTRAR MÓDULOS'];
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
                
        if (isset($_POST) && isset($dados['CADASTRAR_MODULO'])) {
            unset($dados['CADASTRAR_MODULO']);
            if($_SESSION['USU_NIVEL'] >= 15){
                //Verificar se já existe cadastro
                $this->Modulos->setDescricao($dados['MOD_DESCRICAO']);
                $mod = $this->Modulos->checarDescricao();
                
                if(!$mod){

                    $dados += array(
                        'MOD_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'MOD_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                        'MOD_STATUS'=> 1
                    );
                    if($this->Modulos->cadastrar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' SET4- Erro ao cadastrar novo módulo, entre em contato com o desenvolvedor!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' SET3- Cadastro já realizado!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CLI22 - Acesso nao permitido!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' SET1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['modulos'] = $this->Modulos->listarTodos(0);
            $this->render('admin/lctec/modulos/modulos', $this->dados);
        }else {
            $this->render('admin/lctec/modulos/cadastrar', $this->dados);
        }
    }
    public function alteracao()
    {
        Sessao::logadoSistema();
        $this->dados['title'] .= ' LC/TEC >> ALTERAR MÓDULO DO SISTEMA';   
        $this->link[2] = ['link'=> 'lctec/modulos/alteracao','nome' => 'ALTERAR MÓDULO'];

        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2])) {
            
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
    public function alterar()
    {
        $this->dados['title'] = ' LC/TEC >> ALTERAR MÓDULO DO SISTEMA';   
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        $ok = false;
        if (isset($_POST) && isset($dados['ALTERAR_MODULO'])) {
           
            if($_SESSION['USU_NIVEL'] >= 20){

                unset($dados['ALTERAR_MODULO']);

                //Verifica se tem algum valor proibido
                //foreach ($dados as $key => $value) {
                    //$dados[$key] = $this->Check->checarString($value);
                //}
                $this->dados['modulo'] = $this->Modulos->setCodigo($dados['MOD_COD'])->listar(0);

                if ($this->dados['modulo'] != 0) {

                    $this->link[2] = ['link'=> 'lctec/modulos/alteracao/'.$dados['MOD_COD'],'nome' => 'ALTERAR MODULO'];
        
                    $codigo = $dados['MOD_COD'];
                    unset($dados['MOD_COD']);

                    $dados += array(
                        'MOD_DT_ATUALIZACAO'=> date('Y-m-d H:i:s')
                    );
                    
                    if($this->Modulos->alterar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' ERRO: COL24 - Erro ao alterar módulo, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' COL23- Módulo não foi encontrado!, verifque os dados informados, ou entre em contato com o Suporte','fs-4 alert alert-danger');
                }
            }
        }else{
            Sessao::alert('ERRO',' 1- Dados inválido(s)!','fs-4 alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['modulos'] = $this->Modulos->listarTodos(0);
            $this->render('admin/lctec/modulos/modulos', $this->dados);
        }else {
            $this->dados['modulo'] = $this->Modulos->setCodigo($codigo)->listar(0);
            $this->render('admin/lctec/modulos/alterar', $this->dados);
        }
    }
    public function status():void
    {
       //Recupera os dados enviados
       $this->link[1] = ['link'=> 'lctec','nome' => 'MÓDULOS LC/TEC >>'];
       //$dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
       $ok = false;
       $dados = explode("/",$dados['url']);
       if (isset($dados[1]) && $dados[1] == 'status' && isset($dados[2]) && isset($dados[3])) {
       //if (isset($_POST) && isset($dados['STATUS_MODULO'])) {

          if($_SESSION['USU_NIVEL'] >= 20 && $_SESSION['USU_NIVEL'] <= 30){
              //Verifica se os campos foram todos preenchidos
              unset($dados['status']);
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