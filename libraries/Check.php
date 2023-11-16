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
     public function breadcrumb()
     {
        $link = self::getLink();
    
        $qtd = (is_array($link) ? count($link) : 0);
        $printar = '<nav aria-label="breadcrumb"><ol class="breadcrumb">';
        for ($i=0; $i < $qtd; $i++) { 
            $printar .=' <li class="breadcrumb-item ';
            if ($i+1 < $qtd) {
                $ac = '';
                $ar ='';
                $lk = 'href="'.DIRPAGE.$link[$i]['link'].'"';
                $printar.=''.$ac.'"'.$ar.'><a class="text-decoration-none text-dark" '.$lk.' title="VOLTAR À '.$link[$i]['nome'].'">'.$link[$i]['nome'].'</a></li>';
            } else {
                $ac = 'active';
                $ar ='aria-current="page"';
                $lk = '';
                $printar.=''.$ac.'"'.$ar.'><b class="text-success" title="VOCÊ ESTÁ AQUI">'.$link[$i]['nome'].'</b></li>';
            }
            
            // <li class="breadcrumb-item active" aria-current="page">$dados</li>
        }
        $printar.='</ol></nav>';
        return $printar;
     }

     public static function checarString($string)
     {
        $string = strip_tags(trim($string));
        $string = htmlentities($string);
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
    
 }