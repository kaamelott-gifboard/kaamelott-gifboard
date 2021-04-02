<?php

declare(strict_types=1);

namespace KaamelottGifboard\Tests\Service;

use KaamelottGifboard\Helper\ImageHelper;
use KaamelottGifboard\Service\JsonParser;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\AsciiSlugger;

class JsonParserTest extends KernelTestCase
{
    /** @var MockObject|RouterInterface */
    private RouterInterface $router;
    /** @var ImageHelper|MockObject */
    private ImageHelper $helper;
    private JsonParser $parser;

    protected function setUp(): void
    {
        $this->router = $this->createMock(RouterInterface::class);
        $this->helper = $this->createMock(ImageHelper::class);

        $this->helper
            ->expects(static::any())
            ->method('getImageDimensions')
            ->willReturn(['width' => 100, 'height' => 100]);

        $this->parser = new JsonParser(
            __DIR__.'/gifs-test.json',
            $this->router,
            new AsciiSlugger(),
            $this->helper
        );
    }

    public function testGifsJsonIsValid(): void
    {
        $kernel = self::bootKernel();

        $parser = $kernel->getContainer()->get('test_KaamelottGifboard\Service\JsonParser');

        $result = $parser->findAll();

        static::assertIsArray($result);
        static::assertArrayHasKey('gifs', $result);

        /** @var \stdClass $item */
        foreach ($result['gifs'] as $item) {
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

        $parser = $kernel->getContainer()->get('test_KaamelottGifboard\Service\JsonParser');

        $result = $parser->findAll();

        static::assertIsArray($result);
        static::assertArrayHasKey('gifs', $result);

        /** @var \stdClass $item */
        foreach ($result['gifs'] as $item) {
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

    public function testFindAll(): void
    {
        $result = $this->parser->findAll();

        static::assertArrayHasKey('gifs', $result);
        static::assertCount(3, $result['gifs']);
    }

    public function testFindByQuote(): void
    {
        $result = $this->parser->findByQuote('IS');

        static::assertCount(2, $result['gifs']);
        static::assertSame('Here is the qûote 2', $result['gifs'][0]->quote);
        static::assertSame('This is the quote 1', $result['gifs'][1]->quote);

        $result = $this->parser->findByQuote('quote');

        static::assertCount(3, $result['gifs']);
    }

    public function testFindByCharacter(): void
    {
        $result = $this->parser->findByCharacter('character 2');

        static::assertCount(2, $result['gifs']);
        static::assertSame('This is the quote 1', $result['gifs']['quote-1.gif']->quote);
        static::assertSame('Finally, the quote number 3', $result['gifs']['quote-3.gif']->quote);
    }

    public function testFindCharacters(): void
    {
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

        $expected = [
            [
                'slug' => 'character-1',
                'name' => 'Character 1',
                'image' => 'image-1.png',
                'url' => 'character-url-1',
            ],
            [
                'slug' => 'character-2',
                'name' => 'Character 2',
                'image' => 'image-2.png',
                'url' => 'character-url-2',
            ],
            [
                'slug' => 'character-3',
                'name' => 'Character 3',
                'image' => 'image-3.png',
                'url' => 'character-url-3',
            ],
        ];

        $result = $this->parser->findCharacters();

        static::assertArrayHasKey('characters', $result);
        static::assertSame($expected, $result['characters']);
    }

    public function testFindBySlug(): void
    {
        $expected = [
            'quote' => 'Finally, the quote number 3',
            'characters' => [
                [
                    'slug' => 'character-2',
                    'name' => 'Character 2',
                    'image' => null,
                    'url' => null,
                ],
            ],
            'filename' => 'quote-3.gif',
            'slug' => 'finally-the-quote-number-3',
            'url' => null,
            'image' => null,
            'width' => 100,
            'height' => 100,
            'code' => '0c3c899cad',
            'shortUrl' => null,
        ];

        $result = $this->parser->findBySlug('finally-the-quote-number-3');

        static::assertIsArray($result);
        static::assertCount(3, $result);
        static::assertEquals((object) $expected, $result['current']);
        // GIFs are sorted alphabetically, which explains the following order
        static::assertSame('This is the quote 1', $result['previous']->quote);
        static::assertSame('Here is the qûote 2', $result['next']->quote);
    }

    public function testFindForSitemap(): void
    {
        $result = $this->parser->findForSitemap();

        static::assertArrayHasKey('gifs', $result);
        static::assertArrayHasKey('characters', $result);
    }
}
