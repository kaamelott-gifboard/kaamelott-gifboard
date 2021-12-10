<?php

declare(strict_types=1);

namespace KaamelottGifboard\Handler;

use KaamelottGifboard\DataObject\Gif;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SharingAppHandler
{
    public function __construct(
        private RequestStack $requestStack,
        private string $publicPath,
    ) {
    }

    private const SHARING_APP_USER_AGENTS = [
        'api.slack.com',
//        'Discordbot',
    ];

    public function getResponse(Gif $gif): ?BinaryFileResponse
    {
        if (!$request = $this->requestStack->getMainRequest()) {
            return null;
        }

        if (!$this->isASharingApp($request)) {
            return null;
        }

        $image = (array) parse_url($gif->image);

        if (\array_key_exists('path', $image)) {
            $file = new File($this->publicPath.$image['path']);

            $response = new BinaryFileResponse($file);
            $response->headers->set('Content-Type', 'image/gif');
            $response->headers->set('Content-Length', (string) $file->getSize());

            return $response;
        }

        return null;
    }

    private function isASharingApp(Request $request): bool
    {
        $userAgent = (string) $request->headers->get('User-Agent');

        $userAgents = implode('|', self::SHARING_APP_USER_AGENTS);

        return (bool) preg_match(sprintf('#%s#', $userAgents), $userAgent);
    }
}
