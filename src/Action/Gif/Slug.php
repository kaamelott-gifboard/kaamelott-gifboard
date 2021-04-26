<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use KaamelottGifboard\Handler\RedirectionHandler;
use KaamelottGifboard\Handler\SharingAppHandler;
use KaamelottGifboard\Service\GifFinder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Slug extends AbstractAction
{
    public function __construct(
        protected Environment $twig,
        protected GifFinder $finder,
        private RedirectionHandler $redirectionHandler,
        private SharingAppHandler $sharingAppHandler
    ) {
        parent::__construct($this->twig, $this->finder);
    }

    public function __invoke(Request $request, string $slug): Response
    {
        if ($redirection = $this->redirectionHandler->getRedirection($slug)) {
            return $redirection;
        }

        $gifs = $this->finder->findGifsBySlug($slug);

        if (null === $gifs) {
            throw new PouletteNotFoundException('slug', $slug);
        }

        if ($response = $this->sharingAppHandler->getResponse($gifs)) {
            return $response;
        }

        return $this->render('gif.html.twig', $gifs);
    }
}
