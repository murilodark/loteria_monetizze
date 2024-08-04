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
class Class_usuario_sistema
{
    private  $idusuario_sistema;
    private  $nome_usuario;
    private  $email_usuario;
    private  $senha_usuario;
    private  $tipo_usuario;
    private  $resp;
    private $db;

    //construtor da classe
    public function __construct($db = "", $idusuario_sistema = "", $nome_usuario = "", $email_usuario = "", $senha_usuario = "", $tipo_usuario = "")
    {

        $this->setidusuario_sistema($idusuario_sistema);
        $this->setnome_usuario($nome_usuario);
        $this->setemail_usuario($email_usuario);
        $this->setsenha_usuario($senha_usuario);
        $this->settipo_usuario($tipo_usuario);

        $this->db = $db;
    }

    public function getArrayAtributos()
    {
        return [
            'idusuario_sistema' => $this->getidusuario_sistema(),
            'nome_usuario' => $this->getnome_usuario(),
            'email_usuario' => $this->getemail_usuario(),
            'tipo_usuario' => $this->gettipo_usuario(),
        ];
    }
    public function getArrayAtributosJSON()
    {
       $array =  [
            'idusuario_sistema' => $this->getidusuario_sistema(),
            'nome_usuario' => $this->getnome_usuario(),
            'email_usuario' => $this->getemail_usuario(),
            'tipo_usuario' => $this->gettipo_usuario(),
        ];

        return  json_encode($array);
    }
    //funcao de login
    public function loginusuario_sistema()
    {
        $sql = "SELECT * FROM usuario_sistema  
                            WHERE email_usuario='" . $this->email_usuario . "' AND senha_usuario = '" . md5($this->senha_usuario) . "'limit 1";
        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            while ($obj = $this->db->fetchObj()) {

                $this->setidusuario_sistema($obj->idusuario_sistema);
                $this->setnome_usuario($obj->nome_usuario);
                $this->setemail_usuario($obj->email_usuario);
                $this->setsenha_usuario($obj->senha_usuario);
                $this->settipo_usuario($obj->tipo_usuario);
                return true;
            }
        }
        return false;
    }

    /**
     * Função responsável por carregar o registro
     * recebe como parametro o id ou valor extra para consulta
     * retorna positivo se existir registro e carrega os atributos da classe
     * ou retorna false se o registro nao for localizado.
     */
    public function carregausuario_sistema($idusuario_sistema = false, $extra = false)
    {
        $sql = "SELECT * FROM usuario_sistema WHERE idusuario_sistema = '" . $idusuario_sistema . "'";
        if ($extra) {
            $sql = "SELECT * FROM usuario_sistema " . $extra;
        }
        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            while ($obj = $this->db->fetchObj()) {

                $this->setidusuario_sistema($obj->idusuario_sistema);
                $this->setnome_usuario($obj->nome_usuario);
                $this->setemail_usuario($obj->email_usuario);
                $this->setsenha_usuario($obj->senha_usuario);
                $this->settipo_usuario($obj->tipo_usuario);

                return true;
            }
        }
        return false;
    }

    public function inserirusuario_sistema()
    {


        $dados = '';
        $dados .= "'" . $this->getnome_usuario() . "',";
        $dados .= "'" . $this->getemail_usuario() . "',";
        $dados .= "'" . md5($this->getsenha_usuario()) . "',";
        $dados .= "'" . $this->gettipo_usuario() . "'";
        $sql = "INSERT INTO  usuario_sistema (
                        nome_usuario, 
                            email_usuario, 
                            senha_usuario, 
                            tipo_usuario
                            ) 
                VALUES ( " . $dados . ")";
        if ($this->db->query($sql)) {
            $this->setidusuario_sistema($this->db->ultimoId());
            return true;
        } else {
            return false;
        }
    }

    public function atualizausuario_sistema()
    {

        $sql = "UPDATE usuario_sistema SET 
                            idusuario_sistema = '" . $this->getidusuario_sistema() . "',
                            nome_usuario = '" . $this->getnome_usuario() . "',
                            email_usuario = '" . $this->getemail_usuario() . "'                           
                        WHERE idusuario_sistema= '" . $this->getidusuario_sistema() . "'";
        if ($this->db->query($sql)) {

            return true;
        } else {
            return false;
        }
    }



    public function deletausuario_sistema()
    {
        $sql = "DELETE FROM usuario_sistema WHERE idusuario_sistema= '" . $this->getidusuario_sistema() . "'";
        if ($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }



    /**
     * Função responsável por gerar uma lista de elementos
     * recebe como parametro um complemento em sql para gerar a consulta
     * retorna positivo se existir registro para a consulta e armazena a lista
     * na variavel resp.
     */
    public function listausuario_sistema($extra = "")
    {
        $sql = "SELECT * FROM usuario_sistema " . $extra;
        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            while ($obj = $this->db->fetchObj()) {
                $arr[] = new Class_usuario_sistema(
                    $this->db,
                    $obj->idusuario_sistema,
                    $obj->nome_usuario,
                    $obj->email_usuario,
                    $obj->senha_usuario,
                    $obj->tipo_usuario
                );
            }
            $this->setResp($arr);
            return true;
        } else {
            return false;
        }
    }

    public function retornaQuantidadeRegistrosusuario_sistema($extra = false)
    {

        $sql = "SELECT * FROM usuario_sistema " . $extra;

        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            // $this->setResp($this->db->quantidadeRegistros());
            return $this->db->quantidadeRegistros();
        } else {
            return false;
        }
    }

    /**
     * Inicia as funções get para todos os campos
     */
    public function getResp()
    {
        return $this->resp;
    }

    public function getidusuario_sistema()
    {
        return $this->idusuario_sistema;
    }

    public function getnome_usuario()
    {
        return $this->nome_usuario;
    }

    public function getemail_usuario()
    {
        return $this->email_usuario;
    }

    public function getsenha_usuario()
    {
        return $this->senha_usuario;
    }

    public function gettipo_usuario()
    {
        return $this->tipo_usuario;
    }

    /**
     * Inicia as funções set para todos os campos
     */
    public function setResp($value)
    {
        $this->resp = $value;
    }

    public function setidusuario_sistema($idusuario_sistema)
    {
        $this->idusuario_sistema = $idusuario_sistema;
    }

    public function setnome_usuario($nome_usuario)
    {
        $this->nome_usuario = $nome_usuario;
    }

    public function setemail_usuario($email_usuario)
    {
        $this->email_usuario = $email_usuario;
    }

    public function setsenha_usuario($senha_usuario)
    {
        $this->senha_usuario = $senha_usuario;
    }

    public function settipo_usuario($tipo_usuario)
    {
        $this->tipo_usuario = $tipo_usuario;
    }
}
