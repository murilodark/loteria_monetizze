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


class Class_Erros
{

    //put your code here
    private $erros;

    function __construct()
    {
    }
    public function setErros($erros)
    {
        $this->erros[] = $erros;
    }

    public function getErros()
    {
        $conteudo = "";
        if (is_array($this->erros)) {
            for ($i = 0; $i < count($this->erros); $i++) {
                $conteudo .= $this->erros[$i];
            }
        }
        return $conteudo;
    }

    function getErrosJSON()
    {
        $conteudo = "";
        if (is_array($this->erros)) {
            for ($i = 0; $i < count($this->erros); $i++) {
                $conteudo .= $this->erros[$i];
            }
        }
        return json_encode([
            'code' => 201,
            'success' => false,
            'data_is_array' => is_array($conteudo),
            'data' => $conteudo
        ]);
    }
}
