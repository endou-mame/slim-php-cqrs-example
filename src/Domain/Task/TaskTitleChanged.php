<?php

declare(strict_types=1);

namespace App\Domain\Task;

readonly class TaskTitleChanged
{
    public function __construct(
        public TaskId $taskId,
        public TaskTitle $newTitle,
    ) {}
}
