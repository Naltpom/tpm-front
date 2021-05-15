<?php

declare(strict_types=1);

namespace App\Logger\Processor;

use App\Model\User;
use Monolog\Processor\ProcessorInterface;

/**
 * Class UserProcessor.
 */
class UserProcessor implements ProcessorInterface
{
    /**
     * @return array
     */
    public function __invoke(array $record)
    {
        /** @var User $user */
        $user = $record['context']['user'] ?? null;

        if (!$user instanceof User) {
            return $record;
        }

        $lastLogin = $user->getLastLogin();
        $record['context']['user'] = [
            'username' => $user->getUsername(),
            'roles' => $user->getRoles(),
            'lastLogin' => null === $lastLogin ? null : $lastLogin->format('Y-m-d H:i:s'),
        ];

        return $record;
    }
}
