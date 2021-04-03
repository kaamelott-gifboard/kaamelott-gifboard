<?php

declare(strict_types=1);

namespace KaamelottGifboard\Tests\Service;

use KaamelottGifboard\DataObject\Gif;
use KaamelottGifboard\DataObject\GifIterator;
use KaamelottGifboard\Helper\ImageHelper;
use KaamelottGifboard\Service\GifLister;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class GifListerTest extends KernelTestCase
{
    /** @var MockObject|RouterInterface */
    private RouterInterface $router;
    /** @var ImageHelper|MockObject */
    private ImageHelper $helper;

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
            static::assertObjectHasAttribute('quote', $item);
            static::assertIsString($item->quote);
            static::assertObjectHasAttribute('characters', $item);
            static::assertIsArray($item->characters);
            static::assertObjectHasAttribute('filename', $item);
            static::assertIsString($item->filename);
            static::assertMatchesRegularExpression('#^[a-z-]+\.gif$#', $item->filename);
            static::assertObjectHasAttribute('slug', $item);
            static::assertMatchesRegularExpression('#^[a-z-0-9]+$#', $item->slug);
            static::assertObjectHasAttribute('url', $item);
            static::assertObjectHasAttribute('image', $item);
            static::assertObjectHasAttribute('width', $item);
            static::assertIsInt($item->width);
            static::assertObjectHasAttribute('height', $item);
            static::assertIsInt($item->height);
            static::assertObjectHasAttribute('code', $item);
            static::assertMatchesRegularExpression('#[a-z0-9]+#', $item->code);
            static::assertObjectHasAttribute('shortUrl', $item);
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
                static::assertArrayHasKey('slug', $character);
                static::assertMatchesRegularExpression('#^[a-z-0-9]+$#', $character['slug']);
                static::assertArrayHasKey('name', $character);
                static::assertArrayHasKey('image', $character);
                static::assertMatchesRegularExpression('#[a-z-]+\.png$#', $character['image'], $character['name']);
                static::assertArrayHasKey('url', $character);
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
            ->expects(static::exactly(17))
            ->method('generate')
            ->withConsecutive(
                ['get_by_slug', ['slug' => 'this-is-the-quote-1']],
                ['quote_image', ['filename' => 'quote-1.gif']],
                ['get_by_code_short', ['code' => 'f53abd91c9']],
                ['character_image', ['filename' => 'image-1.png']],
                ['get_by_character', ['name' => 'Character 1']],
                ['character_image', ['filename' => 'image-2.png']],
                ['get_by_character', ['name' => 'Character 2']],
                ['get_by_slug', ['slug' => 'here-is-the-quote-2']],
                ['quote_image', ['filename' => 'quote-2.gif']],
                ['get_by_code_short', ['code' => 'cc58ba3582']],
                ['character_image', ['filename' => 'image-3.png']],
                ['get_by_character', ['name' => 'Character 3']],
                ['get_by_slug', ['slug' => 'finally-the-quote-number-3']],
                ['quote_image', ['filename' => 'quote-3.gif']],
                ['get_by_code_short', ['code' => '0c3c899cad']],
                ['character_image', ['filename' => 'image-2.png']],
                ['get_by_character', ['name' => 'Character 2']],
            )
            ->willReturnOnConsecutiveCalls(
                'route-1',
                'gif-1',
                'short-route-1',
                'image-1.png',
                'character-url-1',
                'image-2.png',
                'character-url-2',
                'route-2',
                'gif-2',
                'short-route-2',
                'image-3.png',
                'character-url-3',
                'route-3',
                'gif-3',
                'short-route-3',
                'image-2.png',
                'character-url-2',
            );

        $this->helper
            ->expects(static::exactly(4))
            ->method('getCharacterImage')
            ->withConsecutive(
                ['character-1'],
                ['character-2'],
                ['character-3'],
                ['character-2'],
            )
            ->willReturnOnConsecutiveCalls('image-1.png', 'image-2.png', 'image-3.png', 'image-2.png');

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
        $gif->characters = [
            [
                'slug' => 'character-2',
                'name' => 'Character 2',
                'image' => 'image-2.png',
                'url' => 'character-url-2',
            ],
        ];
        $gif->filename = 'quote-3.gif';
        $gif->slug = 'finally-the-quote-number-3';
        $gif->url = 'route-3';
        $gif->image = 'gif-3';
        $gif->width = 100;
        $gif->height = 100;
        $gif->code = '0c3c899cad';
        $gif->shortUrl = 'short-route-3';

        static::assertEquals($gif, $result[0]);
    }
}
