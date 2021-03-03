<?php

declare(strict_types=1);

namespace App\Action;

use App\Service\JsonParser;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Index
{
    private Environment $twig;
    private JsonParser $jsonParser;

    public function __construct(Environment $twig, JsonParser $jsonParser)
    {
        $this->twig = $twig;
        $this->jsonParser = $jsonParser;
    }

    public function __invoke(): Response
    {
        $gifs = $this->jsonParser->findAll();

        $view = $this->twig->render('body.html.twig', $gifs);

        return (new Response())->setContent($view);
    }
}
