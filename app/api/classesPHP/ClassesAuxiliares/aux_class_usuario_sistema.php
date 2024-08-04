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

class aux_class_usuario_sistema extends Class_Valida_Dados
{

    //classes estrangeiras
    private $Class_usuario_sistema;
    private $db;
    private $Class_Sessao;
    private $aux_class_gerencia_permissao;

    //construtor da classe
    public function __construct()
    {
        $this->db = new DB();
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
        switch ($ACAO) {
            case "LOGIN":
                if (!$this->EfetuaLogin()) {
                    $this->setConteudo($this->getErros());
                    return;
                }
                $this->setConteudo($this->aux_class_gerencia_permissao->getArrayUsuarios());
                break;
            case "INSERT":

                $this->setConteudo($this->aux_class_gerencia_permissao->getArrayUsuarios());
                break;


                if (!$this->PegaPostsusuario_sistema()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                if (!$this->Valida_duplicidade_email_usuario()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                if (!$this->salvausuario_sistema()) {
                    $this->setConteudo($this->getErros());
                    break;
                }
                $this->setConteudo($this->Class_usuario_sistema->getArrayAtributos());
                break;                
            default:
            $this->setErros('Requisição incorreta. Não foi passado parâmetros válidos.');
            $this->setConteudo($this->getErros());
        }
    }


    public function EfetuaLogin()
    {
        $email = $this->getParametroJson("email", "O campo E-mail deve ser informado", true);
        $senha = $this->getParametroJson("senha", "O campo Senha deve ser informado", true);
        if ($this->getErros()) {
            return false;
        }
        $this->Class_usuario_sistema->setemail_usuario($email);
        $this->Class_usuario_sistema->setsenha_usuario($senha);

        if (!$this->Class_usuario_sistema->loginusuario_sistema()) {
            $this->setErros('E-mail ou senha incorretos.');
            return false;
        }
        $this->aux_class_gerencia_permissao->Login($this->Class_usuario_sistema);
        return true;
    }

    private function Valida_duplicidade_email_usuario()
    {
        $email =  $this->Class_usuario_sistema->getemail_usuario();
        if ($this->Class_usuario_sistema->carregausuario_sistema(false, " where email_usuario = '$email'")) {
            $this->setErros('E-mail já utilizado no sistema, insira outro.');
            return false;
        }
        return true;
    }





    function PegaPostsusuario_sistema()
    {
        //trata o post do campo

        $nome = $this->getParametroJson("nome", "O campo Nome deve ser informado", true);
        $this->Class_usuario_sistema->setnome_usuario($nome);

        $email = $this->getParametroJson("email", "O campo E-mail deve ser informado", true);
        $this->Class_usuario_sistema->setemail_usuario($email);

        $senha = $this->getParametroJson("senha", "O campo Senha deve ser informado", true);
        $confsenha = $this->getParametroJson("confsenha", "O campo Confirma Senha deve ser informado", true);

        $this->validaSenha($senha, $confsenha);
        if ($this->getErros()) {
            return false;
        }
        $this->Class_usuario_sistema->setsenha_usuario($senha);
        return true;
    }




    function salvausuario_sistema()
    {
        //inicia a transacao
        $this->db->transacao();
        if ($this->Class_usuario_sistema->inserirusuario_sistema()) {
            //efetua o commit
            $this->db->commit();
            return true;
        }
        $this->setErros("Ocorreu um erro ao cadastrar o novo Usuário, tente novamente.");
        //efetua um rollback
        $this->db->rollback();
        return false;
    }

    private function Excluirusuario_sistema()
    {

        if (!$this->Class_usuario_sistema->deletausuario_sistema()) {
            $this->setErros("<h2>Atenção! O registro '" . $this->Class_usuario_sistema->getnome() . "' não pode ser removido.</h2>");
            $this->setErros("<p>$this->Class_usuario_sistema->getResp()</p>");
            return false;
        }
        //$this->setConteudo("<h2>Registro removido com sucesso '".$this->Class_usuario_sistema->getnome()."'.</h2>");
        return true;
    }

    private function ListaAuxliar()
    {
        $sql = ' where idusuario_sistema';
        //INSERE A ACAO LISTA PARA NOVAS BUSCAS
        $parametros_extras = '&ACAO=LIST';
        if (!$this->Class_usuario_sistema->listausuario_sistema()) {
            $this->setConteudo('<h2>Não existem registros cadastrados para "usuario_sistema".</h2>');
        }
        $this->setConteudo($this->Class_usuario_sistema->getResp());
        return true;
    }
}
