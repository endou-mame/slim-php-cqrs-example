<?php

declare(strict_types=1);

namespace App\Presentation\Http\Action;

use App\Application\Task\Command\ChangeTaskTitle;
use App\Application\Task\Command\ChangeTaskTitleHandler;
use BackedEnum;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

readonly class ChangeTaskTitleAction
{
    public function __construct(
        private ChangeTaskTitleHandler $handler,
    ) {}

    /** @param array<string, string> $args */
    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $id = (string) ($args['id'] ?? '');
        /** @var array<string, mixed> $body */
        $body = (array) $request->getParsedBody();
        $rawTitle = $body['title'] ?? '';
        $title = is_string($rawTitle) ? $rawTitle : '';

        $result = $this->handler->handle(new ChangeTaskTitle($id, $title));

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
