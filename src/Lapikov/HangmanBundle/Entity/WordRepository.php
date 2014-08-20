<?php

namespace Lapikov\HangmanBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * WordRepository
 */
class WordRepository extends EntityRepository
{
    /**
     * ORDER BY RAND() works slow, explanation
     * http://jan.kneschke.de/projects/mysql/order-by-rand/
     *
     * @return string or false if word not found
     */
    public function getRandomWord()
    {
        $stmt = $this->getEntityManager()->getConnection()->executeQuery('
            SELECT word
            FROM Word AS w1 JOIN
                (SELECT (RAND() *
                    (SELECT MAX(id)
                    FROM Word)) AS id)
                AS w2
            WHERE w1.id >= w2.id
            ORDER BY w1.id ASC
            LIMIT 1
        ');
        $row = $stmt->fetch();
        return isset($row['word']) ? $row['word'] : false;
    }
}
