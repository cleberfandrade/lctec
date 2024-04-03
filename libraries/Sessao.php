<?php
namespace Libraries;

use Libraries\Url;

class Sessao
{
    public static function alert($nome, $texto = null,$classe = null)
    {
        if(!empty($nome)){
            if(!empty($texto) && empty($_SESSION[$nome])){
                if(!empty($_SESSION[$nome])){
                    unset($_SESSION[$nome]);
                }
                $_SESSION[$nome] = $texto;
                $_SESSION[$nome.'classe'] = $classe;
            }elseif(!empty($_SESSION[$nome]) && empty($texto)){
                $classe = !empty($_SESSION[$nome.'classe']) ? $_SESSION[$nome.'classe'] : 'alert-success';
                echo '<div id="myAlert" class="text-center '.$classe.'">'.$_SESSION[$nome].'</div>';
                echo '<script>
                    setTimeout(function () {
                        document.getElementById("myAlert").style.display = "none";
                    }, 5000);
                    function hide(){
                        document.getElementById("myAlert").style.display = "none";
                    }
                </script>';
                unset($_SESSION[$nome]);
                unset($_SESSION[$nome.'classe']);
            }
        }
    }
    public static function checarSessao()
    {
        if(isset($_SESSION['USU_COD'])){
           return true;
        }else{
            return false;
        }
    }
    public static function logado()
    {
        if(isset($_SESSION['USU_COD'])){
            Url::redirecionar('admin');
        }
    }
    public static function naoLogado()
    {
        if(!isset($_SESSION['USU_COD'])){
            Url::redirecionar('login');
        }
    }
    public static function logadoSistema()
    {
        if(isset($_SESSION['USU_COD']) && !isset($_SESSION['USU_NIVEL']) && $_SESSION['USU_NIVEL'] >= 30){
            Url::redirecionar('lctec');
        }
    }
    public static function naoLogadoSistema()
    {
        
        if(!isset($_SESSION['USU_COD']) && !isset($_SESSION['USU_NIVEL']) && $_SESSION['USU_NIVEL'] >= 30){
            Url::redirecionar('login');
            dump("ok");
        }
    }
    public static function criarSessao(array $dados = [])
    {
        foreach ($dados as $key => $value) {
            $_SESSION[$key] = $value;
        }
        $valor = isset($_SESSION) ? true : false;
        if ($valor) {
            session_regenerate_id();
            return true;
        } else {
            return false;
        }
    }
}