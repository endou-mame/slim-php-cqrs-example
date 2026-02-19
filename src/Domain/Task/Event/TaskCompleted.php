<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

use App\Domain\Task\ValueObject\TaskId;

readonly class TaskCompleted
{
    public function __construct(
        public TaskId $taskId,
    ) {}
}
