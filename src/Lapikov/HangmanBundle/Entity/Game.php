<?php

namespace Lapikov\HangmanBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Accessor;
use JMS\Serializer\Annotation\Exclude;

/**
 * Game
 *
 * @ORM\Table(name="game")
 * @ORM\Entity(repositoryClass="Lapikov\HangmanBundle\Entity\GameRepository")
 */
class Game
{
    const STATUS_BUSY = 'busy';
    const STATUS_FAIL = 'fail';
    const STATUS_SUCCESS = 'success';

    /**
     * @var integer
     *
     * @Exclude
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * Log of guessed characters
     *
     * @var string
     *
     * @Exclude
     *
     * @ORM\Column(name="chars", type="string", length=26)
     */
    private $chars = '';

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=7)
     */
    private $status = self::STATUS_BUSY;

    /**
     * @var integer
     *
     * @ORM\Column(name="tries_left", type="smallint")
     */
    private $triesLeft = 11;

    /**
     * @var string
     *
     * @Accessor(getter="getPublicWord")
     *
     * @ORM\Column(name="word", type="string", length=255)
     */
    protected $word;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set chars
     *
     * @param string $chars
     * @return Game
     */
    public function setChars($chars)
    {
        $this->chars = $chars;

        return $this;
    }

    /**
     * Get chars
     *
     * @return string 
     */
    public function getChars()
    {
        return $this->chars;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Game
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string 
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set triesLeft
     *
     * @param integer $triesLeft
     * @return Game
     */
    public function setTriesLeft($triesLeft)
    {
        $this->triesLeft = $triesLeft;

        return $this;
    }

    /**
     * Get triesLeft
     *
     * @return integer 
     */
    public function getTriesLeft()
    {
        return $this->triesLeft;
    }

    /**
     * Set word
     *
     * @param string $word
     * @return Game
     */
    public function setWord($word)
    {
        $this->word = $word;

        return $this;
    }

    /**
     * Get word
     *
     * @return string 
     */
    public function getWord()
    {
        return $this->word;
    }

    /**
     * Accessor for json representation of the word property.
     *
     * @return string
     */
    public function getPublicWord()
    {
        if (!$this->word) {
            return '';
        }
        // generate "......" with needed length
        $result = preg_replace('/./i', '.', $this->word);
        $guessedChars = str_split($this->chars);
        $wordChars = str_split($this->word);
        foreach ($wordChars as $key => $wordChar) {
            foreach ($guessedChars as $guessedChar) {
                if ($guessedChar === $wordChar) {
                    $result[$key] = $wordChar;
                }
            }
        }
        return $result;
    }

    /**
     * @return bool
     */
    public function isFailed()
    {
        return $this->status == self::STATUS_FAIL;
    }

    /**
     * @return bool
     */
    public function isWon()
    {
        return $this->status == self::STATUS_SUCCESS || preg_match('/^[a-z]+$/i', $this->getPublicWord());
    }
}
