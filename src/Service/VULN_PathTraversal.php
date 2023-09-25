<?php

namespace App\Service;

use App\Entity\Vulnerability;
use App\Entity\Tool;
use App\Repository\ToolRepository;
use Doctrine\ORM\EntityManagerInterface;

class VULN_PathTraversal {
    /*
        Vulnerability:          Path Traversal Vulnerability
        Responsible:            PY
        OpenProject Phase #:    --

        Summary:
            

        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    private $severity;
    private $html;
    
    // TODO look up the results of a Tool entity from the database
    // I think it's something like: ??

    public function __construct(
        Tool $proTravel,
        Vulnerability $vulnerability,
        EntityManagerInterface $em
    ) {
    }
    
    
    // Now that I have the tool, analyse the output of it

    // Information output
    public function Analyse() {
        // process the data from the tool
        $this->cdnCheck = $this->em->getRepository(Tool::class)->findOneBy([
            'name' => 'protravel',
            'scan' => $vulnerability->getScanId(),
        ]);
        
        // fetch the data and decode the JSON (not sure if this is done by Symfony...?)
        $output = $this->cdnCheck->getResults();

        // analyse it somehow

        // calculate the severities and store
        if($output == 'Done') {
            $this->severity = 0;
            $computedOutput = "Path Traversal attempted and unsuccessful. No vulnerability found.";
        }
        else {
            $this->severity = 3;
            $computedOutput = "Path Traversal attempted and successful. Major vulnerability found. Results not shown for security purposes.";
        }

        // and the HTML:
        $this->html = "Results from Path Traversal check: " . $output . " Severity rating: " . $this->severity;
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