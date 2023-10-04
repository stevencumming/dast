<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// This controller function redirects user on login to dashboard. Must be authenticated to access.
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->getUser()->isVerified()) {
            return $this->redirectToRoute('app_logout');
        }
        else {
            return $this->render('dashboard/index.html.twig', [
                'controller_name' => 'DashboardController',
            ]);    

            echo "Test SC";
        }
    }
}
