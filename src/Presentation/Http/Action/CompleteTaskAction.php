<?php

declare(strict_types=1);

namespace App\Presentation\Http\Action;

use App\Application\Task\Command\CompleteTask;
use App\Application\Task\Command\CompleteTaskHandler;
use BackedEnum;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class CompleteTaskAction
{
    public function __construct(
        private CompleteTaskHandler $handler,
    ) {}

    /** @param array<string, string> $args */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = (string) ($args['id'] ?? '');

        $result = $this->handler->handle(new CompleteTask($id));

        if ($result->isOk()) {
            $response->getBody()->write(json_encode(['id' => $result->unwrap()], JSON_THROW_ON_ERROR));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(200);
        }

        $error = $result->unwrapErr();
        $errorMessage = match (true) {
            $error instanceof BackedEnum => (string) $error->value,
            is_string($error) => $error,
            default => 'Unknown error',
        };

        $isNotFound = is_string($error) && str_contains($error, 'not found');
        $statusCode = $isNotFound ? 404 : 400;

        $response->getBody()->write(json_encode(['error' => $errorMessage], JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
}
