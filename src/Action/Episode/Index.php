<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Episode;

use KaamelottGifboard\Finder\EpisodeFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
class Index extends AbstractController
{
    public function __construct(
        private EpisodeFinder $episodeFinder,
    ) {
    }

    #[Route('/episodes', name: 'get_episodes')]
    public function __invoke(Request $request): Response
    {
        return $this->render('episodes.html.twig', [
            'episodeBySeasons' => $this->episodeFinder->getEpisodeBySeasons(),
        ]);
    }
}
