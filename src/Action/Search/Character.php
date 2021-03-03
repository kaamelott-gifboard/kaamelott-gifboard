<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Search;

use KaamelottGifboard\Service\JsonParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Character
{
    private JsonParser $finder;
    private Environment $twig;

    public function __construct(JsonParser $finder, Environment $twig)
    {
        $this->finder = $finder;
        $this->twig = $twig;
    }

    public function get(Request $request): Response
    {
        $characters = $this->finder->findCharacters();

        if (!$request->isXmlHttpRequest()) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($characters);
    }

    public function byName(string $name): Response
    {
        $view = $this->twig->render(
            'body.html.twig',
            $this->finder->findByCharacter($name)
        );

        return (new Response())->setContent($view);
    }
}
