<?php

namespace Lapikov\HangmanBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * http://symfony.com/doc/current/cookbook/routing/redirect_trailing_slash.html
 *
 * Class RedirectingController
 * @package Lapikov\HangmanBundle\Controller
 */
class DefaultController extends Controller
{
    public function welcomeAction(Request $request)
    {
        return new Response($this->container->get('jms_serializer')->serialize(array('message' => 'Welcome to Hangman! Please use API calls'), 'json'));
    }

    public function removeTrailingSlashAction(Request $request)
    {
        $pathInfo = $request->getPathInfo();
        $requestUri = $request->getRequestUri();

        $url = str_replace($pathInfo, rtrim($pathInfo, ' /'), $requestUri);

        return $this->redirect($url, 301);
    }
}