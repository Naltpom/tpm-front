<?php

declare(strict_types=1);

namespace App\Security\Authenticator;

use App\Model\User;
use ElevenLabs\Api\Service\ApiService;
use ElevenLabs\Api\Service\Resource\ErrorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Role\RoleHierarchyInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;

/**
 * Class LoginFormAuthenticator.
 */
class LoginFormAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * @var ApiService
     */
    private $client;

    /**
     * @var RoleHierarchyInterface
     */
    private $roleHierarchy;

    public function __construct(ApiService $client, RoleHierarchyInterface $roleHierarchy)
    {
        $this->client = $client;
        $this->roleHierarchy = $roleHierarchy;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
        return new Response('Authentication Required', Response::HTTP_FORBIDDEN);
    }

    public function supports(Request $request): bool
    {
        $requestParameters = $request->request;

        return null !== $requestParameters->get('username') && null !== $requestParameters->get('password');
    }

    public function getCredentials(Request $request): array
    {
        $requestParameters = $request->request;

        return [
            'username' => $requestParameters->get('username'),
            'password' => $requestParameters->get('password'),
        ];
    }

    public function getUser($credentials, UserProviderInterface $userProvider): ?UserInterface
    {
        return $userProvider->loadUserByUsername($credentials);
    }

    public function checkCredentials($credentials, UserInterface $user): bool
    {
        return true;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return null;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey): RedirectResponse
    {
        /** @var User $user */
        $user = $token->getUser();
        if (in_array('ROLE_ADMIN', $this->roleHierarchy->getReachableRoleNames($user->getRoles()))) {
            return new RedirectResponse('/');
        }

        $response = $this->client->call('getDriverItem', ['slug' => $user->getSlug()]);
        if ($response instanceof ErrorInterface && 404 === $response->getCode()) {
            return new RedirectResponse('/profile');
        }

        return new RedirectResponse('/');
    }

    public function supportsRememberMe(): bool
    {
        return false;
    }
}
