<?php

namespace App\Service;

use App\Entity\Vulnerability;
use App\Entity\Tool;
use App\Repository\ToolRepository;
use Doctrine\ORM\EntityManagerInterface;

class VULN_DummyVulnerability {
    /*
        Vulnerability:          DDoS Vulnerability
        Responsible:            PY
        OpenProject Phase #:    --

        Summary:
            DDoS Vulnerability check that uses a cdnCheck method as part of TOOL_cdnTool
            to check the target's susceptibility to a DDoS attack. The logic behind
            this method is that targets that employ the use of a CDN are protected
            against DDoS attacks.

        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    private $severity;
    private $html;
    
    // TODO look up the results of a Tool entity from the database
    // I think it's something like: ??

    public function __construct(
        Tool $cdnCheck,
        Vulnerability $vulnerability,
        EntityManagerInterface $em
    ) {
    }
    
    
    // Now that I have the tool, analyse the output of it

    // Information output
    public function Analyse() {
        // process the data from the tool
        $this->cdnCheck = $this->em->getRepository(Tool::class)->findOneBy([
            'name' => 'cdnCheck',
            'scan' => $vulnerability->getScanId(),
        ]);
        
        // fetch the data and decode the JSON (not sure if this is done by Symfony...?)
        $output = $this->cdnCheck->getOutput();

        // analyse it somehow

        // calculate the severities and store
        if($output == 'No commercial CDN is in use. Target may be vulnerable to DDoS attacks.') {
            $this->severity = 2;
        }
        else {
            $this->severity = 0;
        }

        // and the HTML:
        $this->html = "Results from DDoS check: " . $output . " Severity rating: " . $this->severity;
    }

    public function getSeverity(): ?int
    {
        return $this->severity;
    }
    public function getHTML(): ?int
    {
        return $this->html;
    }

}