<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\DataObject\Gif;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Random extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        /** @var Gif $random */
        $random = $this->finder->findRandom()['current'];

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse($random);
        }

        return new RedirectResponse($random->url, Response::HTTP_SEE_OTHER);
    }
}
