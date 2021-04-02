<?php

declare(strict_types=1);

namespace KaamelottGifboard\Exception;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PouletteNotFoundException extends NotFoundHttpException
{
    public function __construct(string $type, string $value)
    {
        $message = sprintf('Elle est où la poulette [%s: %s] ?', $type, $value);

        parent::__construct($message);
    }
}
