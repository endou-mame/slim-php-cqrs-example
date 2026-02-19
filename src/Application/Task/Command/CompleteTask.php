<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

readonly class CompleteTask
{
    public function __construct(
        public string $id,
    ) {}
}
