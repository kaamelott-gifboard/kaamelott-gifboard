<?php

declare(strict_types=1);

namespace KaamelottGifboard\Tests\Handler;

use KaamelottGifboard\DataObject\Gif;
use KaamelottGifboard\Handler\SharingAppHandler;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class SharingAppHandlerTest extends KernelTestCase
{
    private MockObject|RequestStack $requestStack;
    private SharingAppHandler $handler;

    protected function setUp(): void
    {
        $this->requestStack = $this->createMock(RequestStack::class);

        $this->handler = new SharingAppHandler(
            $this->requestStack,
            __DIR__.'/../../public'
        );
    }

    public function testNoRequest(): void
    {
        $this->requestStack
            ->expects(static::once())
            ->method('getMainRequest')
            ->willReturn(null);

        static::assertNull($this->handler->getResponse(new Gif()));
    }

    public function testIsNotASharingApp(): void
    {
        $request = new Request();
        $request->headers->set('User-Agent', 'Foobar');

        $this->requestStack
            ->expects(static::once())
            ->method('getMainRequest')
            ->willReturn($request);

        static::assertNull($this->handler->getResponse(new Gif()));
    }

    public function testIsASharingApp(): void
    {
        $request = new Request();
        $request->headers->set('User-Agent', 'api.slack.com/robots');

        $this->requestStack
            ->expects(static::once())
            ->method('getMainRequest')
            ->willReturn($request);

        $gif = new Gif();
        $gif->image = 'https://kaamelott-gifboard.fr/gifs/elle-est-ou-la-poulette.gif';

        $response = $this->handler->getResponse($gif);

        static::assertInstanceOf(BinaryFileResponse::class, $response);
        static::assertSame('image/gif', $response->headers->get('Content-Type'));
        static::assertSame('24650', $response->headers->get('Content-Length'));
    }
}
