<?php
class VULN_VulnOutComponents extends VULN {
    /*
        Vulnerability:          Vulnerable or outdated components
        Responsible:            SH
        OpenProject Phase #:    452

        Summary:
            

        Output (HTML):
            

    */

    public function Analyse() {
        // Analyse your vulnerability
        $output = " - Vulnerable or outdated components\n";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "Nikto":
                // if the array returned by the nikto tool isn't empty then we know something was found
                    if (isset($tool->getVulnComp()[0])) {
                        // kind of a place holder output here but you get the idea
                        $output .=  "Application presents " .
                                    count($tool->getVulnComp()) .
                                    " potential vulnerabilities:\n";
                        foreach($tool->getVulnComp() as $vuln){
                            $output .= $vuln . "\n";
                        }
                    }

                    else{
                        $output .= "No potential vulnerable or outdated components were found";
                    }
            
                    break;
            }
        }
        
        // calculate the severities and store
        $this->severity = 0;

        // remember to construct the HTML used within the report:
        //   (the final report generated, that includes ALL vulnerabilities, will consist of all of these html segments displayed together)
        //   (We'll standardise this later!)
        $this->html = $output;
    }
}
?>