<?php

declare(strict_types=1);

namespace App\Domain\Task\ValueObject;

use Override;
use EndouMame\PhpValueObject\String\StringValue;

readonly class TaskTitle extends StringValue
{
    #[Override]
    protected static function minLength(): int
    {
        return 1;
    }

    #[Override]
    protected static function maxLength(): int
    {
        return 100;
    }
}
