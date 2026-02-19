<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task\ValueObject;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Task\ValueObject\TaskTitle;

final class TaskTitleTest extends TestCase
{
    #[Test]
    public function 空文字はErrを返す(): void
    {
        $result = TaskTitle::tryFrom('');

        $this->assertTrue($result->isErr());
    }

    #[Test]
    public function 正常な値はOkを返す(): void
    {
        $result = TaskTitle::tryFrom('タスクのタイトル');

        $this->assertTrue($result->isOk());
        $this->assertSame('タスクのタイトル', $result->unwrap()->value);
    }

    #[Test]
    public function 文字数が101文字の場合はErrを返す(): void
    {
        $value = str_repeat('あ', 101);
        $result = TaskTitle::tryFrom($value);

        $this->assertTrue($result->isErr());
    }

    #[Test]
    public function 文字数が100文字の場合はOkを返す(): void
    {
        $value = str_repeat('あ', 100);
        $result = TaskTitle::tryFrom($value);

        $this->assertTrue($result->isOk());
    }

    #[Test]
    public function 文字数が1文字の場合はOkを返す(): void
    {
        $result = TaskTitle::tryFrom('a');

        $this->assertTrue($result->isOk());
    }
}
