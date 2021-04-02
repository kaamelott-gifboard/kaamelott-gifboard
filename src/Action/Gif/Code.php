<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Code extends AbstractAction
{
    public function __invoke(string $code): Response
    {
        $gif = $this->finder->findByCode($code);

        if (null === $gif || false === \array_key_exists('url', $gif)) {
            throw new PouletteNotFoundException('code', $code);
        }

        return new RedirectResponse($gif['url'], Response::HTTP_SEE_OTHER);
    }
}
