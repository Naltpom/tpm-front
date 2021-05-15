<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;
use App\Client\ApiClient;

class DefaultController
{
    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var ApiClient
     */
    private $client;

    public function __construct(Environment $twig, ApiClient $client)
    {
        $this->twig = $twig;
        $this->client = $client;
    }

    /**
     * @Route("/dashboard", name="dashboard")
     */
    public function index(): Response
    {
        $user = $this->client->getItem('getUserItem', ['slug' => '4a9e6526-b991-4c0e-bf5d-fbb570de915f']);

        return new Response($this->twig->render('components/pages/dashboard/index.html.twig', ['user' => $user]));
    }
}
