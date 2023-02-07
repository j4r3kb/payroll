<?php

declare(strict_types=1);

namespace App\Common\Application\Query;

enum SortOrder: string
{
    case ASC = 'ASC';
    case DESC = 'DESC';
}
