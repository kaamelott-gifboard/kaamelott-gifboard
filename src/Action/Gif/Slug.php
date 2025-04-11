<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\DataObject\Gif;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use KaamelottGifboard\Finder\GifFinder;
use KaamelottGifboard\Handler\RedirectionHandler;
use KaamelottGifboard\Handler\SharingAppHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Slug extends AbstractAction
{
    public function __construct(
        protected GifFinder $finder,
        private RedirectionHandler $redirectionHandler,
        private SharingAppHandler $sharingAppHandler,
    ) {
        parent::__construct($this->finder);
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

        /** @var Gif $gif */
        $gif = $gifs['current'];

        if ($response = $this->sharingAppHandler->getResponse($gif)) {
            return $response;
        }

        return $this->render('gif.html.twig', $gifs);
    }
}
