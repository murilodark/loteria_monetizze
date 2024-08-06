<?php

use PHPUnit\Framework\TestCase;

class aux_class_loteriaTest extends TestCase
{
    private $auxClassLoteria;

    protected function setUp(): void
    {
        // Aqui você pode inicializar o banco de dados ou mocks
        $this->auxClassLoteria = new aux_class_loteria();
    }

    public function testPegaPostsloteria()
    {
        // Supondo que você tenha um método para definir parâmetros JSON
        $this->auxClassLoteria->setParametroJson("nome_loteria", "Mega Sena");
        $this->auxClassLoteria->setParametroJson("data_sorteio", "2024-12-31");
        $result = $this->auxClassLoteria->PegaPostsloteria();
        $this->assertTrue($result);
    }

    public function testSalvaLoteria()
    {
        // Mock da classe Class_loteria
        $mockLoteria = $this->createMock(Class_loteria::class);
        $mockLoteria->method('insereloteria')->willReturn(true);

        $this->auxClassLoteria->setClassLoteria($mockLoteria);
        $result = $this->auxClassLoteria->salvaloteria();
        $this->assertTrue($result);
    }

    public function testEfetuaSorteio()
    {
        // Mock das classes necessárias
        $mockClassUsuarioJogos = $this->createMock(Class_usuario_jogos::class);
        $mockClassUsuarioJogos->method('listaUsuarioJogos')->willReturn(true);

        $this->auxClassLoteria->setClassUsuarioJogos($mockClassUsuarioJogos);
        $result = $this->auxClassLoteria->EfetuaSorteio();
        $this->assertTrue($result);
    }

    public function testValidaQuantidadeJogosUsuario()
    {
        // Mock da classe Class_usuario_jogos
        $mockClassUsuarioJogos = $this->createMock(Class_usuario_jogos::class);
        $mockClassUsuarioJogos->method('retornaQuantidadeRegistrosUsuarioJogos')->willReturn(10);

        $this->auxClassLoteria->setClassUsuarioJogos($mockClassUsuarioJogos);
        $result = $this->auxClassLoteria->ValidaQuantidadeJogosUsuario();
        $this->assertTrue($result);
    }

    public function testGeraJogosUsuario()
    {
        // Mock da classe Class_usuario_jogos
        $mockClassUsuarioJogos = $this->createMock(Class_usuario_jogos::class);
        $mockClassUsuarioJogos->method('getquant_dezenas')->willReturn(6);
        $mockClassUsuarioJogos->method('verificarJogoUnico')->willReturn(true);
        $mockClassUsuarioJogos->method('salvarJogoUsuario')->willReturn(true);

        $this->auxClassLoteria->setClassUsuarioJogos($mockClassUsuarioJogos);
        $result = $this->auxClassLoteria->GeraJogosUsuario();
        $this->assertTrue($result);
    }
}
