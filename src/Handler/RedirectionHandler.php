<?php

declare(strict_types=1);

namespace KaamelottGifboard\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class RedirectionHandler
{
    public function __construct(
        private string $redirectionJsonFile,
        private RouterInterface $router,
    ) {
    }

    public function getRedirection(string $slug): ?RedirectResponse
    {
        /** @var array $redirections */
        $redirections = json_decode((string) file_get_contents($this->redirectionJsonFile), true);

        /** @var array $redirection */
        foreach ($redirections as $redirection) {
            if ($redirection['old'] === $slug) {
                $path = $this->router->generate(
                    'get_by_slug',
                    ['slug' => $redirection['new']],
                    RouterInterface::ABSOLUTE_URL
                );

                return new RedirectResponse($path, Response::HTTP_MOVED_PERMANENTLY);
            }
        }

        return null;
    }
}
