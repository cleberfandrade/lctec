<?php
namespace App\Models;

use Core\Model;

class recursosHumanos extends Model
{ 
    private $tabela = 'tb_empresa';
    private $Model = '';
    private $Informacoes = '';
    private $codInformacoes = 1;

    public function __construct()
    {
        $this->Model = new Model();
        $this->Model->setTabela($this->tabela);
    }
    public function setCodInformacoes($codInformacoes)
    {
        $this->codInformacoes = $codInformacoes;
        return $this;
    }
    public static function info()
    {
        $Informacoes = [
            'title' => 'Seja bem-vindo(a) - Igreja Presbiteriana do Brasil de Santo Anastácio-SP',
            'discription' => 'Uma igreja a serviço do reino de Deus',
            'name' => 'Igreja Presbiteriana do Brasil de Santo Anastácio-SP',
            'sigla' => 'IPBSA',
            'favicon' => DIRIMG.'favicon.ico',
            'logo' => DIRIMG.'logo1.png',
            'site' => 'https://ipbsantoanastacio.org.br',
            'logoWhite' => DIRIMG.'logo2.png',
            'banner' => DIRIMG.'b2.jpg'
        ];
        return $Informacoes;
        //return self::listar();
    } 
    public function listar($ver = 0)
    {
        $parametros = "";
        $campos = "*";
        $resultado = $this->Model->exibir($parametros, $campos, $ver = 0, $id = false);
        if ($resultado) {
            return $resultado[0];
        } else {
            return false;
        }
    }
    public function alterar(array $dados, $ver = 0)
    {
        $parametros = " WHERE INF_COD=";
        $this->Model->setParametros($parametros);
        $this->Model->setCodigo($this->codInformacoes);
        $ok = false;
        $ok = $this->Model->alterar($dados, $ver);
        if ($ok) {
            return true;
        } else {
            return false;
        }
    }
}