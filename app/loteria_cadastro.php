<?php 
include("./include_sessao_php.php") 
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <?php include("include_html/include_metas_base.php") ?>
    <title>Cadastro de Loteria</title>
</head>
<body>
    <?php include("include_html/include_header.php") ?>
    <div class="container">
     
        <form method="post" id="formCadastroLoteria" >
            <h2>Cadastro de Loteria</h2>
            <label for="nome_loteria">Nome Loteria:</label>
            <input type="text" id="nome_loteria" name="nome_loteria" required>
            <label for="data_sorteio">Data sorteio:</label>
            <input type="date" id="data_sorteio" name="data_sorteio" placeholder="Data (dd/mm/aaaa)" required>
            <input type="hidden" id="ACAO" name="ACAO" value="INSERT">
            <button type="submit">Inserir</button>
        </form>
    </div>
    <?php include("include_html/include_footer.php") ?>
    <?php include("include_html/include_js_base.php") ?>
    <script>
        async function enviaFormCadastroLoteria(formId, url) {
            try {
                const result = await handleFormSubmit(formId, url);
                if (result.success) {
                    const data = result.data[0];
                    //redireciona para tela visualização da loteria
                    window.location.href = 'loteria_visualiza.php?idloteria='+data.idloteria;
                } else {
                    console.error('Erro:', result.message);
                }
            } catch (error) {
                console.error('Erro ao enviar o formulário:', error); // Lida com erros da Promise
            }
        }
        // Chama a função enviaFormLogin passando o ID do formulário e a URL da requisição
        enviaFormCadastroLoteria('formCadastroLoteria', 'api/api_loteria.php');
    </script>
</body>

</html>