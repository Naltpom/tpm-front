<?php

declare(strict_types=1);

namespace App\Logger\Processor;

use Monolog\Processor\ProcessorInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RequestProcessor.
 */
class RequestProcessor implements ProcessorInterface
{
    public function __invoke(array $records)
    {
        /** @var Request|null $request */
        $request = $records['context']['request'] ?? null;

        if (!$request instanceof Request) {
            return $records;
        }

        $records['context']['request'] = [
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'headers' => [
                'user-agent' => $request->headers->get('user-agent'),
                'accept' => $request->headers->get('accept'),
                'accept-language' => $request->headers->get('accept-language'),
                'cache-control' => $request->headers->get('cache-control'),
                'authorization' => $request->headers->get('authorization'),
            ],
            'body' => (string) $request->getContent(),
        ];

        return $records;
    }
}
