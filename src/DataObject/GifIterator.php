<?php

declare(strict_types=1);

namespace KaamelottGifboard\DataObject;

/**
 * @psalm-suppress MissingConstructor
 * @psalm-suppress MixedInferredReturnType
 * @psalm-suppress MixedReturnStatement
 */
final class GifIterator extends \ArrayIterator
{
    public \ArrayIterator $gifs;

    public function random(): int
    {
        return rand(0, $this->lastIndex());
    }

    public function getElement(int $key): Gif
    {
        $this->seek($key);

        /**
         * @phpstan-var Gif
         */
        return $this->current();
    }

    public function prevElement(int $key): Gif
    {
        if (0 === $key) {
            $this->seek($this->lastIndex());

            /**
             * @phpstan-var Gif
             */
            return $this->current();
        }

        --$key;

        $this->seek($key);

        /**
         * @phpstan-var Gif
         */
        return $this->current();
    }

    public function nextElement(int $key): Gif
    {
        if ($key === $this->lastIndex()) {
            $this->rewind();

            /**
             * @phpstan-var Gif
             */
            return $this->current();
        }

        $this->seek($key);
        $this->next();

        /**
         * @phpstan-var Gif
         */
        return $this->current();
    }

    private function lastIndex(): int
    {
        return $this->count() - 1;
    }
}
