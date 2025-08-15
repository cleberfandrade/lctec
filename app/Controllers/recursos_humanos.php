<?php 
namespace App\Controllers;

use App\Models\Empresas;
use App\Models\Financas;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\ModulosEmpresa;
use App\Models\Vendedores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class recursos_humanos extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa,$ModulosEmpresa,$Colaboradores,$CargosSalarios,$FolhasPagamento,$Divulgacoes,$Desligamentos,$Horarios,$Promocoes,$Recrutamentos,$Treinamentos,$Beneficios;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | RECURSOS HUMANOS >>'; 
        $this->Financas = new Financas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->ModulosEmpresa = new ModulosEmpresa;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodModulo(4)->checarRegistroModuloEmpresa(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'recursos_humanos','nome' => 'MÓDULO DE RECURSOS HUMANOS >>'];
        if ($this->dados['modulos_empresa'] == 0) {
            Sessao::alert('OK',' MÓDULO NÃO DISPONÍVEL!','alert alert-danger');
            Url::redirecionar('admin/painel');
        }else {
            $this->dados['modulo'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo(4)->listarModuloEmpresa(0);
        }
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR RECURSOS HUMANOS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb($this->dados['modulo'][0]['MOD_COR_TEXTO']);
        $this->render('admin/recursos_humanos/recursos_humanos', $this->dados);
    }
}
