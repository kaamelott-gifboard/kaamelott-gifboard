<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action;

use KaamelottGifboard\Service\GifFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Twig\Environment;

abstract class AbstractAction extends AbstractController
{
    public function __construct(
        protected Environment $twig,
        protected GifFinder $finder
    ) {
    }
}
