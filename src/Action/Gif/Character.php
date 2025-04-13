<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Character extends AbstractAction
{
    #[Route('/search/character/{name}', name: 'get_by_character')]
    public function __invoke(string $name): Response
    {
        $character = $this->finder->findCharacter($name);

        if (null === $character) {
            throw new PouletteNotFoundException('character', $name);
        }

        $gifs = $this->finder->findGifsByCharacter($name);

        return $this->render('character.html.twig', [
            'character' => $character,
            'gifs' => $gifs,
        ]);
    }
}
