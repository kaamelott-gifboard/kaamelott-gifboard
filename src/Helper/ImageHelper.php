<?php

declare(strict_types=1);

namespace KaamelottGifboard\Helper;

class ImageHelper
{
    private const CHARACTER_DIR = 'characters';

    private string $publicPath;

    public function __construct(string $publicPath)
    {
        $this->publicPath = $publicPath;
    }

    public function getCharacterImage(string $character): string
    {
        $file = sprintf('%s/%s.png', self::CHARACTER_DIR, $character);

        return file_exists($this->publicPath.'/'.$file) ? $file : sprintf('%s/unknown.jpg', self::CHARACTER_DIR);
    }
}
