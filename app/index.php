<?php
include("./include_sessao_php.php");
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include("include_html/include_metas_base.php") ?>
    <title>Loterias</title>
</head>

<body>
    <?php include("include_html/include_header.php") ?>
    <div class="container">
        <div>
        <?php
    // Supondo que $ClassSessao já foi instanciado corretamente
    echo $ClassSessao->validaSessao()
        ? "<h2>" . $ClassSessao->getNomeUsuario() . ", seja bem-vindo à loteria!</h2>"
        : "<p>Efetue o login para continuar</p>
           <a href='user_login.php' class='link-login'>Efetuar login</a>
           <p>ou se cadastre</p>
           <a href='user_cadastro.php' class='link-cadastro'>Novo Cadastro</a>";
    ?>


        </div>
    </div>
    <?php include("include_html/include_footer.php") ?>
    <?php include("include_html/include_js_base.php") ?>

</body>

</html>