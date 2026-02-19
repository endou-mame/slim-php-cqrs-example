<?php

declare(strict_types=1);

namespace App\Application\Task\Query;

readonly class GetTaskDetail
{
    public function __construct(
        public string $id,
    ) {}
}
