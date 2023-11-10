<?php

declare(strict_types=1);

namespace KaamelottGifboard\Finder;

use KaamelottGifboard\DataObject\Episode;
use KaamelottGifboard\Lister\EpisodeLister;

class EpisodeFinder
{
    public function __construct(private EpisodeLister $lister)
    {
    }

    public function findEpisodes(): \Iterator
    {
        return $this->lister->episodes;
    }

    public function getEpisodeBySeasons(): array
    {
        $episodeBySeasons = [];

        /** @var Episode $episode */
        foreach ($this->lister->episodes as $episode) {
            $episodeBySeasons[$episode->season][] = $episode;
        }

        return $episodeBySeasons;
    }
}
