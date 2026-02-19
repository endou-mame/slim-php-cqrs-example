<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

readonly class ChangeTaskTitle
{
    public function __construct(
        public string $id,
        public string $newTitle,
    ) {}
}
