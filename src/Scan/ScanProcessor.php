<?php

namespace App\Scan;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Clock\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Tool;
use App\Entity\Vulnerability;
use App\Entity\Scan;

use App\Service\DdosService;

// Tools
use App\Service\TOOL_DummyTool;


// Vulnerabilities
use App\Service\VULN_DummyVulnerability;
use Doctrine\ORM\Query\Expr;

// ========================================================================

// currently rigged up for one process as of moment
class ScanProcessor
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClockInterface $clock,
        private Security $security,

        private Scan $scan,

        // Tools
        // private Process $ddosProcess,        (replaced by Tool)
        
        private Tool $Tdummy,
        private Tool $Tsomething,
        private Tool $Tsomethingelse,

        // Vulnerabilities
        private Vulnerability $Vdummy,



    ) {
    }


    /*
        Order of operations:
        1.  Load scan (loadScan)
        2.  Mark that the scan has commenced (  $this->scan->scanCommenced();  )
        3.  Process all of the tools against the target of this scan (processTools)
        4.  Process each vulnerability, analysing the results of executed tools (processVulnerabilities)
        5.  Generate the PDF and email (compileReport)
        6.  Mark that the scan has completed (  $this->scan->scanComplete();  )

    */

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


    public function scanCommenced() {
        // mark scan commencement time and persist
        $em = $this->entityManager;
        $this->scan->setTimeCommenced($this->clock->now())->setStatus('in_progress');
        $em->persist($this->scan);
        $em->flush(); 
    }


    public function processTools() {
        // The first process to be called after a new scan is loaded.
        // Go through all tools to be used and execute them.


        // =============== TOOL ===============
        // Tool Name:       Dummy Tool
        // Responsible:     AA

        // Set the scan ID for the tool
        $this->Tdummy->setScanId($this->scan);

        // Set the name of the tool
        $this->Tdummy->setName("nslookup");

        // Declare the process (service)
        $TdummyProcess = new TOOL_DummyTool($this->Tdummy);

        // Start the tool process execution
        $TdummyProcess->Execute();

        // Error handling is done in the tool service... persist the output to database
        $this->Tdummy->setResults($TdummyProcess->Output());
        $em->persist($Tdummy);
        $em->flush();  

        // DONE
        // Move on to the next tool...
    
        // ...
        // ...

        // =============== TOOL ===============
        // Tool Name:       Another Tool
        // Responsible:     AA
        $this->Tanother->setScanId($this->scan);
        $this->Tanother->setName("nslookup");
        $TanotherProcess = new TOOL_DummyTool($this->Tanother);
        $TanotherProcess->Execute();
        $this->Tanother->setResults($TanotherProcess->Output());
        $em->persist($Tanother);
        $em->flush(); 

        // ...
        // ...

        // =============== TOOL ===============
        // Tool Name:       The next tool
        // Responsible:     AA


    }


    public function processVulnerabilities() {
        // Go through each of the vulnerabilities and analyse output of the relevant tool(s)
        
        // =============== VULNERABILITY ===============
        // Vulnerability:       
        // Responsible: 
        
        // Set the scan ID for the vulnerability
        $this->Vdummy->setScanId($this->scan);

        // Set the name of the tool
        $this->Vdummy->setName("Some vulnerability");

        // Analyse
        $VdummyProcess = new VULN_DummyVulnerability($this->Vdummy);

        // Persist Results
        $this->Vdummy->setSeverity($VdummyProcess->getSeverity());
        $this->Vdummy->setHtml($VdummyProcess->getHTML());

        $em->persist($Vdummy);
        $em->flush();  

        // DONE
        // Move on to the next vulnerability...
    
        // ...
        // ...

        // =============== VULNERABILITY ===============
        // Vulnerability:       
        // Responsible: 
        $this->Vanother->setScanId($this->scan);
        $this->Vanother->setName("Some vulnerability");
        $VanotherProcess = new VULN_AnotherVulnerability($this->Vanother);
        $this->Vanother->setSeverity($Vanother->getSeverity());
        $this->Vanother->setHtml($Vanother->getHTML());
        $em->persist($Vanother);
        $em->flush();  

        // ...
        // ...

        // =============== VULNERABILITY ===============
        // Vulnerability:       
        // Responsible: 

    }


    public function compileReport() {
        // Compile the report using the vulnerability data from analysis above.



    }









    

    public function scanComplete() {
        // mark scan conclusion time and persist
        $em = $this->entityManager;
        $this->scan->setTimeCompleted($this->clock->now())->setStatus('complete');
        $em->persist($this->scan);
        $em->flush(); 
    }











    // public function processCompute() {
    //     // mark the scan commenced
    //     $this->scan->scanCommenced();
        

    //     // ddos process, repeat this for all scan processes
    //     $this->ddosProcess->setStatus('waiting');
    //     $ddosService = new DdosService($ddosProcess);
    //     $ddosResults = $ddosService->cdnCheck();
    //     if (!$ddosResults == null) {
    //         $this->ddosProcess->setResults($ddosService->cdnCheck());
    //         $this->ddosProcess->setStatus('completed');
    //         $em->persist($ddosProcess);
    //         $em->flush();    
    //     } else {
    //         $this->ddosProcess->setStatus('error');  
    //     }
    //     $em->persist($ddosProcess);
    //     $em->flush();

    //     // mark scan complete
    //     $this->scan->scanComplete();
    // }






    

}