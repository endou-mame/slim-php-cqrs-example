<?php

declare(strict_types=1);

namespace App\Domain\Task;

readonly class TaskCreated
{
    public function __construct(
        public TaskId $taskId,
        public TaskTitle $title,
        public TaskDescription $description,
        public TaskStatus $status,
    ) {}
}
