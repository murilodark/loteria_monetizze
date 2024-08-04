<?php
include("./include_sessao_php.php");
$ClassSessao->logout();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include("include_html/include_metas_base.php") ?>
    <title>Login - Loteria</title>
</head>

<body>
    <?php include("include_html/include_header.php") ?>
    <div class="container">
        <form id="loginForm">
            <h2>Login</h2>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="senha">Senha:</label>
            <input type="password" id="senha" name="senha" required>
            <button type="submit">Login</button>
            <a href="/user_cadastro.php" class="link-cadastro">
                Quero me cadastrar
            </a>
            <input type="hidden" id="ACAO" name="ACAO" value="LOGIN">
        </form>
    </div>
    <?php include("include_html/include_footer.php") ?>
    <?php include("include_html/include_js_base.php") ?>
    <script>
        async function enviaFormLogin(formId, url) {
            try {
                const result = await handleFormSubmit(formId, url);
                if (result.success) {
                    // Redireciona para index.php se o login for bem-sucedido
                    window.location.href = 'index.php';
                } else {
                    // Exibe uma mensagem de erro ou lida com o erro de login
                    console.error('Login falhou:', result.message);
                }
            } catch (error) {
                console.error('Erro ao enviar o formulário:', error); // Lida com erros da Promise
            }
        }

        // Chama a função enviaFormLogin passando o ID do formulário e a URL da requisição
        enviaFormLogin('loginForm', 'api/api_user.php');
    </script>
</body>

</html>