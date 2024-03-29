<?php
namespace App\Controllers;

use App\Models\Avisos as ModelsAviso;
use App\Models\Empresas;
use App\Models\Estoques;
use App\Models\ModulosEmpresa;
use App\Models\Movimentacoes;
use App\Models\Produtos;
use App\Models\Tarefas;
use Core\View;
use App\Models\Usuarios;
use App\Models\UsuariosEmpresa;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Util;

class avisos extends View
{
    private $dados = [];
    private $link,$Util,$Check,$Empresa,$Usuarios,$Estoques,$UsuariosEmpresa,$ModulosEmpresa, $Tarefas,$Produtos,$Movimentacoes,$Avisos;
    public function __construct()
    {
        Sessao::naoLogado();
        $this->dados['title'] = 'AVISOS | LC/TEC';
        $this->Usuarios = new Usuarios;
        $this->Empresa = new Empresas;
        $this->Estoques = new Estoques;
        $this->UsuariosEmpresa = new UsuariosEmpresa;
        $this->ModulosEmpresa = new ModulosEmpresa;
        $this->Tarefas = new Tarefas;
        $this->Util = new Util;
        $this->Check = new Check;
        $this->Produtos = new Produtos;
        $this->Movimentacoes = new Movimentacoes;
        $this->Avisos = new ModelsAviso;
        
        $this->dados['usuarios_empresa'] = $this->UsuariosEmpresa->setCodUsuario($_SESSION['USU_COD'])->checarUsuario();
        if (isset($this->dados['usuarios_empresa']['UMP_COD'])) {
          $_SESSION['EMP_COD'] = $this->dados['usuarios_empresa']['EMP_COD'];
        }
        $this->dados['usuario'] = $this->Usuarios->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['empresa'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setCodUsuario($_SESSION['USU_COD'])->listar(0);
        $this->dados['modulos_empresa'] = $this->ModulosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->listar();
        $this->dados['estoques'] = $this->Estoques->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        $this->dados['produtos'] = $this->Produtos->setCodEmpresa($_SESSION['EMP_COD'])->setStatus(1)->listarTodosGeral(0);
        $this->dados['usuarios'] = $this->UsuariosEmpresa->setCodEmpresa($_SESSION['EMP_COD'])->setStatus(1)->listarUsuariosEmpresa(0);
        
        $this->dados['movimentacoes'] = $this->Movimentacoes->setCodEmpresa($_SESSION['EMP_COD'])->setTipo(2)->setStatus(1)->listarTodasPorTipo(0); 
        
        $this->dados['avisos'] = $this->Avisos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        
        $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        $this->dados['breadcrumb'] = $this->Check->setLink($this->link)->breadcrumb();

    }
    public function index()
    {   
        Sessao::naoLogado();
        $this->render('admin/avisos', $this->dados);
    }
    public function checarAvisos()
    {   
         /**
         * AVISOS - TIPO
         * 1 => SUPORTE
         * 2 => PRODUTO
         * 3 => SERVICO
         * 4 => ESTOQUE
         * 5 => FINANCEIRO
         * 6 => RECURSOS HUMANOS
         * 7 => ATUALIZACOES
         * 8 => MENSALIDADE
         * 9 => MANUTENCAO
         * 10 => CADASTROS
         * 11 => GERAL
        */
        /***
         * AVISOS - REFERENCIA
         * 1 => ESTOQUE SEM DEFINICAO DE QUANTIDADE
         * 2 => ESTOQUE DE PRODUTO ABAIXO DO MINIMO
         */
        $this->Produtos = new Produtos;
        $this->Movimentacoes = new Movimentacoes;
        $this->Avisos = new ModelsAviso;
        //CHECAR PRODUTOS COM ESTOQUE ABAIXO DO MINIMO
        $this->dados['avisos'] = $this->Avisos->setCodEmpresa($_SESSION['EMP_COD'])->listarTodos(0);
        //dump($this->dados['avisos']);
        $this->dados['produtos'] = $this->Produtos->setCodEmpresa($_SESSION['EMP_COD'])->setStatus(1)->listarTodosGeral(0);
        
        $qtdA = (is_array($this->dados['avisos']) ? count($this->dados['avisos']) : 0);
        $qtdP = (is_array($this->dados['produtos']) ? count($this->dados['produtos']) : 0);
        $resposta = array(
            'COD'=>'OK',
            'MENSAGEM' => ''
        );
        if ($qtdP) {
            for ($i=0; $i < $qtdP; $i++) { 
                    //checar se existe algum aviso cadastrado
                if ($qtdA) {
                    //checar se foi definido quantidade mínima para o produto a ser verificado
                    if (isset($this->dados['produtos'][$i]['PRO_QTD_MIN']) && $this->dados['produtos'][$i]['PRO_QTD_MIN'] >= $this->dados['produtos'][$i]['PRO_QUANTIDADE']) {
                        //Percorrer os avisos para checar se é do produto pesquisado
                        $pd = false;
                        for ($a=0; $a < $qtdA; $a++) { 
                            //Checar se o status do aviso esta ativado
                            if ($this->dados['avisos'][$a]['AVS_STATUS'] == 1){
                                // Checar se o aviso é do produto pesquisado
                                if($this->dados['avisos'][$a]['PRO_COD'] == $this->dados['produtos'][$i]['PRO_COD']) {
                                    $pd = true;
                                }
                            }
                            //Verificar se o produto foi avisado sobre seu estoque minimo
                            if (!$pd) {
                                //checar se já foi corrigido o problema 
                                if ($this->dados['produtos'][$i]['PRO_QUANTIDADE'] >= $this->dados['produtos'][$i]['PRO_QTD_MIN']) {
                                    $this->Avisos->setCodEmpresa($this->dados['avisos'][$a]['EMP_COD']);
                                    $this->Avisos->setCodigo($this->dados['avisos'][$a]['AVS_COD']);
                                    $this->Avisos->excluir(0);
                                    $resposta = array(
                                        'COD'=>'OK',
                                        'MENSAGEM' => 'Aviso excluído com sucesso!'
                                    );
                                } else {
                                    //JÁ AVISADO
                                    $resposta = array(
                                        'COD'=>'OK',
                                        'MENSAGEM' => 'Status de aviso já realizado com sucesso!'
                                    );
                                }
                            }else {
                                $resposta = array(
                                    'COD'=>'OK',
                                    'MENSAGEM' => 'Produto: '.$this->dados['produtos'][$i]['PRO_COD'].' aviso ja realizado com sucesso!'
                                );
                            }
                        }
                    }else {
                        //Produto está com estoque maior que o minimo
                        $pd = false;
                        for ($a=0; $a < $qtdA; $a++) { 
                            //Checar se o status do aviso esta ativado
                            if ($this->dados['avisos'][$a]['AVS_STATUS'] == 1){
                                // Checar se o aviso é do produto pesquisado
                                if($this->dados['avisos'][$a]['PRO_COD'] == $this->dados['produtos'][$i]['PRO_COD']) {
                                    $pd = true;
                                }
                            }
                            //Verificar se o produto foi avisado sobre seu estoque minimo
                            if ($pd) {
                            //checar se já foi corrigido o problema 
                                if ($this->dados['produtos'][$i]['PRO_QUANTIDADE'] >= $this->dados['produtos'][$i]['PRO_QTD_MIN']) {
                                    //dump($this->dados['avisos'][$a]['EMP_COD']);
                                    //exit;
                                    $this->Avisos->setCodEmpresa($this->dados['avisos'][$a]['EMP_COD']);
                                    $this->Avisos->setCodigo($this->dados['avisos'][$a]['AVS_COD']);
                                    $this->Avisos->excluir(0);
                                    $resposta = array(
                                        'COD'=>'OK',
                                        'MENSAGEM' => 'Aviso excluído com sucesso!'
                                    );
                                } else {
                                    //JÁ AVISADO
                                    $resposta = array(
                                        'COD'=>'OK',
                                        'MENSAGEM' => 'Status de aviso já realizado com sucesso!'
                                    );
                                }
                            }
                        }
                    }
                    
                }else {
                    //NOVO AVISO
                    if (isset($this->dados['produtos'][$i]['PRO_QTD_MIN']) && $this->dados['produtos'][$i]['PRO_QTD_MIN'] >=1 && $this->dados['produtos'][$i]['PRO_QTD_MIN'] >= $this->dados['produtos'][$i]['PRO_QUANTIDADE']) {
                        $aviso = ($this->dados['produtos'][$i]['PRO_QTD_MIN'] >= $this->dados['produtos'][$i]['PRO_QUANTIDADE'])? 'É NECESSÁRIO COMPRAR MAIS DO PRODUTO: <a href="'.DIRPAGE.'produtos/detalhar/'.$this->dados['produtos'][$i]['EMP_COD'].'/'.$this->dados['produtos'][$i]['EST_COD'].'/'.$this->dados['produtos'][$i]['PRO_COD'].'" type="button" class="btn btn-outline-secondary btn-sm d-print-none" title="Detalhar produto"><i class="bi bi-eye"></i> <b>'.$this->dados['produtos'][$i]['PRO_NOME'].'</b></a>' : '';
                        if (!empty($aviso)) {
                            $dados = array(
                                'EMP_COD' => $this->dados['produtos'][$i]['EMP_COD'],
                                'USU_COD' => $_SESSION['USU_COD'],
                                'PRO_COD' => $this->dados['produtos'][$i]['PRO_COD'],
                                'AVS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                'AVS_DT_VISUALIZACAO'=> date('0000-00-00 00:00:00'),  
                                'AVS_DATA' => date('Y-m-d'),
                                'AVS_TIPO' => 2,  
                                'AVS_REFERENCIA' => 2,  
                                'AVS_DESCRICAO' => $aviso,        
                                'AVS_STATUS'=> 1
                            );
                            if($this->Avisos->cadastrar($dados,0)){
                                $resposta = array(
                                    'COD'=>'OK',
                                    'MENSAGEM' => 'Status de avisso checado com sucesso!'
                                );
                           // }else {
                              // $resposta = array(
                                    //'COD'=>'ERRO',
                                    //'MENSAGEM' => 'ERRO NO CADASTRO DO AVISO!'
                             //  );
                            }
                        }
                    }else {
                        $resposta = array(
                            'COD'=>'ERRO',
                            'MENSAGEM' => 'Qtd sem o mínimo informado para comparar!'
                        );
                    }
                }
            }
        }else {
            $resposta = array(
                'COD'=>'ERRO',
                'MENSAGEM' => 'ERRO!'
            );
        }
        echo json_encode($resposta);
    }
}


//VERIFICAR SE A DATA É DIFERENTE DÁ DO AVISO
                                   // Comparando as Datas
                                   /*if (strtotime($this->dados['avisos'][$a]['AVS_DATA']) < strtotime(date('Y-m-d'))) {
                                        $dados = array(
                                            'EMP_COD' => $this->dados['produtos'][$i]['EMP_COD'],
                                            'USU_COD' => $_SESSION['USU_COD'],
                                            'PRO_COD' => $this->dados['produtos'][$i]['PRO_COD'],
                                            'AVS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                            'AVS_DT_VISUALIZACAO'=> date('0000-00-00 00:00:00'),  
                                            'AVS_DATA' => date('Y-m-d'),
                                            'AVS_TIPO' => 2,  
                                            'AVS_REFERENCIA' => 2,  
                                            'AVS_DESCRICAO' => $this->dados['avisos'][$a]['AVS_DESCRICAO'],        
                                            'AVS_STATUS'=> 1
                                        );
                                        
                                        //realizar o cadastro do aviso ao usuário
                                        //if($this->Avisos->cadastrar($dados,0)){
                                         //   $resposta = array(
                                             //   'COD'=>'OK',
                                             //   'MENSAGEM' => 'Status de aviso Recadastrado com sucesso!'
                                          //  );
                                      //  }
                                   } else {*/