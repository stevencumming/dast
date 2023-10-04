<?php

namespace App\Service;

use App\Entity\Scan;

class VULN_HostInfo {
    /*
        Vulnerability:          Victim Host Information - Reconnaissance
        Responsible:            SC
        OpenProject Phase #:    458

        Summary:
            ... quick summary on the vulnerability and what tools are required


        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    /*      [DELETEME] REFACTOR SEP28: This service can now be dumbed down a little... Essentially, there is the constructor
            that takes in the Vulnerability Entity object (for database persistence) and an **array** of tools.
            Only the tools that are needed are passed to this service.
                e.g., if this was the SQL Injection gets the TOOL_Sitemap tool object, and TOOL_Nmap tool object (etc)
            The tools are stored within this Vulnerability service as an array

            And the Analyse function that actually looks at the tool output(s) to work out the severity and the html output which
            is to be used in the report.
            The tools are indexed inside of the array by their names, i.e. $tools[i]->name
            (foreach used for better readability)                 
    */

    private array $tools;
    private Scan $scan;

    private $severity;
    private $html;

    
    
    // May as well pass in the scan object too, so that the Scan entity members are available here if needed (like target etc)
    public function __construct(Scan $aScan, array $aTools){
        $this->tools = $aTools;
        $this->scan = $aScan;
    }
    

    public function Analyse() {
        // Analyse your vulnerability

        // Local variables here for using when analysing the tools
        // xxxx
        $output = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName) {
                case "DummyTool":
                    // Do stuff with DummyTool
                    
                    // E.g. DummyTool (object of type TOOL_DummyTool has $addresses and $domain_names private members (variables))
                    // example, list all of the ip addresses found against their domain names reading in the array stored in the $tool TOOL_DummyTool
                    for ($i=0; $i < sizeof($tool->getAddresses); $i++) { 
                        $output += $tool->getAddresses[$i] . ": " . $tool->getDomain_names[$i];
                    }

                    break;
                case "Nmap":
                    // Do other stuff with nmap
                    // Analyse the output inside the Nmap object
                    //  ... which at this point in code would simply be accessed with $tool
                    //  ... this $tool object would be of type TOOL_Nmap
                    // because it is the CURRENT tool index in the foreach loop

                    $output = "Some other value";
                    break;
                case "another_tool_that_might_have_been_passed":
                    // Do more stuff
                    break;  // don't forget to break
                // we don't really need a default case, the condition should never occur.
            }
        }

        // ++ All tools have been analysed at this point


        
        // calculate the severities and store
        $this->severity = 0;

        // remember to construct the HTML used within the report:
        //   (the final report generated, that includes ALL vulnerabilities, will consist of all of these html segments displayed together)
        //   (We'll standardise this later!)
        $this->html = "<p>The results are: " . $output . ". Yes, this will need more formatting and extraction
        of $output...";

    }

    public function getSeverity(): ?int
    {
        return $this->severity;
    }
    public function getHTML(): ?string
    {
        return $this->html;
    }

}


