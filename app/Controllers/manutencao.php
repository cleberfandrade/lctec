<?php 
namespace App\Controllers;

use App\Models\Empresas;
use App\Models\Financas;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Vendedores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class manutencao extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa,$Colaboradores,$CargosSalarios,$FolhasPagamento,$Divulgacoes,$Desligamentos,$Horarios,$Promocoes,$Recrutamentos,$Treinamentos,$Beneficios;
    public function __construct()
    {
        Sessao::naoLogadoSistema();
        $this->dados['title'] = 'MÓDULO | LCTEC >>'; 
        $this->Financas = new Financas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        
        $this->link[0] = ['link'=> 'lctec/painel','nome' => 'PAINEL ADMINISTRATIVO LCTEC'];
        $this->link[1] = ['link'=> 'manutencao','nome' => 'MÓDULO DA LCTEC >>'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR MANUTENÇÃO NO SISTEMA';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/lctec/manutencao', $this->dados);
    }
}
