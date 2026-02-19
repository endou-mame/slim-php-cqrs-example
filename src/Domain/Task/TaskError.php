<?php

declare(strict_types=1);

namespace App\Domain\Task;

enum TaskError: string
{
    case AlreadyCompleted = 'Task is already completed';
    case InvalidTitle = 'Invalid task title';
}
