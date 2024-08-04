<?php
include("./include_sessao_php.php");

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <?php include("include_html/include_metas_base.php") ?>
    <title>Listagem de Loteria</title>
</head>

<body>
    <?php include("include_html/include_header.php") ?>
    <div class="container">
        <form method="post" id="formListaLoteria" style="display: none;">
            <input type="hidden" id="ACAO" name="ACAO" value="LIST">
        </form>
        <div class="table-container" id="tabela-loterias">
            <table>
                <thead>
                    <tr>
                        <th>ID Loteria</th>
                        <th>Data Cadastro</th>
                        <th>Data Sorteio</th>
                        <th>Nome da Loteria</th>
                        <th>Dezenas Sorteadas</th>
                        <th>Status</th>
                        <th>Usuário Cadastro</th>
                        <th>Usuário Sorteio</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Os dados da tabela serão inseridos aqui via JavaScript -->
                </tbody>
            </table>
        </div>
    </div>
    <?php include("include_html/include_footer.php") ?>
    <?php include("include_html/include_js_base.php") ?>
    <script>
        async function consultaLoteria(formId, url) {
            try {
                const result = await consultaForm(formId, url);

                if (result.success) {
                    const data = result.data[0]; // Acessa o primeiro item do array de dados

                    // Cria a tabela HTML
                    let tabelaHTML = `
                        <table border="1">
                            <thead>
                                <tr>
                                    <th>ID Loteria</th>
                                    <th>Data Cadastro</th>
                                    <th>Data Sorteio</th>
                                    <th>Nome Loteria</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;

                    // Preenche a tabela com os dados
                    data.forEach(item => {
                        tabelaHTML += `
                            <tr>
                                <td>${item.idloteria}</td>
                                <td>${item.data_cadastro}</td>
                                <td>${item.data_sorteio || 'Não disponível'}</td>
                                <td>${item.nome_loteria || 'Não disponível'}</td>
                                <td>${item.status_loteria || 'Não disponível'}</td>
                                <td>
                                    <a href="loteria_visualiza.php?idloteria=${item.idloteria}">Visualizar</a>
                                </td>
                            </tr>
                        `;
                    });

                    tabelaHTML += `
                            </tbody>
                        </table>
                    `;

                    // Atualiza o conteúdo da div com id "tabela-loterias" com a tabela HTML
                    const tabelaLoterias = document.getElementById('tabela-loterias');
                    tabelaLoterias.innerHTML = tabelaHTML;
                } else {
                    console.error('Erro:', result.message);
                    document.getElementById('tabela-loterias').innerText = 'Erro ao carregar dados.';
                }
            } catch (error) {
                console.error('Erro ao enviar o formulário:', error); // Lida com erros da Promise
            }
        }

        // Carregar dados ao carregar a página
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Página carregada, iniciando consulta...');
            consultaLoteria('formListaLoteria', 'api/api_loteria.php');

        });
    </script>
</body>

</html>