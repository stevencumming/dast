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
                    // Do other stuff with nmap
                    // Analyse the output inside the Nmap object
                    //  ... which at this point in code would simply be accessed with $tool
                    //  ... this $tool object would be of type TOOL_Nmap
                    // because it is the CURRENT tool index in the foreach loop

                    $nmapOutput = "Something related to nmap results";
                    break;
                case "Dirbuster":
                    // Do more stuff
                    $dirbusterOutput = "Something related to dirbuster results";
                    break;  // don't forget to break
                // we don't really need a default case, the condition should never occur.
            }
        }

        // ++ All tools have been analysed at this point


        
        // calculate the severities and store
        // this should only be set and returned if there is something vulnerable found
        $this->severity = 0;

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


