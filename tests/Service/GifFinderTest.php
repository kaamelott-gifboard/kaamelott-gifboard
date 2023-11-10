<?php

declare(strict_types=1);

namespace KaamelottGifboard\Tests\Service;

use KaamelottGifboard\DataObject\Character;
use KaamelottGifboard\DataObject\Gif;
use KaamelottGifboard\DataObject\GifIterator;
use KaamelottGifboard\Finder\GifFinder;
use KaamelottGifboard\Helper\ImageHelper;
use KaamelottGifboard\Lister\GifLister;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class GifFinderTest extends KernelTestCase
{
    private MockObject|GifLister $lister;
    private GifFinder $finder;

    protected function setUp(): void
    {
        $router = $this->createMock(RouterInterface::class);
        $router->expects(static::any())->method('generate')->willReturn('route');

        $helper = $this->createMock(ImageHelper::class);
        $helper->expects(static::any())->method('getImageDimensions')->willReturn(['width' => 100, 'height' => 100]);

        $this->lister = new GifLister(__DIR__.'/gifs-test.json', $router, new AsciiSlugger(), $helper);
        $this->finder = new GifFinder($this->lister);
    }

    public function testCountGifs(): void
    {
        static::assertSame(3, $this->finder->countGifs());
    }

    public function testFindGifs(): void
    {
        $result = $this->finder->findGifs();

        static::assertInstanceOf(GifIterator::class, $result);
        static::assertCount(3, $result);
    }

    public function testFindGifsByOffset(): void
    {
        $result = $this->finder->findGifs(0, 1);

        static::assertInstanceOf(\LimitIterator::class, $result);
        static::assertCount(1, $result);
    }

    public function testFindGifsByQuote(): void
    {
        $result = $this->finder->findGifsByQuote('IS');

        static::assertCount(2, $result);
        static::assertSame('Here is the qûote 2', $result[0]->quote);
        static::assertSame('This is the quote 1', $result[1]->quote);

        $result = $this->finder->findGifsByQuote('quote');

        static::assertCount(3, $result);
    }

    public function testFindGifsByCharacter(): void
    {
        $result = $this->finder->findGifsByCharacter('character 2');

        static::assertCount(1, $result);
        static::assertSame('Finally, the quote number 3', $result[0]->quote);
    }

    public function testFindGifsBySlug(): void
    {
        $expected = (new Gif());
        $expected->quote = 'Finally, the quote number 3';
        $expected->characters = [new Character('character-2', 'Character 2', 'route', 'route')];
        $expected->charactersSpeaking = [new Character('character-2', 'Character 2', 'route', 'route')];
        $expected->filename = 'quote-3.gif';
        $expected->slug = 'finally-the-quote-number-3';
        $expected->url = 'route';
        $expected->image = 'route';
        $expected->width = 100;
        $expected->height = 100;
        $expected->code = '0c3c899cad';
        $expected->shortUrl = 'route';
        $expected->episode = 'S01E03';

        $result = $this->finder->findGifsBySlug('finally-the-quote-number-3');

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals($expected, $result['current']);
        // GIFs are sorted alphabetically, which explains the following order
        static::assertSame('This is the quote 1', $result['previous']->quote);
        static::assertSame('Here is the qûote 2', $result['next']->quote);
    }

    public function testFindGifsBySlugNull(): void
    {
        static::assertNull($this->finder->findGifsBySlug('foobar'));
    }

    public function testFindGifsByCode(): void
    {
        $expected = (new Gif());
        $expected->quote = 'Finally, the quote number 3';
        $expected->characters = [new Character('character-2', 'Character 2', 'route', 'route')];
        $expected->charactersSpeaking = [new Character('character-2', 'Character 2', 'route', 'route')];
        $expected->filename = 'quote-3.gif';
        $expected->slug = 'finally-the-quote-number-3';
        $expected->url = 'route';
        $expected->image = 'route';
        $expected->width = 100;
        $expected->height = 100;
        $expected->code = '0c3c899cad';
        $expected->shortUrl = 'route';
        $expected->episode = 'S01E03';

        $gif = $this->finder->findGifsByCode('0c3c899cad');

        static::assertInstanceOf(Gif::class, $gif);
        static::assertEquals($expected, $gif);
    }

    public function testFindGifsByCodeNull(): void
    {
        static::assertNull($this->finder->findGifsByCode('foobar'));
    }

    public function testFindCharacter(): void
    {
        $character = $this->finder->findCharacter('Character 3');

        $expected = (new Character('character-3', 'Character 3', 'route', 'route'));

        static::assertEquals($expected, $character);
    }

    public function testFindCharacterNull(): void
    {
        static::assertNull($this->finder->findCharacter('foobar'));
    }

    public function testFindCharacters(): void
    {
        $characters = $this->finder->findCharacters();

        static::assertIsArray($characters);
        static::assertCount(3, $characters);
    }

    public function testFindRandom(): void
    {
        $result = $this->finder->findRandom();

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertArrayHasKey('previous', $result);
        static::assertArrayHasKey('current', $result);
        static::assertArrayHasKey('next', $result);
    }
}
