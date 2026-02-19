<?php

declare(strict_types=1);

namespace App\Domain\Task;

readonly class TaskCompleted
{
    public function __construct(
        public TaskId $taskId,
    ) {}
}
