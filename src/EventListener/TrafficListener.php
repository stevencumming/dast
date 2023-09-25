<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use App\Service\SERVICE_TrafficMonitor;

#[AsEventListener]
final class TrafficListener
{
    public function __construct(private SERVICE_TrafficMonitor $tms) {}

    public function __invoke(RequestEvent $event): void
    {
        if ($event->isMainRequest()) {
            $this->tms->persistTraffic();
        }
    }
}