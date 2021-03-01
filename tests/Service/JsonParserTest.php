<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Service\JsonParser;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class JsonParserTest extends KernelTestCase
{
    private JsonParser $parser;

    protected function setUp(): void
    {
        $this->parser = new JsonParser(__DIR__.'/gifs-test.json');
    }

    public function testJsonIsValid(): void
    {
        $kernel = self::bootKernel();

        $gifsJsonPath = $kernel->getContainer()->getParameter('gifs_json_path');

        $parser = new JsonParser($gifsJsonPath);

        $result = $parser->findAll();

        static::assertIsArray($result);
        static::assertArrayHasKey('gifs', $result);

        foreach ($result['gifs'] as $item) {
            static::assertObjectHasAttribute('quote', $item);
            static::assertIsString($item->quote);
            static::assertObjectHasAttribute('characters', $item);
            static::assertIsArray($item->characters);
            static::assertObjectHasAttribute('filename', $item);
            static::assertIsString($item->filename);
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
        $result = $this->parser->findCharacters();

        static::assertArrayHasKey('characters', $result);
        static::assertSame($result['characters'], [
            'Character 1',
            'Character 2',
            'Character 3',
        ]);
    }
}
