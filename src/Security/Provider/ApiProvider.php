<?php

declare(strict_types=1);

namespace App\Security\Provider;

use App\Model\User;
use ElevenLabs\Api\Service\ApiService;
use ElevenLabs\Api\Service\Resource\ErrorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

/**
 * Class ApiProvider.
 */
class ApiProvider implements UserProviderInterface
{
    const EXPIRATION_DELTA = 300;

    /**
     * @var ApiService
     */
    private $authClient;

    public function __construct(ApiService $client)
    {
        $this->authClient = $client;
    }

    public function loadUserByUsername($username): ?UserInterface
    {
        return $this->buildUserFromToken($this->getToken($username['username'], $username['password']));
    }

    public function refreshUser(UserInterface $user): UserInterface
    {
        if (null !== $user->getSessionExpiredAt() && $user->getSessionExpiredAt() < (new \DateTime())) {
            return $this->loadUserByUsername(['username' => $user->getUsername(), 'password' => $user->getPassword()]);
        }

        return $user;
    }

    public function supportsClass($class): bool
    {
        return User::class === $class;
    }

    public function getUserToken(string $username, string $password): string
    {
        $request = $this->authClient->call('getAccessToken', ['username' => $username, 'password' => $password]);

        if ($request instanceof ErrorInterface) {
            $e = new UsernameNotFoundException('exception.security.errors.auth');
            $e->setUsername($username);
            throw $e;
        }
        $data = $request->getData();

        return $data['token'];
    }

    /**
     * @deprecated
     */
    public function confirmEmail(string $token)
    {
        $response = $this->authClient->call('getCheckEmailItem', ['token' => $token]);

        if ($response instanceof ErrorInterface) {
            throw new UsernameNotFoundException('exception.security.errors.auth');
        }

        $data = $response->getData();

        return $data['token'];
    }

    private function buildUserFromToken(string $token): ?UserInterface
    {
        try {
            $tokenParts = explode('.', $token);
            $data = json_decode(base64_decode($tokenParts[1], true), true);
            $this->checkApplication($data['applications'] ?? []);

            $user = new User();
            $user
                ->setSlug($data['slug'])
                ->setFirstName($data['givenName'])
                ->setLastName($data['familyName'])
                ->setEmail($data['username'])
                ->setRoles($data['roles'])
                ->setLastLogin(new \DateTime())
                ->setJwtToken($token)
                ->setSessionExpiredAt((new \DateTime())->setTimestamp($data['exp']))
            ;

            return $user;
        } catch (AuthenticationException $e) {
            return null;
        }
    }

    private function getToken(string $username, string $password): string
    {
        $request = $this->authClient->call('getAccessToken', ['username' => $username, 'password' => $password]);
        if ($request instanceof ErrorInterface) {
            $e = new UsernameNotFoundException('exception.security.errors.auth');
            $e->setUsername($username);
            throw $e;
        }

        $data = $request->getData();

        return $data['token'];
    }

    /**
     * @throws AuthenticationException
     */
    private function checkApplication(array $applications)
    {
        if (!\in_array('Mobads', $applications)) {
            throw new AuthenticationException('Bad application');
        }
    }
}
