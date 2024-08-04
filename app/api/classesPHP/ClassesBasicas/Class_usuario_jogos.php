<?php

class Class_usuario_jogos
{
    private $idusuario_jogos;
    private $quant_dezenas;
    private $dezenas_escolhidas;
    private $jogo_vencedor;
    private $loteria_idloteria;
    private $usuario_sistema_idusuario_sistema;
    private $db;
    private $resp;

    // Construtor da classe
    public function __construct($db = "", $idusuario_jogos = "", $quant_dezenas = "", $dezenas_escolhidas = "", $jogo_vencedor = "", $loteria_idloteria = "", $usuario_sistema_idusuario_sistema = "")
    {
        $this->setidusuario_jogos($idusuario_jogos);
        $this->setquant_dezenas($quant_dezenas);
        $this->setdezenas_escolhidas($dezenas_escolhidas);
        $this->setjogo_vencedor($jogo_vencedor);
        $this->setloteria_idloteria($loteria_idloteria);
        $this->setusuario_sistema_idusuario_sistema($usuario_sistema_idusuario_sistema);
        $this->db = $db;
    }

    public function getArrayAtributos()
    {
        return [
            'idusuario_jogos' => $this->getidusuario_jogos(),
            'quant_dezenas' => $this->getquant_dezenas(),
            'dezenas_escolhidas' => $this->getdezenas_escolhidas(),
            'jogo_vencedor' => $this->getjogo_vencedor(),
            'loteria_idloteria' => $this->getloteria_idloteria(),
            'usuario_sistema_idusuario_sistema' => $this->getusuario_sistema_idusuario_sistema(),
        ];
    }

    // Função para carregar um registro do jogo
    public function carregaUsuarioJogos($idusuario_jogos = false, $extra = false)
    {
        $sql = "SELECT * FROM usuario_jogos WHERE idusuario_jogos = '" . $idusuario_jogos . "'";
        if ($extra) {
            $sql = "SELECT * FROM usuario_jogos " . $extra;
        }
        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            while ($obj = $this->db->fetchObj()) {
                $this->setidusuario_jogos($obj->idusuario_jogos);
                $this->setquant_dezenas($obj->quant_dezenas);
                $this->setdezenas_escolhidas($obj->dezenas_escolhidas);
                $this->setjogo_vencedor($obj->jogo_vencedor);
                $this->setloteria_idloteria($obj->loteria_idloteria);
                $this->setusuario_sistema_idusuario_sistema($obj->usuario_sistema_idusuario_sistema);
                return true;
            }
        }
        return false;
    }

    public function insereUsuarioJogos()
    {
        $dados = '';
        $dados .= "'" . $this->getquant_dezenas() . "',";
        $dados .= "'" . $this->getdezenas_escolhidas() . "',";
        $dados .= "'" . $this->getloteria_idloteria() . "',";
        $dados .= "'" . $this->getusuario_sistema_idusuario_sistema() . "'";

        $sql = "INSERT INTO usuario_jogos (
                        quant_dezenas, 
                        dezenas_escolhidas, 
                        loteria_idloteria, 
                        usuario_sistema_idusuario_sistema
                    ) 
                VALUES (" . $dados . ")";
        if ($this->db->query($sql)) {
            $this->setidusuario_jogos($this->db->ultimoId());
            return true;
        } else {
            return false;
        }
    }

    
    

    public function listaUsuarioJogos($extra = "")
    {
        $sql = "SELECT * FROM usuario_jogos " . $extra;
        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            while ($obj = $this->db->fetchObj()) {
                $arr[] = new Class_usuario_jogos(
                    $this->db,
                    $obj->idusuario_jogos,
                    $obj->quant_dezenas,
                    $obj->dezenas_escolhidas,
                    $obj->jogo_vencedor,
                    $obj->loteria_idloteria,
                    $obj->usuario_sistema_idusuario_sistema
                );
            }
            $this->setResp($arr);
            return true;
        } else {
            return false;
        }
    }

    public function retornaQuantidadeRegistrosUsuarioJogos($extra = false)
    {
        $sql = "SELECT * FROM usuario_jogos " . $extra;

        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            return $this->db->quantidadeRegistros();
        } else {
            return false;
        }
    }

    /**
     * Funções get para todos os campos
     */
    public function getResp()
    {
        return $this->resp;
    }

    public function getidusuario_jogos()
    {
        return $this->idusuario_jogos;
    }

    public function getquant_dezenas()
    {
        return $this->quant_dezenas;
    }

    public function getdezenas_escolhidas()
    {
        return $this->dezenas_escolhidas;
    }

    public function getjogo_vencedor()
    {
        return $this->jogo_vencedor;
    }

    public function getloteria_idloteria()
    {
        return $this->loteria_idloteria;
    }

    public function getusuario_sistema_idusuario_sistema()
    {
        return $this->usuario_sistema_idusuario_sistema;
    }

    /**
     * Funções set para todos os campos
     */
    public function setResp($value)
    {
        $this->resp = $value;
    }

    public function setidusuario_jogos($idusuario_jogos)
    {
        $this->idusuario_jogos = $idusuario_jogos;
    }

    public function setquant_dezenas($quant_dezenas)
    {
        $this->quant_dezenas = $quant_dezenas;
    }

    public function setdezenas_escolhidas($dezenas_escolhidas)
    {
        $this->dezenas_escolhidas = $dezenas_escolhidas;
    }

    public function setjogo_vencedor($jogo_vencedor)
    {
        $this->jogo_vencedor = $jogo_vencedor;
    }

    public function setloteria_idloteria($loteria_idloteria)
    {
        $this->loteria_idloteria = $loteria_idloteria;
    }

    public function setusuario_sistema_idusuario_sistema($usuario_sistema_idusuario_sistema)
    {
        $this->usuario_sistema_idusuario_sistema = $usuario_sistema_idusuario_sistema;
    }
}
