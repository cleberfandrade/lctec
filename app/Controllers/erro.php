<?php
namespace App\Controllers;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;


class erro extends View
{
    private $dados = [];
    public $link;
    public function __construct()
    {
        $this->dados['title'] = 'ERRO 404 !! PÁGINA NÃO ENCONTRADA';
        if(Sessao::checarSessao()){
            $this->link[0] = ['link'=> 'admin','nome' => 'PAINEL ADMINISTRATIVO'];
        }else {
            $this->link[0] = ['link'=> 'site','nome' => 'LC TEC | ACESSO ADMINISTRATIVO'];
        }
    }
    public function index()
    { 
        $this->dados["erro 404"];
        $this->render('site/erro', $this->dados);
    }
}