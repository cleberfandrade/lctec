<?php
namespace App\Controllers;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

use App\Models\Empresas;
use App\Models\Financas;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\ContasPagarReceber;

class pagamentos extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa, $ContasPagarReceber;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Financas = new Financas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->ContasPagarReceber = new ContasPagarReceber;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['contas_pagar_receber'] = $this->ContasPagarReceber->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
    }

    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR PAGAMENTOS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/pagamentos/listar', $this->dados);
    }
}