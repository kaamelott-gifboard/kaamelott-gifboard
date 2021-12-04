<?php

declare(strict_types=1);

namespace KaamelottGifboard\Tests\Handler;

use KaamelottGifboard\Handler\RedirectionHandler;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\RouterInterface;

class RedirectionHandlerTest extends KernelTestCase
{
    private MockObject|RouterInterface $router;
    private RedirectionHandler $handler;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);

        $this->handler = new RedirectionHandler(__DIR__.'/redirection-test.json', $this->router);
    }

    public function testGetRedirectionNothing(): void
    {
        static::assertNull($this->handler->getRedirection('nothing'));
    }

    public function testGetRedirection(): void
    {
        $this->router
            ->expects(static::once())
            ->method('generate')
            ->with('get_by_slug', ['slug' => 'bar'], 0)
            ->willReturn('new-path');

        $response = $this->handler->getRedirection('foo');

        static::assertInstanceOf(RedirectResponse::class, $response);
        static::assertSame(301, $response->getStatusCode());
        static::assertSame('new-path', $response->getTargetUrl());
    }
}
