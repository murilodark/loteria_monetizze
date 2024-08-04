<?php 
include("./include_sessao_php.php") 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include("include_html/include_metas_base.php") ?>
    <title>Cadastro - Loteria</title>
</head>
<body>
    <?php include("include_html/include_header.php") ?>
    <div class="container">

        <form method="post" id="formCadastro">
            <h2>Cadastro Usuário</h2>
            <label for="nome">Nome:</label>
            <input type="text" id="nome" name="nome" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <label for="confsenha">Confirma Senha:</label>
            <input type="password" id="confsenha" name="confsenha" required>
            <input type="hidden" id="ACAO" name="ACAO" value="INSERT">

            <button type="submit">Cadastrar</button>
        </form>
    </div>
    <?php include("include_html/include_footer.php") ?>
    <?php include("include_html/include_js_base.php") ?>
    <script>
        async function enviaFormCadastro(formId, url) {
            try {
                const result = await handleFormSubmit(formId, url);
                // console.log(result); // Mostra o resultado no console
            } catch (error) {
                console.error('Erro ao enviar o formulário:', error); // Lida com erros da Promise
            }
        }

        // Chama a função enviaFormLogin passando o ID do formulário e a URL da requisição
        enviaFormCadastro('formCadastro', 'api/api_user.php');
    </script>
</body>

</html>