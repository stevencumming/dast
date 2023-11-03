<?php
class VULN_InsecDesign extends VULN {
    /*
        Vulnerability:          Insecure Design
        Responsible:            SH
        OpenProject Phase #:    450

        Summary:
            Insecure design is self explanatorily a poorly designed web app that has easily targetable design flaws. Because of the broad
            nature of the vulnerability, many vulnerabilities could fall under insecure design. The vulnerabilities found with this tool
            tend not to fall into other vulnerabilities so they are listed with this tool instead.

        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {
        // Analyse your vulnerability
        $output = "<h3>Insecure design:</h3><br>";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "Nikto":
                // if the array returned by the nikto tool isn't empty then we know something was found
                    if (isset($tool->getInsecDes()[0])) {
                        // kind of a place holder output here but you get the idea
                        $output .=  "<h4>Application presents " .
                                    count($tool->getInsecDes()) .
                                    " potential vulnerabilities:</h4><br><p>";
                        foreach($tool->getInsecDes() as $vuln){
                            $output .= $vuln . "<br>";
                        }
												
                        $output .= "</p>";
                    }

                    else{
                        $output .= "<p>No potential design based vulnerabilities were found.</p><br>";
                    }
            
                    break;
            }
        }
        
        // calculate the severities and store
        $this->severity = 0;

        $this->html = $output;
    }
}
?>
