<?php

declare(strict_types=1);

namespace KaamelottGifboard\Normalizer;

use KaamelottGifboard\DataObject\Gif;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;

class GifNormalizer implements ContextAwareNormalizerInterface
{
    /**
     * @param Gif $object
     *
     * @psalm-suppress MoreSpecificImplementedParamType
     */
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        return [
            'gif' => $object->image,
            'url' => $object->url,
        ];
    }

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Gif;
    }
}
