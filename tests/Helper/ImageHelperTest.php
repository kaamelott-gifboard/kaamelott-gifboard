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
            ['arthur', 'arthur.jpg'],
            ['perceval', 'unknown.jpg'],
        ];
    }

    /**
     * @dataProvider getImages
     */
    public function testGetImageDimensions(string $file, array $expected): void
    {
        $dimensions = $this->helper->getImageDimensions($file);

        static::assertSame($expected, $dimensions);
    }

    public function getImages(): array
    {
        return [
            ['elle-est-ou-la-poulette.gif', ['width' => 220, 'height' => 124]],
            ['foobar.gif', ['width' => null, 'height' => null]],
        ];
    }
}
