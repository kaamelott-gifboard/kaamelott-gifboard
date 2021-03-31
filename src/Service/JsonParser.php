<?php

declare(strict_types=1);

namespace KaamelottGifboard\Service;

use KaamelottGifboard\Helper\CodeHelper;
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
        $selectedCharacter = null;

        foreach ($this->getGifs() as $gif) {
            foreach ($gif->characters as $character) {
                if ($this->match($search, $character['name'])) {
                    $results[$gif->filename] = $gif;

                    $selectedCharacter = $character;
                }
            }
        }

        return array_merge(['character' => $selectedCharacter], $this->formatResult($results));
    }

    public function findCharacters(): array
    {
        $results = [];

        foreach ($this->getGifs() as $gif) {
            foreach ($gif->characters as $character) {
                if (!\array_key_exists($character['slug'], $results)) {
                    $results[$character['slug']] = $character;
                }
            }
        }

        sort($results);

        return ['characters' => $results];
    }

    public function findBySlug(string $slug): ?array
    {
        foreach ($this->getGifs() as $gif) {
            if ($gif->slug === $slug) {
                return (array) $gif;
            }
        }

        return null;
    }

    public function findByCode(string $code): ?array
    {
        foreach ($this->getGifs() as $gif) {
            if ($gif->code === $code) {
                return (array) $gif;
            }
        }

        return null;
    }

    public function findForSitemap(): array
    {
        return array_merge($this->findAll(), $this->findCharacters());
    }

    private function getGifs(): array
    {
        if (!$json = file_get_contents($this->gifsJsonFile)) {
            return [];
        }

        $gifs = json_decode($json);

        foreach ($gifs as $gif) {
            $slug = $this->lowerSlug($gif->quote);

            $dimensions = $this->imageHelper->getImageDimensions($gif->filename);

            $gif->slug = $slug;
            $gif->url = $this->router->generate('search_slug', ['slug' => $slug], RouterInterface::ABSOLUTE_URL);
            $gif->image = $this->router->generate('gif_image', ['filename' => $gif->filename], RouterInterface::ABSOLUTE_URL);
            $gif->width = $dimensions['width'];
            $gif->height = $dimensions['height'];

            $this->formatShortUrl($gif);
            $this->formatCharacters($gif);
        }

        sort($gifs);

        return $gifs;
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

    private function formatCharacters(\stdClass $gif): void
    {
        $formattedCharacters = [];

        foreach ($gif->characters as $character) {
            $sluggedCharacter = $this->lowerSlug($character);

            $characterImage = $this->router->generate('character_image', [
                'filename' => $this->imageHelper->getCharacterImage($sluggedCharacter),
            ], RouterInterface::ABSOLUTE_URL);

            $characterUrl = $this->router->generate('search_character', [
                'name' => $character,
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $formattedCharacters[] = [
                'slug' => $sluggedCharacter,
                'name' => $character,
                'image' => $characterImage,
                'url' => $characterUrl,
            ];
        }

        $gif->characters = $formattedCharacters;
    }

    private function formatShortUrl(mixed $gif): void
    {
        $code = CodeHelper::getCode($gif);

        $gif->code = $code;
        $gif->shortUrl = $this->router->generate('search_short_url', ['code' => $code], RouterInterface::ABSOLUTE_URL);
    }
}
