<?php

namespace App\Service;

use App\Entity\Scan;

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

    private array $tools;
    private Scan $scan;
    // private Tool $TDirBustingTool;
    private $Severity;
    private $HTML;

    
    // TODO look up the results of a Tool entity from the database
    // I think it's something like: ??
    // have added another tool construction for the directory busting (if implemented)

   public function __construct(Scan $aScan, array $aTools){
        $this->tools = $aTools;
        $this->scan = $aScan;
        // set the initial severity level to  -1 so that if another class calls the GetSeverity function it will know nothing was found if the severity is less than zero
        $this->severity = -1;
    }
    
    
    // Now that I have the tool, analyse the output of it

    // Information output
   public function Analyse() {
        // Analyse your vulnerability

        // Local variables here for using when analysing the tools
        // xxxx
        $nmapOutput = "";
        $dirbusterOutput = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->name) {
                
                case "Nmap":
                    // if the array of cves returned by nmap tool isn't empty then we know something was found
                    if (sizeof($tool->getCVEs() > 0) {
                        // kind of a place holder output here but you get the idea
                        $nmapOutput = "Potential CVEs found during port scanning: " . $tool->getCVEs();
                        // if something was found then set the severity
                        $this->severity = 0;
                    }
                    break;
                case "Dirbuster":
                    // I think we should implement an 'admin page found' boolean in the directory busting tool with its own getter so that other classes can just call that rather than sift through all entries
                    // then check if the getter returns a true value
                    if ($tool->GetAdminPage()) {
                        // kind of a place holder output here but you get the idea
                        $dirbusterOutput = "Admin page was found amongst application directories";
                        // need to set the severity every time a tool is checked so even in case one doesn't return any results
                        $this->severity = 0;
                    }
                    break;  // don't forget to break
                // we don't really need a default case, the condition should never occur.
            }
        }

       
        // do we still want to have output for vulnerabilities that aren't found?
        // remember to construct the HTML used within the report:
        //   (the final report generated, that includes ALL vulnerabilities, will consist of all of these html segments displayed together)
        //   (We'll standardise this later!)
        $this->html = "<p>The results are: " . $nmapOutput . ", " . $dirbusterOutput;

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


