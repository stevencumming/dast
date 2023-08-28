<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FunctionalTestController extends AbstractController
{
    #[Route('/functional/test', name: 'app_functional_test')]
    public function index(): Response
    {
        return $this->render('functional_test/index.html.twig', [
            'controller_name' => 'FunctionalTestController',
        ]);
    }
}
