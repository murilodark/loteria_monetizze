async function handleFormSubmit(formId, url) {
    const form = document.getElementById(formId);
    const loadingElement = document.getElementById('loading');
    const errorElement = document.getElementById('error');

    // Retorna uma Promise que será resolvida quando o formulário for enviado
    return new Promise((resolve, reject) => {
        form.addEventListener('submit', async function (event) {
            event.preventDefault(); // Evita o envio padrão do formulário

            const formData = new FormData(this); // Captura os dados do formulário
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value; // Converte os dados do formulário em um objeto
            });

            // Mostra o elemento de carregamento e esconde o elemento de erro
            loadingElement.style.display = 'flex';
            errorElement.style.display = 'none';
            errorElement.textContent = '';

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(data) // Converte o objeto para JSON
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const jsonArray = await response.json(); // Converte a resposta para JSON
                if(jsonArray.success==false){
                    errorElement.textContent = `Erro: ${jsonArray.data}`;
                    errorElement.style.display = 'block';
                }
                resolve(jsonArray); // Resolve a Promise com o JSON recebido
            } catch (error) {
                console.error('Erro ao buscar o array JSON:', error);
                errorElement.textContent = `Erro: ${error.message}`;
                errorElement.style.display = 'block';
                reject({ 'code': 201, 'success': false, 'data_is_array': false, 'data': error.message }); // Rejeita a Promise com o erro
            } finally {
                // Esconde o elemento de carregamento
                loadingElement.style.display = 'none';
            }
        });
    });
}

// Chame a função para cada formulário e URL correspondente
//handleFormSubmit('dataForm1', 'https://api.exemplo.com/endpoint1');
//handleFormSubmit('dataForm2', 'https://api.exemplo.com/endpoint2');

