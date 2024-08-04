<?php

class Class_loteria
{
    private $idloteria;
    private $data_cadastro;
    private $data_sorteio;
    private $nome_loteria;
    private $dezenas_sorteadas;
    private $status_loteria;
    private $usuario_sistema_cadastro;
    private $usuario_sistema_sorteio;
    private $resp;
    private $db;
    private $Class_usuario_sistema_cadastro;
    private $Class_usuario_sistema_sorteio;
    private $Class_usuario_jogos;

    // Construtor da classe
    public function __construct($db = "", $idloteria = "", $data_cadastro = "", $data_sorteio = "", $nome_loteria = "", $dezenas_sorteadas = "", $status_loteria = "", $usuario_sistema_cadastro = "", $usuario_sistema_sorteio = "")
    {
        $this->setidloteria($idloteria);
        $this->setdata_cadastro($data_cadastro);
        $this->setdata_sorteio($data_sorteio);
        $this->setnome_loteria($nome_loteria);
        $this->setdezenas_sorteadas($dezenas_sorteadas);
        $this->setstatus_loteria($status_loteria);
        $this->setusuario_sistema_cadastro($usuario_sistema_cadastro);
        $this->setusuario_sistema_sorteio($usuario_sistema_sorteio);
        $this->db = $db;
        $this->Class_usuario_sistema_cadastro = new Class_usuario_sistema($this->db);
        $this->Class_usuario_sistema_sorteio = new Class_usuario_sistema($this->db);
        $this->Class_usuario_jogos = new Class_usuario_jogos($this->db);
    }

    public function getArrayAtributos()
    {
        return [
            'idloteria' => $this->getidloteria(),
            'data_cadastro' => $this->getdata_cadastro(),
            'data_sorteio' => $this->getdata_sorteio(),
            'nome_loteria' => $this->getnome_loteria(),
            'dezenas_sorteadas' => $this->getdezenas_sorteadas(),
            'status_loteria' => $this->getstatus_loteria(),
            'usuario_sistema_cadastro' => $this->Class_usuario_sistema_cadastro->getnome_usuario(),
            'usuario_sistema_sorteio' =>  $this->Class_usuario_sistema_sorteio->getnome_usuario(),
        ];
    }

    // Função para carregar um registro da loteria
    public function carregaLoteria($idloteria = false, $extra = false)
    {
        $sql = "SELECT * FROM loteria WHERE idloteria = '" . $idloteria . "'";
        if ($extra) {
            $sql = "SELECT * FROM loteria " . $extra;
        }
        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            while ($obj = $this->db->fetchObj()) {
                $this->setidloteria($obj->idloteria);
                $this->setdata_cadastro($obj->data_cadastro);
                $this->setdata_sorteio($obj->data_sorteio);
                $this->setnome_loteria($obj->nome_loteria);
                $this->setdezenas_sorteadas($obj->dezenas_sorteadas);
                $this->setstatus_loteria($obj->status_loteria);
                $this->setusuario_sistema_cadastro($obj->usuario_sistema_cadastro);
                $this->setusuario_sistema_sorteio($obj->usuario_sistema_sorteio);
                $this->Class_usuario_sistema_cadastro->carregausuario_sistema($this->getusuario_sistema_cadastro());
                if ($this->getusuario_sistema_sorteio()) {
                    $this->Class_usuario_sistema_sorteio->carregausuario_sistema($this->getusuario_sistema_sorteio());
                }
                return true;
            }
        }
        return false;
    }

    public function insereLoteria()
    {
        $dados = '';
        $dados .= "'" . $this->getdata_cadastro() . "',";
        $dados .= "'" . $this->getnome_loteria() . "',";
        $dados .= "'" . $this->getdata_sorteio() . "',";
        $dados .= "'" . $this->getstatus_loteria() . "',";
        $dados .= "'" . $this->getusuario_sistema_cadastro() . "'";

        $sql = "INSERT INTO loteria (
                        data_cadastro, 
                        nome_loteria, 
                        data_sorteio, 
                        status_loteria, 
                        usuario_sistema_cadastro
                    ) 
                VALUES (" . $dados . ")";
        if ($this->db->query($sql)) {
            $this->setidloteria($this->db->ultimoId());
            return true;
        } else {
            return false;
        }
    }

    public function atualizaLoteria()
    {
        $sql = "UPDATE loteria SET 
                    data_cadastro = '" . $this->getdata_cadastro() . "',
                    data_sorteio = '" . $this->getdata_sorteio() . "',
                    nome_loteria = '" . $this->getnome_loteria() . "',
                    dezenas_sorteadas = '" . $this->getdezenas_sorteadas() . "',
                    status_loteria = '" . $this->getstatus_loteria() . "',
                    usuario_sistema_cadastro = '" . $this->getusuario_sistema_cadastro() . "',
                    usuario_sistema_sorteio = '" . $this->getusuario_sistema_sorteio() . "'
                WHERE idloteria = '" . $this->getidloteria() . "'";
        if ($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function deletaLoteria()
    {
        $sql = "DELETE FROM loteria WHERE idloteria = '" . $this->getidloteria() . "'";
        if ($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function listaLoteria($extra = "")
    {
        $sql = "SELECT * FROM loteria " . $extra;
        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            while ($obj = $this->db->fetchObj()) {
                $arr[] = new Class_loteria(
                    $this->db,
                    $obj->idloteria,
                    $obj->data_cadastro,
                    $obj->data_sorteio,
                    $obj->nome_loteria,
                    $obj->dezenas_sorteadas,
                    $obj->status_loteria,
                    $obj->usuario_sistema_cadastro,
                    $obj->usuario_sistema_sorteio
                );
            }
            $this->setResp($arr);
            return true;
        } else {
            return false;
        }
    }

    /**
     * listaJogosLoteria - método responsável por listar os jogos existentes da loteria
     * retorna dependendo dos parâmetros todos os jogos da loteria, 
     * todoso os jogos da loteria de um usuário específico
     * ou ainda todos os jogos vencedores (neste caso existirá apenas um)
     * @param $idusuario_jogos - se informado pegará os jogos do usuario
     * @param $jogo_vencedor - se informado pegara os ou o jogo vencedor
     */
    public function listaJogosLoteria($idusuario_jogos = '', $jogo_vencedor = '')
    {
        $sql = "SELECT * FROM usuario_jogos where loteria_idloteria = {$this->getidloteria()}";
        if ($idusuario_jogos)
            $sql .= " and idusuario_jogos = {$idusuario_jogos}";
        if ($idusuario_jogos)
            $sql .= " and jogo_vencedor = {$jogo_vencedor}";
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

    public function retornaQuantidadeRegistrosLoteria($extra = false)
    {
        $sql = "SELECT * FROM loteria " . $extra;

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

    public function getidloteria()
    {
        return $this->idloteria;
    }

    public function getdata_cadastro()
    {
        return $this->data_cadastro;
    }

    public function getdata_sorteio()
    {
        return $this->data_sorteio;
    }

    public function getnome_loteria()
    {
        return $this->nome_loteria;
    }

    public function getdezenas_sorteadas()
    {
        return $this->dezenas_sorteadas;
    }

    public function getstatus_loteria()
    {
        return $this->status_loteria;
    }

    public function getusuario_sistema_cadastro()
    {
        return $this->usuario_sistema_cadastro;
    }

    public function getusuario_sistema_sorteio()
    {
        return $this->usuario_sistema_sorteio;
    }

    /**
     * Funções set para todos os campos
     */
    public function setResp($value)
    {
        $this->resp = $value;
    }

    public function setidloteria($idloteria)
    {
        $this->idloteria = $idloteria;
    }

    public function setdata_cadastro($data_cadastro)
    {
        $this->data_cadastro = $data_cadastro;
    }

    public function setdata_sorteio($data_sorteio)
    {
        $this->data_sorteio = $data_sorteio;
    }

    public function setnome_loteria($nome_loteria)
    {
        $this->nome_loteria = $nome_loteria;
    }

    public function setdezenas_sorteadas($dezenas_sorteadas)
    {
        $this->dezenas_sorteadas = $dezenas_sorteadas;
    }

    public function setstatus_loteria($status_loteria)
    {
        $this->status_loteria = $status_loteria;
    }

    public function setusuario_sistema_cadastro($usuario_sistema_cadastro)
    {
        $this->usuario_sistema_cadastro = $usuario_sistema_cadastro;
    }

    public function setusuario_sistema_sorteio($usuario_sistema_sorteio)
    {
        $this->usuario_sistema_sorteio = $usuario_sistema_sorteio;
    }
}
