<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Random extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $random = $this->finder->findRandom();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($random['current']);
        }

        return new RedirectResponse($random['current']->url, Response::HTTP_SEE_OTHER);
    }
}
