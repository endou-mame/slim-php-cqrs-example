<?php

declare(strict_types=1);

namespace App\Application\Task\Port;

use App\Domain\Task\Task;
use App\Domain\Task\ValueObject\TaskId;
use EndouMame\PhpMonad\Option;

interface TaskEventStore
{
    public function save(Task $task): void;

    /** @return Option<Task> */
    public function findById(TaskId $id): Option;
}
