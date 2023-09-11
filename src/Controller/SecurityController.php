<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, LoggerInterface $logger): Response
    {
        // checks if user is logged in and redirects to dashboard
        if ($this->getUser() and $this->getUser()->isVerified()) {
            $logger->info('User {userId} is verified and has logged in.', [
                'userId' => $this->getUser()->getId(),
            ]);
            return $this->redirectToRoute('app_dashboard');
        }
        elseif ($this->getUser() and !$this->getUser()->isVerified()) {
            $logger->info('User {userId} has attempted to login, but is not verified. They have been automatically logged out.', [
                'userId' => $this->getUser()->getId(),
            ]);
            return $this->redirectToRoute('app_logout');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['controller_name' => 'SecurityController', 'last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Security $security, LoggerInterface $logger): Response
    {
        $response = $security->logout(false);
        $logger->info('User has logged out.');
        return $this->redirectToRoute('app_login');
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
