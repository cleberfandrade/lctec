<?php
namespace App\Helpers;

class Upload
{
    private $arquivo, $imagem, $pasta;
    
    public function getArquivo()
    {
        return $this->arquivo;
    }
    public function setArquivo($arquivo)
    {
        $this->arquivo = $arquivo;

        return $this;
    }
    public function getImagem()
    {
        return $this->imagem;
    }
    public function setImagem($imagem)
    {
        $this->imagem = $imagem;

        return $this;
    }
    public function getPasta()
    {
        return $this->pasta;
    }
    public function setPasta($pasta)
    {
        $this->pasta = $pasta;

        return $this;
    }
    public function enviarArquivo()
    {

    }
    public function enviarImgem()
    {

    }
    public function checarArquivoDiretorio()
    {

    }
    public function deletarArquivo()
    {

    }
    public function criarPasta()
    {

    }
    public function deletarPasta()
    {
        $files = array_diff(scandir($this->pasta), array('.','..')); 
        foreach ($files as $file) { 
          (is_dir("{$this->pasta}/$file")) ? $this->deletarPasta("{$this->pasta}/$file") : unlink("{$this->pasta}/$file"); 
        } 
        return rmdir($this->pasta); 
    }
    public function renomarArquivo()
    {
        rename("{$this->pasta}/{$this->arquivo}", "{$this->pasta}/{$this->nome}");
    }
    public function moverArquivo()
    {
       
    }
    public function renomarPasta()
    {
        rename("{$this->pasta}", "{$this->nome}");
    }
    public function moverPasta()
    {

    }
}