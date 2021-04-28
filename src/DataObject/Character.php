<?php

declare(strict_types=1);

namespace KaamelottGifboard\DataObject;

final class Character
{
    public function __construct(
        public string $slug,
        public string $name,
        public string $image,
        public string $url,
    ) {
    }
}
