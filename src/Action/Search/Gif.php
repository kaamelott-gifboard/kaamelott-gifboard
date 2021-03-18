<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Search;

use KaamelottGifboard\Service\JsonParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Twig\Environment;

class Gif
{
    private Environment $twig;
    private JsonParser $jsonParser;

    public function __construct(Environment $twig, JsonParser $jsonParser)
    {
        $this->twig = $twig;
        $this->jsonParser = $jsonParser;
    }

    public function byCharacter(string $name): Response
    {
        $gifs = $this->jsonParser->findByCharacter($name);

        $view = $this->twig->render('body.html.twig', $gifs);

        return (new Response())->setContent($view);
    }

    public function bySlug(string $slug): Response
    {
        $gif = $this->jsonParser->findBySlug($slug);

        if (null === $gif) {
            throw new NotFoundHttpException(sprintf('Elle est où la poulette [slug: %s]', $slug));
        }

        $view = $this->twig->render('gif.html.twig', $gif);

        return (new Response())->setContent($view);
    }

    public function getAll(): Response
    {
        $gifs = $this->jsonParser->findAll();

        $view = $this->twig->render('body.html.twig', $gifs);

        return (new Response())->setContent($view);
    }

    public function countAll(Request $request): Response
    {
        if (!$request->isXmlHttpRequest()) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $result = $this->jsonParser->findAll();

        return new JsonResponse(count($result['gifs']));
    }
}
