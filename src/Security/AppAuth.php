<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User;
use Http\Message\Authentication;
use Psr\Http\Message\RequestInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class AppAuth.
 */
class AppAuth implements Authentication
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function authenticate(RequestInterface $request)
    {
        $token = $this->tokenStorage->getToken();
        if (null === $token) {
            return $request;
        }

        /** @var User $user */
        $user = $token->getUser();

        return $request
            ->withHeader('Authorization', sprintf('Bearer %s', $user->getJwtToken()))
        ;
    }
}
