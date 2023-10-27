<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use App\Service\TrafficMonitorService;
use App\Repository\ScanRepository;
use App\Entity\Scan;
use App\Entity\AllowedDomains;
use Symfony\Component\Clock\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;

// This controller function redirects user on login to dashboard. Must be authenticated to access.
class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'app_dashboard')]
    public function index(LoggerInterface $logger, Request $request, TrafficMonitorService $tms): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->getUser()->isVerified()) {
            $logger->info('User {userId} has attempted to access their dashboard, but is not verified. They have been automatically logged out.  IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->redirectToRoute('app_logout');
        }
        else {
            $logger->info('User {userId} has accessed their dashboard. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->render('dashboard/index.html.twig', [
                'controller_name' => 'DashboardController',
                'scans' => $this->getUser()->getScans(),
            ]);
        }
    }

    #[Route('/dashboard/new-scan', name: 'app_new_scan')]
    public function newScan(LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, TrafficMonitorService $tms, ClockInterface $clock): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->getUser()->isVerified()) {
            $logger->info('User {userId} has attempted to access their dashboard, but is not verified. They have been automatically logged out.  IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->redirectToRoute('app_logout');
        }
        else {
            if ($request->getMethod() == 'POST') {
                $target = $request->request->get('target');
                $domainsArr = $entityManager->getRepository(AllowedDomains::class)->findAll();
                for($i = 0; $i < sizeof($domainsArr); $i++) {
                    if($domainsArr[$i]->getDomain() == $target) {
                        $logger->info('User {userId} has started a new target scan. IP Address: {ip}', [
                            'userId' => $this->getUser()->getId(),
                            'ip' => $request->getClientIp(),
                        ]);
                        // send scan off here
                        $time = $clock->now();
                        $scan = new Scan();
                        $scan->setUser($this->getUser());
                        $scan->setTimeRequested($time);
                        $scan->setTarget($target);
                        $scan->setStatus("waiting");
                        $entityManager->persist($scan);
                        $entityManager->flush();
                        $request->getSession()->getFlashBag()->add('success', 'Scan has been queued!');
                        return $this->redirectToRoute('app_dashboard');        
                    }
                }
                $logger->info('User {userId} has attempted to scan an un-allowed domain. IP Address: {ip}', [
                    'userId' => $this->getUser()->getId(),
                    'ip' => $request->getClientIp(),
                ]);
                $request->getSession()->getFlashBag()->add('success', 'You are not authorised to scan that target or you have entered an invalid URL! Please try again.');
                return $this->redirectToRoute('app_dashboard');
            }
            $logger->info('User {userId} is on the newScan page. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->render('dashboard/newScan.html.twig', [
                'controller_name' => 'DashboardController',
            ]);
        }
    }

    #[Route('/dashboard/completed-scans', name: 'app_past_scans')]
    public function pastScans(LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, TrafficMonitorService $tms): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->getUser()->isVerified()) {
            $logger->info('User {userId} has attempted to access their dashboard, but is not verified. They have been automatically logged out.  IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->redirectToRoute('app_logout');
        }
        else {
            $logger->info('User {userId} is viewing their previous scans. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            $scans = $entityManager->getRepository(Scan::class);
            return $this->render('dashboard/pastScans.html.twig', [
                'controller_name' => 'DashboardController',
                'scans' => $this->getUser()->getScans(),
            ]);    
        }
    }

    #[Route('/dashboard/queued-scans', name: 'app_queued_scans')]
    public function queuedScans(LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, TrafficMonitorService $tms): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->getUser()->isVerified()) {
            $logger->info('User {userId} has attempted to access their dashboard, but is not verified. They have been automatically logged out.  IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->redirectToRoute('app_logout');
        }
        else {
            $logger->info('User {userId} is viewing their queued scans. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            $scans = $entityManager->getRepository(Scan::class);
            return $this->render('dashboard/queuedScans.html.twig', [
                'controller_name' => 'DashboardController',
                'scans' => $this->getUser()->getScans(),
            ]);    
        }
    }

    #[Route('/dashboard/in-progress-scans', name: 'app_in_progress_scans')]
    public function inProgressScans(LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, TrafficMonitorService $tms): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->getUser()->isVerified()) {
            $logger->info('User {userId} has attempted to access their dashboard, but is not verified. They have been automatically logged out.  IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->redirectToRoute('app_logout');
        }
        else {
            $logger->info('User {userId} is viewing their in progress scans. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            $scans = $entityManager->getRepository(Scan::class);
            return $this->render('dashboard/inProgressScans.html.twig', [
                'controller_name' => 'DashboardController',
                'scans' => $this->getUser()->getScans(),
            ]);    
        }
    }

    #[Route('/dashboard/past-scans/scan-report/{scanId}', name: 'app_scan_report')]
    public function scanReport(LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, TrafficMonitorService $tms, string $scanId): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $scan = $entityManager->getRepository(Scan::class)->find($scanId);
        if (!$this->getUser()->isVerified()) {
            $logger->info('User {userId} has attempted to access their dashboard, but is not verified. They have been automatically logged out.  IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->redirectToRoute('app_logout');
        }
        elseif(($scan->getUser()->getId() == $this->getUser()->getId())) {
            $logger->info('User {userId} is viewing a previous scan report. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->render('dashboard/scanReport.html.twig', [
                'controller_name' => 'DashboardController',
                'scan' => $scan,
                'userId' => $this->getUser()->getId(),
            ]);    
        }
        else {
            $logger->info('User {userId} is attempting to access a scan report that they do not have access to. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            $request->getSession()->getFlashBag()->add('success', 'You are not authorised to perform this action!');
            return $this->redirectToRoute('app_dashboard');
        }
    }

    #[Route('/dashboard/allowed-domains', name: 'app_allowed_domains')]
    public function allowedDomains(LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, TrafficMonitorService $tms, ClockInterface $clock): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        if (!$this->getUser()->isVerified()) {
            $logger->info('User {userId} has attempted to access their dashboard, but is not verified. They have been automatically logged out.  IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->redirectToRoute('app_logout');
        }
        else {
            $domainsArr = $entityManager->getRepository(AllowedDomains::class)->findAll();
            $logger->info('User {userId} is on the allowed domains page. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            return $this->render('dashboard/allowedDomains.html.twig', [
                'controller_name' => 'DashboardController',
                'domains' => $domainsArr
            ]);
        }
    }
}
