<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class Slug extends AbstractAction
{
    public function __invoke(string $slug): Response
    {
        $gifs = $this->finder->findBySlug($slug);

        if (null === $gifs) {
            throw new PouletteNotFoundException('slug', $slug);
        }

        return $this->render('gif.html.twig', $gifs);
    }
}
