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

        $expected = '{"count":2,"results":[{"quote":"Ca suffit ! Elle est o\u00f9 la poulette ? Elle est bien cach\u00e9e ?!","characters":[{"slug":"kadoc","name":"Kadoc","image":"http:\/\/localhost\/characters\/kadoc.jpg","url":"http:\/\/localhost\/search\/character\/Kadoc"},{"slug":"merlin","name":"Merlin","image":"http:\/\/localhost\/characters\/merlin.jpg","url":"http:\/\/localhost\/search\/character\/Merlin"}],"charactersSpeaking":[{"slug":"kadoc","name":"Kadoc","image":"http:\/\/localhost\/characters\/kadoc.jpg","url":"http:\/\/localhost\/search\/character\/Kadoc"}],"filename":"poulette-bien-cachee.gif","slug":"ca-suffit-elle-est-ou-la-poulette-elle-est-bien-cachee","url":"http:\/\/localhost\/gif\/ca-suffit-elle-est-ou-la-poulette-elle-est-bien-cachee","image":"http:\/\/localhost\/gifs\/poulette-bien-cachee.gif","width":220,"height":124,"code":"09efd4d8a3","shortUrl":"http:\/\/localhost-alt\/g\/09efd4d8a3","episode":null},{"quote":"O\u00f9 elle est la poulette ?","characters":[{"slug":"kadoc","name":"Kadoc","image":"http:\/\/localhost\/characters\/kadoc.jpg","url":"http:\/\/localhost\/search\/character\/Kadoc"},{"slug":"lancelot-du-lac","name":"Lancelot du Lac","image":"http:\/\/localhost\/characters\/lancelot-du-lac.jpg","url":"http:\/\/localhost\/search\/character\/Lancelot%20du%20Lac"}],"charactersSpeaking":[{"slug":"kadoc","name":"Kadoc","image":"http:\/\/localhost\/characters\/kadoc.jpg","url":"http:\/\/localhost\/search\/character\/Kadoc"}],"filename":"elle-est-ou-la-poulette.gif","slug":"ou-elle-est-la-poulette","url":"http:\/\/localhost\/gif\/ou-elle-est-la-poulette","image":"http:\/\/localhost\/gifs\/elle-est-ou-la-poulette.gif","width":320,"height":180,"code":"d47a127b93","shortUrl":"http:\/\/localhost-alt\/g\/d47a127b93","episode":"S02E81"}]}';

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
