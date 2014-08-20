<?php

namespace Lapikov\HangmanBundle\Tests\Controller;

use Lapikov\HangmanBundle\Entity\Game;
use Lapikov\HangmanBundle\Service\GameService;

class GameServiceTest extends \PHPUnit_Framework_TestCase
{
    private $gameRepository;
    private $wordRepository;

    public function setUp()
    {
        $this->gameRepository = $this->getMockBuilder('Lapikov\HangmanBundle\Entity\GameRepository')
            ->disableOriginalConstructor()
            ->getMock();

        $this->wordRepository = $this->getMockBuilder('Lapikov\HangmanBundle\Entity\WordRepository')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @group unit
     */
    public function testCreate()
    {
        $this->wordRepository->expects($this->any())
            ->method('getRandomWord')
            ->will($this->returnValue('abc'));

        $gameService = new GameService($this->gameRepository, $this->wordRepository);
        $game = $gameService->create();
        $this->assertEquals('abc', $game->getWord());
    }

    /**
     * @group unit
     * @expectedException Exception
     */
    public function testCreateException()
    {
        $this->wordRepository->expects($this->any())
            ->method('getRandomWord')
            ->will($this->returnValue(false));

        $gameService = new GameService($this->gameRepository, $this->wordRepository);
        $gameService->create();
    }

    /**
     * @group unit
     * @expectedException \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function testGuessNotFoundHttpException()
    {
        $this->gameRepository->expects($this->any())
            ->method('find')
            ->will($this->returnValue(false));

        $gameService = new GameService($this->gameRepository, $this->wordRepository);
        $gameService->guess(1, 'abc');
    }
    /**
     * @group unit
     */
    public function testGuess()
    {
        $game = new Game();
        $game->setWord('abc');
        $this->gameRepository->expects($this->any())
            ->method('find')
            ->will($this->returnValue($game));
        $gameService = new GameService($this->gameRepository, $this->wordRepository);

        $result = $gameService->guess(false, 'a');
        $this->assertEquals(11, $result->getTriesLeft());
        $this->assertEquals('a', $result->getChars());
        $this->assertEquals(Game::STATUS_BUSY, $result->getStatus());

        $result = $gameService->guess(false, 'a');
        $this->assertEquals(11, $result->getTriesLeft());
        $this->assertEquals('a', $result->getChars());
        $this->assertEquals(Game::STATUS_BUSY, $result->getStatus());

        $result = $gameService->guess(false, 'z');
        $this->assertEquals(10, $result->getTriesLeft());
        $this->assertEquals('az', $result->getChars());
        $this->assertEquals(Game::STATUS_BUSY, $result->getStatus());

        $result = $gameService->guess(false, 'b');
        $this->assertEquals(10, $result->getTriesLeft());
        $this->assertEquals('azb', $result->getChars());
        $this->assertEquals(Game::STATUS_BUSY, $result->getStatus());

        $result = $gameService->guess(false, 'c');
        $this->assertEquals(10, $result->getTriesLeft());
        $this->assertEquals('azbc', $result->getChars());
        $this->assertEquals(Game::STATUS_SUCCESS, $result->getStatus());

        $game->setChars('ab');
        $game->setTriesLeft(1);
        $game->setStatus(Game::STATUS_BUSY);

        $result = $gameService->guess(false, 'z');
        $this->assertEquals(0, $result->getTriesLeft());
        $this->assertEquals('abz', $result->getChars());
        $this->assertEquals(Game::STATUS_FAIL, $result->getStatus());
    }
}
