<?php

declare(strict_types=1);

namespace App\Presentation\Http\Action;

use App\Application\Task\Command\CreateTask;
use App\Application\Task\Command\CreateTaskHandler;
use BackedEnum;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class CreateTaskAction
{
    public function __construct(
        private CreateTaskHandler $handler,
    ) {}

    /** @param array<string, string> $args */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        /** @var array<string, mixed> $body */
        $body = (array) $request->getParsedBody();
        $rawTitle = $body['title'] ?? '';
        $title = is_string($rawTitle) ? $rawTitle : '';
        $rawDescription = $body['description'] ?? '';
        $description = is_string($rawDescription) ? $rawDescription : '';

        $result = $this->handler->handle(new CreateTask($title, $description));

        if ($result->isOk()) {
            $id = $result->unwrap();
            $response->getBody()->write(json_encode(['id' => $id], JSON_THROW_ON_ERROR));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(201);
        }

        $error = $result->unwrapErr();
        /** @var mixed $error */
        $errorMessage = match (true) {
            $error instanceof BackedEnum => (string) $error->value,
            is_array($error) => implode(', ', array_map(static fn (mixed $e): string => ($e instanceof \Stringable ? $e->__toString() : 'error'), $error)),
            is_string($error) => $error,
            default => 'Unknown error',
        };
        $response->getBody()->write(json_encode(['error' => $errorMessage], JSON_THROW_ON_ERROR));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(400);
    }
}
