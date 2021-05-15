<?php

declare(strict_types=1);

namespace App\Logger\Processor;

use ElevenLabs\Api\Service\Resource\ErrorInterface;
use Monolog\Processor\ProcessorInterface;

/**
 * Class ApiErrorProcessor.
 */
class ApiErrorProcessor implements ProcessorInterface
{
    public function __invoke(array $records)
    {
        /** @var ErrorInterface|null $error */
        $error = $records['context']['error'] ?? null;

        if (!$error instanceof ErrorInterface) {
            return $records;
        }

        $records['context']['error'] = [
            'code' => $error->getCode(),
            'message' => $error->getMessage(),
            'violations' => $error->getViolations(),
        ];

        return $records;
    }
}
