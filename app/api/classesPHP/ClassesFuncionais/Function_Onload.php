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
// session_start();

function functionAutoload($classe) {
    $dir = str_replace("\\", "/", dirname(__FILE__));
    if (file_exists($dir . "/" . $classe . ".php")) {
        require_once($dir . "/" . $classe . ".php");
    } else {
        if (file_exists("classesPHP/ClassesAuxiliares/" . $classe . ".php")) {
            require_once("classesPHP/ClassesAuxiliares/" . $classe . ".php");
        } elseif (file_exists("classesPHP/ClassesBasicas/" . $classe . ".php")) {
            require_once("classesPHP/ClassesBasicas/" . $classe . ".php");
        } elseif (file_exists("classesPHP/ClassesDependentes/" . $classe . ".php")) {
            require_once("classesPHP/ClassesDependentes/" . $classe . ".php");
        } elseif (file_exists("classesPHP/ClassesFuncionais/" . $classe . ".php")) {
            require_once("classesPHP/ClassesFuncionais/" . $classe . ".php");
        } else {
            return false;
        }
    }
}

// Registrar a função de autoload
spl_autoload_register('functionAutoload');

// $new_sessao = new Class_Sessao();
// $C_Valida_Dados = new Class_Valida_Dados();
?>
