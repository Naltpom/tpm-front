<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\LoginForm;
use App\Form\RegisterForm;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

/**
 * Class DefaultController.
 */
class SecurityController
{
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Environment
     */
    private $twig;

    public function __construct(FormFactoryInterface $formFactory, Environment $twig)
    {
        $this->formFactory = $formFactory;
        $this->twig = $twig;
    }

    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->formFactory->create(LoginForm::class);

        return new Response($this->twig->render('components/pages/security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ]));
    }

    
    /**
     * @Route("/register", name="register")
     */
    public function register(AuthenticationUtils $authenticationUtils): Response
    {
        $form = $this->formFactory->create(RegisterForm::class);

        return new Response($this->twig->render('components/pages/security/register.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
            'form' => $form->createView(),
        ]));
    }
}
