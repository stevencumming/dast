<?php
namespace App\Service;

use App\Entity\Scan;

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

    private array $tools;
    private Scan $scan;   
    private $Severity;
    private $HTML;

    

   // May as well pass in the scan object too, so that the Scan entity members are available here if needed (like target etc)
    public function __construct(Scan $aScan, array $aTools){
        $this->tools = $aTools;
        $this->scan = $aScan;
        // set the initial severity level to  -1 so that if another class calls the GetSeverity function it will know nothing was found if the severity is less than zero
        $this->severity = -1;
    }
    
    
    // Now that I have the tool, analyse the output of it

    // Information output
    public function Analyse() {
        $output = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "XSRFProbe":
                // if the array returned by the xsrfprobe tool isn't empty then we know something was found
                    if (sizeof($tool->getVulnTypes() > 0)) {
                        // kind of a place holder output here but you get the idea
                        $output = "Application is potentially vulnerable to " . $tool->getVulnTypes();
                        // if something was found then set the severity
                        $this->severity = 0;
                    }
            
                    break;  // don't forget to break
                // we don't really need a default case, the condition should never occur.
            }

        // and the HTML:
        $this->HTML = "<p>The results are: " . $output . ". Yes, this will need more formatting and extraction
        of $output...";

    }

    public function getSeverity(): ?int
    {
        return $this->Severity;
    }
    public function getHTML(): ?string
    {
        return $this->HTML;
    }

}


