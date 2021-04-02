<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Xhr;

use KaamelottGifboard\Action\AbstractAction;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetCharacters extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $characters = $this->finder->findCharacters();

        if (!$request->isXmlHttpRequest()) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        return new JsonResponse($characters);
    }
}
