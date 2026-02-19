<?php

declare(strict_types=1);

namespace App\Presentation\Http\Action;

use App\Application\Task\Query\ListTasks;
use App\Application\Task\Query\ListTasksHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class ListTasksAction
{
    public function __construct(
        private ListTasksHandler $handler,
    ) {}

    /** @param array<string, string> $args */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $tasks = $this->handler->handle(new ListTasks());

        $response->getBody()->write(json_encode($tasks, JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
