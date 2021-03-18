<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Sitemap;

use KaamelottGifboard\Service\JsonParser;
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
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        $view = $this->twig->render('sitemap.html.twig', [
            'gifs' => $this->jsonParser->findForSitemap(),
        ]);

        return $response->setContent($view);
    }
}
