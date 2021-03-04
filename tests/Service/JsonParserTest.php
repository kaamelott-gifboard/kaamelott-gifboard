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

        $this->parser = new JsonParser(
            __DIR__.'/gifs-test.json',
            $this->router,
            new AsciiSlugger(),
            $this->helper
        );
    }

    public function testJsonIsValid(): void
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
        static::assertSame('This is the quote 1', $result['gifs'][0]->quote);
        static::assertSame('Here is the quote 2', $result['gifs'][1]->quote);
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
            ->expects(static::exactly(3))
            ->method('generate')
            ->withConsecutive(
                ['search_character', ['name' => 'Character 1']],
                ['search_character', ['name' => 'Character 2']],
                ['search_character', ['name' => 'Character 3']],
            )
            ->willReturnOnConsecutiveCalls('route-1', 'route-2', 'route-3');

        $this->helper
            ->expects(static::exactly(3))
            ->method('getCharacterImage')
            ->withConsecutive(
                ['character-1'],
                ['character-2'],
                ['character-3'],
            )
            ->willReturnOnConsecutiveCalls('image-1.png', 'image-2.png', 'image-3.png');

        $expected = [
            [
                'name' => 'Character 1',
                'url' => 'route-1',
                'image' => 'image-1.png',
            ],
            [
                'name' => 'Character 2',
                'url' => 'route-2',
                'image' => 'image-2.png',
            ],
            [
                'name' => 'Character 3',
                'url' => 'route-3',
                'image' => 'image-3.png',
            ],
        ];

        $result = $this->parser->findCharacters();

        static::assertArrayHasKey('characters', $result);
        static::assertSame($result['characters'], $expected);
    }

    public function testFindBySlug(): void
    {
        $expected = [
            'quote' => 'Finally, the quote number 3',
            'characters' => [
                'Character 2',
            ],
            'filename' => 'quote-3.gif',
        ];

        $result = $this->parser->findBySlug('finally-the-quote-number-3');

        static::assertIsArray($result);
        static::assertSame($expected, $result);
    }

    public function testFindForSitemap(): void
    {
        $this->router
            ->expects(static::exactly(6))
            ->method('generate')
            ->withConsecutive(
                ['search_slug', ['slug' => 'this-is-the-quote-1']],
                ['gif_image', ['filename' => 'quote-1.gif']],
                ['search_slug', ['slug' => 'here-is-the-quote-2']],
                ['gif_image', ['filename' => 'quote-2.gif']],
                ['search_slug', ['slug' => 'finally-the-quote-number-3']],
                ['gif_image', ['filename' => 'quote-3.gif']],
            )
            ->willReturnOnConsecutiveCalls('route-1', 'gif-1', 'route-2', 'gif-2', 'route-3', 'gif-3');

        $expected = [
            [
                'url' => 'route-1',
                'gif' => 'gif-1',
                'quote' => 'This is the quote 1',
            ],
            [
                'url' => 'route-2',
                'gif' => 'gif-2',
                'quote' => 'Here is the quote 2',
            ],
            [
                'url' => 'route-3',
                'gif' => 'gif-3',
                'quote' => 'Finally, the quote number 3',
            ],
        ];

        static::assertSame($expected, $this->parser->findForSitemap());
    }
}
