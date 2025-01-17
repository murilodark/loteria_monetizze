<?php

use PHPUnit\Framework\TestCase;

require_once './vendor/autoload.php';
require_once("./api/classesPHP/ClassesFuncionais/Function_Onload.php");
require_once './api/classesPHP/ClassesAuxiliares/aux_class_loteria.php';
require_once './api/classesPHP/ClassesBasicas/Class_loteria.php';
require_once './api/classesPHP/ClassesBasicas/Class_usuario_sistema.php';
require_once './api/classesPHP/ClassesBasicas/Class_usuario_jogos.php';
require_once './api/classesPHP/ClassesFuncionais/aux_class_gerencia_permissao.php';
require_once './api/classesPHP/ClassesFuncionais/Class_Sessao.php';

class aux_class_loteriaTest extends TestCase
{
    private $auxClassLoteria;

    protected function setUp(): void
    {
        $this->auxClassLoteria = $this->getMockBuilder(aux_class_loteria::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function gerarListaDeJogos($quantidade, $quantidadeDezenas)
    {
        $lista = [];
        while (count($lista) < $quantidade) {
            $dezenas = range(1, 60);
            shuffle($dezenas);
            $jogo = array_slice($dezenas, 0, $quantidadeDezenas);
            sort($jogo);
            $stringJogo = implode(',', $jogo);
            if (!in_array($stringJogo, $lista)) {
                // $lista[] = ['arrayDezenas' => $jogo];
                $lista[] =   [
                    "idjogo" => rand(),
                    "arrayDezenas" => $jogo
                ];
            }
        }
        return $lista;
    }

    public function testEfetuaSorteio()
{
    // Simular a variável $_SERVER['REQUEST_METHOD']
    $_SERVER['REQUEST_METHOD'] = 'GET';

    // Usando Reflection para acessar o método privado
    $reflection = new ReflectionClass($this->auxClassLoteria);
    $method = $reflection->getMethod('EfetuaSorteio');
    $method->setAccessible(true);

    // Criar jogos simulados para testar
    $arrayJogos = $this->gerarListaDeJogos(10, 7); // Ajuste o número de dezenas conforme necessário

    // Executar o método
    list($jogoPremiado, $dezenasPremiadas) = $method->invoke($this->auxClassLoteria, $arrayJogos);

    // Debug info
    // echo "Jogo Premiado: \n";
    // var_dump($jogoPremiado);
    // echo "Dezenas Premiadas: " . implode(',', $dezenasPremiadas) . "\n";

    // Verificação do jogo premiado
    $this->assertNotEmpty($jogoPremiado, 'Não foi encontrado um jogo premiado.');
    $this->assertCount(1, $jogoPremiado, 'Deve haver exatamente um jogo premiado.');

    // O primeiro elemento do array de jogos premiados pode ter uma chave específica
    $jogoPremiadoDezenas = reset($jogoPremiado)['arrayDezenas'] ?? [];

    // Verifica se o jogo premiado contém todas as 6 dezenas sorteadas
    foreach ($dezenasPremiadas as $dezena) {
        if (!in_array($dezena, $jogoPremiadoDezenas)) {
            $this->fail("Erro: A dezena sorteada {$dezena} não está presente no jogo premiado. -> Jogo premiado: " . implode(',', $jogoPremiadoDezenas));
        }
    }

    // Verifica se as dezenas premiadas são únicas
    $this->assertCount(count(array_unique($dezenasPremiadas)), $dezenasPremiadas, 'As dezenas premiadas não devem se repetir.');
}


    // Método auxiliar para chamar métodos privados
    private function callMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new ReflectionClass($object);
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }
}
