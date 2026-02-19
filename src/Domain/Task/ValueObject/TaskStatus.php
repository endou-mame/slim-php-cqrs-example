<?php

declare(strict_types=1);

namespace App\Domain\Task\ValueObject;

enum TaskStatus: string
{
    case Open = 'open';
    case Completed = 'completed';
}
