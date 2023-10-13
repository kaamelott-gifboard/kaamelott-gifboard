<?php

declare(strict_types=1);

namespace KaamelottGifboard\Tests\Service;

use KaamelottGifboard\DataObject\Character;
use KaamelottGifboard\DataObject\Gif;
use KaamelottGifboard\DataObject\GifIterator;
use KaamelottGifboard\Helper\ImageHelper;
use KaamelottGifboard\Lister\GifLister;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class GifListerTest extends KernelTestCase
{
    private RouterInterface|MockObject $router;
    private ImageHelper|MockObject $helper;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->helper = $this->createMock(ImageHelper::class);
    }

    public function testGifsJsonIsValid(): void
    {
        $kernel = self::bootKernel();

        $lister = $kernel->getContainer()->get('test.service_container')->get(GifLister::class);

        static::assertInstanceOf(GifIterator::class, $lister->gifs);

        /** @var Gif $item */
        foreach ($lister->gifs as $item) {
            static::assertObjectHasProperty('quote', $item);
            static::assertIsString($item->quote);
            static::assertObjectHasProperty('characters', $item);
            static::assertIsArray($item->characters);
            static::assertObjectHasProperty('charactersSpeaking', $item);
            static::assertIsArray($item->charactersSpeaking);
            static::assertObjectHasProperty('filename', $item);
            static::assertIsString($item->filename);
            static::assertMatchesRegularExpression('#^[a-z-0-9]+\.gif$#', $item->filename);
            static::assertObjectHasProperty('slug', $item);
            static::assertMatchesRegularExpression('#^[a-z-0-9]+$#', $item->slug);
            static::assertObjectHasProperty('url', $item);
            static::assertObjectHasProperty('image', $item);
            static::assertObjectHasProperty('width', $item);
            static::assertIsInt($item->width);
            static::assertObjectHasProperty('height', $item);
            static::assertIsInt($item->height);
            static::assertObjectHasProperty('code', $item);
            static::assertMatchesRegularExpression('#[a-z0-9]+#', $item->code);
            static::assertObjectHasProperty('shortUrl', $item);
        }
    }

    public function testCharactersJsonIsValid(): void
    {
        $kernel = self::bootKernel();

        $lister = $kernel->getContainer()->get('test.service_container')->get(GifLister::class);

        static::assertInstanceOf(GifIterator::class, $lister->gifs);

        /** @var Gif $item */
        foreach ($lister->gifs as $item) {
            static::assertIsArray($item->characters);

            foreach ($item->characters as $character) {
                static::assertObjectHasProperty('slug', $character);
                static::assertMatchesRegularExpression('#^[a-z-0-9]+$#', $character->slug);
                static::assertObjectHasProperty('name', $character);
                static::assertObjectHasProperty('image', $character);
                static::assertMatchesRegularExpression('#[a-z-]+\.jpg$#', $character->image, $character->name);
                static::assertObjectHasProperty('url', $character);
            }
        }
    }

    public function testCharactersSpeakingJsonIsValid(): void
    {
        $kernel = self::bootKernel();

        $lister = $kernel->getContainer()->get('test.service_container')->get(GifLister::class);

        static::assertInstanceOf(GifIterator::class, $lister->gifs);

        /** @var Gif $item */
        foreach ($lister->gifs as $item) {
            static::assertIsArray($item->charactersSpeaking);

            foreach ($item->charactersSpeaking as $character) {
                static::assertObjectHasProperty('slug', $character);
                static::assertMatchesRegularExpression('#^[a-z-0-9]+$#', $character->slug);
                static::assertObjectHasProperty('name', $character);
                static::assertObjectHasProperty('image', $character);
                static::assertMatchesRegularExpression('#[a-z-]+\.jpg$#', $character->image, $character->name);
                static::assertObjectHasProperty('url', $character);
            }
        }
    }

    public function testInit(): void
    {
        $this->helper
            ->expects(static::exactly(3))
            ->method('getImageDimensions')
            ->willReturn(['width' => 100, 'height' => 100]);

        $this->router
            ->expects(static::exactly(23))
            ->method('generate')
            ->willReturn('route');

        $this->helper
            ->expects(static::exactly(7))
            ->method('getCharacterImage')
            ->willReturn('image');

        $lister = new GifLister(
            __DIR__.'/gifs-test.json',
            $this->router,
            new AsciiSlugger(),
            $this->helper
        );

        $result = $lister->gifs;

        static::assertCount(3, $result);
        static::assertInstanceOf(Gif::class, $result[0]);
        static::assertInstanceOf(Gif::class, $result[1]);
        static::assertInstanceOf(Gif::class, $result[2]);

        $gif = new Gif();
        $gif->quote = 'Finally, the quote number 3';
        $gif->characters = [new Character('character-2', 'Character 2', 'route', 'route')];
        $gif->charactersSpeaking = [new Character('character-2', 'Character 2', 'route', 'route')];
        $gif->filename = 'quote-3.gif';
        $gif->slug = 'finally-the-quote-number-3';
        $gif->url = 'route';
        $gif->image = 'route';
        $gif->width = 100;
        $gif->height = 100;
        $gif->code = '0c3c899cad';
        $gif->shortUrl = 'route';

        static::assertEquals($gif, $result[0]);
    }
}
