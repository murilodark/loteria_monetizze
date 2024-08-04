<?php
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header('location:index.php');
}

/**
 * CLASSE SESSAO RESPONSÁVEL POR EFETUAR O CONTROLE DE SESSÕES E PERMISSÕES NO SISTEMA.
 */
class Class_Sessao extends Class_Erros
{


    // Início do construtor, caso não exista uma sessão será criada
    function __construct()
    {
        $this->start();
    }

    // Método privado para iniciar a sessão
    private function start()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Método responsável por inserir a sessão com informações do usuário
     */
    public function GeraSessaoUsuario(Class_usuario_sistema $Class_usuario_sistema)
    {
        $this->start();
        $this->setIdUsuario($Class_usuario_sistema->getidusuario_sistema());
        $this->setEmailUsuario($Class_usuario_sistema->getemail_usuario());
        $this->setNomeUsuario($Class_usuario_sistema->getnome_usuario());
        $this->setTipoUsuario($Class_usuario_sistema->gettipo_usuario());
    }

    // Métodos responsáveis por criar, excluir e consultar variáveis de sessão
    private function setVar($_var, $valor)
    {
        $_SESSION[$_var] = $valor;
    }

    private function unSetvar($_var)
    {
        if ($this->getVar($_var)) {
            unset($_SESSION[$_var]);
        }
    }

    private function getVar($_var)
    {
        return $_SESSION[$_var] ?? false;
    }


    // Método para logout (destrói variáveis de login, mas mantém o ID da sessão)
    public function logout()
    {
        $this->unSetvar('ID_USUARIO');
        $this->unSetvar("NOME_USUARIO");
        $this->unSetvar('EMAIL_USUARIO');
        $this->unSetvar("TIPO_USUARIO");
        session_unset();
        session_destroy();
        $this->start();
    }

    // Método para destruir todas as variáveis de sessão
    public function destroy($_inicia = FALSE)
    {
        if (session_status() == PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }
        if ($_inicia === TRUE) {
            $this->start();
        }
    }

    // Método para validar todas as variáveis de sessão
    function validaSessao()
    {
        return $this->getIdUsuario() && $this->getNomeUsuario() && $this->getEmailUsuario();
    }


    private function setIdUsuario($valor)
    {
        return $this->setVar("ID_USUARIO", $valor);
    }
    private function setEmailUsuario($valor)
    {
        return $this->setVar("EMAIL_USUARIO", $valor);
    }
    private function setNomeUsuario($valor)
    {
        return $this->setVar("NOME_USUARIO", $valor);
    }
    private function setTipoUsuario($valor)
    {
        return $this->setVar("TIPO_USUARIO", $valor);
    }




    public function getArrayUsuarios()
    {
        return [
            'idusuario_sistema' => $this->getIdUsuario(),
            'nome_usuario' => $this->getNomeUsuario(),
            'email_usuario' => $this->getEmailUsuario(),
            'tipo_usuario' => $this->getTipoUsuario(),
        ];
    }


    // Métodos get
    public function getIdSessao()
    {
        return session_id();
    }
    public function getIdUsuario()
    {
        return $this->getVar("ID_USUARIO");
    }
    public function getEmailUsuario()
    {
        return $this->getVar("EMAIL_USUARIO");
    }
    public function getNomeUsuario()
    {
        return $this->getVar("NOME_USUARIO");
    }
    public function getTipoUsuario()
    {
        return $this->getVar("TIPO_USUARIO");
    }
}
