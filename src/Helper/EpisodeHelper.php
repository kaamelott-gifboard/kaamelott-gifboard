<?php

declare(strict_types=1);

namespace KaamelottGifboard\Helper;

class EpisodeHelper
{
    private const SEASONS = ['Pilote', 'I', 'II', 'III', 'IV', 'V', 'VI'];

    /**
     * @return array{string, string}
     */
    public static function getSeasonAndNumber(string $episode): array
    {
        preg_match('#^S0([0-9])E([0-9]+)$#', $episode, $matches);

        return [
            sprintf('Livre %s', self::SEASONS[(int) $matches[1]]), // @phpstan-ignore-line
            $matches[2], // @phpstan-ignore-line
        ];
    }
}
