<?php

namespace App\Service;

use App\Entity\Vulnerability;
use App\Entity\Tool;

use App\Repository\ToolRepository;

class VULN_CSRF {
    /*
        Vulnerability:          Cross-Site Request Forgery
        Responsible:            MG
        OpenProject Phase #:    444

        Summary:
            ... quick summary on the vulnerability and what tools are required
            Using an authenticated user to perform malicious requests on a web application
            Often involves the use of some sort of social engineering to successfully execute
            XSRFProbe will be the tool used to check this, as it provides general informational results that may lead to csrf attack vectors


        Output (HTML): *TODO*
            HTML formatted output to go straight into the Report.
    */

    private ToolRepository $ToolsRepository;
    private Tool $TXSRFProbe;
    private $Severity;
    private $HTML;

    
    // TODO look up the results of a Tool entity from the database
    // I think it's something like: ??

    public function __construct(private Vulnerability $vulnerability){
        $this->ToolsRepository = $entityManager->getRepository(Tool::class);
        $this->TXSRFProbe = $this->ToolsRepository->findOneBy([
            'name' => 'xsrfprobe',
            'scan' => $vulnerability->getScanId(),
        ]);

    }
    
    
    // Now that I have the tool, analyse the output of it

    // Information output
    public function Analyse() {
        // process the data from the tool
        
        // fetch the data and decode the JSON (not sure if this is done by Symfony...?)
        //$output = json_decode($this->TXSRFProbe->getResults(), true);
        $output = $this->TXSRFProbe->getResults();

        // TODO analyse it somehow (see screenshot of tool in resources doc)
        // regex will need to check for things like "Possible CSRF Vulnerability Detected"

        // calculate the severities and store
        // severity should be informational so leaving as zero should be fine
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


