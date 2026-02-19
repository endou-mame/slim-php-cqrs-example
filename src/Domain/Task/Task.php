<?php

declare(strict_types=1);

namespace App\Domain\Task;

use EndouMame\PhpMonad\Result;

readonly class Task
{
    /** @param list<TaskCreated|TaskCompleted|TaskTitleChanged> $uncommittedEvents */
    private function __construct(
        public TaskId $id,
        public TaskTitle $title,
        public TaskDescription $description,
        public TaskStatus $status,
        private array $uncommittedEvents,
    ) {}

    /**
     * @return Result<self, TaskError>
     */
    public static function create(TaskId $id, TaskTitle $title, TaskDescription $description): Result
    {
        $event = new TaskCreated($id, $title, $description, TaskStatus::Open);

        return Result\ok(new self(
            id: $id,
            title: $title,
            description: $description,
            status: TaskStatus::Open,
            uncommittedEvents: [$event],
        ));
    }

    /**
     * @return Result<self, TaskError>
     */
    public function complete(): Result
    {
        if ($this->status === TaskStatus::Completed) {
            return Result\err(TaskError::AlreadyCompleted); // @phpstan-ignore return.type
        }

        $event = new TaskCompleted($this->id);

        return Result\ok(new self(
            id: $this->id,
            title: $this->title,
            description: $this->description,
            status: TaskStatus::Completed,
            uncommittedEvents: [...$this->uncommittedEvents, $event],
        ));
    }

    /**
     * @return Result<self, TaskError>
     */
    public function changeTitle(TaskTitle $newTitle): Result
    {
        $event = new TaskTitleChanged($this->id, $newTitle);

        return Result\ok(new self(
            id: $this->id,
            title: $newTitle,
            description: $this->description,
            status: $this->status,
            uncommittedEvents: [...$this->uncommittedEvents, $event],
        ));
    }

    /**
     * @return list<TaskCreated|TaskCompleted|TaskTitleChanged>
     */
    public function uncommittedEvents(): array
    {
        return $this->uncommittedEvents;
    }

    /**
     * @param list<TaskCreated|TaskCompleted|TaskTitleChanged> $events
     */
    public static function reconstitute(TaskId $id, array $events): self
    {
        assert($events !== [], 'Events must not be empty');
        assert($events[0] instanceof TaskCreated, 'First event must be TaskCreated');

        $firstEvent = $events[0];
        $initial = new self(
            id: $firstEvent->taskId,
            title: $firstEvent->title,
            description: $firstEvent->description,
            status: $firstEvent->status,
            uncommittedEvents: [],
        );

        $remainingEvents = array_slice($events, 1);

        return array_reduce(
            $remainingEvents,
            static fn (self $task, TaskCreated|TaskCompleted|TaskTitleChanged $event): self => $task->apply($event),
            $initial,
        );
    }

    private function apply(TaskCreated|TaskCompleted|TaskTitleChanged $event): self
    {
        return match (true) {
            $event instanceof TaskCreated => new self(
                id: $event->taskId,
                title: $event->title,
                description: $event->description,
                status: $event->status,
                uncommittedEvents: [],
            ),
            $event instanceof TaskCompleted => new self(
                id: $this->id,
                title: $this->title,
                description: $this->description,
                status: TaskStatus::Completed,
                uncommittedEvents: [],
            ),
            $event instanceof TaskTitleChanged => new self(
                id: $this->id,
                title: $event->newTitle,
                description: $this->description,
                status: $this->status,
                uncommittedEvents: [],
            ),
        };
    }
}
