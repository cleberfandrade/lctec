<?php
namespace Libraries;

class Util
{
    private $data;

    public function setData($d){
        $this->data = $d;
        return $this;
    }

    public static function mesUppercase()
    {
        $vt = array();
        $vt[1] = "JANEIRO";
        $vt[2] = "FEVEREIRO";
        $vt[3] = "MAR&Ccedil;O";
        $vt[4] = "ABRIL";
        $vt[5] = "MAIO";
        $vt[6] = "JUNHO";
        $vt[7] = "JULHO";
        $vt[8] = "AGOSTO";
        $vt[9] = "SETEMBRO";
        $vt[10] = "OUTUBRO";
        $vt[11] = "NOVEMBRO";
        $vt[12] = "DEZEMBRO";
        return $vt;
    }
    public static function mesLowercase($x)
    {
        switch ($x) {
            case 1:$vt = "Janeiro";break;
            case 2:$vt = "Fevereiro";break;
            case 3:$vt = "Março";break;
            case 4:$vt = "Abril";break;
            case 5:$vt = "Maio";break;
            case 6:$vt = "Junho";break;
            case 7:$vt = "Julho";break;
            case 8:$vt = "Agosto";break;
            case 9:$vt = "Setembro";break;
            case 10:$vt = "Outubro";break;
            case 11:$vt = "Novembro";break;
            case 12:$vt = "Dezembro";break;
        }
        return $vt;
    }
    public static function diaSemana($numero)
    {
        switch ($numero) {
            case "0":
                $diasemana = "Segunda";
                break;
            case "1":
                $diasemana = "Terça";
                break;
            case "2":
                $diasemana = "Quarta";
                break;
            case "3":
                $diasemana = "Quinta";
                break;
            case "4":
                $diasemana = "Sexta";
                break;
            case "5":
                $diasemana = "Sábado";
                break;
            case "6":
                $diasemana = "Domingo";
                break;
        }
        return "$diasemana";
    }
    public function get()
    {
        return stripslashes(htmlentities($this->data));
    }
    public static function retMes($time)
    {
        return date('Y-m-d',strtotime("-1 month",$time));
    }
     static function avcMes($time)
    {
        return date('Y-m-d',strtotime("+1 month",$time));
    }
    public function getMesAtual()
    {
        if (isset($this->data) && $this->data != '') {
            extract(date_parse_from_format("Y-m-d", $this->data));
            $mesAtual = strtotime("{$year}-{$month}-1");
        }else {
            $mesAtual = strtotime(date("Y-m-1"));
        }
        return $mesAtual;
    }   
}