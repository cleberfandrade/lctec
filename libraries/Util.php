<?php
namespace Libraries;

class Util
{
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
    public static function mesLowercase()
    {
        $vt = array();
        $vt[1] = "Janeiro";
        $vt[2] = "Fevereico";
        $vt[3] = "Março";
        $vt[4] = "Abril";
        $vt[5] = "Maio";
        $vt[6] = "Junho";
        $vt[7] = "Julho";
        $vt[8] = "Agosto";
        $vt[9] = "Setembro";
        $vt[10] = "Outubro";
        $vt[11] = "Novembro";
        $vt[12] = "Dezembro";
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
}