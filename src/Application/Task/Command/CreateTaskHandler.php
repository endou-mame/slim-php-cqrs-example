<?php

declare(strict_types=1);

namespace App\Application\Task\Command;

use App\Application\Task\Port\TaskEventStore;
use App\Application\Task\Port\TaskReadModel;
use App\Domain\Task\Task;
use App\Domain\Task\ValueObject\TaskDescription;
use App\Domain\Task\ValueObject\TaskId;
use App\Domain\Task\ValueObject\TaskTitle;
use EndouMame\PhpMonad\Result;
use EndouMame\PhpValueObject\Error\ValueObjectError;
use App\Domain\Task\Error\TaskError;
use function EndouMame\PhpMonad\Result\combine;

readonly class CreateTaskHandler
{
    public function __construct(
        private TaskEventStore $eventStore,
        private TaskReadModel $readModel,
    ) {}

    /** @return Result<string, non-empty-list<ValueObjectError>|TaskError> */
    public function handle(CreateTask $command): Result
    {
        $id = TaskId::generate();

        $titleResult = TaskTitle::tryFrom($command->title);
        $descResult = TaskDescription::tryFrom($command->description);

        return combine($titleResult, $descResult)
            ->andThen(fn () => Task::create(
                $id,
                $titleResult->unwrap(),
                $descResult->unwrap(),
            ))
            ->map(function (Task $task): string {
                $this->eventStore->save($task);
                foreach ($task->uncommittedEvents() as $event) {
                    $this->readModel->project($event);
                }
                return (string) $task->id;
            });
    }
}
