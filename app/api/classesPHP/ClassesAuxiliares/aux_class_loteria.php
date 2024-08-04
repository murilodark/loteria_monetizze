<?php

class aux_class_loteria extends Class_Valida_Dados
{
    //classes estrangeiras
    private $Class_loteria;
    private $Class_usuario_sistema;
    private $Class_usuario_jogos;
    private $db;
    private $aux_class_gerencia_permissao;
    private $Class_Sessao;
    //variáveis utilizadas para armazenar a quantidade de jogos solicitada pelo um usuário
    //e utilizada nas funcoes relacionadas aos jogos dos usuários
    private $quant_jogos;
    private $qauntidadeJogosDisponivel;
    private $jogosGerados;
    //construtor da classe
    public function __construct()
    {
        $this->db = new DB();
        $this->Class_loteria = new Class_loteria($this->db);
        $this->Class_usuario_sistema = new Class_usuario_sistema($this->db);
        $this->Class_usuario_jogos = new Class_usuario_jogos($this->db);
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
            case "LOAD":
                if (!$this->CarregaLoteria()) {
                    break;
                }
                //converto as datas para o formato brasileiro 
                $this->Class_loteria->setdata_cadastro($this->getDataBrasil($this->Class_loteria->getdata_cadastro()));
                if ($this->Class_loteria->getdata_sorteio()) {
                    $this->Class_loteria->setdata_sorteio($this->getDataBrasil($this->Class_loteria->getdata_sorteio()));
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
                $listaloteria = $this->Listaloteria();
                if (!$listaloteria) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($listaloteria);
                break;

            case "GERAJOGO":
                if (!$this->CarregaLoteria()) {
                    break;
                }
                if (!$this->PegaPostsUsuarioJogos()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                if (!$this->ValidaQuantidadeJogosUsuario()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                if (!$this->GeraJogosUsuario()) {
                    $this->setConteudo($this->getErros());
                    break;
                }

                $listaJogos = $this->ListarJogoUsuario();
                if (!$listaJogos) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($listaJogos);
                break;

                $this->setConteudo($this->Class_loteria->getArrayAtributos());
                break;

            case "LISTAJOGOSUSUARIO":
                $listaJogos = $this->ListarJogoUsuario();
                if (!$listaJogos) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($listaJogos);
                break;

                $this->setConteudo($this->Class_loteria->getArrayAtributos());
                break;
            case "LISTATODOSJOGOS":

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
        $this->Class_loteria->setstatus_loteria('Andamento');

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

        if (!$this->Class_loteria->listaloteria()) {
            $this->setErros("Não existem registros cadastrados para 'loteria'.");
            return false;
        }
        $Class_loteria = new Class_loteria($this->db);
        $arrayAtributos = [];
        foreach ($this->Class_loteria->getResp() as  $Class_loteria) {
            $arrayAtributos[] = $Class_loteria->getArrayAtributos();
        }
        return $arrayAtributos;
    }


    function PegaPostsUsuarioJogos()
    {
        if (!$quant_dezenas = $this->getParametroJson("quant_dezenas", "A quantidade de dezenas deve ser informada", true)) {
            return false;
        }
        if (!$this->quant_jogos = $this->getParametroJson("quant_jogos", "A quantidade de dezenas deve ser informada", true)) {
            return false;
        }
        if ($quant_dezenas < 6 || $quant_dezenas > 10) {
            $this->setErros('A quantidade de dezenas deve estar entre 6 e 10.');
        }
        if ($this->quant_jogos < 1 || $this->quant_jogos > 50) {
            $this->setErros('A quantidade de jogos deve estar entre 1 e 50.');
        }
        $this->Class_usuario_jogos->setquant_dezenas($quant_dezenas);
        if ($this->getErros()) {
            return false;
        }
        return true;
    }




    /**
     * método responsável por validade a quantidade de jogos que estão disponível
     * para um determinado usuário e uma loteria específica.
     * Efetua a contagem já efetuada e valida se a nova solicitação ao ultrapaça quantidade de 50 jogos
     * armazena a quantidade de jogos disponível na variável da classe $this->qauntidadeJogosDisponivel
     * que será utilizada em outros métodos
     */
    function ValidaQuantidadeJogosUsuario()
    {

        $extra = "  where usuario_sistema_idusuario_sistema = {$this->Class_usuario_sistema->getidusuario_sistema()}";
        $extra .= "  and loteria_idloteria = {$this->Class_loteria->getidloteria()}";
        $qauntidadeJogosJaEfetuado = $this->Class_usuario_jogos->retornaQuantidadeRegistrosUsuarioJogos($extra);
        if ($qauntidadeJogosJaEfetuado >= 50) {
            $this->setErros('Limite de 50 já atingindo.');
            return false;
        }
        $qauntidadeJogosDisponivel = 50 - $qauntidadeJogosJaEfetuado;
        if ($qauntidadeJogosDisponivel < $this->quant_jogos) {
            $this->setErros("O seu limite de jogos disponível para essa loteria é de {$qauntidadeJogosDisponivel}, altere e tente novamente.");
            return false;
        }
        $this->qauntidadeJogosDisponivel = $qauntidadeJogosDisponivel;
        return true;
    }


    /**
     * Método responsável gernciar a quantidade de jogos gerados
     * efetua um loop acessando o método $this->gerarJogoUnico,
     * em seguida verifica se o jogo é unico na base de dados $this->verificarJogoUnico($jogo)
     * e pro fim salva o jogo gerado  $this->salvaUsuarioJogos($jogo);
     * Em casos de erros efetua um rollback no db 
     * desfazendo todas as operações de salvamento já realizadas.
     * @return boolean = true para ok e false para erro
     */
    function GeraJogosUsuario()
    {
        $jogosGerados = [];
        //inicia a transacao
        $this->db->transacao();
        try {
            while (count($jogosGerados) < $this->qauntidadeJogosDisponivel) {
                $jogo = $this->gerarJogoUnico($this->Class_usuario_jogos->getquant_dezenas());

                if ($this->verificarJogoUnico($jogo)) {
                    $this->salvarJogoUsuario($jogo);
                    $jogosGerados[] = $jogo;
                }
            }
        } catch (exception $e) {
            $this->setErros($e->getMessage());
            //efetua um rollback
            $this->db->rollback();
            return false;
        }
        //efetua o commit
        $this->db->commit();
        $this->jogosGerados = $jogosGerados;
        return true;
    }


    /**
     * Método responsável por gerar um jogo único na quantidade de dezenas especificada
     * O método não faz a validação junto ao banco dos jogos já gerados,
     * essa validação será feita por outro método 
     * o método $this->verificarJogoUnico($dezenas_escolhidas)
     */
    private function gerarJogoUnico($quantidadeDezenas)
    {
        // Cria um array contendo os números de 1 a 60
        $dezenas = range(1, 60);
        // Embaralha os números do array
        shuffle($dezenas);
        // Seleciona uma quantidade específica 
        $dezenas_escolhidas = array_slice($dezenas, 0, $quantidadeDezenas);
        // Ordena os números selecionados em ordem crescente
        sort($dezenas_escolhidas);
        // Converte o array em uma string
        return implode(',', $dezenas_escolhidas);
    }


    /**
     * Método responsável por efetuar a validação de um jogo gerado (dezenas_escolhidas),
     * Essa validação e efetuada através de uma consulta na db,
     * O jogo deve ser único para uma loteria específica.
     */
    private function verificarJogoUnico($dezenas_escolhidas)
    {
        $extra = "  where loteria_idloteria = {$this->Class_loteria->getidloteria()}";
        $extra .= "  and dezenas_escolhidas = {$dezenas_escolhidas}";
        $quantidade = $this->Class_usuario_jogos->retornaQuantidadeRegistrosUsuarioJogos($extra);
        if ($quantidade > 0) {
            return false;
        }
        return true;
    }


    /**
     * Método responsável por salvar no db um jogo da loteria
     * recebe as dezenas escolhidas ou jogo e inclui os demais 
     * parâmetros antes de salvar.
     */
    function salvarJogoUsuario($dezenas_escolhidas)
    {

        $this->Class_usuario_jogos->setdezenas_escolhidas($dezenas_escolhidas);
        $this->Class_usuario_jogos->setloteria_idloteria($this->Class_loteria->getidloteria());
        $this->Class_usuario_jogos->setusuario_sistema_idusuario_sistema($this->Class_usuario_sistema->getidusuario_sistema());
        if (!$this->Class_usuario_jogos->insereUsuarioJogos()) {
            throw new exception('Erro ao inserir os jogos gerados, tente novamente.');
        }
        return true;
    }

    private function ListarJogoUsuario()
    {

        $extra = "  where loteria_idloteria = {$this->Class_loteria->getidloteria()}";

        if (!$this->Class_usuario_jogos->listaUsuarioJogos($extra)) {
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
