<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action;

use KaamelottGifboard\Service\GifFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

abstract class AbstractAction extends AbstractController
{
    protected Environment $twig;
    protected GifFinder $finder;

    public function __construct(Environment $twig, GifFinder $jsonParser)
    {
        $this->twig = $twig;
        $this->finder = $jsonParser;
    }
}
