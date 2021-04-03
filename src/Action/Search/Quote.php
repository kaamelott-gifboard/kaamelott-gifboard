<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Search;

use KaamelottGifboard\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Quote extends AbstractAction
{
    public function __invoke(Request $request): Response
    {
        $search = (string) $request->query->get('search', '');

        return $this->render('body.html.twig', [
            'gifs' => $this->finder->findGifsByQuote($search),
        ]);
    }
}
