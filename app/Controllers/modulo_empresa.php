<?php
namespace App\Controllers;

use App\Models\Empresas;
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
        $this->dados['title'] = 'MÓDULO | CADASTROS >> ';
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listar(0);
        $this->dados['modulos'] = $this->Modulos->listarTodos(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'cadastros','nome' => 'MÓDULO DE CADASTROS >> '];
        $this->link[2] = ['link'=> 'modulos_empresa','nome' => 'GERENCIAR MÓDULOS DA EMPRESA'];
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
    }
    public function index()
    {
        $this->dados['title'] .= ' MÓDULOS DA EMPRESA';
        $this->render('admin/cadastros/modulos/listar', $this->dados);
    }
    public function alterar()
    {
        $this->dados['title'] .= ' MÓDULOS DA EMPRESA';
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alterar' && isset($dados[2]) && isset($dados[3]) && isset($dados[4])) {
            /* [2] = EMP_COD [3] = MOD_COD [4] = MLE_STATUS */
            if ($_SESSION['EMP_COD'] == $dados[2]) {

                $ck = $this->ModulosEmpresa->setCodEmpresa($dados[2])->setCodModulo($dados[3])->checarRegistroModuloEmpresa(0);
           
                if ($ck) {
                    //EXCLUIR MÓDULO
                    $this->ModulosEmpresa->setCodigo($ck['MLE_COD']);
                    if($this->ModulosEmpresa->excluir(0)){
                        $ok = true;
                        $respota = array(
                            'COD'=>'OK',
                            'MENSAGEM' => 'Status alterado com sucesso!'
                        );
                        Sessao::alert('OK','Exclusão efetuada com sucesso!','fs-4 alert alert-success');
                    }else {
                        Sessao::alert('ERRO',' MLE4- Erro ao excluir cadastro, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                } else {
                   //CADASTRAR MÓDULO
                   $db = array(
                    'EMP_COD' => $dados[2],
                    'MOD_COD' => $dados[3],
                    'MLE_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                    'MLE_STATUS' => 1
                );
                    if ($this->ModulosEmpresa->cadastrar($db,0)) {
                        $ok = true;
                        $respota = array(
                            'COD'=>'OK',
                            'MENSAGEM' => 'Status alterado com sucesso!'
                        );
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    } else {
                        Sessao::alert('ERRO',' MLE3- Erro ao cadastrar módulo, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }
            } else {
                Sessao::alert('ERRO',' MLE2 - Acesso inválido(s)!','alert alert-danger');
            }

        }else{
            Sessao::alert('ERRO',' MLE1- Dados inválido(s)!','fs-4 alert alert-danger');
        }
        $this->dados['modulos'] = $this->Modulos->listarTodos(0);
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listar(0);
        
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/cadastros/modulos/listar', $this->dados);
    }
    public function alterar_modulo_empresa()
    {
        $dados = filter_input_array(INPUT_POST, FILTER_SANITIZE_URL);
        //$dados = explode("/",$dados['url']);

        if (isset($dados['ALTERAR_MODULO_EMPRESA']) && isset($dados['EMP_COD']) && isset($dados['MOD_COD']) && isset($dados['MOD_STATUS'])) {
            /* [2] = EMP_COD [3] = MOD_COD [4] = MLE_STATUS */
            unset($dados['ALTERAR_MODULO_EMPRESA']);
            $ck = $this->ModulosEmpresa->setCodEmpresa($dados['EMP_COD'])->setCodModulo($dados['MOD_COD'])->checarRegistroModuloEmpresa(0);
            if ($ck) {
                //EXCLUIR MÓDULO
                $this->ModulosEmpresa->setCodigo($ck['MLE_COD']);
                if($this->ModulosEmpresa->excluir(0)){
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Exclusão efetuada com sucesso!'
                    );
                }else {
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM' => 'Erro ao excluir cadastro, entre em contato com o suporte!'
                    );
                }
            } else {
            //CADASTRAR MÓDULO
                $db = array(
                    'EMP_COD' => $dados['EMP_COD'],
                    'MOD_COD' => $dados['MOD_COD'],
                    'MLE_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                    'MLE_STATUS' => 1
                );
                if ($this->ModulosEmpresa->cadastrar($db,0)) {
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Cadastro efetuado com sucesso!'
                    );
                } else {
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM' => 'Erro ao cadastrar módulo, entre em contato com o suporte!'
                    );
                }
            }
        }else{
            $respota = array(
                'COD'=>'ERRO',
                'MENSAGEM'=> 'ERRO 1- Acesso inválido!'
            );
        }
        echo json_encode($respota);
    }
    public function status():void
    {
       //Recupera os dados enviados
       $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
       if (isset($_POST) && isset($dados['STATUS_MODULO'])) {

          if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
              //Verifica se os campos foram todos preenchidos
              unset($dados['STATUS_MODULO']);
              $this->ModulosEmpresa->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['MOD_COD']);
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