<?php

declare(strict_types=1);

namespace App\Application\Task\Port;

use App\Domain\Task\Event\TaskCompleted;
use App\Domain\Task\Event\TaskCreated;
use App\Domain\Task\Event\TaskTitleChanged;

interface TaskReadModel
{
    public function project(TaskCreated|TaskCompleted|TaskTitleChanged $event): void;

    /** @return list<array{id: string, title: string, status: string}> */
    public function findAll(): array;

    /** @return array{id: string, title: string, description: string, status: string}|null */
    public function findById(string $id): ?array;
}
