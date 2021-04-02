<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class Character extends AbstractAction
{
    public function __invoke(string $name): Response
    {
        $gifs = $this->finder->findByCharacter($name);

        if (null === $gifs['character']) {
            throw new PouletteNotFoundException('name', $name);
        }

        return $this->render('character.html.twig', $gifs);
    }
}
