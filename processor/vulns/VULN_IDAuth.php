<?php
class VULN_IDAuth extends VULN {
    /*
        Vulnerability:          Vulnerable or outdated components
        Responsible:            SH
        OpenProject Phase #:    452

        Summary:
            

        Output (HTML):
            

    */

    public function Analyse() {
        // Analyse your vulnerability
        $output = "<h3>Identification or authentication failures:</h3><br>";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "Nikto":
                // if the array returned by the nikto tool isn't empty then we know something was found
                    if (isset($tool->getIDAuth()[0])) {
                        // kind of a place holder output here but you get the idea
                        $output .=  "<h4>Application presents " .
                                    count($tool->getIDAuth()) .
                                    " potential vulnerabilities:</h4><br><p>";
                        foreach($tool->getIDAuth() as $vuln){
                            $output .= $vuln . "<br>";
                        }
			$output .= "</p>";
			
			$this->severity = 1;
                    }

                    else{
                        $output .= "<p>No potential identification or authentication based vulnerabilities were found.</p><br>";
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
