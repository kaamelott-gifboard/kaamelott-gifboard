<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Search;

use KaamelottGifboard\Service\JsonParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Quote extends AbstractController
{
    private JsonParser $finder;
    private Environment $twig;

    public function __construct(JsonParser $finder, Environment $twig)
    {
        $this->finder = $finder;
        $this->twig = $twig;
    }

    public function __invoke(Request $request): Response
    {
        $search = (string) $request->query->get('search', '');

        return $this->render('body.html.twig', $this->finder->findByQuote($search));
    }
}
