<?php

declare(strict_types=1);

namespace App\Application\Score\Query;

final class GetTopScoresQuery
{
    public function __construct(public readonly int $limit = 10)
    {
    }
}
