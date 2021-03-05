<?php

declare(strict_types=1);

namespace KaamelottGifboard\Tests\Helper;

use KaamelottGifboard\Helper\ImageHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ImageHelperTest extends KernelTestCase
{
    private ImageHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new ImageHelper(__DIR__);
    }

    /**
     * @dataProvider getCharacters
     */
    public function testGetCharacterImage(string $character, string $expectedImage): void
    {
        $image = $this->helper->getCharacterImage($character);

        static::assertSame($expectedImage, $image);
    }

    public function getCharacters(): array
    {
        return [
            ['arthur', 'characters/arthur.png'],
            ['perceval', 'characters/unknown.jpg'],
        ];
    }
}
