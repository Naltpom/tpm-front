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
     * @Route("/default", name="default")
     */
    public function index(): Response
    {
        $metrics = $this->client->collection('getTeamCollection');
        dd($metrics);
        return new Response($this->twig->render('components/pages/dashboard.html.twig', ['metrics' => $metrics]));
    }
}
