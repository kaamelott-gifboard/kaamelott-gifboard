<?php

declare(strict_types=1);

namespace KaamelottGifboard\Tests\Action;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\Routing\RouterInterface;

class QuoteTest extends KernelTestCase
{
    public function testJson(): void
    {
        $request = $this->getRequest();
        $request->headers->set('content-type', 'application/json');

        $response = $this->getContainer()->get(KernelInterface::class)->handle($request);

        $expected = '{"count":2,"results":[{"gif":"http:\/\/localhost\/gifs\/poulette-bien-cachee.gif","url":"http:\/\/localhost\/gif\/ca-suffit-elle-est-ou-la-poulette-elle-est-bien-cachee"},{"gif":"http:\/\/localhost\/gifs\/elle-est-ou-la-poulette.gif","url":"http:\/\/localhost\/gif\/elle-est-ou-la-poulette"}]}';

        static::assertSame(200, $response->getStatusCode());
        static::assertSame('application/json', $response->headers->get('content-type'));
        static::assertSame($expected, $response->getContent());
    }

    public function testHtml(): void
    {
        $response = $this->getContainer()->get(KernelInterface::class)->handle($this->getRequest());

        static::assertSame(200, $response->getStatusCode());
        static::assertSame('text/html; charset=UTF-8', $response->headers->get('content-type'));
    }

    private function getRequest(): Request
    {
        $router = $this->getContainer()->get(RouterInterface::class);

        return Request::create($router->generate('search_quote', [
            'search' => 'poulette',
        ]));
    }
}
