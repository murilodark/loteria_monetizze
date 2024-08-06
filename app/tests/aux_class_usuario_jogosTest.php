<?php

use PHPUnit\Framework\TestCase;


require_once './vendor/autoload.php';
require_once("./api/classesPHP/ClassesFuncionais/Function_Onload.php");
require_once './api/classesPHP/ClassesAuxiliares/aux_class_loteria.php';
require_once './api/classesPHP/ClassesAuxiliares/aux_class_usuario_jogos.php';
require_once './api/classesPHP/ClassesBasicas/Class_loteria.php';
require_once './api/classesPHP/ClassesBasicas/Class_usuario_sistema.php';
require_once './api/classesPHP/ClassesBasicas/Class_usuario_jogos.php';
require_once './api/classesPHP/ClassesFuncionais/aux_class_gerencia_permissao.php';
require_once './api/classesPHP/ClassesFuncionais/Class_Sessao.php';


class aux_class_usuario_jogosTest extends TestCase
{
    private $auxClassUsuarioJogos;

    protected function setUp(): void
    {
        $this->auxClassUsuarioJogos = $this->getMockBuilder(aux_class_usuario_jogos::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    private function gerarListaDezenasJaGeradas($quantidade, $quantidadeDezenas)
    {
        $lista = [];
        while (count($lista) < $quantidade) {
            $dezenas = range(1, 60);
            shuffle($dezenas);
            $jogo = array_slice($dezenas, 0, $quantidadeDezenas);
            sort($jogo);
            $stringJogo = implode(',', $jogo);
            if (!in_array($stringJogo, $lista)) {
                $lista[] = $stringJogo;
            }
        }
        return $lista;
    }

    public function testGerarJogosUnicos()
    {
        // Usando Reflection para acessar o método privado
        $reflection = new ReflectionClass($this->auxClassUsuarioJogos);
        $method = $reflection->getMethod('gerarJogosUnicos');
        $method->setAccessible(true);

        // Definir parâmetros de entrada
        $quantidadeDezenas = 6;
        $qaunt_jogos_solicitados = 5;
        $listaDezenasJaGeradas = $this->gerarListaDezenasJaGeradas(10, $quantidadeDezenas);
        // Executar o método
        $result = $method->invoke($this->auxClassUsuarioJogos, $quantidadeDezenas, $qaunt_jogos_solicitados, $listaDezenasJaGeradas);

        // Verificar se o resultado contém a quantidade correta de jogos
        $this->assertCount($qaunt_jogos_solicitados, $result, 'O número de jogos gerados deve ser igual a $qaunt_jogos_solicitados');

        // Verificar se todos os jogos são únicos
        $resultArray = array();
        foreach ($result as $jogo) {
            $this->assertNotContains($jogo, $resultArray, 'O jogo não deve se repetir entre os jogos gerados');
            $resultArray[] = $jogo;
        }

        // Verificar se cada jogo tem a quantidade correta de dezenas
        foreach ($result as $jogo) {
            $dezenas = explode(',', $jogo);
            $this->assertCount($quantidadeDezenas, $dezenas, 'Cada jogo deve ter $quantidadeDezenas dezenas');
        }
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
