<?php

declare(strict_types=1);

namespace Cloudflare\Proxy\Enum;

enum ProxyMode: string
{
    case APPEND = 'append';
    case OVERRIDE = 'override';
}
