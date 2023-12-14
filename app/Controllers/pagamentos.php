<?php 
namespace App\Controllers;

use App\Models\FormasPagamentos;
use App\Models\Lancamentos;
use App\Models\Movimentacoes;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class pagamentos extends View
{
    private $dados = [];
    private $link,$Check,$Usuarios,$UsuariosEmpresa,$Lancamentos,$Contas,$Movimentacoes, $FormasPagamentos;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Check = new Check;
       
        $this->FormasPagamentos = new FormasPagamentos;
        $this->Lancamentos = new Lancamentos;  
        $this->Movimentacoes = new Movimentacoes;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO FINANCEIRO'];
        $this->link[2] = ['link'=> 'pagamentos','nome' => 'GERENCIAR PAGAMENTOS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR PAGAMENTOS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/pagamentos', $this->dados);
    }
    public function pagar()
    {
        $this->dados['title'] .= ' GERENCIAR PAGAMENTOS';   
        $this->dados['lancamentos_pagar'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(1)->listarTodosTipo();
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/pagamentos/pagar', $this->dados);
    }
    public function receber()
    {
        $this->dados['title'] .= ' GERENCIAR PAGAMENTOS';   
        $this->dados['lancamentos_receber'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(2)->listarTodosTipo();
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/pagamentos/receber', $this->dados);
    }
}