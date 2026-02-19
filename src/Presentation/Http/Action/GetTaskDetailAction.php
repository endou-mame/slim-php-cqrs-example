<?php

declare(strict_types=1);

namespace App\Presentation\Http\Action;

use App\Application\Task\Query\GetTaskDetail;
use App\Application\Task\Query\GetTaskDetailHandler;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class GetTaskDetailAction
{
    public function __construct(
        private GetTaskDetailHandler $handler,
    ) {}

    /** @param array<string, string> $args */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = (string) ($args['id'] ?? '');

        $task = $this->handler->handle(new GetTaskDetail($id));

        if ($task === null) {
            $response->getBody()->write(json_encode(['error' => 'Task not found'], JSON_THROW_ON_ERROR));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(404);
        }

        $response->getBody()->write(json_encode($task, JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(200);
    }
}
