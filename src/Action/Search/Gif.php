<?php

declare(strict_types=1);

namespace KaamelottGifboard\Action\Search;

use KaamelottGifboard\Exception\PouletteNotFoundException;
use KaamelottGifboard\Service\JsonParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class Gif extends AbstractController
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

        return $this->render('character.html.twig', $gifs);
    }

    public function byCode(string $code): Response
    {
        $gif = $this->jsonParser->findByCode($code);

        if (null === $gif || false === \array_key_exists('url', $gif)) {
            throw new PouletteNotFoundException('code', $code);
        }

        return new RedirectResponse($gif['url'], Response::HTTP_SEE_OTHER);
    }

    public function bySlug(string $slug): Response
    {
        $gifs = $this->jsonParser->findBySlug($slug);

        if (null === $gifs) {
            throw new PouletteNotFoundException('slug', $slug);
        }

        return $this->render('gif.html.twig', $gifs);
    }

    public function getAll(): Response
    {
        $gifs = $this->jsonParser->findAll();

        return $this->render('body.html.twig', $gifs);
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
