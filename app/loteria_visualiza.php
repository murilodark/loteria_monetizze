<?php
include("./include_sessao_php.php");
// Obter e validar o valor do parâmetro idloteria
$idloteria = isset($_GET['idloteria']) ? $_GET['idloteria'] : '';
$idloteria = filter_var($idloteria, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$erroid = '';
if ($idloteria === false) {
    $erroid = 'Não foi informado uma loteria para consulta.';
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include("include_html/include_metas_base.php") ?>
    <title>Detalhes da Loteria</title>
</head>

<body>
    <?php include("include_html/include_header.php") ?>
    <div class="container">
        <form method="post" id="formLoadLoteria">
            <input type="hidden" id="idloteria" name="idloteria" value="<?php echo $idloteria ?>">
            <input type="hidden" id="erroid" name="erroid" value="<?php echo $erroid ?>">
            <input type="hidden" id="ACAO" name="ACAO" value="LOAD">
        </form>

    </div>
    <?php include("include_html/include_footer.php") ?>
    <?php include("include_html/include_js_base.php") ?>
    <script>
        async function consultaLoteria(formId, url) {
            try {
                const result = await consultaForm(formId, url);

                if (result.success) {
                    const data = result.data[0]; // Acessa o primeiro item do array de dados

                    // Inicializa o conteúdo HTML do div
                    let visualizaLoteriaHTML = `
    <p><strong>ID da Loteria:</strong> ${data.idloteria || 'Não disponível'}</p>
    <p><strong>Cadastro por:</strong> ${data.usuario_sistema_cadastro || 'Não disponível'}</p>
    <p><strong>Data de Cadastro:</strong> ${data.data_cadastro || 'Não disponível'}</p>
    <p><strong>Nome da Loteria:</strong> ${data.nome_loteria || 'Não disponível'}</p>
    <p><strong>Data do Sorteio:</strong> ${data.data_sorteio || 'Não disponível'}</p>   
    <p><strong>Status da Loteria:</strong> ${data.status_loteria || 'Não disponível'}</p>
    <p><strong>Sorteado por:</strong> ${data.usuario_sistema_sorteio || 'Não disponível'}</p>
    <p><strong>Dezenas Sorteadas:</strong> ${data.dezenas_sorteadas || 'Não disponível'}</p>
`;

                    // Condicional para adicionar os links apenas se data.data_sorteio estiver vazio
                    if (data.status_loteria == "Andamento" ) {
                        visualizaLoteriaHTML += `
        <a href='loteria_sorteio.php?idloteria=${data.idloteria}' class='link-login'>Efetuar Sorteio</a>
        <a href='loteria_gera_dezenas.php?idloteria=${data.idloteria}' class='link-cadastro'>Gerar Minhas Dezenas</a>
    `;
                    }

                    // Atualiza o conteúdo do div com id "formLoadLoteria"
                    const visualizaLoteria = document.getElementById('formLoadLoteria');
                    visualizaLoteria.innerHTML = visualizaLoteriaHTML;

                } else {
                    console.error('Erro:', result.message);
                    document.getElementById('formLoadLoteria').innerText = 'Erro ao carregar dados.';
                }
            } catch (error) {
                console.error('Erro ao enviar o formulário:', error); // Lida com erros da Promise
            }
        }

        // Carregar dados ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            if (!document.getElementById('erroid').value) {
                console.log('Página carregada, iniciando consulta...');
                consultaLoteria('formLoadLoteria', 'api/api_loteria.php');
            } else {
                const visualizaLoteria = document.getElementById('formLoadLoteria');
                visualizaLoteria.innerHTML = `
                        <p><strong>Erro: </strong> ${document.getElementById('erroid').value}</p>
                    `;
            }
        });
    </script>
</body>

</html>