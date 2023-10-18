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
        $output = "Vulnerable or outdated components:\n";
        
        $outCount = 0;

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "Nikto":
                // if the array returned by the nikto tool isn't empty then we know something was found
                    if (isset($tool->getVulnComp()[0])) {
                        //Set count for outdated components
                        $outCount = count($tool->getVulnComp())
												
                        $output .=  "Application presents " .
                                    $outCount .
                                    " potential vulnerabilities:\n";
                        foreach($tool->getVulnComp() as $vuln){
                            $output .= $vuln . "\n";
                        }
												
                        $this->severity = 1;
                    }

                    else{
                        $output .= "No potential vulnerable or outdated components were found.\n";
                    }
            
                    break;
            }
        }
				
        // calculate the severities and store
        if ($outCount <= 2) {
            $this->severity = $outCount;
        }
        
        else {
            $this->severity = 3;
        }
				
        $this->html = $output;
    }
}
?>
