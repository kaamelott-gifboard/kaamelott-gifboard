<?php

declare(strict_types=1);

namespace KaamelottGifboard\Helper;

use KaamelottGifboard\Finder\GifFinder;

class GifPageHelper
{
    public static function getOffset(int $page): int
    {
        $page = 0 === $page ? 1 : $page;

        return ($page - 1) * GifFinder::GIFS_PER_PAGE;
    }

    public static function getNumberOfPages(int $nbGifs): int
    {
        return (int) ceil($nbGifs / GifFinder::GIFS_PER_PAGE);
    }
}
