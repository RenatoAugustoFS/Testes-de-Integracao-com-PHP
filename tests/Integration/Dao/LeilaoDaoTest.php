<?php

namespace Alura\Leilao\Tests\Integration\Dao;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Infra\ConnectionCreator;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
    private static \PDO $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory::');
        self::$pdo->exec(
            'create table leiloes (
            id INTEGER primary key,
            descricao TEXT,
            finalizado BOOL,
            dataInicio TEXT
        );');
    }

    protected function setUp(): void
    {

        self::$pdo->beginTransaction();
    }

    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesFinalizados(array $leiloes)
    {
        //Arrange
        $leilaoDao = new LeilaoDao(self::$pdo);

        foreach ($leiloes as $leilao){
            $leilaoDao->salva($leilao);
        }

        //Act
        $leiloes = $leilaoDao->recuperarFinalizados();

        //Assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Fiat 147', $leiloes[0]->recuperarDescricao());
    }

    /**
     * @dataProvider leiloes
     */
    public function testBuscaLeiloesNaoFinalizados(array $leiloes)
    {
        //Arrange
        $leilaoDao = new LeilaoDao(self::$pdo);

        foreach ($leiloes as $leilao){
            $leilaoDao->salva($leilao);
        }

        //Act
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        //Assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Variante 0km', $leiloes[0]->recuperarDescricao());
    }

    public function leiloes()
    {
        $leilaoNaoFinalizado = new Leilao('Variante 0km');

        $leilaoFinalizado = new Leilao('Fiat 147');
        $leilaoFinalizado->finaliza();

        return [
            [
                [$leilaoNaoFinalizado, $leilaoFinalizado]
            ]
        ];
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}