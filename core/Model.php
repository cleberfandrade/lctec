<?php
namespace Core;

use Core\Connection;

class Model extends Connection
{
    private $database, $tabela,$parametros,$campos,$codigoId,$dados;
    public function __construct()
    {
        $db = new Connection;
        $this->database = $db->connect();
        return $this->database;
    }
    public function setTabela($tabela)
    {
        $this->tabela = $tabela;
        return $this;
    }
    public function setCodigo($cod)
    {
        $this->codigoId = $cod;
        return $this;
    }
    public function setParametros($parametros)
    {
        $this->parametros = $parametros;
        return $this;
    }
    public function setCampos($campos)
    {
        $this->campos = $campos;
        return $this;
    }
    public function criarCampos($campos)
    {
        return implode(',', array_keys($campos));
    }
    public function criarValores($valores)
    {
        return ':' . implode(',:', array_keys($valores));
    }
    public function ligarCriacaoParametros($dados)
    {
        $values = $this->criarValores($dados);
        $parametros = array_combine(explode(',', $values), array_values($dados));
        return $parametros;
    }
    public function criarParametro()
    {
        foreach ($this->dados as $key => $valores) {
            $campos[] = "{$key} ='{$valores}'";
        }
        if (isset($campos) && count($campos) >= 1 && !empty($campos) && $campos != null) {
            $parametros = implode(' AND ', $campos);
            $this->parametros = ($parametros) ? " WHERE {$parametros}" : null;
        } else {
            $this->parametros = "";
        }
        $this->setParametros($this->parametros);
        return  $this->parametros;
    }
    public function cadastrar(array $dados, $ver = 0)
    {
        $campos =  $this->criarCampos($dados);
        $valores = $this->criarValores($dados);
        $query = "INSERT INTO $this->tabela ($campos) VALUES ($valores)";
        $pdo = $this->database->prepare($query);
        $parametros = $this->ligarCriacaoParametros($dados);
        if ($ver) {
            echo "<hr>";
            echo __FUNCTION__ . ": ";
            dump($query);
            echo "<br/><hr>";
            return false;
        } else {
            try {
                $pdo->execute($parametros);
                $id = $this->database->lastInsertId();
                return $id;
            } catch (\PDOException $e) {
                dump($e->getMessage());
            }
        }
    }
    public function alterar(array $dados, $ver = 0)
    {
        foreach ($dados as $inds => $vals) {
            $campos[] = "{$inds} ='{$vals}'";
        }
        $campos = implode(",", $campos);
        $query = "UPDATE `$this->tabela` SET {$campos} {$this->parametros} $this->codigoId";
        $pdo = $this->database->prepare($query);
        if ($ver) {
            echo "<hr>";
            echo __FUNCTION__ . ": ";
            dump($query);
            echo "<br/><hr>";
            return false;
        } else {
            try {
                if ($pdo->execute()) {
                    return true;
                    exit;
                } else {
                    return false;
                    exit;
                }
                exit;
            } catch (\PDOException $e) {
                dump($e->getMessage());
            }
        }
    }
    public function deletar($ver = 0)
    {
        $query = "DELETE FROM `$this->tabela` {$this->parametros}{$this->codigoId}";
        $pdo = $this->database->prepare($query);
        if ($ver) {
            echo "<hr>";
            echo __FUNCTION__ . ": ";
            dump($query);
            echo "<hr><br/>";
            return false;
        } else {
            try {
                $pdo->execute();
                return $pdo->rowCount();
            } catch (\PDOException $e) {
                dump($e->getMessage());
            }
        }
    }
    public function exibir($parametros = null, $campos = "*", $ver = 0, $id = false)
    {
        $parametros = ($parametros) ? " {$parametros}" : null;
        $query      = "SELECT {$campos} FROM {$this->tabela}{$parametros}";
        $pdo        = $this->database->prepare($query);
        $dados = array();
        if ($ver) {
            echo "<hr>";
            echo __FUNCTION__ . ": ";
            dump($query);
            echo "<br/><hr>";
            return false;
        } else {
            try {
                $dados = array();
                $pdo->execute();
                while ($res = $pdo->fetch(\PDO::FETCH_ASSOC)) {
                    $dados[] = $res;
                }
                return $dados;
            } catch (\PDOException $e) {
                dump($e->getMessage());
            }
        }
    }
}