<?php
namespace App\Controllers;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class movimentacoes extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa;
}