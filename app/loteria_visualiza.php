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
            <input type="hidden" id="todososjogos" name="todososjogos" value="SIM">
            <input type="hidden" id="ACAO" name="ACAO" value="LOAD">
        </form>

    </div>
    <div id="error"></div>
    <div class="container">
        <div class="table-container" id="tabela-jogos">
            <table>
                <thead>
                    <tr>
                        <th>Id </th>
                        <th>N° Dezenas</th>
                        <th>Dezenas</th>
                        <th>Colaborador</th>
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
                                <th>Colaborador</th>
                            </tr>
                        </thead>
                        <tbody>
                `;

                // Preenche a tabela com os dados
                dataJogos.forEach(item => {
                    let classVencedor = '';
                    if (item.jogo_vencedor == 'S') {
                        classVencedor = 'jogo-vencedor';
                    }
                    tabelaJogosHTML += `
                        <tr class="${classVencedor}">
                            <td>${item.idusuario_jogos}</td>
                            <td>${item.quant_dezenas}</td>
                            <td>${item.dezenas_escolhidas}</td>
                            <td>${item.nome_usuario}</td>
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
        async function consultaLoteria(formId, url) {
            try {
                const result = await consultaForm(formId, url);

                if (result.success) {
                    const data = result.data[0]; // Acessa o primeiro item do array de dados
                    const dataJogos = result.data[1]; // Jogos do usuario
                    //carrega no html do form as informações da loteria 
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

                    // Condicional para adicionar o link de Meus Jogos e o botão para Sortea, 
                    //inclui tambem os inputs necessários para postagem do sorteio 
                    if (data.status_loteria == "Andamento") {
                        visualizaLoteriaHTML += `
                         <input type="hidden" id="idloteria" name="idloteria" value="${data.idloteria}">
                    <input type="hidden" id="ACAO" name="ACAO" value="EFETUASORTEIO">
                    <button type="submit">Efetuar Sorteio</button>
                     `;
                    }
                    visualizaLoteriaHTML += `
                            <a href='loteria_gera_dezenas.php?idloteria=${data.idloteria}' class='link-cadastro'>Meus Jogos</a>
                            
                         `;

                    // Atualiza o conteúdo do div com id "formLoadLoteria"
                    const visualizaLoteria = document.getElementById('formLoadLoteria');
                    visualizaLoteria.innerHTML = visualizaLoteriaHTML;
                    if (dataJogos) {
                        IncluiJogosTabela(dataJogos);
                    }


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


        async function enviaFormLoteria(formId, url) {
            try {
                const result = await handleFormSubmit(formId, url);
                if (result.success) {
                    const data = result.data[0];
                    alert('Registro alterado com sucesso.');
                    //redireciona para tela visualização da loteria
                    window.location.href = 'loteria_visualiza.php?idloteria=' + data.idloteria;

                } else {
                    console.error('Erro:', result.message);
                }
            } catch (error) {
                console.error('Erro ao enviar o formulário:', error); // Lida com erros da Promise
            }
        }
        // Chama a função enviaFormLogin passando o ID do formulário e a URL da requisição
        enviaFormLoteria('formLoadLoteria', 'api/api_loteria.php');
    </script>
</body>

</html>