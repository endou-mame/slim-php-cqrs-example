<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

use App\Application\Task\Port\TaskEventStore;
use App\Application\Task\Port\TaskReadModel;
use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpValueObject\Error\ValueObjectError;
use App\Domain\Task\TaskError;
use function EndouMame\PhpMonad\Result\err;

readonly class CompleteTaskHandler
{
    public function __construct(
        private TaskEventStore $eventStore,
        private TaskReadModel $readModel,
    ) {}

    /** @return Result<string, ValueObjectError|TaskError|string> */
    public function handle(CompleteTask $command): Result
    {
        return TaskId::tryFrom($command->id) // @phpstan-ignore return.type
            ->andThen(function (TaskId $id): Result { // @phpstan-ignore argument.type
                $option = $this->eventStore->findById($id);
                if ($option->isNone()) {
                    return err("Task not found: {$id}");
                }
                return $option->unwrap()->complete();
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
