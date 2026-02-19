<?php

declare(strict_types=1);

namespace App\Infrastructure\ReadModel;

use App\Application\Task\Port\TaskReadModel;
use App\Domain\Task\TaskCompleted;
use App\Domain\Task\TaskCreated;
use App\Domain\Task\TaskTitleChanged;
use App\Domain\Task\TaskStatus;

class InMemoryTaskReadModel implements TaskReadModel
{
    /** @var array<string, array{id: string, title: string, description: string, status: string}> */
    private array $tasks = [];

    public function project(TaskCreated|TaskCompleted|TaskTitleChanged $event): void
    {
        match (true) {
            $event instanceof TaskCreated => $this->applyTaskCreated($event),
            $event instanceof TaskCompleted => $this->applyTaskCompleted($event),
            $event instanceof TaskTitleChanged => $this->applyTaskTitleChanged($event),
        };
    }

    /** @return list<array{id: string, title: string, status: string}> */
    public function findAll(): array
    {
        return array_values(array_map(
            fn (array $task): array => [
                'id' => $task['id'],
                'title' => $task['title'],
                'status' => $task['status'],
            ],
            $this->tasks,
        ));
    }

    /** @return array{id: string, title: string, description: string, status: string}|null */
    public function findById(string $id): ?array
    {
        return $this->tasks[$id] ?? null;
    }

    private function applyTaskCreated(TaskCreated $event): void
    {
        $id = (string) $event->taskId;
        $this->tasks[$id] = [
            'id' => $id,
            'title' => $event->title->value,
            'description' => $event->description->value,
            'status' => TaskStatus::Open->value,
        ];
    }

    private function applyTaskCompleted(TaskCompleted $event): void
    {
        $id = (string) $event->taskId;
        if (isset($this->tasks[$id])) {
            $existing = $this->tasks[$id];
            $this->tasks[$id] = [
                'id' => $existing['id'],
                'title' => $existing['title'],
                'description' => $existing['description'],
                'status' => TaskStatus::Completed->value,
            ];
        }
    }

    private function applyTaskTitleChanged(TaskTitleChanged $event): void
    {
        $id = (string) $event->taskId;
        if (isset($this->tasks[$id])) {
            $existing = $this->tasks[$id];
            $this->tasks[$id] = [
                'id' => $existing['id'],
                'title' => $event->newTitle->value,
                'description' => $existing['description'],
                'status' => $existing['status'],
            ];
        }
    }
}
