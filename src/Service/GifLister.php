<?php

declare(strict_types=1);

namespace KaamelottGifboard\Service;

use KaamelottGifboard\DataObject\Character;
use KaamelottGifboard\DataObject\Gif;
use KaamelottGifboard\DataObject\GifIterator;
use KaamelottGifboard\Helper\CodeHelper;
use KaamelottGifboard\Helper\ImageHelper;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class GifLister
{
    /** @var GifIterator|Gif[] */
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
            $gif = new Gif();
            $gif->slug = $gifItem->slug;
            $gif->quote = $gifItem->quote;
            $gif->filename = $gifItem->filename;
            $gif->url = $this->router->generate('get_by_slug', ['slug' => $gifItem->slug], RouterInterface::ABSOLUTE_URL);
            $gif->image = $this->router->generate('quote_image', ['filename' => $gif->filename], RouterInterface::ABSOLUTE_URL);

            $dimensions = $this->imageHelper->getImageDimensions($gif->filename);

            $gif->width = $dimensions['width'];
            $gif->height = $dimensions['height'];

            $this->formatShortUrl($gif);
            $this->formatCharacters($gif, $gifItem);
            $this->formatCharacters($gif, $gifItem, true);

            $gifs[] = $gif;
        }

        sort($gifs);

        $this->gifs = new GifIterator($gifs);
    }

    private function formatCharacters(Gif $gif, \stdClass $gifItem, bool $speaking = false): void
    {
        $characters = !$speaking ? $gifItem->characters : $gifItem->characters_speaking;

        foreach ($characters as $characterName) {
            $slug = $this->slugger->slug($characterName)->lower()->__toString();

            $image = $this->router->generate('character_image', [
                'filename' => $this->imageHelper->getCharacterImage($slug),
            ], RouterInterface::ABSOLUTE_URL);

            $url = $this->router->generate('get_by_character', [
                'name' => $characterName,
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            $character = (new Character(
                $slug,
                $characterName,
                $image,
                $url
            ));

            if (!$speaking) {
                $gif->characters[] = $character;
            } else {
                $gif->charactersSpeaking[] = $character;
            }
        }
    }

    private function formatShortUrl(Gif $gif): void
    {
        $code = CodeHelper::getCode($gif);

        $gif->code = $code;
        $gif->shortUrl = $this->router->generate('get_by_code_short', ['code' => $code], RouterInterface::ABSOLUTE_URL);
    }
}
