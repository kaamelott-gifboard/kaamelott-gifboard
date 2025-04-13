<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action;

use KaamelottGifboard\Finder\GifFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
abstract class AbstractAction extends AbstractController
{
    public function __construct(
        protected GifFinder $finder,
    ) {
    }
}
