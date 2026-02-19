<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

readonly class CreateTask
{
    public function __construct(
        public string $title,
        public string $description,
    ) {}
}
