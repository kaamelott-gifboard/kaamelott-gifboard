<?php

declare(strict_types=1);

namespace KaamelottGifboard\Lister;

use KaamelottGifboard\DataObject\Episode;
use KaamelottGifboard\Finder\GifFinder;
use KaamelottGifboard\Helper\EpisodeHelper;
use Symfony\Component\Routing\RouterInterface;

class EpisodeLister
{
    public \ArrayIterator $episodes;

    public function __construct(
        private string $episodesJsonFile,
        private RouterInterface $router,
        private GifFinder $gifFinder,
    ) {
        $this->init();
    }

    private function init(): void
    {
        $json = (string) file_get_contents($this->episodesJsonFile);

        $episodes = [];

        /** @var array $data */
        $data = json_decode($json);

        $gifByEpisodes = $this->gifFinder->findGifsByEpisodes();

        /** @var \stdClass $episodeItem */
        foreach ($data as $episodeItem) {
            [$season, $episodeNumber] = EpisodeHelper::getSeasonAndNumber((string) $episodeItem->code);

            $episode = new Episode();
            $episode->code = (string) $episodeItem->code;
            $episode->season = $season;
            $episode->episode = $episodeNumber;
            $episode->title = (string) $episodeItem->title;
            $episode->nbGif = \count($gifByEpisodes[$episode->code] ?? []);
            $episode->url = $episode->nbGif ? $this->router->generate('get_by_episode', ['code' => $episode->code], RouterInterface::ABSOLUTE_URL) : null;

            $episodes[] = $episode;
        }

        $this->episodes = new \ArrayIterator($episodes);
    }
}
