<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Slug extends AbstractAction
{
    private const SHARING_APP_USER_AGENTS = [
        'api.slack.com/robots',
        // 'Discordbot',
    ];

    public function __invoke(Request $request, string $slug): Response
    {
        $gifs = $this->finder->findGifsBySlug($slug);

        if (null === $gifs) {
            throw new PouletteNotFoundException('slug', $slug);
        }

        if ($this->isASharingApp($request)) {
            $image = (array) parse_url($gifs['current']->image);

            if (\array_key_exists('path', $image)) {
                $response = new BinaryFileResponse(ltrim($image['path'], '/'));
                $response->headers->set('Content-Type', 'image/gif');

                return $response;
            }
        }

        return $this->render('gif.html.twig', $gifs);
    }

    private function isASharingApp(Request $request): bool
    {
        $userAgent = (string) $request->headers->get('User-Agent');

        $userAgents = (string) implode('|', self::SHARING_APP_USER_AGENTS);

        return (bool) preg_match(sprintf('#%s#', $userAgents), $userAgent);
    }
}
