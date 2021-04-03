<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Random extends AbstractAction
{
    public function __invoke(): Response
    {
        $random = $this->finder->findRandom();

        return new RedirectResponse($random['current']->url, Response::HTTP_SEE_OTHER);
    }
}
