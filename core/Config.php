<?php

namespace Core;

// Iniciar a sessão
if (!isset($_SESSION)) {
    session_start();
}
ob_start();

ini_set('display_errors', 1);
date_default_timezone_set("America/Sao_Paulo");

if (!empty($_SERVER['HTTPS'])) {
    $pastaInterna = "";
}else{
    $pastaInterna = "lctec/";
}
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
define('EMAILADM', 'contato@lctec.com.br');
define('EMAILSUPORTE','suporte@lctec.com.br');
#Acesso ao banco de dados
define ('DB',[
    "DRIVER" => "mysql",
    "HOSTNAME" => "localhost",
    "DATABASE"=> "u693937037_lctec",
    "USERNAME" => "u693937037_administrador",
    "PASSWORD"=> "Cf@10100801"
]);
