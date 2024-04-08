<?php 
namespace App\Controllers;

use App\Models\AberturaFechamentoCaixas;
use App\Models\Caixas as ModelsCaixa;
use App\Models\Empresas;
use App\Models\Contas;
use App\Models\ModulosEmpresa;
use App\Models\Movimentacoes;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use App\Models\Vendedores;
use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;



class caixas extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa,$Contas,$Movimentacoes,$Caixas,$AberturaFechamentoCaixas, $ModulosEmpresa;
    public function __construct()
    {

        Sessao::naoLogado();
        $this->dados['title'] = 'MÓDULO | PDV >>'; 
        $this->Contas = new Contas;  
        $this->Check = new Check;
        $this->Usuarios = new Usuarios;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->Movimentacoes = new Movimentacoes;
        $this->Caixas = new ModelsCaixa;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->AberturaFechamentoCaixas = new AberturaFechamentoCaixas;

        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['contas'] = $this->Contas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodas();
        $this->dados['modulo'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodigo(5)->listarModuloEmpresa(0);
        $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);

        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->link[1] = ['link'=> 'pdv','nome' => 'MÓDULO PDV'];
        $this->link[2] = ['link'=> 'caixas','nome' => 'GERENCIAR SEUS CAIXAS'];

    }
    public function index()
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' GERENCIAR CAIXAS';   
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/pdv/caixas/listar', $this->dados);
    }
    public function cadastro()
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' CADASTRAR CAIXA DA EMPRESA/NEGÓCIO';   
        $this->link[3] = ['link'=> 'caixas/cadastro','nome' => 'CADASTRO DE CAIXAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/pdv/caixas/cadastrar', $this->dados);
    }
    public function movimentacoes()
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' MOVIMENTAÇÕES DO CAIXA DA EMPRESA/NEGÓCIO';   
        $this->link[3] = ['link'=> 'caixas/cadastro','nome' => 'MOVIMENTAÇÕES DE CAIXAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $this->render('admin/pdv/caixas/movimentacoes', $this->dados);
    }
    public function aberturas_fechamentos()
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' ABERTURAS E FECHAMENTOS';   
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'aberturas_fechamentos' && isset($dados[2]) && isset($dados[3])) {

            $this->link[3] = ['link'=> 'caixas/aberturas_fechamentos/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ABERTURAS E FECHAMENTOS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
           
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['caixa'] = $this->Caixas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['caixa'] != 0) {
                    if(!isset($dados[4])){
                        $dados[4] = date('Y-m-d');
                    }
                    $this->dados['ab_caixas'] = $this->AberturaFechamentoCaixas->setCodEmpresa($dados[2])->setCodCaixa($dados[3])->setData($dados[4])->checarStatusCaixa(0);
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CAT22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CAT11 - Acesso inválido(s)!','alert alert-danger');
        }    
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if($ok){
            $this->render('admin/pdv/caixas/aberturas_fechamentos', $this->dados);
        }else{
            $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/pdv/caixas/listar', $this->dados);
        }
    }
    public function cadastrar()
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' CADASTRAR CAIXA DA EMPRESA/NEGÓCIO';
        $this->link[3] = ['link'=> 'caixas/listar','nome' => 'CADASTRO DE CAIXAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        if (isset($_POST) && isset($dados['CADASTRAR_NOVO_CAIXA'])) {
            unset($dados['CADASTRAR_NOVO_CAIXA']);
            if( $this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }


                //Verificar se já existe cadastro
                $cxa = $this->Caixas->setCodEmpresa($dados['EMP_COD'])->setDescricao($dados['CXA_DESCRICAO'])->checarDescricao();
                if(!$cxa){

                    $dados += array(
                        'CXA_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                        'CXA_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),          
                        'CXA_STATUS'=> 1
                    );
                    //unset($dados['SET_TIPO']);
                    if($this->Caixas->cadastrar($dados,0)){
                        $ok = true;
                        Sessao::alert('OK','Cadastro efetuado com sucesso!','fs-4 alert alert-success');
                    }else{
                        Sessao::alert('ERRO',' CXA4- Erro ao cadastrar novo caixa, entre em contato com o suporte!','fs-4 alert alert-danger');
                    }
                }else {
                    Sessao::alert('ERRO',' CXA3- Cadastro já realizado!','fs-4 alert alert-warning');
                }
            }else{
                Sessao::alert('ERRO',' CXA2 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' CXA1 - Acesso inválido(s)!','alert alert-danger');
        }
        if ($ok) {
            $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/pdv/caixas/listar', $this->dados);
        }else {
            $this->render('admin/pdv/caixas/cadastrar', $this->dados);
        }
    }
    public function movimentacao_caixa():void
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' MOVIMENTAÇÕES DE CAIXAS';
        $this->link[3] = ['link'=> 'caixas/movimentacao_caixa','nome' => 'MOVIMENTAÇÕES DE CAIXAS'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
        
    }
    public function fechar()
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' ALTERAR CAIXAS';
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'fechar' && isset($dados[2]) && isset($dados[3])) {

            $this->link[3] = ['link'=> 'caixas/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR CAIXAS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['caixa'] = $this->Caixas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['caixa'] != 0) {
                    $dados = array(
                        'MVC_COD' => '',
                        'USU_COD_FECHAMENTO' => '',
                        'AFC_DT_ATUALIZACAO' => date('Y-m-d H:i:s'),
                        'AFC_DT_FECHAMENTO' =>  date('Y-m-d'),
                        'AFC_SALDO_FINAL' =>$this->dados['caixa'][0]['CXA_SALDO'],
                        'AFC_STATUS' => 2
                    );
                    $this->AberturaFechamentoCaixas->setCodEmpresa($dados[2])->setCodCaixa($dados[3]);
                    if($this->AberturaFechamentoCaixas->alterar($dados,0)){
                        $ok = true;
                    }
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CXA22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CXA11 - Acesso inválido(s)!','alert alert-danger');
        }      
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if($ok){
            $this->render('admin/pdv/caixas/alterar', $this->dados);
        }else{
            $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/pdv/caixas/listar', $this->dados);
        }
    }
    public function alteracao():void
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' ALTERAR CAIXAS';
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'alteracao' && isset($dados[2]) && isset($dados[3])) {

            $this->link[3] = ['link'=> 'caixas/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'ALTERAR CAIXAS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['caixa'] = $this->Caixas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['caixa'] != 0) {
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CAT22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CAT11 - Acesso inválido(s)!','alert alert-danger');
        }      
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if($ok){
            $this->render('admin/pdv/caixas/alterar', $this->dados);
        }else{
            $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/pdv/caixas/listar', $this->dados);
        }
    }
    public function detalhar():void
    {
        Sessao::naoLogado();
        $this->dados['title'] .= ' DETALHAR CAIXAS';
       
        $dados = filter_input_array(INPUT_GET, FILTER_SANITIZE_URL);
        $dados = explode("/",$dados['url']);
        $ok = false;
        if (isset($dados[1]) && $dados[1] == 'detalhar' && isset($dados[2]) && isset($dados[3])) {

            $this->link[3] = ['link'=> 'caixas/detalhar/'.$_SESSION['EMP_COD'].'/'.$dados[3],'nome' => 'DETALHAR CAIXAS'];
            $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
            //verificar se o usuario que vai efetuar a acao é da empresa e se está correto(pertence) a empresa para os dados a serem alterados
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados[2]){
             
                $this->dados['caixa'] = $this->Caixas->setCodEmpresa($dados[2])->setCodigo($dados[3])->listar(0);
                if ($this->dados['caixa'] != 0) {
                    $this->dados['af_caixa'] = $this->AberturaFechamentoCaixas->setCodEmpresa($dados[2])->setCodCaixa($dados[3])->listar(0);
                    $ok = true;
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CAT22 - Acesso inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CAT11 - Acesso inválido(s)!','alert alert-danger');
        }      
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if($ok){
            $this->render('admin/pdv/caixas/detalhar', $this->dados);
        }else{
            $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/pdv/caixas/listar', $this->dados);
        }
    }
    public function alterar()
    {
        $this->dados['title'] .= ' ALTERAR CAIXAS';
        
        $ok = false;
        $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);       
        $cod = 0;
        if (isset($_POST) && isset($dados['ALTERAR_CAIXA'])) {

            if($this->dados['empresa']['USU_COD'] == $dados['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){

                $this->link[3] = ['link'=> 'caixas/alteracao/'.$_SESSION['EMP_COD'].'/'.$dados['CXA_COD'],'nome' => 'ALTERAR CAIXAS'];
                $cod = $dados['CXA_COD'];
                unset($dados['ALTERAR_CAIXA']);
                //Verifica se tem algum valor proibido
                foreach ($dados as $key => $value) {
                    $dados[$key] = $this->Check->checarString($value);
                }
                
                $this->Caixas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CXA_COD']);

                unset($dados['CXA_COD']);

                $dados += array(
                    'CXA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'CXA_STATUS'=> 1
                );
                if($this->Caixas->alterar($dados,0)){
                    $ok = true;
                    Sessao::alert('OK','Cadastro alterado com sucesso!','fs-4 alert alert-success');
                }else{
                    Sessao::alert('ERRO',' ERRO: CXA33- Erro ao alterar o caixa, entre em contato com o suporte!','fs-4 alert alert-danger');
                }
            }else{
                Sessao::alert('ERRO',' ERRO: CXA22 - Dados inválido(s)!','alert alert-danger');
            }
        }else{
            Sessao::alert('ERRO',' ERRO: CXA21 - Acesso inválido(s)!','alert alert-danger');
        }
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();
        if ($ok) {
            $this->dados['caixas'] = $this->Caixas->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
            $this->render('admin/pdv/caixas/listar', $this->dados);
        }else {
            $this->dados['caixa'] = $this->Caixas->setCodEmpresa($dados['EMP_COD'])->setCodigo($cod)->listar(0);
            $this->render('admin/pdv/caixas/alterar', $this->dados);
        }
    }
    public function status()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['STATUS_CAIXA'])) {
            //Verifica se os campos foram todos preenchidos
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                unset($dados['STATUS_CAIXA']);
                $this->Caixas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CXA_COD']);
                ($dados['CXA_STATUS'] == 1)? $dados['CXA_STATUS'] = 0: $dados['CXA_STATUS'] = 1;
                
                $db = array(
                    'USU_COD' => $_SESSION['USU_COD'],
                    'CXA_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                    'CXA_STATUS' => $dados['CXA_STATUS']
                );
                if($this->Caixas->alterar($db,0)){
                    $respota = array(
                        'COD'=>'OK',
                        'MENSAGEM' => 'Status alterado com sucesso!'
                    );
                }else{
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do caixa, entre em contato com o suporte!'
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
    public function status_abertura_fechamento()
    {
         //Recupera os dados enviados
         $dados = filter_input_array(INPUT_POST, FILTER_DEFAULT);
         if (isset($_POST) && isset($dados['ABERTURA_FECHAMENTO'])) {
            //Verifica se os campos foram todos preenchidos
            if($this->dados['empresa']['USU_COD'] == $_SESSION['USU_COD'] && $this->dados['empresa']['EMP_COD'] == $dados['EMP_COD']){
                
                unset($dados['ABERTURA_FECHAMENTO']);
                //checar se foi preenchido o usuario e senha para liberação do caixa
                if(!empty($dados['USU_COD_CAIXA']) && !empty($dados['USU_SENHA_CAIXA'])){

                    //checar se existe esse usuario para essa empresa
                    if($this->UsuariosEmpresa->setCodEmpresa($dados['EMP_COD'])->setEmail($dados['USU_COD_CAIXA'])->setSenha($dados['USU_SENHA_CAIXA'])->checarAcessoUsuarioEmpresa(0)){

                        //checar os dados de abertura e fechamento de caixa para a data solicitada
                        $this->dados['abfc_cxa'] = $this->AberturaFechamentoCaixas->setCodEmpresa($dados['EMP_COD'])->setCodCaixa($dados['CXA_COD'])->setData($dados['AFC_DATA'])->checarStatusCaixa(0);
                        if (empty($this->dados['abfc_cxa']) or $this->dados['abfc_cxa'] == 0) {
                            //ABRIR CAIXA PARA A DATA ATUAL
                            
                            //CRIAR ABERTURA DE CAIXA NO FLUXO DE CAIXA
                            $this->Caixas->setCodEmpresa($dados['EMP_COD'])->setCodigo($dados['CXA_COD']);
                            ($dados['CXA_STATUS'] == 1)? $dados['CXA_STATUS'] = 0: $dados['CXA_STATUS'] = 1;
    
                            $db = array(
                                'EMP_COD'=> $dados['EMP_COD'],
                                'CXA_COD'=> $dados['CXA_COD'],
                                'FLX_COD'=> '',
                                'USU_COD_ABERTURA' => $dados['USU_COD_CAIXA'],
                                'USU_COD_FECHAMENTO' => '',
                                'AFC_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                'AFC_DT_ATUALIZACAO'=> date('0000-00-00 00:00:00'),
                                'AFC_DT_ABERTURA'=> $dados['AFC_DATA'],
                                'AFC_DT_FECHAMENTO'=> '',
                                'AFC_SALDO_INICIAL'=> $dados['CXA_SALDO'],
                                'AFC_SALDO_FINAL'=> '',
                                'AFC_STATUS' => $dados['AFC_STATUS']
                            );
    
                            /*
                           
                            `MVC_COD` INT NOT NULL,
                          */
                           
                        }else {
                            //FECHAR CAIXA
                            //CRIAR FECHAMENTO DE CAIXA NO FLUXO DE CAIXA

                            
                            $db = array(
                                'FLX_COD'=> '',
                                'USU_COD_FECHAMENTO' => $dados['USU_COD_CAIXA'],
                                'AFC_DT_ATUALIZACAO'=> date('Y-m-d H:i:s'),
                                'AFC_DT_FECHAMENTO'=> $dados['AFC_DATA'],
                                'AFC_SALDO_FINAL'=> $dados['CXA_SALDO'],
                            );

                            //if($this->AberturaFechamentoCaixas->alterar($db,0)){
                                
                              //  $respota = array(
                                  //  'COD'=>'OK',
                                //);
                            //  }else{
                                //  $respota = array(
                                    ///    'COD'=>'ERRO',
                                    //    'MENSAGEM'=> 'ERRO 2- Erro ao mudar status do caixa, entre em contato com o suporte!'
                                // );
                            //  }
                        }
                       
                    }else {
                        $respota = array(
                            'COD'=>'ERRO',
                            'MENSAGEM'=> 'ERRO 4- Informe o usuário e senha com autorização!'
                        );
                    }
                }else {
                    $respota = array(
                        'COD'=>'ERRO',
                        'MENSAGEM'=> 'ERRO 3- Informe o usuário e senha!'
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