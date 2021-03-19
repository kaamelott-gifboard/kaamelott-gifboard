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
        $filename = sprintf('%s.png', $character);
        $filePath = sprintf('%s/%s', self::CHARACTER_DIR, $filename);

        return file_exists($this->publicPath.'/'.$filePath) ? $filename : 'unknown.jpg';
    }
}
