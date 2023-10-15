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
                $logger->info('User {userId} has started a new target scan. IP Address: {ip}', [
                    'userId' => $this->getUser()->getId(),
                    'ip' => $request->getClientIp(),
                ]);
                // send scan off here
                $time = $clock->now();
                $scan = new Scan();
                $target = $request->request->get('target');
                $scan->setUser($this->getUser());
                $scan->setTimeRequested($time);
                $scan->setTarget($target);
                $scan->setStatus("waiting");
                $scan->setTimeCommenced($time);
                $scan->setTimeCompleted($time);
                $entityManager->persist($scan);
                $entityManager->flush();
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

    #[Route('/dashboard/past-scans', name: 'app_past_scans')]
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
                'scans' => $scans, //deal with sorting in twig, need to create controller route for report view
                'userId' => $this-getUser()->getId(),
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
                'scans' => $scans, //deal with sorting in twig
                'userId' => $this-getUser()->getId(),
            ]);    
        }
    }

    #[Route('/dashboard/past-scans/scan-report{scanId}', name: 'app_scan_report')]
    public function scanReport(LoggerInterface $logger, Request $request, EntityManagerInterface $entityManager, TrafficMonitorService $tms, string $scanId): Response
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
            $logger->info('User {userId} is viewing a previous scan report. IP Address: {ip}', [
                'userId' => $this->getUser()->getId(),
                'ip' => $request->getClientIp(),
            ]);
            $scan = $entityManager->getRepository(Scan::class)->find($scanId);
            //$html?? get from scan?
            return $this->render('dashboard/scanReport.html.twig', [
                'controller_name' => 'DashboardController',
                'scan' => $scan,
                'userId' => $this-getUser()->getId(),
            ]);    
        }
    }
}
