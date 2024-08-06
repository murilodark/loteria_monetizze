<?php


/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of classesErros
 *
 * @author darksp
 */
if (basename($_SERVER["PHP_SELF"]) == basename(__FILE__)) {
    header("location:index.php");
}

class DB {
    // Nome do serviço definido no docker-compose.yml
    private static $server = 'db'; 
    private static $usuario = 'username';
    private static $senha = 'userpass';
    private static $banco = 'dbloteria';
    
    private $conn;
    private $query;
    private static $mysqli;
    private static $sessao;
    private $erro = array();

    // Construtor da classe já é iniciado uma conexão automaticamente ao ser instanciado
    public function __construct() {
        self::$sessao = new Class_Sessao();
        $this->conexao();
    }

    // Cria uma conexão com o MySQL
    private static function conexao() {
        // Verifica se ainda não existe uma instância do mysqli, se não existir cria uma nova
        if (empty(self::$mysqli)) {
            self::$mysqli = new mysqli(self::$server, self::$usuario, self::$senha, self::$banco);

            // Verifica se houve erro na conexão
            if (self::$mysqli->connect_error) {
                die('Connect Error (' . self::$mysqli->connect_errno . ') ' . self::$mysqli->connect_error);
            }
        }
    }

    public function exitConexao() {
        self::$mysqli->close();
    }

    public function transacao() {
        self::$mysqli->autocommit(FALSE);
    }

    public function commit() {
        self::$mysqli->commit();
    }

    public function rollback() {
        self::$mysqli->rollback();
    }

    // Faz uma query
    public function query($sql) {
        if ($this->query = self::$mysqli->query($sql)) {
            return true;
        }
        $this->erro[] = self::$mysqli->error;
        return false;
    }

    // Retorna o fetchObject da última consulta
    public function fetchObj() {
        return $this->query->fetch_object();
    }

    // Retorna o ID do insert referido
    public function ultimoId() {
        return self::$mysqli->insert_id;
    }

    // Retorna a quantidade de registros encontrados
    public function quantidadeRegistros() {
        return self::$mysqli->affected_rows;
    }

    // Mostra mensagem de erro na query
    public function getErro() {
        if (empty($this->erro)) {
            return false;
        } else {
            return $this->erro;
        }
    }
}

?>