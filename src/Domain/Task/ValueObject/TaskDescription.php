<?php

declare(strict_types=1);

namespace App\Domain\Task\ValueObject;

use Override;
use EndouMame\PhpValueObject\String\StringValue;

readonly class TaskDescription extends StringValue
{
    #[Override]
    protected static function minLength(): int
    {
        return 0;
    }

    #[Override]
    protected static function maxLength(): int
    {
        return 1000;
    }
}
