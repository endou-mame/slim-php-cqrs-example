<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Task\TaskDescription;

final class TaskDescriptionTest extends TestCase
{
    #[Test]
    public function 空文字はOkを返す(): void
    {
        $result = TaskDescription::tryFrom('');

        $this->assertTrue($result->isOk());
        $this->assertSame('', $result->unwrap()->value);
    }

    #[Test]
    public function 正常な値はOkを返す(): void
    {
        $result = TaskDescription::tryFrom('タスクの説明文');

        $this->assertTrue($result->isOk());
        $this->assertSame('タスクの説明文', $result->unwrap()->value);
    }

    #[Test]
    public function 文字数が1001文字の場合はErrを返す(): void
    {
        $value = str_repeat('あ', 1001);
        $result = TaskDescription::tryFrom($value);

        $this->assertTrue($result->isErr());
    }

    #[Test]
    public function 文字数が1000文字の場合はOkを返す(): void
    {
        $value = str_repeat('あ', 1000);
        $result = TaskDescription::tryFrom($value);

        $this->assertTrue($result->isOk());
    }
}
