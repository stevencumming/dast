<?php

namespace App\Service;

use App\Entity\Vulnerability;
use App\Entity\Tool;

use App\Repository\ToolRepository;

class VULN_CryptographicFlrs {
    /*
        Vulnerability:          Cryptographic Failures
        Responsible:            LC
        OpenProject Phase #:    448

        Summary:
            Cryptographic failures are classed as any security misconfigurations involving cryptography
            One of the most common misconfigurations is http not being redirected to https. This will be checked with cURL using the -I flag


        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    private ToolRepository $toolsRepository;
    private Tool $TCURL;
    private $severity;
    private $html;

    
    // TODO look up the results of a Tool entity from the database
    // I think it's something like: ??

    public function __construct(private Vulnerability $vulnerability){
        $this->toolsRepository = $entityManager->getRepository(Tool::class);
        $this->tDummy = $this->toolsRepository->findOneBy([
            'name' => 'nslookup',
            'scan' => $vulnerability->getScanId(),
        ]);

    }
    
    
    // Now that I have the tool, analyse the output of it

    // Information output
    public function Analyse() {
        // process the data from the tool
        
        // fetch the data and decode the JSON (not sure if this is done by Symfony...?)
        //$output = json_decode($this->TDummy->getResults(), true);
        $output = $this->tDummy->getResults();

        // analyse it somehow

        // calculate the severities and store
        $this->severity = 0;

        // and the HTML:
        $this->html = "<p>The results are: " . $output . ". Yes, this will need more formatting and extraction
        of $output...";

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


