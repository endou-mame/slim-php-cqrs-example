<?php

declare(strict_types=1);

namespace App\Infrastructure\EventStore;

use App\Application\Task\Port\TaskEventStore;
use App\Domain\Task\Event\TaskCompleted;
use App\Domain\Task\Event\TaskCreated;
use App\Domain\Task\Event\TaskTitleChanged;
use App\Domain\Task\Task;
use App\Domain\Task\ValueObject\TaskId;
use EndouMame\PhpMonad\Option;
use function EndouMame\PhpMonad\Option\none;
use function EndouMame\PhpMonad\Option\some;

class InMemoryTaskEventStore implements TaskEventStore
{
    /** @var array<string, list<TaskCreated|TaskCompleted|TaskTitleChanged>> */
    private array $events = [];

    public function save(Task $task): void
    {
        $id = (string) $task->id;
        $this->events[$id] = [
            ...($this->events[$id] ?? []),
            ...$task->uncommittedEvents(),
        ];
    }

    /** @return Option<Task> */
    public function findById(TaskId $id): Option
    {
        $key = (string) $id;
        if (!isset($this->events[$key]) || $this->events[$key] === []) {
            return none();
        }

        return some(Task::reconstitute($id, $this->events[$key]));
    }
}
