<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Class_Valida_Dados
 *
 * @author darksp
 */
// require_once("classesPHP/ClassesFuncionais/Class_Erros.php");

//require_once("classesPHP/ClassesFuncionais/rmv_olirum.php");
class Class_Valida_Dados extends Class_Erros
{
    private $conteudo;

    private $dataJson;
    //put your code here
    function carregaParametrosJson($metodo = 'POST')
    {

        // Verifica se a requisição é o tipo desejado
        if ($_SERVER['REQUEST_METHOD'] === $metodo) {
            // Recebe o JSON da requisição
            $json = file_get_contents('php://input');

            // Decodifica o JSON para um array associativo
            $this->dataJson = json_decode($json, true);

            // Verifica se a decodificação foi bem-sucedida
            if (json_last_error() === JSON_ERROR_NONE) {
                return true;
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Erro ao decodificar JSON.']);
                $this->setErros('Erro ao decodificar JSON.');
            }
        } else {
            $this->setErros('Método de requisição inválido.');
        }
        return false;
    }


    /**
     * Método responsável por regatar os parâmetro informado em um json
     * pode ser aperfeiçoado incluindo a validação correta para cada tipo de parâmetro
     * @param string $val - variável a ser resgatada
     * @param string $msg_erro - mensagem de erro caso exista
     * @param boolean $obrigatorio - se o parâmetro é o brigatório existir no json
     * @param string $tipoParametro - tipo de paramento int, string, deve ser desenvolvido
     * 
     */
    public function getParametroJson($val, $msg_erro = '', $obrigatorio = false, $tipoParametro = 'string')
    {

        $parametro = '';
        if ($obrigatorio) {
            if (array_key_exists($val, $this->dataJson)) {
                $parametro = filter_var($this->dataJson[$val], FILTER_SANITIZE_SPECIAL_CHARS);
                // Verifica se o parâmetro está vazio ou é nulo
                if (empty($parametro) || is_null($parametro)) {
                    $this->setErros($msg_erro);
                    return false;
                }
            } else {
                $this->setErros($msg_erro);
                return false;
            }
        } else {
            if (array_key_exists($val, $this->dataJson)) {
                $parametro = filter_var($this->dataJson[$val], FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $parametro;
    }





    /**
     * funcao getDataTime retorna a data e horario no formato do db aaaa/mm/dd h:m:s
     * @return type
     */
    public function getDataTime($separador = '-')
    {
        if ($separador == '/') {
            return date('Y/m/d H:m:s');
        }
        return date('Y-m-d H:m:s');
    }


    /**
     * funcao retorna a tada atual em um dos formatos:
     * brasil = true retorna data no formato 99/99/9999
     * db = rue retorna data no formato 9999/99/99
     * @param type $brasil
     * @param type $db
     * @return type
     */
    public function getData($brasil = false, $db = false)
    {
        if ($brasil) {
            return date('d/m/Y');
        }
        if ($db) {
            return date('Y/m/d');
        }
        return;
    }

    /**
     * Funcao getDataBrasil pega uma data no formato do BD 9999-99-99 e transforma para o formato brasileiro 99/99/9999
     * se time for true, devolve como o tempo se falso devolve apenas a data sem o tempo
     * @param type $data
     * @return string
     */
    public function getDataBrasil($data, $time = true)
    {

        $p1 = explode(" ", $data); //separa o time caso exista
        if (!empty($data)) {
            $datab = str_replace('/', '-', $p1[0]);
            $datab = explode("-", $datab);
            $novadata = $datab[2] . "/" . $datab[1] . "/" . $datab[0]; // Formato PT-BR(DD/MM/YYYY)
            if (count($p1) > 1) {
                if ($time) {
                    return $novadata . ' ' . $p1[1];
                } else {
                    return $novadata;
                }
            } else {
                return $novadata;
            }
        }
        return;
    }

    /**
     * Funcao getDataBD pega uma data no formado brasileiro 99/99/9999 e transforma no formato bd para salvar no banco 9999-99-99
     * se time for true, devolve como o tempo se falso devolve apenas a data sem o tempo
     * @param string $data
     * @param boorlean $time
     * @return string
     */
    public function getDataBD($data, $time = true)
    {
        // Verificar se a data está no formato dd/mm/aaaa ou dd-mm-aaaa
        if (preg_match("/^(\d{2})[\/-](\d{2})[\/-](\d{4})$/", $data, $matches)) {
            $dia = $matches[1];
            $mes = $matches[2];
            $ano = $matches[3];
        }
        // Verificar se a data está no formato aaaa/mm/dd ou aaaa-mm-dd
        elseif (preg_match("/^(\d{4})[\/-](\d{2})[\/-](\d{2})$/", $data, $matches)) {
            $ano = $matches[1];
            $mes = $matches[2];
            $dia = $matches[3];
        } else {
            $this->setErros('Formato inválido de data. Formatos válidos: aaaa/mm/dd ou dd/mm/aaaa');
            return false;
        }

        // Verificar se a data é válida
        if (!checkdate($mes, $dia, $ano)) {
            $this->setErros('Data inválida. A data deve ser uma data existente e em um dos formatos: aaaa/mm/dd ou dd/mm/aaaa');
            return false;
        }

        // Criar objeto DateTime para a data
        $dataObj = DateTime::createFromFormat('Y-m-d', "$ano-$mes-$dia");

        // Retornar a data no formato desejado
        if ($time) {
            return $dataObj->format('Y/m/d H:i:s');
        } else {
            return $dataObj->format('Y/m/d');
        }
    }

    /**
     * Funcao que recebe uma data no formato aaaa/mm/dd ou dd/mm/aaaa
     * e valida se é uma data válida e que seja o presente ou no futuro
     * não aceita datas passadas     * 
     * @param string $data
     * @return boorlean true = válida e false = inválida
     */
    function validaDataFutura($data)
    {
        // Verificar se a data está no formato dd/mm/aaaa ou dd-mm-aaaa
        if (preg_match("/^(\d{2})[\/-](\d{2})[\/-](\d{4})$/", $data, $matches)) {
            $dia = $matches[1];
            $mes = $matches[2];
            $ano = $matches[3];
        }
        // Verificar se a data está no formato aaaa/mm/dd ou aaaa-mm-dd
        elseif (preg_match("/^(\d{4})[\/-](\d{2})[\/-](\d{2})$/", $data, $matches)) {
            $ano = $matches[1];
            $mes = $matches[2];
            $dia = $matches[3];
        } else {
            $this->setErros($data . ' Formato inválido de data. Formatos válidos: aaaa/mm/dd ou dd/mm/aaaa');
            return false;
        }

        // Verificar se a data é válida
        if (!checkdate($mes, $dia, $ano)) {
            $this->setErros('Data inválida. A data deve ser uma data existente e em um dos formatos: aaaa/mm/dd ou dd/mm/aaaa');
            return false;
        }

        // Criar objetos DateTime para a data e a data atual
        $dataObj = DateTime::createFromFormat('Y-m-d', "$ano-$mes-$dia");
        $dataAtual = new DateTime();

        // Verificar se a data é no passado
        if ($dataObj < $dataAtual) {
            $this->setErros('A data não pode ser no passado.');
            return false;
        }

        return true; // Data válida
    }





    /**
     * Funcao responsável por receber um e-mail e se o mesmo for correto retorna ele mesmo
     * do contrário, retorna false e armazena mensagem de erro no metodo setErros() que pode ser regatado pelo getErros()
     * @param type $valor
     * @return boolean
     */
    public function ValidaEmail($valor, $msg_erro = false, $obrigatorio = false)
    {
        if (!empty($valor)) {
            $conta = "/^[a-zA-Z0-9\._-]+@";
            $domino = "[a-zA-Z0-9\._-]+.";
            $extensao = "([a-zA-Z]{2,4})$/";
            $expressao = $conta . $domino . $extensao;
            if (preg_match($expressao, $valor))
                return $valor;
        }
        if ($obrigatorio) {
            $this->setErros($msg_erro);
            return false;
        }
        $this->setErros("<li>E-mail inválido.</li>");
        return false;
    }


    /**
     * Funcao validaSenha, recebe duas senhas e as comparam se forem diferente retorna falso
     * se tiver menos ou mais que 10 caracteres retorna falso
     * se tiver espacos em branco retorna falso
     * @param type $senha1
     * @param type $senha2
     * @return type
     */
    public function validaSenha($senha1, $senha2)
    {
        if ($senha1 !== $senha2) {
            $this->setErros('As senhas não conferem');
            return false;
        }
        if (strlen($senha1) != 8) {
            $this->setErros('A senha deve possuir 8 cacacteres.');
            return false;
        }
        if (strstr($senha1, " ")) {
            $this->setErros('A senha não deve possuir espaços em branco.');
            return false;
        }
        return true;
    }

    /**
     * responsável por retornar o conteúdo solicitado das páginas
     * @return string
     */
    function getConteudo()
    {
        $conteudo = "";

        if (is_array($this->conteudo)) {
            for ($i = 0; $i < count($this->conteudo); $i++) {
                $conteudo .= $this->conteudo[$i];
            }
        }
        return $conteudo;
    }



    public function getConteudoJSON()
    {
        // Atualiza data_is_array para refletir se o conteúdo original era um array
        return json_encode([
            'code' => $this->getErros() ? 201 : 200,
            'success' => $this->getErros() ? false : true,
            'data' => $this->conteudo // Não aplicar json_encode aqui
        ], JSON_UNESCAPED_UNICODE);
    }



    /**
     * responsável por atribuir conteúdo as páginas
     * @param string $valor
     */
    function setConteudo($valor)
    {
        $this->conteudo[] = $valor;
    }
}
