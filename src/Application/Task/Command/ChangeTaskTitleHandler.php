<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

use App\Application\Task\Port\TaskEventStore;
use App\Application\Task\Port\TaskReadModel;
use App\Domain\Task\Task;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\Task\ValueObject\TaskTitle;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpValueObject\Error\ValueObjectError;
use App\Domain\Task\Error\TaskError;
use function EndouMame\PhpMonad\Result\err;

readonly class ChangeTaskTitleHandler
{
    public function __construct(
        private TaskEventStore $eventStore,
        private TaskReadModel $readModel,
    ) {}

    /** @return Result<string, ValueObjectError|TaskError|string> */
    public function handle(ChangeTaskTitle $command): Result
    {
        $titleResult = TaskTitle::tryFrom($command->newTitle);

        return TaskId::tryFrom($command->id) // @phpstan-ignore return.type
            ->andThen(function (TaskId $id) use ($titleResult): Result { // @phpstan-ignore argument.type
                $option = $this->eventStore->findById($id);
                if ($option->isNone()) {
                    return err("Task not found: {$id}");
                }
                return $titleResult->andThen(
                    fn (TaskTitle $title) => $option->unwrap()->changeTitle($title),
                );
            })
            ->map(function (Task $task): string {
                $this->eventStore->save($task);
                foreach ($task->uncommittedEvents() as $event) {
                    $this->readModel->project($event);
                }
                return (string) $task->id;
            });
    }
}
