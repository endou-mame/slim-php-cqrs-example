<?php

declare(strict_types=1);

namespace App\Domain\Task\Event;

use App\Domain\Task\ValueObject\TaskId;
use App\Domain\Task\ValueObject\TaskTitle;

readonly class TaskTitleChanged
{
    public function __construct(
        public TaskId $taskId,
        public TaskTitle $newTitle,
    ) {}
}
