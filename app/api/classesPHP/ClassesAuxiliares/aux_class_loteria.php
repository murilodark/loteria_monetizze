<?php

class aux_class_loteria extends Class_Valida_Dados
{
    //classes estrangeiras
    private $Class_loteria;
    private $Class_usuario_sistema;
    private $db;
    private $aux_class_gerencia_permissao;
    private $Class_Sessao;
    //construtor da classe
    public function __construct()
    {
        $this->db = new DB();
        $this->Class_loteria = new Class_loteria($this->db);
        $this->Class_usuario_sistema = new Class_usuario_sistema($this->db);
        $this->aux_class_gerencia_permissao = new aux_class_gerencia_permissao($this->db);
        $this->Class_Sessao = new Class_Sessao();
        $this->Controle();
    }

    function Controle()
    {
        if (!$this->carregaParametrosJson('POST')) {
            $this->setConteudo($this->getErros());
            return false;
        }
        $ACAO = $this->getParametroJson("ACAO", "A ação a ser executada é obrigatória", true);
        if ($this->getErros()) {
            $this->setConteudo($this->getErros());
            return false;
        }
        if (!$this->aux_class_gerencia_permissao->ValidaSessao()) {
            $this->setConteudo($this->getErros());
            return false;
        }
        if (!$this->Class_usuario_sistema->carregausuario_sistema($this->Class_Sessao->getIdUsuario())) {
            $this->aux_class_gerencia_permissao->Logout();
            $this->setErros('Usuário não localizado, efetue o login novamente.');
            $this->setConteudo($this->getErros());
            return false;
        }
        switch ($ACAO) {
            case "INSERT":

                if (!$this->PegaPostsloteria()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                if (!$this->salvaloteria()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($this->Class_loteria->getArrayAtributos());
                break;
            case "UPDATE":
                if (!$this->PegaPostsloteria()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                if (!$this->atualizaloteria()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($this->Class_loteria->getArrayAtributos());
                break;
            case "DELETE":
                if (!$this->Excluiloteria()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo("Loteria excluída com sucesso");
                break;
            case "LIST":
                if (!$this->Listaloteria()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($this->Class_loteria->getResp());
                break;
            default:
                $this->setErros('Requisição incorreta. Não foi passado parâmetros.');
                $this->setConteudo($this->getErros());
        }
    }

    function PegaPostsloteria()
    {
        // $data_cadastro = $this->getParametroJson("data_cadastro", "A data de cadastro deve ser informada", true);
        // $this->Class_loteria->setdata_cadastro($data_cadastro);

        $nome_loteria = $this->getParametroJson("nome_loteria", "O nome da loteria deve ser informado", true);
        $this->Class_loteria->setnome_loteria($nome_loteria);

        $data_sorteio = $this->getParametroJson("data_sorteio", "A data de sorteio deve ser informada", true);

        if (!$this->validaDataFutura($data_sorteio)) {
            return false;
        }
        if (!$data_sorteio =  $this->getDataBD($data_sorteio)) {
            return false;
        }
        $this->Class_loteria->setdata_sorteio($data_sorteio);


        // $dezenas_sorteadas = $this->getParametroJson("dezenas_sorteadas", "As dezenas sorteadas devem ser informadas", true);
        // $this->Class_loteria->setdezenas_sorteadas($dezenas_sorteadas);

        // $status_loteria = $this->getParametroJson("status_loteria", "O status da loteria deve ser informado", true);
        // $this->Class_loteria->setstatus_loteria($status_loteria);

        // $usuario_sistema_cadastro = $this->getParametroJson("usuario_sistema_cadastro", "O usuário do sistema que cadastrou deve ser informado", true);
        // $this->Class_loteria->setusuario_sistema_cadastro($usuario_sistema_cadastro);

        // $usuario_sistema_sorteio = $this->getParametroJson("usuario_sistema_sorteio", "O usuário do sistema que sorteou deve ser informado", true);
        // $this->Class_loteria->setusuario_sistema_sorteio($usuario_sistema_sorteio);
        if ($this->getErros()) {
            return false;
        }
        return true;
    }

    function salvaloteria()
    {
        //inicia a transacao
        $this->db->transacao();

        $this->Class_loteria->setusuario_sistema_cadastro($this->Class_usuario_sistema->getidusuario_sistema());
        $this->Class_loteria->setdata_cadastro($this->getDataTime());
        $this->Class_loteria->setstatus_loteria('A');

        if ($this->Class_loteria->insereloteria()) {
            //efetua o commit
            $this->db->commit();
            return true;
        }
        $this->setErros("Ocorreu um erro ao cadastrar a nova loteria, tente novamente.");
        //efetua um rollback
        $this->db->rollback();
        return false;
    }

    function atualizaloteria()
    {
        if ($this->Class_loteria->atualizaloteria()) {
            return true;
        } else {
            $this->setErros("Ocorreu um erro ao atualizar a loteria, tente novamente.");
            return false;
        }
    }

    private function Excluiloteria()
    {
        if (!$this->Class_loteria->deletaloteria()) {
            $this->setErros("A loteria não pode ser removida.");
            return false;
        }
        return true;
    }

    private function Listaloteria()
    {
        $extra = $this->getParametroJson("extra", "", false);
        if (!$this->Class_loteria->listaloteria($extra)) {
            $this->setErros("Não existem registros cadastrados para 'loteria'.");
            return false;
        }
        return true;
    }
}
