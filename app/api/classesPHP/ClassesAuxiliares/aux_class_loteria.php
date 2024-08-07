<?php

/**
 * Classe responsável por gerencia as funções relacionados a loteria
 * como: cadastro, consulta, listagem e sorteio
 */

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
    private $qaunt_jogos_solicitados;
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
                // if (!$this->PegaPostsloteria()) {
                //     $this->setConteudo($this->getErros());
                //     break;
                // }
                // if (!$this->atualizaloteria()) {
                //     $this->setConteudo($this->getErros());
                //     break;
                // }
                // $this->setConteudo($this->Class_loteria->getArrayAtributos());
                // break;
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
                $this->setConteudo($this->ListarTodososJogos());
                $this->setConteudo($this->Class_loteria->getInformacoesUsuarioPremiado());

                break;
            case "DELETE":
                // if (!$this->Excluiloteria()) {
                //     $this->setConteudo($this->getErros());
                //     break;
                // }
                // $this->setConteudo("Loteria excluída com sucesso");
                // break;
            case "LIST":
                $listaloteria = $this->Listaloteria();
                if (!$listaloteria) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($listaloteria);
                $this->setConteudo($this->ListarTodososJogos());
                break;
            case "EFETUASORTEIO":
                if (!$this->CarregaLoteria()) {
                    break;
                }
                if (!$this->IniciaProcessoSorteio()) {
                    $this->setConteudo($this->getErros());
                    return;
                }
                //converto as datas para o formato brasileiro 
                $this->Class_loteria->setdata_cadastro($this->getDataBrasil($this->Class_loteria->getdata_cadastro()));
                if ($this->Class_loteria->getdata_sorteio()) {
                    $this->Class_loteria->setdata_sorteio($this->getDataBrasil($this->Class_loteria->getdata_sorteio()));
                }
                $this->setConteudo($this->Class_loteria->getArrayAtributos());
                break;

            default:
                $this->setErros('Requisição incorreta. Não foi passado parâmetros.');
                $this->setConteudo($this->getErros());
        }
    }

    private function PegaPostsloteria()
    {


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
        if ($this->getErros()) {
            return false;
        }
        return true;
    }

    private function salvaloteria()
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


    // private function atualizaloteria()
    // {
    //     if ($this->Class_loteria->atualizaloteria()) {
    //         return true;
    //     } else {
    //         $this->setErros("Ocorreu um erro ao atualizar a loteria, tente novamente.");
    //         return false;
    //     }
    // }
    // private function Excluiloteria()
    // {
    //     if (!$this->Class_loteria->deletaloteria()) {
    //         $this->setErros("A loteria não pode ser removida.");
    //         return false;
    //     }
    //     return true;
    // }
    private function Listaloteria()
    {
        $arrayAtributos = [];
        if (!$this->Class_loteria->listaloteria()) {
            return $arrayAtributos;
        }
        $Class_loteria = new Class_loteria($this->db);
        foreach ($this->Class_loteria->getResp() as  $Class_loteria) {
            $arrayAtributos[] = $Class_loteria->getArrayAtributos();
        }
        return $arrayAtributos;
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

    /**
     * Método responsável por iniciar e executar todos os processos
     * referentes ao sorteio da loteria
     */
    private function IniciaProcessoSorteio()
    {
        if (!$this->ValidaSorteioLoteria()) {
            return false;
        }

        $arrayJogos = $this->MontaArrayJogosEfetuados();
        if (!$arrayJogos) {
            return false;
        }
        list($jogoPremiados, $arrayDezenasPremiadas) = $this->EfetuaSorteio($arrayJogos);
        $stringDezenasSorteadas = implode(',', $arrayDezenasPremiadas);
        $jogopremiado = array_values($jogoPremiados);
        $idjogopremiado = $jogopremiado[0]['idjogo'];
        if (!$this->SalvaSorteio($stringDezenasSorteadas,  $idjogopremiado)) {
            return false;
        }

        return true;
    }

    private function ValidaSorteioLoteria()
    {
        if ($this->Class_loteria->getusuario_sistema_sorteio() || $this->Class_loteria->getdezenas_sorteadas()) {
            $this->setErros('Já foi efetuado o sorteio para esta loteria anteriormente.');
            return false;
        }
        return true;
    }

    /**
     * Método responsável por consultar todos os jogos da loteria
     * e montar um array será utilizado para identificar o jogo premiado
     * @return array - contendo todos os jogos da loteria
     */
    private function MontaArrayJogosEfetuados()
    {
        //carrega todos os jogos da loteria
        $extra = "  where loteria_idloteria = {$this->Class_loteria->getidloteria()}";
        if (!$this->Class_usuario_jogos->listaUsuarioJogos($extra)) {
            $this->setErros('A loteria não pode ser sorteada, pois não existem jogos.');
            return false;
        }
        //monta um array com todos os jogos e suas respectivas dezenas        
        $Class_usuario_jogos = new Class_usuario_jogos($this->db);
        $arrayJogos = [];
        foreach ($this->Class_usuario_jogos->getResp() as $Class_usuario_jogos) {
            $arrayJogos[] = [
                "idjogo" => $Class_usuario_jogos->getidusuario_jogos(),
                "arrayDezenas" => explode(',', $Class_usuario_jogos->getdezenas_escolhidas())
            ];
        }
        if (count($arrayJogos) > 0) {
            return  $arrayJogos;
        }
        $this->setErros('O sorteio não pode ser realizado, não existem jogos efetuados.');
        return false;
    }

    /**
     * Método responsável por efetuar o sorteio das dezenas
     * efetua o sorteio uma a uma e vai filtrando os jogos
     * que possuem as dezenas sorteadas.
     * @param array $arrayJogos - array contendo todos os jogos da loteria
     * @return array - retorna um array contendo o jogo premiados e dezenas sorteadas [$jogoPremiados, $arrayDezenasPremiadas]; 
     */
    private function EfetuaSorteioOk(array $arrayJogos)
    {
        $dezenas = range(1, 60);
        $arrayDezenasSorteadas = [];
        $arrayDezenasPremiadas = [];
        $jogoPremiados = $arrayJogos; // Inicia com todos os jogos

        $i = 1;
        while ($i < 60 && count($arrayDezenasPremiadas) < 6) {
            // Embaralha os números do array
            shuffle($dezenas);
            // Seleciona uma dezena específica
            $dezenaSorteada = array_slice($dezenas, 0, 1)[0];

            // Remove a dezena sorteada do array $dezenas
            $dezenas = array_diff($dezenas, [$dezenaSorteada]);

            // Adiciona a dezena sorteada ao array de dezenas sorteadas
            if (!in_array($dezenaSorteada, $arrayDezenasSorteadas)) {
                $arrayDezenasSorteadas[] = $dezenaSorteada;
            }
            // Filtra os jogos que contêm todas as dezenas sorteadas até agora
            $jogoPremiados = array_filter($arrayJogos, function ($jogo) use ($arrayDezenasSorteadas) {
                // Verifica se o jogo contém todas as dezenas sorteadas
                return empty(array_diff($arrayDezenasSorteadas, $jogo['arrayDezenas']));
            });
            // Verifica se nenhum jogo foi encontrado
            if (empty($jogoPremiados)) {
                // Recarrega o array com as dezenas premiadas
                $arrayDezenasSorteadas = $arrayDezenasPremiadas;
                sort($dezenas);
            } else {
                // Adiciona a dezena sorteada ao array de dezenas premiadas
                $arrayDezenasPremiadas[] = $dezenaSorteada;
            }
            $i++;
        }
        sort($arrayDezenasPremiadas);
        //passa o jogos filtrado e as dezenas premiadas para um array de resultado
        $resultado = [$jogoPremiados, $arrayDezenasPremiadas];

        return $resultado;
    }


    private function EfetuaSorteio(array $arrayJogos)
{
    $dezenas = range(1, 60);
    $arrayDezenasSorteadas = [];
    $arrayDezenasPremiadas = [];
    $jogoPremiados = $arrayJogos; // Inicia com todos os jogos

    $i = 1;
    while ($i < 60 && count($arrayDezenasPremiadas) < 6) {
        // Embaralha os números do array
        shuffle($dezenas);
        // Seleciona uma dezena específica
        $dezenaSorteada = array_slice($dezenas, 0, 1)[0];

        // Remove a dezena sorteada do array $dezenas
        $dezenas = array_diff($dezenas, [$dezenaSorteada]);

        // Adiciona a dezena sorteada ao array de dezenas sorteadas
        if (!in_array($dezenaSorteada, $arrayDezenasSorteadas)) {
            $arrayDezenasSorteadas[] = $dezenaSorteada;
        }
        // Saída de debug
        // var_dump($dezenaSorteada);
        // var_dump($arrayDezenasSorteadas);

        // Filtra os jogos que contêm todas as dezenas sorteadas até agora
        $jogoPremiados = array_filter($arrayJogos, function ($jogo) use ($arrayDezenasSorteadas) {
            // Verifica se o jogo contém todas as dezenas sorteadas
            return empty(array_diff($arrayDezenasSorteadas, $jogo['arrayDezenas']));
        });
        // Saída de debug
        // var_dump($jogoPremiados);

        // Verifica se nenhum jogo foi encontrado
        if (empty($jogoPremiados)) {
            // Recarrega o array com as dezenas premiadas
            $arrayDezenasSorteadas = $arrayDezenasPremiadas;
            sort($dezenas);
        } else {
            // Adiciona a dezena sorteada ao array de dezenas premiadas
            $arrayDezenasPremiadas[] = $dezenaSorteada;
        }
        $i++;
    }
    sort($arrayDezenasPremiadas);
    //passa o jogos filtrado e as dezenas premiadas para um array de resultado
    $resultado = [$jogoPremiados, $arrayDezenasPremiadas];

    return $resultado;
}


    /**
     * Método responsável por persistir o resultado do sorteio no banco
     * Altera o registro da loteria e do jogo sorteado
     */
    private function SalvaSorteio(string $stringDezenasSorteadas, int $idjogopremiado)
    {
        // atualiza a loteria e o jogo premiado com as informações de sorteio
        // inicia a transacao
        $this->db->transacao();
        if (!$this->Class_loteria->insereSorteioLoteria($this->getDataTime(), $stringDezenasSorteadas, $idjogopremiado)) {
            $this->setErros("Ocorreu um erro ao salvar o sorteio da loteria, tente novamente.");
            //efetua um rollback
            $this->db->rollback();
            return false;
        }
        //efetua o commit
        $this->db->commit();
        return true;
    }

    /**
     * Método responsável por listar todos os jogos 
     * de todos os usuários de uma loteria
     */
    private function ListarTodososJogos()
    {
        $todososjogos = $this->getParametroJson("todososjogos");
        $arrayAtributos = '';
        if ($todososjogos == 'SIM') {
            $extra = "  where loteria_idloteria = {$this->Class_loteria->getidloteria()}";
            if (!$this->Class_usuario_jogos->listaUsuarioJogos($extra)) {
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
}
