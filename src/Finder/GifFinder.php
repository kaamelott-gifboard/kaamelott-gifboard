<?php

declare(strict_types=1);

namespace KaamelottGifboard\Finder;

use KaamelottGifboard\DataObject\Character;
use KaamelottGifboard\DataObject\Gif;
use KaamelottGifboard\Lister\GifLister;
use Symfony\Component\String\UnicodeString;

class GifFinder
{
    public const GIFS_PER_PAGE = 100;

    public function __construct(private GifLister $lister)
    {
    }

    public function countGifs(): int
    {
        return $this->lister->gifs->count();
    }

    public function findGifs(?int $offset = null, int $limit = self::GIFS_PER_PAGE): \Iterator
    {
        if (null !== $offset) {
            return new \LimitIterator($this->lister->gifs, $offset, $limit);
        }

        return $this->lister->gifs;
    }

    public function findGifsByQuote(string $search): array
    {
        $results = [];

        /** @var Gif $gif */
        foreach ($this->lister->gifs as $gif) {
            if ($this->match($search, $gif->quote, true)) {
                $results[] = $gif;
            }
        }

        return $results;
    }

    public function findGifsByCharacter(string $search): ?array
    {
        $results = [];

        /** @var Gif $gif */
        foreach ($this->lister->gifs as $gif) {
            foreach ($gif->charactersSpeaking as $character) {
                if ($this->match($search, $character->name)) {
                    $results[] = $gif;
                }
            }
        }

        return $results;
    }

    public function findGifsBySlug(string $slug): ?array
    {
        /**
         * @var int $key
         * @var Gif $gif
         */
        foreach ($this->lister->gifs as $key => $gif) {
            if ($gif->slug === $slug) {
                return $this->getGifByKey($key);
            }
        }

        return null;
    }

    public function findGifsByCode(string $code): ?Gif
    {
        /** @var Gif $gif */
        foreach ($this->lister->gifs as $gif) {
            if ($gif->code === $code) {
                return $gif;
            }
        }

        return null;
    }

    public function findCharacter(string $search): ?Character
    {
        /** @var Gif $gif */
        foreach ($this->lister->gifs as $gif) {
            foreach ($gif->characters as $character) {
                if ($this->match($search, $character->name)) {
                    return $character;
                }
            }
        }

        return null;
    }

    public function findCharacters(): array
    {
        $results = [];

        /** @var Gif $gif */
        foreach ($this->lister->gifs as $gif) {
            foreach ($gif->charactersSpeaking as $character) {
                if (!\array_key_exists($character->slug, $results)) {
                    $results[$character->slug] = $character;
                }
            }
        }

        sort($results);

        return $results;
    }

    public function findRandom(): array
    {
        return $this->getGifByKey($this->lister->gifs->random());
    }

    public function findGifsByEpisode(string $episode): array
    {
        /** @var Gif[] $gifs */
        $gifs = iterator_to_array($this->lister->gifs);

        return array_filter(
            $gifs,
            static fn (Gif $gif): bool => $episode === $gif->episode,
        );
    }

    public function findGifsByEpisodes(): array
    {
        $gifByEpisodes = [];

        /** @var Gif $gif */
        foreach ($this->lister->gifs as $gif) {
            if (null === $gif->episode) {
                continue;
            }

            $gifByEpisodes[$gif->episode][] = $gif;
        }

        return $gifByEpisodes;
    }

    private function match(string $search, string $subject, bool $clean = false): bool
    {
        if ($clean) {
            $search = (new UnicodeString($search))->ascii()->toString();
            $subject = (new UnicodeString($subject))->ascii()->toString();
        }

        return (bool) preg_match(sprintf('#%s#ui', $search), $subject);
    }

    /**
     * @return array{previous:Gif, current:Gif, next:Gif}
     */
    private function getGifByKey(int $key): array
    {
        return [
            'previous' => $this->lister->gifs->prevElement($key),
            'current' => $this->lister->gifs->getElement($key),
            'next' => $this->lister->gifs->nextElement($key),
        ];
    }
}
