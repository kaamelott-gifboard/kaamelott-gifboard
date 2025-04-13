<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Gif;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Exception\PouletteNotFoundException;
use KaamelottGifboard\Helper\GifPageHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class Index extends AbstractAction
{
    #[Route('/', name: 'index')]
    public function __invoke(Request $request): Response
    {
        $page = (int) $request->query->get('page', '1');
        $offset = GifPageHelper::getOffset($page);

        if ($page > GifPageHelper::getNumberOfPages($this->finder->countGifs())) {
            throw new PouletteNotFoundException('page', (string) $page);
        }

        return $this->render('body.html.twig', [
            'gifs' => $this->finder->findGifs($offset),
            'pagination' => true,
        ]);
    }
}
