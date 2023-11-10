<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;

class Episode extends AbstractAction
{
    public function __invoke(string $code): Response
    {
        $gifs = $this->finder->findGifsByEpisode($code);

        return $this->render('episode.html.twig', [
            'episode' => $code,
            'gifs' => $gifs,
        ]);
    }
}
