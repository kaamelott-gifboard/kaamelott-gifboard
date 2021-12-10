<?php

declare(strict_types=1);

namespace KaamelottGifboard\DataObject;

/**
 * @psalm-suppress MissingConstructor
 */
final class Gif
{
    public string $quote;
    /** @var array<Character> */
    public array $characters;
    /** @var array<Character> */
    public array $charactersSpeaking;
    public string $filename;
    public string $slug;
    public string $url;
    public string $image;
    public ?int $width;
    public ?int $height;
    public string $code;
    public string $shortUrl;
}
