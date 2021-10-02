<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class CharacterInScene extends AbstractAction
{
    public function __invoke(string $name): Response
    {
        $characterinscene = $this->finder->findCharacterInScene($name);

        if (null === $characterinscene) {
            throw new PouletteNotFoundException('characterinscene', $name);
        }

        $gifs = $this->finder->findGifsByCharacterInScene($name);

        return $this->render('character.html.twig', [
            'characterinscene' => $characterinscene,
            'gifs' => $gifs,
        ]);
    }
}
