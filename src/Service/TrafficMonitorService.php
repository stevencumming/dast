<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Clock\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\TrafficMonitor;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Security;

// DAST test feature
class TrafficMonitorService
{
    private $user;
    private $userId;

    public function __construct(
        private RequestStack $requestStack,
        private EntityManagerInterface $entityManager,
        private ClockInterface $clock,
        private Security $security,
    ) {
        $this->user = $this->security->getUser();
        if(!empty($user)) {
            $this->userId = $user->getId();
        }
        else {
            $this->userId = 'Not Logged In';
        }
    }

    public function persistTraffic(): void
    {
        $traffic = new TrafficMonitor();
        $request = $this->requestStack->getCurrentRequest();

        $em = $this->entityManager;
        $time = $this->clock->now();
        $ip = $request->getClientIp();
        $url = $request->get('_route');

        $traffic->setUser($this->userId)
                ->setTime($time)
                ->setIp($ip)
                ->setUrl($url);
        $em->persist($traffic);
        $em->flush();
    }
}