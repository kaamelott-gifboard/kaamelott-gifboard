<?php

declare(strict_types=1);

namespace KaamelottGifboard\Helper;

class CodeHelper
{
    public static function getCode(\stdClass $gif): string
    {
        $fields = [
            $gif->slug,
            $gif->width,
            $gif->height,
        ];

        return substr(md5(implode('', $fields)), 0, 10);
    }
}
