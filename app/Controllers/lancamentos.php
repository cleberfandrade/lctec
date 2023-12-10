<?php
namespace App\Controllers;

use App\Models\Categorias;
use App\Models\Classificacoes;
use App\Models\Clientes;
use App\Models\Contas;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;
use App\Models\Empresas;
use App\Models\Financas;
use App\Models\Fornecedores;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Lancamentos as ModelsLancamentos;

class lancamentos extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa, $Lancamentos,$Categorias,$Contas,$Classificacoes,$Clientes,$Fornecedores;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | FINANCEIRO >>'; 
        $this->Financas = new Financas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Lancamentos = new ModelsLancamentos;
        $this->Categorias = new Categorias;
        $this->Contas = new Contas;
        $this->Classificacoes = new Classificacoes;
        $this->Fornecedores = new Fornecedores;
        $this->Clientes= new Clientes;
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['categorias'] = $this->Categorias->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['contas'] = $this->Contas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas(0);
        $this->dados['classificacoes'] = $this->Classificacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo('LAN')->listarTodosPorTipo(0);
        $this->dados['fornecedores'] = $this->Fornecedores->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['clientes'] = $this->Clientes->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'financeiro','nome' => 'MÓDULO FINANCEIRO'];
        $this->link[2] = ['link'=> 'lancamentos','nome' => 'GERENCIAR LANÇAMENTOS'];
    }
    public function index()
    {
        $this->dados['title'] .= ' GERENCIAR LANÇAMENTOS A PARGAR E RECEBER';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/lancamentos/listar', $this->dados);
    }
    public function cadastro():void
    {
        $this->dados['title'] .= ' CADASTRAR LANÇAMENTOS A PARGAR E RECEBER';
        $this->link[3] = ['link'=> 'lancamentos/cadastrar','nome' => 'CADASTRO DE LANÇAMENTOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/financeiro/lancamentos/cadastrar', $this->dados);
    }
    public function cadastrar()
    {
        $this->dados['title'] .= ' CADASTRAR LANÇAMENTOS A PARGAR E RECEBER';
        $this->link[3] = ['link'=> 'lancamentos/cadastrar','nome' => 'CADASTRAR LANÇAMENTOS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_LANCAMENTO'])) {
            unset($dados['CADASTRAR_NOVO_LANCAMENTO']);
            
            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                ($dados['LAN_RESULTADOS'] == "on")? $dados['LAN_RESULTADOS'] = 1: $dados['LAN_RESULTADOS'] = 0;
                //Verificar se já existe cadastro
                $lan = $this->Lancamentos->setCodEmpresa($dados['EMP_COD'])->setDataVencimento($dados['LAN_DT_VENCIMENTO'])->setDescricao($dados['LAN_DESCRICAO'])->checarDescricao();
                      
                if(!$lan){
                    $total = 0;
                    
                    if ($dados['LAN_PARCELA'] >=2) {
    
                        $dados['LAN_VALOR'] = str_replace(',', '.', str_replace('.', '', $dados['LAN_VALOR']));

                        $vl_parcela = ($dados['LAN_VALOR']/$dados['LAN_PARCELA']);
                        $vl_parcela = number_format( $vl_parcela,2,'.','');
                        $descricao = $dados['LAN_DESCRICAO'];
                        $qtd = $dados['LAN_PARCELA'];
                        $qtdDias = 30;
                        //$data = new DateTime($dados['LAN_DT_VENCIMENTO']);
                        //$vencimento = $data->format('d/m/Y');
                        $vencimento = $dados['LAN_DT_VENCIMENTO'];
                        $data = explode("-", $vencimento);
                        unset($dados['LAN_VALOR']);
                        unset($dados['LAN_DESCRICAO']);
                        unset($dados['LAN_DT_VENCIMENTO']);
                        unset($dados['LAN_PARCELA']);
                        for ($i = 1; $i <= $qtd; $i++) { 

                            $dados += array(
                                'LAN_VALOR' => $vl_parcela,
                                'LAN_DT_VENCIMENTO' => $vencimento,
                                'LAN_DESCRICAO' => $descricao.' - '.$i.'/'.$qtd,
                                'LAN_PARCELA' => $i,
                                'LAN_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                'LAN_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                                'LAN_STATUS'=> 1
                            );
    
                            if($this->Lancamentos->cadastrar($dados,0)){
                                $total++;
                                $vencimento = date('Y-m-'.$data[2], strtotime("+{$qtdDias} days",strtotime($vencimento)));
                                unset($dados['LAN_DESCRICAO']);
                                unset($dados['LAN_DT_VENCIMENTO']);
                                unset($dados['LAN_PARCELA']);
                               
                            }
                        }
                    } else {
                        $dados += array(
                            'LAN_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                            'LAN_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                            'LAN_STATUS'=> 1
                        );
                        if($this->Lancamentos->cadastrar($dados,0)){
                            $total++;
                        }
                    }
            
                    if($total){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' LAN4- Erro ao cadastrar novo lançamento, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' LAN3- Cadastro já realizado!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' LAN2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' LAN1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/financeiro/lancamentos/listar', $this->dados);
        }else {
            $this->render('admin/financeiro/lancamentos/cadastrar', $this->dados);
        }
    }
    public function alteracao():void
    {
        $this->dados['title'] .= ' ALTERAR LANÇAMENTOS';
       
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {

            $this->link[3] = ['link'=> 'lancamentos/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR LANÇAMENTOS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['lancamento'] = $this->Lancamentos->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['lancamento'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: LAN22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: LAN11 - Acesso inválido(s)!','alert alert-danger');
        }      
        if($ok){
            $this->render('admin/financeiro/lancamentos/alterar', $this->dados);
        }else{
            $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/financeiro/lancamentos/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR LANÇAMENTOS';
        
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       

        if (isset($_POST) && isset($dados['ALTERAR_LANCAMENTO'])) {

            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                $this->link[3] = ['link'=> 'lancamentos/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['LAN_COD'],'nome' => 'ALTERAR LANÇAMENTOS'];

                unset($dados['ALTERAR_LANCAMENTO']);
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                ($dados['LAN_RESULTADOS'] == "on")? $dados['LAN_RESULTADOS'] = 1: $dados['LAN_RESULTADOS'] = 0;
              
                $this->Lancamentos->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['LAN_COD']);
                
                $dados += array(
                    'LAN_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'LAN_STATUS'=> 1
                );
                if($this->Lancamentos->alterar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' ERRO: LAN33- Erro ao alterar o categoria, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: LAN22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: LAN21 - Acesso inválido(s)!','alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['lancamentos'] = $this->Lancamentos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/financeiro/lancamentos/listar', $this->dados);
        }else {
            $this->dados['lancamento'] = $this->Lancamentos->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['LAN_COD'])->listar(0);
            $this->render('admin/financeiro/lancamentos/alterar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_LANCAMENTO'])) {
            //Verifica se os campos foram todos preenchidos
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                unset($dados['STATUS_LANCAMENTO']);
                $this->Lancamentos->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['LAN_COD']);
                ($dados['LAN_STATUS'] == 1)? $dados['LAN_STATUS'] = 0: $dados['LAN_STATUS'] = 1;
                              
                $db = array(
                    'USU_COD' => $_SESSION['USU_COD'],
                    'LAN_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'LAN_STATUS' => $dados['LAN_STATUS']
                );
                if($this->Lancamentos->alterar($db,0)){
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do lançamento, entre em contato com o suporte!'
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