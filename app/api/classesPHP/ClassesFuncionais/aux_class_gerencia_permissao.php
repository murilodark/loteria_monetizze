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

/**
 * Classe responsável por gerenciar o acesso dos usuarios no sistema,
 * cria os menus de acordo com o seu perfil
 * disponibiliza botões de edição e consulta de acordo com o perfil do usuário
 */
class aux_class_gerencia_permissao extends Class_Valida_Dados
{

    private $Class_Sessao;
    public function __construct()
    {
        $this->Class_Sessao = new Class_Sessao();
    }

    /**
     * Método responsável por validar a sessao do usuário
     * deve ser chamado em scripts que necessita de uma sessão aberta
     */
    function ValidaSessao()
    {

        if (!$this->Class_Sessao->validaSessao()) {
            $this->Class_Sessao->destroy();
            $this->setErros('Algo deu errado com a sua sessão. Efetue o login para continuar.');
            return false;
        }
        return true;
    }

    public function Login(Class_usuario_sistema $Class_usuario_sistema)
    {
        $this->Class_Sessao->GeraSessaoUsuario($Class_usuario_sistema);
        return true;
    }

    public function getArrayUsuarios()
    {
        return  $this->Class_Sessao->getArrayUsuarios();
    
    }

    /**
     * Logout() método responsável por efetuar o logout
     * pode ser implementada alguma regra
     */
    public function Logout()
    {
        //implementa alguma regra caso necessário
        $this->Class_Sessao->logout();
    }


    /**
     * Verifica_Permissao() método responsável por validar a permissão de acesso 
     * para um determinado perfil de usuario, nesse caso o tipo do usuário
     * @return boorlean
     */
    function Verifica_Permissao($tipo_usuario)
    {
        return $this->Class_Sessao->getTipoUsuario() == $tipo_usuario;
    }
}
