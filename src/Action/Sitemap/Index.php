<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Sitemap;

use KaamelottGifboard\Service\JsonParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Index extends AbstractController
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

        return $this->render('sitemap.html.twig', $this->jsonParser->findForSitemap());
    }
}
