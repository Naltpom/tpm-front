<?php

declare(strict_types=1);

namespace App\Logger\Processor;

use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class ResponseProcessor.
 */
class ResponseProcessor implements ProcessorInterface
{
    public function __invoke(array $records)
    {
        /** @var Response|null $response */
        $response = $records['context']['response'] ?? null;

        if (!$response instanceof Response) {
            return $records;
        }

        $records['context']['response'] = [
            'status_code' => $response->getStatusCode(),
            'version' => $response->getProtocolVersion(),
            'headers' => $this->getHeaders($response),
        ];

        return $records;
    }

    private function getHeaders(Response $response): array
    {
        $headers = [];
        foreach (['cache-control', 'date', 'x-version', 'content-type', 'expires'] as $header) {
            $headers[$header] = $response->headers->get($header);
        }

        return $headers;
    }
}
