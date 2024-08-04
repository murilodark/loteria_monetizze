<?php
require_once("classesPHP/ClassesFuncionais/Function_Onload.php");
$aux_class_usuarios_sistema = new aux_class_usuario_sistema();
echo $aux_class_usuarios_sistema->getConteudoJSON();
?>
