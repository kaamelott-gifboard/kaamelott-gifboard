<?php

declare(strict_types=1);

namespace KaamelottGifboard\Service;

use KaamelottGifboard\DataObject\Gif;
use KaamelottGifboard\DataObject\GifIterator;
use KaamelottGifboard\Helper\CodeHelper;
use KaamelottGifboard\Helper\ImageHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class GifLister
{
    public GifIterator $gifs;

    public function __construct(
        private string $gifsJsonFile,
        private RouterInterface $router,
        private SluggerInterface $slugger,
        private ImageHelper $imageHelper
    ) {
        $this->init();
    }

    private function init(): void
    {
        $json = (string) file_get_contents($this->gifsJsonFile);

        $gifs = [];

        foreach (json_decode($json) as $gifItem) {
            $slug = $this->lowerSlug($gifItem->quote);

            $gif = new Gif();
            $gif->slug = $slug;
            $gif->quote = $gifItem->quote;
            $gif->filename = $gifItem->filename;
            $gif->url = $this->router->generate('get_by_slug', ['slug' => $slug], RouterInterface::ABSOLUTE_URL);
            $gif->image = $this->router->generate('quote_image', ['filename' => $gif->filename], RouterInterface::ABSOLUTE_URL);

            $dimensions = $this->imageHelper->getImageDimensions($gif->filename);

            $gif->width = $dimensions['width'];
            $gif->height = $dimensions['height'];

            $this->formatShortUrl($gif);
            $this->formatCharacters($gif, $gifItem);

            $gifs[] = $gif;
        }

        sort($gifs);

        $this->gifs = new GifIterator($gifs);
    }

    private function lowerSlug(string $string): string
    {
        return $this->slugger->slug($string)->lower()->__toString();
    }

    private function formatCharacters(Gif $gif, \stdClass $gifItem): void
    {
        $formattedCharacters = [];

        foreach ($gifItem->characters as $character) {
            $sluggedCharacter = $this->lowerSlug($character);

            $characterImage = $this->router->generate('character_image', [
                'filename' => $this->imageHelper->getCharacterImage($sluggedCharacter),
            ], RouterInterface::ABSOLUTE_URL);

            $characterUrl = $this->router->generate('get_by_character', [
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

    private function formatShortUrl(Gif $gif): void
    {
        $code = CodeHelper::getCode($gif);

        $gif->code = $code;
        $gif->shortUrl = $this->router->generate('get_by_code_short', ['code' => $code], RouterInterface::ABSOLUTE_URL);
    }
}
