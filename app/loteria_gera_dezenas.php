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
    <title>Minas Dezenas</title>
</head>

<body>
    <?php include("include_html/include_header.php") ?>
    <div class="container">
        <form method="post" id="formGeraJogosLoteria">
            <h2>Gerar meus jogos</h2>
            <div id="campos-info">
            </div>
            <div id="campos-gera">
                <label for="quant_dezenas">Número de Dezenas</label>
                <input type="number" id="quant_dezenas" name="quant_dezenas" required>
                <label for="quant_jogos">Quantidade de Jogos:</label>
                <input type="number" id="quant_jogos" name="quant_jogos" required>
                <input type="hidden" id="idloteria" name="idloteria" value="<?php echo $idloteria ?>" required>
                <input type="hidden" id="erroid" name="erroid" value="<?php echo $erroid ?>">
                <input type="hidden" id="ACAO" name="ACAO" value="LISTAJOGOSUSUARIO">
                <button type="submit">Gerar Jogos</button>

            </div>
            <div id="error"></div>
        </form>
    </div>
    <div class="container">
        <div class="table-container" id="tabela-jogos">
            <table>
                <thead>
                    <tr>
                        <th>Id </th>
                        <th>N° Dezenas</th>
                        <th>Dezenas</th>
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
        function IncluiJogosTabela(dataJogos) {
            if (dataJogos) {
                //inicia a tabela de jogos caso exista
                // Inicia a tabela de jogos caso exista
                let tabelaJogosHTML = `
                    <table>
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>N° Dezenas</th>
                                <th>Dezenas</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                // Preenche a tabela com os dados
                dataJogos.forEach(item => {
                    let classVencedor='';
                    if (item.jogo_vencedor == 'S') {
                        classVencedor = 'jogo-vencedor';
                    }
                    tabelaJogosHTML += `
                        <tr class="${classVencedor}">
                            <td>${item.idusuario_jogos}</td>
                            <td>${item.quant_dezenas}</td>
                            <td>${item.dezenas_escolhidas}</td>
                        </tr>
                    `;
                });

                tabelaJogosHTML += `
                        </tbody>
                    </table>
                `;
                // Atualiza o conteúdo da div com id "tabela-loterias" com a tabela HTML
                const tabelaJogosElement = document.getElementById('tabela-jogos');
                tabelaJogosElement.innerHTML = tabelaJogosHTML;
            }
        }

        async function consultaLoteria(formId, url, infoLoteria, infoUsuario) {
            try {
                const result = await consultaForm(formId, url);

                if (result.success) {
                    const data = result.data[0]; // dados da loteria
                    const dataJogos = result.data[1]; // Jogos do usuario
                    const dataLimite = result.data[2]; // Total jogos usuario

                    // Inicializa o conteúdo da informaçao da loteria
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
                    // Condicional para adicionar os links apenas se data.data_sorteio estiver vazio
                    if (data.status_loteria != "Andamento" || dataLimite.limiteJogo == 0) {
                        const visualizaInfoUsuario = document.getElementById(infoUsuario);
                        let visualizaInfoHTML = `<p><strong>Limite de 50 jogos atingido</strong></p> `;
                        if (dataLimite.limiteJogo == 0) {
                            visualizaInfoHTML += ` <p><strong>Seus Jogos:</strong> ${dataLimite.jogosrealizados || 'Não disponível'}</p>`;
                        }
                        visualizaInfoUsuario.innerHTML = visualizaInfoHTML;
                    } else {
                        visualizaLoteriaHTML += ` <p><strong>Limite de Jogos:</strong> ${dataLimite.limiteJogo}</p>`;
                    }
                    // Atualiza o conteúdo da informação da loteria
                    const visualizaLoteria = document.getElementById(infoLoteria);
                    visualizaLoteria.innerHTML = visualizaLoteriaHTML;

                    if (dataJogos) {
                        IncluiJogosTabela(dataJogos);
                    }

                    // Atualiza o valor do input hidden para próxima ação
                    document.getElementById('ACAO').value = 'GERAJOGO';
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
                consultaLoteria('formGeraJogosLoteria', 'api/api_loteria.php', 'campos-info', 'campos-gera');
            } else {
                const visualizaLoteria = document.getElementById('formGeraJogosLoteria');
                visualizaLoteria.innerHTML = `
                        <p><strong>Erro: </strong> ${document.getElementById('erroid').value}</p>
                    `;
            }
        });


        async function enviaFormGeraJogos(formId, url) {
            try {
                const result = await handleFormSubmit(formId, url);
                if (result.success) {
                    const data = result.data[0];
                    alert('Jogos gerados com sucesso.');
                    //efetua um reload
                    window.location.href = 'loteria_gera_dezenas.php?idloteria=' + data.idloteria;
                } else {
                    console.error('Erro:', result.message);
                }
            } catch (error) {
                console.error('Erro ao enviar o formulário:', error.message); // Lida com erros da Promise
            }
        }

        // Chama a função enviaFormLogin passando o ID do formulário e a URL da requisição
        enviaFormGeraJogos('formGeraJogosLoteria', 'api/api_loteria.php');
    </script>
</body>

</html>