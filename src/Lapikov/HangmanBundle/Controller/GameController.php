<?php

namespace Lapikov\HangmanBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Validator\Constraints\Regex;

class GameController extends FOSRestController
{
    const NOT_FOUND_MESSAGE = 'The resource \'%s\' was not found.';

    public function getGamesAction()
    {
        return $this->container->get('lapikov_hangman.game.repository')->findAll();
    }

    public function postGamesAction()
    {
        $game = $this->container->get('lapikov_hangman.game.service')->create();
        $this->persist($game);
        return $game;
    }

    public function getGameAction($id)
    {
        $game = $this->container->get('lapikov_hangman.game.repository')->find($id);
        if (!$game) {
            throw new NotFoundHttpException(sprintf(self::NOT_FOUND_MESSAGE, $id));
        }
        return $game;
    }

    public function postGameAction(Request $request, $id)
    {
        $char = $request->request->get('char', false);
        $errors = $this->get('validator')->validateValue(
            $char,
            new Regex(array('pattern' => '/^[a-z]$/'))
        );
        if (count($errors)) {
            throw new BadRequestHttpException('Param "char" should be specified and match /^[a-zA-Z]{1}$/');
        }
        $game = $this->container->get('lapikov_hangman.game.service')->guess($id, $char);
        $this->persist($game);
        return $game;
    }

    private function persist($entity)
    {
        $em = $this->container->get('doctrine.orm.entity_manager');
        $em->persist($entity);
        $em->flush();
    }
}
