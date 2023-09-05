<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // checks if user is logged in and redirects to dashboard
        if ($this->getUser() and $this->getUser()->isVerified()) {
            return $this->redirectToRoute('app_dashboard');
        }
        elseif ($this->getUser() and !$this->getUser()->isVerified()) {
            return $this->redirectToRoute('app_logout');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['controller_name' => 'SecurityController', 'last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Security $security): Response
    {
        $response = $security->logout(false);

        return $this->redirectToRoute('app_login');
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
