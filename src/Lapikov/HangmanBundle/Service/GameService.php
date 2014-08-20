<?php

namespace Lapikov\HangmanBundle\Service;

use Lapikov\HangmanBundle\Entity\Game;
use Lapikov\HangmanBundle\Entity\GameRepository;
use Lapikov\HangmanBundle\Entity\WordRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class GameService
{
    const NOT_FOUND_MESSAGE = 'The resource \'%s\' was not found.';
    /**
     * @var GameRepository
     */
    private $gameRepository;
    /**
     * @var WordRepository
     */
    private $wordRepository;
    public function __construct(GameRepository $gameRepository, WordRepository $wordRepository)
    {
        $this->gameRepository = $gameRepository;
        $this->wordRepository = $wordRepository;
    }
    /**
     * Create new Game.
     *
     * @throws \Exception
     * @return Game
     */
    public function create()
    {
        $game = new Game();
        $word = $this->wordRepository->getRandomWord();
        if (!$word) {
            throw new \Exception('Word not found. Maybe database is empty.');
        }
        $game->setWord($word);
        return $game;
    }
    /**
     * Guess the letter in the word.
     *
     * @param integer $id
     * @param string $char
     * @throws NotFoundHttpException
     * @return Game
     */
    public function guess($id, $char)
    {
        $char = strtolower($char);
        $game = $this->gameRepository->find($id);
        if (!$game) {
            throw new NotFoundHttpException(sprintf(self::NOT_FOUND_MESSAGE, $id));
        }
        // already finished game?
        if ($game->isWon() || $game->isFailed() || $game->getTriesLeft() < 1) {
            return $game;
        }
        // already existing character?
        if (strstr($game->getChars(), $char) !== false) {
            return $game;
        }
        // keep log of guessed characters
        $game->setChars($game->getChars() . $char);
        // is letter correct?
        if (strstr($game->getWord(), $char) !== false) {
            if ($game->isWon()) {
                $game->setStatus(Game::STATUS_SUCCESS);
            }
            return $game;
        }
        // letter is wrong - decrease tries
        $tries = $game->getTriesLeft() - 1;
        $game->setTriesLeft($tries);
        if ($tries < 1 && !$game->isWon()) {
            $game->setStatus(Game::STATUS_FAIL);
        }
        return $game;
    }
}