<?php

namespace App\Adapter\Line;

use App\Entity\ExternalUser;
use App\Entity\Player;
use App\Entity\User;
use App\Repository\ExternalUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class LineAuthenticator extends SocialAuthenticator
{
    /**
     * @var ClientRegistry
     */
    private $clientRegistry;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var ExternalUserRepository
     */
    private $externalUserRepository;

    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(
        ClientRegistry $clientRegistry,
        EntityManagerInterface $entityManager,
        RouterInterface $router,
        ExternalUserRepository $externalUserRepository
    ) {
        $this->clientRegistry = $clientRegistry;
        $this->router = $router;
        $this->externalUserRepository = $externalUserRepository;
        $this->entityManager = $entityManager;
    }

    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new RedirectResponse(
            '/line/auth',
            Response::HTTP_TEMPORARY_REDIRECT
        );
    }

    public function supports(Request $request)
    {
        return $request->attributes->get('_route') === 'line_callback';
    }

    public function getCredentials(Request $request)
    {
        return $this->fetchAccessToken($this->getLineProvider());
    }

    private function getLineProvider(): OAuth2ClientInterface
    {
        return $this->clientRegistry->getClient('line');
    }

    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $lineUser = $this->getLineProvider()->fetchUserFromToken($credentials);
        $userData = $lineUser->toArray();

        $externalUser = $this->externalUserRepository->findOneBy([
            'externalId' => $userData['userId'],
            'source' => ExternalUser::SOURCE_LINE,
        ]);

        if ($externalUser instanceof ExternalUser) {
            $user = $externalUser->getUser();
            $user->setActivePlayer($user->getPlayers()[0]);
            $this->entityManager->flush();

            return $user;
        }

        $user = new User();
        $user->setName($userData['displayName']);
        $this->entityManager->persist($user);

        $externalUser = new ExternalUser();
        $externalUser->setExternalId($userData['userId']);
        $externalUser->setSource(ExternalUser::SOURCE_LINE);
        $externalUser->setUser($user);
        $this->entityManager->persist($externalUser);

        $player = new Player();
        $player->setName($userData['displayName']);
        $player->setUser($user);
        $this->entityManager->persist($player);

        $user->setActivePlayer($player);

        $this->entityManager->flush();

        return $user;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        $message = strtr($exception->getMessageKey(), $exception->getMessageData());

        return new Response($message, Response::HTTP_FORBIDDEN);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $providerKey)
    {
        return new RedirectResponse($this->router->generate('home'));
    }

}