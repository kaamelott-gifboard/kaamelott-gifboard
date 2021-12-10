<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Search;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Finder\GifFinder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Twig\Environment;

class Quote extends AbstractAction
{
    public function __construct(
        protected Environment $twig,
        protected GifFinder $finder,
        private NormalizerInterface $normalizer,
    ) {
        parent::__construct($this->twig, $this->finder);
    }

    public function __invoke(Request $request): Response|JsonResponse
    {
        $search = (string) $request->query->get('search', '');

        $gifs = $this->finder->findGifsByQuote($search);

        $contentType = $request->headers->get('Content-Type');

        if ('application/json' === $contentType) {
            /** @var array $gifs */
            $gifs = $this->normalizer->normalize($gifs);

            return new JsonResponse([
                'count' => count($gifs),
                'results' => $gifs,
            ]);
        }

        return $this->render('body.html.twig', [
            'gifs' => $gifs,
        ]);
    }
}
