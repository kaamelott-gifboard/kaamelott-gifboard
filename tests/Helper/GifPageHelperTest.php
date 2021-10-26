<?php

declare(strict_types=1);

namespace KaamelottGifboard\Tests\Helper;

use KaamelottGifboard\Helper\GifPageHelper;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class GifPageHelperTest extends KernelTestCase
{
    private GifPageHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new GifPageHelper();
    }

    /**
     * @dataProvider getPages
     */
    public function testGetOffset(int $page, int $expected): void
    {
        static::assertSame($expected, $this->helper::getOffset($page));
    }

    public function getPages(): array
    {
        return [
            [0, 0],
            [1, 0],
            [2, 100],
            [3, 200],
        ];
    }

    /**
     * @dataProvider getNbGifs
     */
    public function testGetNumberOfPages(int $nbGifs, int $expected): void
    {
        static::assertSame($expected, $this->helper::getNumberOfPages($nbGifs));
    }

    public function getNbGifs(): array
    {
        return [
            [950, 10],
            [901, 10],
            [900, 9],
            [899, 9],
            [1, 1],
            [1, 1],
        ];
    }
}
