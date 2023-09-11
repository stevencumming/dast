<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Psr\Log\LoggerInterface;
use Symfony\Component\Clock\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TrafficMonitor;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, ClockInterface $clock): Response
    {
        $ip = $request->getClientIp();
        $traffic = new TrafficMonitor();
        // checks if user is logged in and redirects to dashboard
        if ($this->getUser() and $this->getUser()->isVerified()) {
            $logger->info('User {userId} is verified and has logged in. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $ip,
            ]);
            $traffic->setUser($this->getUser()->getId())
                ->setTime($clock->now())
                ->setIp($ip)
                ->setUrl('/');
            $entityManager->persist($traffic);
            $entityManager->flush();
            return $this->redirectToRoute('app_dashboard');
        }
        elseif ($this->getUser() and !$this->getUser()->isVerified()) {
            $logger->info('User {userId} has attempted to login, but is not verified. They have been automatically logged out.  IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $ip,
            ]);
            $traffic->setUser($this->getUser()->getId())
                ->setTime($clock->now())
                ->setIp($ip)
                ->setUrl('/');
            $entityManager->persist($traffic);
            $entityManager->flush();
            return $this->redirectToRoute('app_logout');
        }

        $traffic->setUser('Not Logged In')
            ->setTime($clock->now())
            ->setIp($ip)
            ->setUrl('/');
        $entityManager->persist($traffic);
        $entityManager->flush();

        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['controller_name' => 'SecurityController', 'last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(Security $security, LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, ClockInterface $clock): Response
    {
        $ip = $request->getClientIp();
        $traffic = new TrafficMonitor();
        $response = $security->logout(false);
        $logger->info('User has logged out. IP Address: {ip}', [
            'ip' => $ip,
        ]);
        $traffic->setUser('Not Logged In')
            ->setTime($clock->now())
            ->setIp($ip)
            ->setUrl('/logout');
        $entityManager->persist($traffic);
        $entityManager->flush();

        return $this->redirectToRoute('app_login');
        // throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
