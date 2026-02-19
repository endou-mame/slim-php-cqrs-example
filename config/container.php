<?php

declare(strict_types=1);

use App\Application\Task\Port\TaskEventStore;
use App\Application\Task\Port\TaskReadModel;
use App\Infrastructure\EventStore\InMemoryTaskEventStore;
use App\Infrastructure\ReadModel\InMemoryTaskReadModel;
use DI\ContainerBuilder;

$containerBuilder = new ContainerBuilder();

$containerBuilder->addDefinitions([
    TaskEventStore::class => DI\autowire(InMemoryTaskEventStore::class),
    TaskReadModel::class => DI\autowire(InMemoryTaskReadModel::class),
]);

return $containerBuilder->build();
