<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Episode extends AbstractAction
{
    #[Route('/search/episode/{code}', name: 'get_by_episode')]
    public function __invoke(string $code): Response
    {
        $gifs = $this->finder->findGifsByEpisode($code);

        return $this->render('episode.html.twig', [
            'episode' => $code,
            'gifs' => $gifs,
        ]);
    }
}
