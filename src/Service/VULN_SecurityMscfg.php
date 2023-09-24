<?php

namespace App\Service;

use App\Entity\Vulnerability;
use App\Entity\Tool;

use App\Repository\ToolRepository;

class VULN_SecurityMscfg {
    /*
        Vulnerability:          Security Misconfiguration
        Responsible:            MG
        OpenProject Phase #:    451

        Summary:
            ... quick summary on the vulnerability and what tools are required
            Broad term for any kind of poor configuration leaving systems vulnerable
            This includes (but is not limited to) default credentials, out of date software, unnecessary ports being open etc.
            Implementation of this vulnerability will involve the use of Nmap with the vulners script, as well as (potentially) the results of a directory busting tool.


        Output (HTML): TODO
            HTML formatted output to go straight into the Report.
    */

    private ToolRepository $ToolsRepository;
    private Tool $TNmap;
    // private Tool $TDirBustingTool;
    private $Severity;
    private $HTML;

    
    // TODO look up the results of a Tool entity from the database
    // I think it's something like: ??
    // have added another tool construction for the directory busting (if implemented)

    public function __construct(private Vulnerability $vulnerability){
        $this->ToolsRepository = $entityManager->getRepository(Tool::class);
        $this->TNmap = $this->ToolsRepository->findOneBy([
            'name' => 'nmap',
            'scan' => $vulnerability->getScanId(),
        ]);
        //$this->TDirBustingTool = $this->ToolsRepository->findOneBy([
            //'name' => 'dirbuster',
            //'scan' => $vulnerability->getScanId(),
        //]);

    }
    
    
    // Now that I have the tool, analyse the output of it

    // Information output
    public function Analyse() {
        // process the data from the tool
        
        // fetch the data and decode the JSON (not sure if this is done by Symfony...?)
        //$output = json_decode($this->TNmap->getResults(), true);
        $output = $this->TNmap->getResults();
        //$output = $this->TDirBustingTool->getResults();

        // analyse it somehow TODO
        // will need to essentially go through the Nmap scan and see what vulners came up with for potential cves against each port
        // plus analyse the results of the directory busting to see if /admin was reachable 

        // calculate the severities and store
        // this will be informational since the results of the scans will be "this looks interesting" so leaving it at zero should be fine
        $this->Severity = 0;

        // and the HTML:
        $this->HTML = "<p>The results are: " . $output . ". Yes, this will need more formatting and extraction
        of $output...";

    }

    public function getSeverity(): ?int
    {
        return $this->Severity;
    }
    public function getHTML(): ?int
    {
        return $this->HTML;
    }

}


