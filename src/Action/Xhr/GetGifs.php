<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Xhr;

use KaamelottGifboard\Action\AbstractAction;
use KaamelottGifboard\Helper\GifPageHelper;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GetGifs extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $page = (int) $request->query->get('page', '2');
        $offset = GifPageHelper::getOffset($page);

        if ($page > GifPageHelper::getNumberOfPages($this->finder->countGifs())) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $gifs = iterator_to_array($this->finder->findGifs($offset), false);

        return $this->render('includes/list-items.html.twig', [
            'gifs' => $gifs,
        ]);
    }
}
