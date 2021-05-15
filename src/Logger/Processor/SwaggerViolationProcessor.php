<?php

declare(strict_types=1);

namespace App\Logger\Processor;

use ElevenLabs\Api\Validator\ConstraintViolation;
use Monolog\Processor\ProcessorInterface;

/**
 * class SwaggerViolationProcessor.
 */
class SwaggerViolationProcessor implements ProcessorInterface
{
    public function __invoke(array $records)
    {
        /** @var ConstraintViolation|null $violation */
        $violation = $records['context']['violation'] ?? null;

        if (!$violation instanceof ConstraintViolation) {
            return $records;
        }

        $records['context']['violation'] = [
            'property' => $violation->getProperty(),
            'location' => $violation->getLocation(),
            'constraint' => $violation->getConstraint(),
            'message' => $violation->getMessage(),
        ];

        return $records;
    }
}
