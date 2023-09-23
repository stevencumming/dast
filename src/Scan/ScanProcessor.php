<?php

namespace App\Scan;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Clock\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Process;
use App\Entity\Scan;
use App\Service\DdosService;

// currently rigged up for one process as of moment
class ScanProcessor
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClockInterface $clock,
        private Security $security,

        private Scan $scan,

        private Process $ddosProcess,
    ) {
    }

    public function loadScan(string $target) {
        // load the entity manager for initial persist
        $em = $this->entityManager;

        // get the user to assign scan to
        $user = $this->security->getUser();

        // give the scan some properties
        $this->scan->setUser($user)->setTimeRequested($this->clock->now())->setTarget($target)->setStatus('queued');

        // persist the base scan to the database
        $em->persist($this->scan);
        $em->flush(); 
    }

    public function processCompute() {
        // mark the scan commenced
        $this->scan->scanCommenced();

        // ddos process, repeat this for all scan processes
        $this->ddosProcess->setStatus('waiting');
        $ddosService = new DdosService($ddosProcess);
        $ddosResults = $ddosService->cdnCheck();
        if (!$ddosResults == null) {
            $this->ddosProcess->setResults($ddosService->cdnCheck());
            $this->ddosProcess->setStatus('completed');
            $em->persist($ddosProcess);
            $em->flush();    
        } else {
            $this->ddosProcess->setStatus('error');  
        }
        $em->persist($ddosProcess);
        $em->flush();

        // mark scan complete
        $this->scan->scanComplete();
    }

    public function scanCommenced() {
        // mark scan commencement time and persist
        $em = $this->entityManager;
        $this->scan->setTimeCommenced($this->clock->now())->setStatus('in_progress');
        $em->persist($this->scan);
        $em->flush(); 
    }

    public function scanComplete() {
        // mark scan conclusion time and persist
        $em = $this->entityManager;
        $this->scan->setTimeCompleted($this->clock->now())->setStatus('complete');
        $em->persist($this->scan);
        $em->flush(); 
    }

}