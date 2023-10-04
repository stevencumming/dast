<?php

namespace App\Scan;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Clock\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Vulnerability;
use App\Entity\Scan;

use App\Service\DdosService;
use App\Service\TOOL as ServiceTOOL;
use App\Service\TOOL_cURL;
// Tools
use App\Service\TOOL_DummyTool;
use App\Service\TOOL_Nmap;
use App\Service\TOOL_Sitemap;
use App\Service\TOOL_XSRFProbe;
// Vulnerabilities
use App\Service\VULN_DummyVulnerability;
use App\Service\VULN_SSRF;
use App\Service\VULN_CSRF;
use App\Service\VULN_SecurityMscfg;
use Doctrine\ORM\Query\Expr;

// ========================================================================

// currently rigged up for one process as of moment
class ScanProcessor
{
    // Class Members 
    //      Tools Processes
    /*          [DELETEME] REFACTOR SEP28: Removing database persistence for the 'Tool' entity. For the execution flow,
                the tool process (so the actual TOOL_XX service for each tool we implement) will be easier to access as
                an object rather than converting to JSON as an intermediary step - just to read back as an object in
                the VULN_xx services.
                Refactoring to have the TOOL_xxx services (instances of the TOOL_xxx class) stored against this ScanProcessor
                (for this particular scan) as a class member (variable); we can pass around as an OBJECT instead.
                This saves an unnessary persistance to the database.

                Alright, so this is where it gets a little confusing.
                So I've redefined the TOOL type to be a parent (abstract) class of the TOOL_xxx classes that we implement.
                This is so that we can pass them around as TOOL objects, and extend (override) the implementations in their
                respective TOOL_xxx classes we each create.
    */
    private TOOL_DummyTool $Tdummy;
    private TOOL_cURL $TcUrl;
    private TOOL_Nmap $Tnmap;
    private TOOL_Sitemap $Tsitemap;
    private TOOL_XSRFProbe $Txsrfprobe;


    //      Vulnerabilities
    /*          [DELETEME] REFACTOR SEP28: Vulnerabilities stay as Vulnerability typed entities (for now) as they ARE
                persisted to the database.
    */
    private Vulnerability $Vdummy;
    private Vulnerability $Vanother;
    private Vulnerability $Vsecuritymscfg;
    private Vulnerability $Vcsrf;
    private Vulnerability $Vssrf;


    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClockInterface $clock,
        private Security $security,

        private Scan $scan,
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

        // Declare the process (service)
        // naming it, and passing it a reference to this scan (so it can grab the target)
        $this->Tdummy = new TOOL_DummyTool("DummyTool", $this->scan);

        // Start the tool process execution
        $this->Tdummy->Execute();

        // DONE
        // Move on to the next tool...
    
        // ...
        // ...


        // =============== TOOL ===============
        // Tool Name:       Nmap
        // Responsible:     MG + others

        // Declare the process (service)
        // naming it, and passing it a reference to this scan (so it can grab the target)
        $this->Tnmap = new TOOL_Nmap("Nmap", $this->scan);

        // Start the tool process execution
        $this->Tnmap->Execute();


        // DONE
        // Move on to the next tool...

        // =============== TOOL ===============
        // Tool Name:       XSRFProbe
        // Responsible:     MG

        // Declare the process (service)
        // naming it, and passing it a reference to this scan (so it can grab the target)
        $this->Txsrfprobe = new TOOL_XSRFProbe("XSRFProbe", $this->scan);

        // Start the tool process execution
        $this->Txsrfprobe->Execute();


        // DONE
        // Move on to the next tool...

        // =============== TOOL ===============
        // Tool Name:       cURL
        // Responsible:     MG (and whoever else wants to use)

        // Declare the process (service)
        // naming it, and passing it a reference to this scan (so it can grab the target)
        $this->TcUrl = new TOOL_cURL("cURL", $this->scan);

        // Start the tool process execution
        $this->TcUrl->Execute();


        // DONE
        // Move on to the next tool...
    
        // ...
        // ...


        


    }


    public function processVulnerabilities() {
        // Go through each of the vulnerabilities and analyse output of the relevant tool(s)
        
        // =============== VULNERABILITY ===============
        // Vulnerability:       
        // Responsible: 
        
        // Set the scan ID for the vulnerability
        $this->Vdummy->setScanId($this->scan);

        // Set the name of the vulnerability
        $this->Vdummy->setName("Some vulnerability");


        // Instantiate vulnerability process and pass it the required tools
        // Also pass this Scan object
        $VdummyProcess = new VULN_DummyVulnerability($this->scan, [$this->Tdummy, $this->Tnmap]);

        // Analyse Vuln
        $VdummyProcess->Analyse();

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

        // =============== VULNERABILITY ===============
        // Vulnerability: Security Misconfiguration       
        // Responsible: MG
        $this->Vsecuritymscfg->setScanId($this->scan);
        $this->Vsecuritymscfg->setName("Security Misconfiguration");
        // Tdummy will be replaced by the dirbuster tool Steve will be using
        $SecurityMscfgProcess = new VULN_SecurityMscfg($this->scan, [$this->tNmap, $this->Tdummy]);
        // the analyse function for the process will need be called I believe
        $this->Vsecuritymscfg->setSeverity($SecurityMscfgProcess->getSeverity());
        $this->Vsecuritymscfg->setHtml($SecurityMscfgProcess->getHTML());

        $em->persist($Vsecuritymscfg);
        $em->flush();

        // =============== VULNERABILITY ===============
        // Vulnerability: Cross Site Request Forgery   
        // Responsible: MG
        $this->Vcsrf->setScanId($this->scan);
        $this->Vcsrf->setName("Cross Site Request Forgery");
        
        $CsrfProcess = new VULN_CSRF($this->scan, [$this->Txsrfprobe]);
        // the analyse function for the process will need be called I believe
        $this->Vcsrf->setSeverity($CsrfProcess->getSeverity());
        $this->Vcsrf->setHtml($CsrfProcess->getHTML());

        $em->persist($Vcsrf);
        $em->flush();

         // =============== VULNERABILITY ===============
        // Vulnerability: Server Side Request Forgery
        // Responsible: MG
        $this->Vssrf->setScanId($this->scan);
        $this->Vssrf->setName("Server Site Request Forgery");
        
        $SsrfProcess = new VULN_SSRF($this->scan, [$this->TcUrl]);
        // the analyse function for the process will need be called I believe
        $this->Vssrf->setSeverity($SsrfProcess->getSeverity());
        $this->Vssrf->setHtml($SsrfProcess->getHTML());

        $em->persist($Vssrf);
        $em->flush();
        
        // ...
        // ...
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



    

}