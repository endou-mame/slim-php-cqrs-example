<?php

declare(strict_types=1);

namespace Tests\Unit\Domain\Task;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskTitle;
use App\Domain\Task\TaskDescription;
use App\Domain\Task\TaskStatus;
use App\Domain\Task\TaskError;
use App\Domain\Task\TaskCreated;
use App\Domain\Task\TaskCompleted;
use App\Domain\Task\TaskTitleChanged;

final class TaskTest extends TestCase
{
    #[Test]
    public function createで正常にTaskを作成しTaskCreatedイベントが生成される(): void
    {
        $id = TaskId::generate();
        $title = TaskTitle::from('テストタスク');
        $description = TaskDescription::from('タスクの説明');

        $result = Task::create($id, $title, $description);

        $this->assertTrue($result->isOk());

        $task = $result->unwrap();
        $this->assertTrue($task->id->equals($id));
        $this->assertTrue($task->title->equals($title));
        $this->assertTrue($task->description->equals($description));
        $this->assertSame(TaskStatus::Open, $task->status);

        $events = $task->uncommittedEvents();
        $this->assertCount(1, $events);
        $this->assertInstanceOf(TaskCreated::class, $events[0]);
        $this->assertTrue($events[0]->taskId->equals($id));
        $this->assertSame(TaskStatus::Open, $events[0]->status);
    }

    #[Test]
    public function completeでOpenなTaskを完了しTaskCompletedイベントが生成される(): void
    {
        $task = $this->createOpenTask();

        $result = $task->complete();

        $this->assertTrue($result->isOk());

        $completedTask = $result->unwrap();
        $this->assertSame(TaskStatus::Completed, $completedTask->status);

        $events = $completedTask->uncommittedEvents();
        $this->assertCount(2, $events);
        $this->assertInstanceOf(TaskCompleted::class, $events[1]);
        $this->assertTrue($events[1]->taskId->equals($task->id));
    }

    #[Test]
    public function completeで既にCompletedなTaskはErrを返す(): void
    {
        $task = $this->createOpenTask();
        $completedTask = $task->complete()->unwrap();

        $result = $completedTask->complete();

        $this->assertTrue($result->isErr());
        $this->assertSame(TaskError::AlreadyCompleted, $result->unwrapErr());
    }

    #[Test]
    public function changeTitleでタイトルを変更しTaskTitleChangedイベントが生成される(): void
    {
        $task = $this->createOpenTask();
        $newTitle = TaskTitle::from('新しいタイトル');

        $result = $task->changeTitle($newTitle);

        $this->assertTrue($result->isOk());

        $updatedTask = $result->unwrap();
        $this->assertTrue($updatedTask->title->equals($newTitle));

        $events = $updatedTask->uncommittedEvents();
        $this->assertCount(2, $events);
        $this->assertInstanceOf(TaskTitleChanged::class, $events[1]);
        $this->assertTrue($events[1]->newTitle->equals($newTitle));
    }

    #[Test]
    public function reconstituteでイベント列からTaskを再構成できる(): void
    {
        $id = TaskId::generate();
        $title = TaskTitle::from('元のタイトル');
        $description = TaskDescription::from('説明文');
        $newTitle = TaskTitle::from('変更後のタイトル');

        $events = [
            new TaskCreated($id, $title, $description, TaskStatus::Open),
            new TaskTitleChanged($id, $newTitle),
            new TaskCompleted($id),
        ];

        $task = Task::reconstitute($id, $events);

        $this->assertTrue($task->id->equals($id));
        $this->assertTrue($task->title->equals($newTitle));
        $this->assertTrue($task->description->equals($description));
        $this->assertSame(TaskStatus::Completed, $task->status);
        $this->assertCount(0, $task->uncommittedEvents());
    }

    private function createOpenTask(): Task
    {
        $id = TaskId::generate();
        $title = TaskTitle::from('テストタスク');
        $description = TaskDescription::from('タスクの説明');

        return Task::create($id, $title, $description)->unwrap();
    }
}
