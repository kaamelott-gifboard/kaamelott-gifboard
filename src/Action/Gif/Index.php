<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

class Index extends AbstractAction
{
    public function __invoke(): Response
    {
        return $this->render('body.html.twig', [
            'gifs' => $this->finder->findGifs(),
        ]);
    }
}
