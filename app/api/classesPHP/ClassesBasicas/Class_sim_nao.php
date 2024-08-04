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
class Class_sim_nao {
    /*
     *
     */

    private $idsim_nao;
    /*
     *
     */
    private $valor;
    private $resp;
    private $db;

    //construtor da classe
    public function __construct($db="", $idsim_nao="", $valor="") {

        $this->setidsim_nao($idsim_nao);
        $this->setvalor($valor);

        $this->db = $db;
    }

    /**
     * Função responsável por carregar o registro
     * recebe como parametro o id ou valor extra para consulta
     * retorna positivo se existir registro e carrega os atributos da classe
     * ou retorna false se o registro nao for localizado.
     */
    public function carregasim_nao($idsim_nao=false, $extra=false) {
        $sql = "SELECT * FROM sim_nao WHERE idsim_nao = '" . $idsim_nao . "'";
        if ($extra) {
            $sql = "SELECT * FROM sim_nao " . $extra;
        }
        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            while ($obj = $this->db->fetchObj()) {

                $this->setidsim_nao($obj->idsim_nao);
                $this->setvalor($obj->valor);

                return true;
            }
        }
        return false;
    }

    public function inserirsim_nao() {


        $dados = '';
        $dados .= "'" . $this->getvalor() . "'";
        $sql = "INSERT INTO  sim_nao (
                        valor
                            )
                VALUES ( " . $dados . ")";
        if ($this->db->query($sql)) {
            $this->setidsim_nao($this->db->ultimoId());
            return true;
        } else {
            return false;
        }
    }

    public function atualizasim_nao() {

        $sql = "UPDATE sim_nao SET
                            idsim_nao = '" . $this->getidsim_nao() . "',
                            valor = '" . $this->getvalor() . "'
                        WHERE idsim_nao= '" . $this->getidsim_nao() . "'";
        if ($this->db->query($sql)) {

            return true;
        } else {
            return false;
        }
    }

    public function atualizaidsim_nao() {

        $sql = "UPDATE sim_nao SET idsim_nao = '" . $this->getidsim_nao() . "'
                                        WHERE idsim_nao= '" . $this->getidsim_nao() . "'";
        if ($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function atualizavalor() {

        $sql = "UPDATE sim_nao SET valor = '" . $this->getvalor() . "'
                                        WHERE idsim_nao= '" . $this->getidsim_nao() . "'";
        if ($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    public function deletasim_nao() {



        $sql = "DELETE FROM sim_nao WHERE idsim_nao= '" . $this->getidsim_nao() . "'";
        if ($this->db->query($sql)) {
            return true;
        } else {
            return false;
        }
    }

    //monta o menu lista para quando precisar
    //MONTA MENU LIST
    function MontaMenuList($nomecampo='idsim_nao', $idsim_nao="", $selecione=false, $class="", $title='') {
        $this->listasim_nao(" ORDER BY idsim_nao limit 50");
        $total_registro = count($this->getResp());
        $registros = $this->getResp();
        $MENU = "";
        if ($selecione) {
            $MENU .= '<option value="" selected="selected">Selecione...</option>';
        }
        for ($i = 0; $i < $total_registro; $i++) {
            $obj = $registros[$i];
            if ($idsim_nao == $obj->idsim_nao) {
                $MENU .= '<option value="' . $obj->idsim_nao . '" selected="selected">' . $obj->valor . '</option>';
            } else {
                $MENU .= '<option value="' . $obj->idsim_nao . '">' . $obj->valor . '</option>';
            }
        }
        return '<select title="' . $title . '" class="' . $class . '" id="' . $nomecampo . '" name="' . $nomecampo . '">
                                        ' . $MENU . '
                                </select>';
    }

    /**
     * Função responsável por gerar uma lista de elementos
     * recebe como parametro um complemento em sql para gerar a consulta
     * retorna positivo se existir registro para a consulta e armazena a lista
     * na variavel resp.
     */
    public function listasim_nao($extra="") {
        $sql = "SELECT * FROM sim_nao " . $extra;
        $this->db->query($sql);
        if ($this->db->quantidadeRegistros() > 0) {
            while ($obj = $this->db->fetchObj()) {
                $arr[] = new Class_sim_nao($this->db,
                                $obj->idsim_nao,
                                $obj->valor
                );
            }
            $this->setResp($arr);
            return true;
        } else {
            return false;
        }
    }

    public function retornaQuantidadeRegistrossim_nao($extra=false) {

        $sql = "SELECT * FROM sim_nao " . $extra;

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
    public function getResp() {
        return $this->resp;
    }

    /**
     * funcao responsável por retornar o valor do atributo -
     * se não existir valor retorna false;
     */
    public function getidsim_nao() {
        return $this->idsim_nao;
    }

    /**
     * funcao responsável por retornar o valor do atributo -
     * se não existir valor retorna false;
     */
    public function getvalor() {
        return $this->valor;
    }

    /**
     * Inicia as funções set para todos os campos
     */
    public function setResp($value) {
        $this->resp = $value;
    }

    /**
     * funcao responsável por atribuir valor ao atributo idsim_nao -
     *  @param int(11) $idsim_nao
     */
    public function setidsim_nao($idsim_nao) {
        $this->idsim_nao = $idsim_nao;
    }

    /**
     * funcao responsável por atribuir valor ao atributo valor -
     *  @param varchar(4) $valor
     */
    public function setvalor($valor) {
        $this->valor = $valor;
    }

}

?>
