<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Sitemap;

use KaamelottGifboard\Finder\EpisodeFinder;
use KaamelottGifboard\Finder\GifFinder;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

#[AsController]
class Index extends AbstractController
{
    public function __construct(
        private GifFinder $gifFinder,
        private EpisodeFinder $episodeFinder,
        private RouterInterface $router,
    ) {
    }

    #[Route('/sitemap.xml', name: 'sitemap', defaults: ['_format' => 'xml'])]
    public function __invoke(): Response
    {
        $response = new Response();
        $response->headers->set('Content-Type', 'text/xml');

        return $this->render('sitemap.html.twig', [
            'allEpisodes' => $this->router->generate('get_episodes', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'episodes' => $this->episodeFinder->findEpisodes(),
            'characters' => $this->gifFinder->findCharacters(),
            'gifs' => $this->gifFinder->findGifs(),
        ], $response);
    }
}
