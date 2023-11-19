<?php

namespace Core;

// Iniciar a sessão
if (!isset($_SESSION)) {
    session_start();
}
ob_start();

ini_set('display_errors', 0);
date_default_timezone_set("America/Sao_Paulo");


$pastaInterna = "lctec/";
define('DIRPAGE', "http://{$_SERVER['HTTP_HOST']}/{$pastaInterna}");

#Diretorios Publicos
define('DIRIMG', DIRPAGE . "public/images/");
define('DIRICON', DIRPAGE . "public/images/icones/");
define('DIRMOVIES', DIRPAGE . "public/movies/");
define('DIRSOUNDS', DIRPAGE . "public/sounds/");
define('DIRDOCS', DIRPAGE . "public/docs/");
define('DIRPAGES', DIRPAGE . "public/pages/");
define('DIRPUBLIC', DIRPAGE . "public/");
define('DIRDESIGN', DIRPAGE . "public/design/");
define('DIRCSS', DIRPAGE . "public/css/");
define('DIRFONT', DIRPAGE . "public/fonts/");
define('DIRJS', DIRPAGE . "public/js/");
define('DIRADMINC', DIRPAGE . "app/Views/admin/inc/");
define('CONTROLLER', 'site');
define('CONTROLLERERRO', 'site/error');
define('EMAILADM', 'contato@lctech.com.br');
#Acesso ao banco de dados
define ('DB',[
    "DRIVER" => "mysql",
    "HOSTNAME" => "localhost",
    "DATABASE"=> "lctech",
    "USERNAME" => "root",
    "PASSWORD"=> ""
]);
