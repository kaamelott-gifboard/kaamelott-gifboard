<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action;

use KaamelottGifboard\Finder\GifFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

abstract class AbstractAction extends AbstractController
{
    public function __construct(
        protected GifFinder $finder,
    ) {
    }
}
