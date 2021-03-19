<?php

declare(strict_types=1);

namespace KaamelottGifboard\Service;

use KaamelottGifboard\Helper\ImageHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class JsonParser
{
    private string $gifsJsonFile;
    private RouterInterface $router;
    private SluggerInterface $slugger;
    private ImageHelper $imageHelper;

    public function __construct(
        string $gifsJsonFile,
        RouterInterface $router,
        SluggerInterface $slugger,
        ImageHelper $imageHelper
    ) {
        $this->gifsJsonFile = $gifsJsonFile;
        $this->router = $router;
        $this->slugger = $slugger;
        $this->imageHelper = $imageHelper;
    }

    public function findAll(): array
    {
        return $this->formatResult($this->getGifs());
    }

    public function findByQuote(string $search): array
    {
        $results = [];

        foreach ($this->getGifs() as $gif) {
            if ($this->match($search, $gif->quote)) {
                $results[] = $gif;
            }
        }

        return $this->formatResult($results);
    }

    public function findByCharacter(string $search): array
    {
        $results = [];

        foreach ($this->getGifs() as $gif) {
            foreach ($gif->characters as $character) {
                if ($this->match($search, $character)) {
                    $results[$gif->filename] = $gif;
                }
            }
        }

        return $this->formatResult($results);
    }

    public function findCharacters(): array
    {
        $results = [];

        foreach ($this->getGifs() as $gif) {
            foreach ($gif->characters as $character) {
                $sluggedCharacter = $this->lowerSlug($character);

                if (!\array_key_exists($sluggedCharacter, $results)) {
                    $characterUrl = $this->router->generate('search_character', ['name' => $character], UrlGeneratorInterface::ABSOLUTE_URL);
                    $characterImage = $this->router->generate('character_image', [
                        'filename' => $this->imageHelper->getCharacterImage($sluggedCharacter),
                    ], RouterInterface::ABSOLUTE_URL);

                    $results[$sluggedCharacter] = [
                        'name' => $character,
                        'url' => $characterUrl,
                        'image' => $characterImage,
                    ];
                }
            }
        }

        sort($results);

        return ['characters' => $results];
    }

    public function findBySlug(string $slug): ?array
    {
        foreach ($this->getGifs() as $gif) {
            if ($this->lowerSlug($gif->quote) === $slug) {
                return (array) $gif;
            }
        }

        return null;
    }

    public function findForSitemap(): array
    {
        $items = [];
        $gifs = $this->findAll()['gifs'];

        foreach ($gifs as $gif) {
            $slug = $this->lowerSlug($gif->quote);

            $url = $this->router->generate('search_slug', ['slug' => $slug], RouterInterface::ABSOLUTE_URL);
            $image = $this->router->generate('gif_image', ['filename' => $gif->filename], RouterInterface::ABSOLUTE_URL);

            $items['gifs'][] = [
                'url' => $url,
                'gif' => $image,
                'quote' => $gif->quote,
            ];
        }

        $items = array_merge($items, $this->findCharacters());

        return $items;
    }

    private function getGifs(): array
    {
        if (!$json = file_get_contents($this->gifsJsonFile)) {
            return [];
        }

        return json_decode($json);
    }

    private function match(string $search, string $subject): bool
    {
        return (bool) preg_match(sprintf('#%s#i', $search), $subject);
    }

    private function formatResult(array $results): array
    {
        return ['gifs' => $results];
    }

    private function lowerSlug(string $string): string
    {
        return $this->slugger->slug($string)->lower()->__toString();
    }
}
