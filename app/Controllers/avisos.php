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
        $this->dados['produtos'] = $this->Produtos->setCodEmpresa($_SESSION['EMP_COD'])->setStatus(1)->listarTodosGeral(0);
        $qtdA = (is_array($this->dados['avisos']) ? count($this->dados['avisos']) : 0);
        $qtdP = (is_array($this->dados['produtos']) ? count($this->dados['produtos']) : 0);
    
        if ($qtdP) {
            for ($i=0; $i < $qtdP; $i++) { 
                    //checar se existe algum aviso cadastrado
                if ($qtdA) {
                    for ($a=0; $a < $qtdA; $a++) { 
                        //checar se foi definido quantidade mínima para o produto a ser verificado
                        if (isset($this->dados['produtos'][$i]['PRO_QTD_MIN'])) {
                            //checar se já foi realizado o aviso sobre o produto a ser verificado
                            if ($this->dados['avisos'][$a]['AVS_STATUS'] == 1 && $this->dados['avisos'][$a]['PRO_COD'] == $this->dados['produtos'][$i]['PRO_COD']) {
                                //JA AVISOU
                                //checar se já foi corrigido o problema 
                                if ($this->dados['produtos'][$i]['PRO_QUANTIDADE'] >= $this->dados['produtos'][$i]['PRO_QTD_MIN']) {
                                    $this->Avisos->setCodEmpresa($this->dados['avisos'][$a]['EMP_COD'])->setCodigo($this->dados['avisos'][$a]['AVS_COD'])->excluir(0);
                                    $resposta = array(
                                        'COD'=>'OK',
                                        'MENSAGEM' => 'Aviso excluído com sucesso!'
                                    );
                                } else {
                                   //VERIFICAR SE A DATA É DIFERENTE DÁ DO AVISO
                                   // Comparando as Datas
                                   if (strtotime($this->dados['avisos'][$a]['AVS_DT_CADASTRO']) < strtotime(date('Y-m-d H:i:s'))) {
                                        $dados = array(
                                            'EMP_COD' => $this->dados['produtos'][$i]['EMP_COD'],
                                            'USU_COD' => $_SESSION['USU_COD'],
                                            'PRO_COD' => $this->dados['produtos'][$i]['PRO_COD'],
                                            'AVS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                            'AVS_DT_VISUALIZACAO'=> date('0000-00-00 00:00:00'),  
                                            'AVS_TIPO' => 2,  
                                            'AVS_REFERENCIA' => 2,  
                                            'AVS_DESCRICAO' => $this->dados['aviso'][$a]['AVS_DESCRICAO'],        
                                            'AVS_STATUS'=> 1
                                        );
                                        //realizar o cadastro do aviso ao usuário
                                        if($this->Avisos->cadastrar($dados,0)){
                                            $resposta = array(
                                                'COD'=>'OK',
                                                'MENSAGEM' => 'Status de aviso Recadastrado com sucesso!'
                                            );
                                        }
                                   } else {
                                    //JÁ AVISADO
                                    $resposta = array(
                                        'COD'=>'OK',
                                        'MENSAGEM' => 'Status de aviso já realizado com sucesso!'
                                    );
                                   }
                                }
                                
                            }else {
                                //NAO AVISOU
                                $aviso = ($this->dados['produtos'][$i]['PRO_QTD_MIN']>= $this->dados['produtos'][$i]['PRO_QUANTIDADE'])? 'É NECESSÁRIO COMPRAR MAIS DESTE PRODUTO' : '';
                               //checar se o produto está com estoque abaixo do mínimo definido
                                if (!empty($aviso)) {
                                    $dados = array(
                                        'EMP_COD' => $this->dados['produtos'][$i]['EMP_COD'],
                                        'USU_COD' => $_SESSION['USU_COD'],
                                        'PRO_COD' => $this->dados['produtos'][$i]['PRO_COD'],
                                        'AVS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                        'AVS_DT_VISUALIZACAO'=> date('0000-00-00 00:00:00'),  
                                        'AVS_TIPO' => 2,  
                                        'AVS_REFERENCIA' => 2,  
                                        'AVS_DESCRICAO' => $aviso,        
                                        'AVS_STATUS'=> 1
                                    );
                                    //realizar o cadastro do aviso ao usuário
                                    if($this->Avisos->cadastrar($dados,0)){
                                        $resposta = array(
                                            'COD'=>'OK',
                                            'MENSAGEM' => 'Status de aviso checado com sucesso!'
                                        );
                                    }
                                }else {
                                    $resposta = array(
                                        'COD'=>'OK',
                                        'MENSAGEM' => 'Status de aviso checado com sucesso!'
                                    );
                                }
                            } 
                        }
                    }
                }else {
                    //NOVO AVISO
                    if (isset($this->dados['produtos'][$i]['PRO_QTD_MIN'])) {
                        $aviso = ($this->dados['produtos'][$i]['PRO_QTD_MIN'] >= $this->dados['produtos'][$i]['PRO_QUANTIDADE'])? 'É NECESSÁRIO COMPRAR MAIS '.$this->dados['produtos'][$i]['PRO_NOME'] : '';
                        if (!empty($aviso)) {
                            $dados = array(
                                'EMP_COD' => $this->dados['produtos'][$i]['EMP_COD'],
                                'USU_COD' => $_SESSION['USU_COD'],
                                'PRO_COD' => $this->dados['produtos'][$i]['PRO_COD'],
                                'AVS_DT_CADASTRO'=> date('Y-m-d H:i:s'),
                                'AVS_DT_VISUALIZACAO'=> date('0000-00-00 00:00:00'),  
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
                            }else {
                                $resposta = array(
                                    'COD'=>'ERRO',
                                    'MENSAGEM' => 'ERRO NO CADASTRO DO AVISO!'
                                );
                            }
                        }
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