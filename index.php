<?php
use Core\Controller;

require_once __DIR__.'/core/Config.php';
require_once __DIR__.'/vendor/autoload.php';

$Router = new Controller();
$Router->carregar();