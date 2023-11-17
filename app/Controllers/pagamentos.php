<?php
namespace App\Controllers;

use Core\View;
use Libraries\Check;
use Libraries\Sessao;
use Libraries\Url;

class pagamentos extends View
{
    private $dados = [];
    private $link,$Financas,$Check,$Usuarios,$UsuariosEmpresa;
}