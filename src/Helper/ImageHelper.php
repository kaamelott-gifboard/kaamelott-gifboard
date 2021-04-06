<?php

declare(strict_types=1);

namespace KaamelottGifboard\Helper;

class ImageHelper
{
    private const CHARACTER_DIR = 'characters';
    private const GIF_DIR = 'gifs';

    public function __construct(private string $publicPath)
    {
    }

    public function getCharacterImage(string $character): string
    {
        $filename = sprintf('%s.png', $character);
        $filePath = sprintf('%s/%s', self::CHARACTER_DIR, $filename);

        return file_exists($this->publicPath.'/'.$filePath) ? $filename : 'unknown.jpg';
    }

    public function getImageDimensions(string $filename): array
    {
        $filePath = sprintf('%s/%s', self::GIF_DIR, $filename);

        $details = @getimagesize($this->publicPath.'/'.$filePath);

        $width = $details ? $details[0] : null;
        $height = $details ? $details[1] : null;

        return [
            'width' => $width,
            'height' => $height,
        ];
    }
}
