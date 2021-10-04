<?php

declare(strict_types=1);

namespace KaamelottGifboard\DataObject;

final class Gif
{
    public string $quote;
    public array $characters;
    public array $charactersSpeaking;
    public string $filename;
    public string $slug;
    public string $url;
    public string $image;
    public int $width;
    public int $height;
    public string $code;
    public string $shortUrl;
}
