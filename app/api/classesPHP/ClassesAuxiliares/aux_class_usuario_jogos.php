<?php

class aux_class_usuario_jogos extends Class_Valida_Dados
{
    //classes estrangeiras
    private $Class_usuario_jogos;
    private $Class_usuario_sistema;
    private $Class_loteria;
    private $db;
    private $aux_class_gerencia_permissao;
    private $Class_Sessao;

    //construtor da classe
    public function __construct()
    {
        $this->db = new DB();
        $this->Class_usuario_jogos = new Class_usuario_jogos($this->db);
        $this->Class_usuario_sistema = new Class_usuario_sistema($this->db);
        $this->Class_loteria = new Class_loteria($this->db);    
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
        if (!$this->CarregaLoteria()) {
            break;
        }
        switch ($ACAO) {
            case "INSERT":
                if (!$this->PegaPostsUsuarioJogos()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                if (!$this->salvaUsuarioJogos()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($this->Class_usuario_jogos->getArrayAtributos());
                break;
            case "UPDATE":
                if (!$this->PegaPostsUsuarioJogos()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                if (!$this->atualizaUsuarioJogos()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($this->Class_usuario_jogos->getArrayAtributos());
                break;
            case "LOAD":
                if (!$this->CarregaUsuarioJogos()) {
                    break;
                }
                $this->setConteudo($this->Class_usuario_jogos->getArrayAtributos());
                break;
            case "DELETE":
                if (!$this->ExcluiUsuarioJogos()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo("Jogo excluído com sucesso");
                break;
            case "LIST":
                $listaUsuarioJogos = $this->ListaUsuarioJogos();
                if (!$listaUsuarioJogos) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($listaUsuarioJogos);
                break;
            default:
                $this->setErros('Requisição incorreta. Não foi passado parâmetros.');
                $this->setConteudo($this->getErros());
        }
    }

    function PegaPostsUsuarioJogos()
    {
        $quant_dezenas = $this->getParametroJson("quant_dezenas", "A quantidade de dezenas deve ser informada", true);
        $this->Class_usuario_jogos->setquant_dezenas($quant_dezenas);

        // $dezenas_escolhidas = $this->getParametroJson("dezenas_escolhidas", "As dezenas escolhidas devem ser informadas", true);
        // $this->Class_usuario_jogos->setdezenas_escolhidas($dezenas_escolhidas);

        // $jogo_vencedor = $this->getParametroJson("jogo_vencedor", "Indicação se é um jogo vencedor deve ser informada", true);
        // $this->Class_usuario_jogos->setjogo_vencedor($jogo_vencedor);

        $loteria_idloteria = $this->getParametroJson("loteria_idloteria", "O ID da loteria deve ser informado", true);
        $this->Class_usuario_jogos->setloteria_idloteria($loteria_idloteria);

        // $usuario_sistema_idusuario_sistema = $this->getParametroJson("usuario_sistema_idusuario_sistema", "O ID do usuário deve ser informado", true);
        // $this->Class_usuario_jogos->setusuario_sistema_idusuario_sistema($usuario_sistema_idusuario_sistema);

        if ($this->getErros()) {
            return false;
        }
        return true;
    }

    function salvaUsuarioJogos()
    {
        //inicia a transacao
        $this->db->transacao();

        if ($this->Class_usuario_jogos->insereUsuarioJogos()) {
            //efetua o commit
            $this->db->commit();
            return true;
        }
        $this->setErros("Ocorreu um erro ao cadastrar o novo jogo, tente novamente.");
        //efetua um rollback
        $this->db->rollback();
        return false;
    }

    private function CarregaUsuarioJogos()
    {
        if (!$idusuario_jogos = $this->getParametroJson("idusuario_jogos", "ID do jogo não informado.", true)) {
            return false;
        }
        if (!$this->Class_usuario_jogos->carregaUsuarioJogos($idusuario_jogos)) {
            $this->setErros("Jogo não localizado.");
            return false;
        }
        return true;
    }

    private function CarregaLoteria()
    {
        if (!$idloteria = $this->getParametroJson("idloteria", "ID da loteria não informado.", true)) {
            return false;
        }
        if (!$this->Class_loteria->carregaLoteria($idloteria)) {
            $this->setErros("Loteria não localizada.");
            return false;
        }
        return true;
    }

    private function ListaUsuarioJogos()
    {
        if (!$this->Class_usuario_jogos->listaUsuarioJogos()) {
            $this->setErros("Não existem registros cadastrados para 'jogos'.");
            return false;
        }
        $Class_usuario_jogos = new Class_usuario_jogos($this->db);
        $arrayAtributos = [];
        foreach ($this->Class_usuario_jogos->getResp() as $Class_usuario_jogos) {
            $arrayAtributos[] = $Class_usuario_jogos->getArrayAtributos();
        }
        return $arrayAtributos;
    }
}
?>
