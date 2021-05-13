<?php

declare(strict_types=1);

namespace App\Security\Authenticator;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class ConfirmEmailAuthenticator.
 *
 * @deprecated
 */
class ConfirmEmailAuthenticator extends AbstractGuardAuthenticator
{
    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new Response('Authentication Required', Response::HTTP_FORBIDDEN);
    }

    public function supports(Request $request): bool
    {
        return 'confirm_email' === $request->get('_route') && null !== $request->get('token');
    }

    public function getCredentials(Request $request): array
    {
        return [
            'token' => $request->get('token'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $userProvider->confirmEmail($credentials['token']);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new RedirectResponse('/register');
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        return new RedirectResponse('/profile');
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
