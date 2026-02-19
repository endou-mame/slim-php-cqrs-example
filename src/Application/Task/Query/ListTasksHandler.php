<?php

declare(strict_types=1);

namespace App\Application\Task\Query;

use App\Application\Task\Port\TaskReadModel;

readonly class ListTasksHandler
{
    public function __construct(
        private TaskReadModel $readModel,
    ) {}

    /** @return list<array{id: string, title: string, status: string}> */
    public function handle(ListTasks $query): array
    {
        return $this->readModel->findAll();
    }
}
