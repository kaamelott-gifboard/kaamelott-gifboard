<?php

declare(strict_types=1);

namespace KaamelottGifboard\DataObject;

/**
 * @psalm-suppress MissingConstructor
 */
final class Episode
{
    public function __construct(
        public string $code,
        public string $season,
        public string $episode,
        public string $title,
        public ?string $url,
        public int $nbGif,
    ) {
    }
}
