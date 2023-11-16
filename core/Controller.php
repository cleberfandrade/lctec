<?php
namespace Core;

class Controller
{
    private $controller = 'login';
    private $method = 'index';
    private $params = [];
    private $url = '';
    private $rota = '';

    
    public function __construct()
    {
        $this->url = filter_input(INPUT_GET, 'url', FILTER_DEFAULT);
        $this->rota = explode('/', $this->url);
        
        if (isset($this->rota[0]) && !empty($this->rota[0])) {
            $this->controller = $this->rota[0];
        } 
        if (isset($this->rota[1]) && !empty($this->rota[1])) {
            $this->method = $this->rota[1];
        } 
        if (isset($this->rota[2])) {
            $this->params = $this->rota[2];
        } 
       
    }
    public function getUrl()
    {
        return $this->url;
    }
    public function getController()
    {
        return $this->controller;
    }
    public function getMethod()
    {
        return $this->method;
    }
    public function getParams()
    {
        return $this->params;
    }
    public function carregar()
    {
        $classe = '\\App\\Controllers\\' . $this->controller;
        if (class_exists($classe)) {
            $classeCarregar = new $classe;
            $metodo = $this->method;
            $classeCarregar->$metodo();
        } else {
            $classe = '\\App\\Controllers\\erro';
            $classeCarregar = new $classe;
            $classeCarregar->index();           
        }
    }
}
