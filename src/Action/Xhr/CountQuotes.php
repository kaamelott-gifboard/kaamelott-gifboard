<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Xhr;

use KaamelottGifboard\Action\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CountQuotes extends AbstractAction
{
    #[Route('/xhr/count_quotes', name: 'count_quotes')]
    public function __invoke(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->finder->countGifs());
    }
}
