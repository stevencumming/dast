<?php

namespace App\Scan;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Clock\ClockInterface;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\Expr;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Bundle\SecurityBundle\Security;
use App\Entity\Vulnerability;
use App\Entity\Scan;

use App\Service\DdosService;
use App\Service\TOOL as ServiceTOOL;
use App\Service\TOOL_cURL;
// Tools
//use App\Service\TOOL_DummyTool;
use App\Service\TOOL_Nmap;
use App\Service\TOOL_Sitemap;
use App\Service\TOOL_XSRFProbe;
use App\Service\TOOL_cdnCheck;
use App\Service\TOOL_ProTravel;
// Vulnerabilities
//use App\Service\VULN_DummyVulnerability;
use App\Service\VULN_SSRF;
use App\Service\VULN_CSRF;
use App\Service\VULN_SecurityMscfg;
use App\Service\VULN_DDOS;
use App\Service\VULN_PathTraversal;

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
    private TOOL_cURL $TcUrl;
    private TOOL_Nmap $Tnmap;
    private TOOL_Sitemap $Tsitemap;
    private TOOL_XSRFProbe $Txsrfprobe;
    private TOOL_cdnCheck $T_cdnCheck;
    private TOOL_ProTravel $T_proTravel;


    //      Vulnerabilities
    /*          [DELETEME] REFACTOR SEP28: Vulnerabilities stay as Vulnerability typed entities (for now) as they ARE
                persisted to the database.
    */
    private Vulnerability $Vsecuritymscfg;
    private Vulnerability $Vcsrf;
    private Vulnerability $V_ddos;
    private Vulnerability $V_pathTrav;


    public function __construct(
        private EntityManagerInterface $entityManager,
        private ClockInterface $clock,
        private Security $security,
        private Scan $scan
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
    
        // =============== TOOL ===============
        // Tool Name:       CDNCheck
        // Responsible:     PY

        // Declare the process (service)
        // naming it, and passing it a reference to this scan (so it can grab the target)
        $this->T_cdnCheck = new TOOL_cdnCheck("cdnCheck", $this->scan);

        // Start the tool process execution
        $this->T_cdnCheck->Execute();

        // DONE
        // Move on to the next tool...
    
        // =============== TOOL ===============
        // Tool Name:       ProTravel
        // Responsible:     PY

        // Declare the process (service)
        // naming it, and passing it a reference to this scan (so it can grab the target)
        $this->T_proTravel = new TOOL_cdnCheck("ProTravel", $this->scan);

        // Start the tool process execution
        $this->T_proTravel->Execute();

    }


    public function processVulnerabilities() {
        // Go through each of the vulnerabilities and analyse output of the relevant tool(s)

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

        $em->persist($this->Vsecuritymscfg);
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

        $em->persist($this->Vcsrf);
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

        $em->persist($this->Vssrf);
        $em->flush();
        
        // =============== VULNERABILITY ===============
        // Vulnerability: Distributed Denial Of Service
        // Responsible: PY
        $this->V_ddos->setScanId($this->scan);
        $this->V_ddos->setName("Distributed Denial Of Service");
        
        $ddosProcess = new VULN_DDOS($this->scan, [$this->T_cdnCheck]);
        // the analyse function for the process will need be called I believe
        $this->V_ddos->setSeverity($ddosProcess->getSeverity());
        $this->V_ddos->setHtml($ddosProcess->getHTML());

        $em->persist($this->V_ddos);
        $em->flush();    

        // =============== VULNERABILITY ===============
        // Vulnerability: Path Traversal
        // Responsible: PY
        $this->V_pathTrav->setScanId($this->scan);
        $this->V_pathTrav->setName("Path Traversal");
        
        $pathTravProcess = new VULN_DDOS($this->scan, [$this->T_proTravel]);
        // the analyse function for the process will need be called I believe
        $this->V_pathTrav->setSeverity($pathTravProcess->getSeverity());
        $this->V_pathTrav->setHtml($pathTravProcess->getHTML());

        // PY- might be able to for loop this code once we have all this in. lot of repeatable code here.
        $em->persist($this->V_pathTrav); 
        $em->flush();   
    }

    public function compileReport() {
        // Compile the report using the vulnerability data from analysis above.
        // PARKER YOUNG TODO
    }

    public function scanComplete() {
        // mark scan conclusion time and persist
        $em = $this->entityManager;
        $this->scan->setTimeCompleted($this->clock->now())->setStatus('complete');
        $em->persist($this->scan);
        $em->flush(); 
    }

}