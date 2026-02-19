<?php

declare(strict_types=1);

use App\Presentation\Http\Action\ChangeTaskTitleAction;
use App\Presentation\Http\Action\CompleteTaskAction;
use App\Presentation\Http\Action\CreateTaskAction;
use App\Presentation\Http\Action\GetTaskDetailAction;
use App\Presentation\Http\Action\ListTasksAction;
use Slim\App;

return function (App $app): void {
    $app->post('/tasks', CreateTaskAction::class);
    $app->patch('/tasks/{id}/complete', CompleteTaskAction::class);
    $app->patch('/tasks/{id}/title', ChangeTaskTitleAction::class);
    $app->get('/tasks', ListTasksAction::class);
    $app->get('/tasks/{id}', GetTaskDetailAction::class);
};
