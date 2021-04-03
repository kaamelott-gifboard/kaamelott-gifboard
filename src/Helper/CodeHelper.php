<?php

declare(strict_types=1);

namespace KaamelottGifboard\Helper;

use KaamelottGifboard\DataObject\Gif;

class CodeHelper
{
    public static function getCode(Gif $gif): string
    {
        $fields = [
            $gif->slug,
            $gif->width,
            $gif->height,
        ];

        return substr(md5(implode('', $fields)), 0, 10);
    }
}
