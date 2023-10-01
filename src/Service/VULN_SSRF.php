<?php

namespace App\Service;

use App\Entity\Scan;


class VULN_SSRF {
    /*
        Vulnerability:          Server Side Request Forgery
        Responsible:            MG
        OpenProject Phase #:    456

        Summary:
            ... quick summary on the vulnerability and what tools are required
            Making the server send a request to an unauthorised destination without first validating the target URL
            This can be used to do things like read the contents of etc/passwd or gain access to the admin dashboard of a web app
            cURL will be used to request the target to display the contents of its etc/passwd 
            and will also be used to navigate to the admin console if a /admin page was found during directory busting


        Output (HTML): TODO
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
                case "cURL":
                // check the value of the curl tool's reply flag to see if etc/passwd was returned
                    if ($tool->getReply = true){
                        $output = "Contents of /etc/passwd discovered using Server Side Request Forgery";
                        $this->severity = 2;
                    }
               
                    break;  // don't forget to break
                // we don't really need a default case, the condition should never occur.
            }


        // this might have to go into the above if statement if we don't want to return anything if nothing is found
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