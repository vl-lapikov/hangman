<?php

namespace Lapikov\HangmanBundle\Tests\Controller;

use Lapikov\HangmanBundle\Entity\Game;

class GameTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @group unit
     * @dataProvider provider
     */
    public function testGetPublicWord($word, $chars, $expected)
    {
        $game = new Game();
        $game->setWord($word);
        $game->setChars($chars);
        $this->assertEquals($expected, $game->getPublicWord());
    }

    public function provider()
    {
        return array(
            array('a', 'a', 'a'),
            array('aa', 'a', 'aa'),
            array('ab', 'a', 'a.'),
            array('abccbe', 'ac', 'a.cc..'),
            array('abbc', 'zxy', '....'),
        );
    }

    /**
     * @group unit
     */
    public function testIsWon()
    {
        $game = new Game();
        $this->assertFalse($game->isWon());
        $game->setWord('ab');
        $game->setChars('a');
        $this->assertFalse($game->isWon());
        $game->setStatus(Game::STATUS_SUCCESS);
        $this->assertTrue($game->isWon());
        $game->setStatus(Game::STATUS_BUSY);
        $game->setChars('ab');
        $this->assertTrue($game->isWon());
    }
}
