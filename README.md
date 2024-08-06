# loteria_monetizze
desafio sistema de loterias e sorteios para colaboradores da monetizze

#Como configurar
Efetue o clone do repositório através do endereço
https://github.com/murilodark/loteria_monetizze.git

#iniciando o projeto
acesse o diretório clonado e execute o arquivo docker-compose.yml
utilizando o comando 
docker-compose up -d

#inicialização 
Se não houve problemas com o cocker como conflitos de usuários de banco de dados,
nesse ponto já foram criadas todas as tabelas e alguns registros inseridos.

As informações de conexão com o banco de dados estão inclusas no arquivo .env
e caso sejam alterada devem ser editadas também no aruivo
api/ClassesPHP/ClassesFuncionais/DB.php 
linhas:
// Nome do serviço definido no docker-compose.yml
20 - private static $server = 'db'; 
21 - private static $usuario = 'username';
22 - private static $senha = 'userpass';
23 - private static $banco = 'dbloteria';

#acessano o sistema
pela configuração horiginal do projeto o acesso se dá através do endereço
http://localhost:8989/

Efetue o cadastro e continue navegando pela funcionalidades.



#instruções para os teste:
Após rodar o docker-compose.yml, deve acessar o container do mesmo
#para acessar o container 
docker exec -it loterias-app-1 bash


#verificar se o composer foi configurado verificando a versao
(essa instlação está definido nos arquivos do docker dockerfile)
composer --version

#se não estiver configurado, instaleo manualemnte 
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#confirme através da verificao da versão
composer --version

#instale agora o phpunit
composer require --dev phpunit/phpunit

#agora que foi instalado, utilize o comando
vendor/bin/phpunit --colors=always
para rodar os testes que estão localizados no diretório app/tests



