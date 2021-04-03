<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Sitemap;

use KaamelottGifboard\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

class Index extends AbstractAction
{
    public function __invoke(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        return $this->render('sitemap.html.twig', [
            'characters' => $this->finder->findCharacters(),
            'gifs' => $this->finder->findGifs(),
        ], $response);
    }
}
