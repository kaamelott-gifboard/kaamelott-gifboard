<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class Code extends AbstractAction
{
    #[Route('/g/{code}', name: 'get_by_code', requirements: ['code' => '[a-z0-9]+'])]
    public function __invoke(string $code): Response
    {
        $gif = $this->finder->findGifsByCode($code);

        if (null === $gif) {
            throw new PouletteNotFoundException('code', $code);
        }

        return new RedirectResponse($gif->url, Response::HTTP_SEE_OTHER);
    }
}
