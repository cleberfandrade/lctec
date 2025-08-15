<?php
 namespace Libraries;

 class Check
 {
    private $string,$link;
     
    public function setLink($link)
    {
        $this->link = $link;
        return $this;
    }
    public function getLink()
    {
        return $this->link ;
    }
    public static function checarNome($string)
    {
        if(preg_match('',$string)){
            return true;
        }else{
            return false;
        }
    }
    public static function checarEmail($string)
    {
        if(filter_var($string,FILTER_VALIDATE_EMAIL)){
            return true;
        }else{
            return false;
        }
    }
    public function breadcrumb($textcolor = "text-dark")
    {
        $link = self::getLink();
    
        $qtd = (is_array($link) ? count($link) : 0);
       
        $printar = '<nav aria-label="breadcrumb mx-auto align-middle '.$textcolor.'"><ol class="breadcrumb align-middle">';
        for ($i=0; $i < $qtd; $i++) { 
            $printar .=' <li class="breadcrumb-item ';
            if ($i+1 < $qtd) {
                $ac = '';
                $ar ='';
                $lk = 'href="'.DIRPAGE.$link[$i]['link'].'"';
                $printar.=''.$ac.'"'.$ar.'><a class=" btn-sm align-middle text-decoration-none '.$textcolor.'" '.$lk.' title="VOLTAR À '.$link[$i]['nome'].'">'.$link[$i]['nome'].'</a></li>';
            } else {
                $ac = 'active';
                $ar ='aria-current="page"';
                $lk = '';
                $printar.=''.$ac.'"'.$ar.'><b class="'.$textcolor.' fw-bold" title="VOCÊ ESTÁ AQUI">'.$link[$i]['nome'].'</b></li>';
            }
            
            // <li class="breadcrumb-item active" aria-current="page">$dados</li>
        }
        $printar.='</ol></nav>';
        return $printar;
    }

    public static function checarString($string)
    {
        $string = strip_tags(trim($string));
        $string = htmlspecialchars($string,ENT_QUOTES);
        //$string = htmlentities($string, ENT_QUOTES | ENT_IGNORE, "UTF-8");
        return $string;
    }
    public function codificarSenha($string)
    {
        $codificada  = password_hash($string, PASSWORD_DEFAULT);
        return $codificada;
    }
    public function getString()
    {
        return $this->string;
    }
    public function setString($string)
    {
        $this->string = $string;

        return $this;
    }
    public function token($tamanho=10, $id="", $up=false) 
    {
        $characters = $id.'abcdefghijklmnopqrstuvwxyz0123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $tamanho; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        if($up === true) {
            return strtoupper($id.$randomString);
        } else {
            return $id.$randomString;
        }
    }
    public function onlyNumbers($str)
    {
        $str = preg_replace('/\D/', '', $str);
        return $str;
    }
    public function formatMoneyDb($str)
    {
        $str = number_format((self::onlyNumbers($str) / 100), 2);
        $str = str_replace(',', '', $str);
        return $str;
    }
 }