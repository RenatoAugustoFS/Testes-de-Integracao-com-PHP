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

    public function testInsercaoEBuscaDeveFuncionar()
    {
        //Arrange
        $leilao = new Leilao('Variante 0km');
        $leilaoDao = new LeilaoDao(self::$pdo);
        $leilaoDao->salva($leilao);

        //Act
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        //Assert
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Variante 0km', $leiloes[0]->recuperarDescricao());
    }

    protected function tearDown(): void
    {
        self::$pdo->rollBack();
    }
}