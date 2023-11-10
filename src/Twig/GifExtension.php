<?php

declare(strict_types=1);

namespace KaamelottGifboard\Twig;

use KaamelottGifboard\Helper\EpisodeHelper;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class GifExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('format_episode_long_name', [$this, 'formatEpisodeLongName']),
        ];
    }

    public function formatEpisodeLongName(string $code): string
    {
        [$season, $episode] = EpisodeHelper::getSeasonAndNumber($code);

        return sprintf('%s, Episode %s', $season, $episode);
    }
}
