<?php
namespace Core;
class View
{
    private $pagina = 'home';
    private $dados = [];
    
    public function render($pagina, array $dados)
    {
        $this->dados = $dados;
        if (file_exists('app/Views/' . $pagina . '.phtml')) {
            include_once 'app/Views/' . $pagina . '.phtml';
        } else {
            if (file_exists('app/Views/' . $pagina . '.php')) {
                include_once 'app/Views/' . $pagina . '.php';
            } else {
                $pagina = explode('/', $pagina);
                if (isset($pagina[2])) {
                    $texto = $pagina[2];
                } else {
                    $texto = '';
                }
                $this->dados['mensagemErro'] =  "ERRO 404, PÁGINA {$texto} NÃO ENCONTRADA!";
                $pagina = (string) 'erro';
                include_once 'app/Views/site/' . $pagina . '.phtml';
            }
        }
    }
    public function pages()
    {
        include_once 'app/Views/adm/storage/pages/' . $pagina . '.phtml';
    }
}