<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action;

use KaamelottGifboard\Service\JsonParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

abstract class AbstractAction extends AbstractController
{
    protected Environment $twig;
    protected JsonParser $finder;

    public function __construct(Environment $twig, JsonParser $jsonParser)
    {
        $this->twig = $twig;
        $this->finder = $jsonParser;
    }
}
