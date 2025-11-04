<?php

namespace App\Shared\Infrastructure\Security;

enum JwtAlgoEnum: string
{
    case HS256 = 'sha256';
    case HS384 = 'sha384';
    case HS512 = 'sha512';

    /**
     * @return self
     */
    static public function default(): self
    {
        return self::HS256;
    }
}
