<?php

declare(strict_types=1);

namespace App\Application\Task\Query;

use App\Application\Task\Port\TaskReadModel;

readonly class GetTaskDetailHandler
{
    public function __construct(
        private TaskReadModel $readModel,
    ) {}

    /** @return array{id: string, title: string, description: string, status: string}|null */
    public function handle(GetTaskDetail $query): ?array
    {
        return $this->readModel->findById($query->id);
    }
}
