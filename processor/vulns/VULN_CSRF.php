<?php
class VULN_CSRF extends VULN {
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

    public function Analyse() {
        $output = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "XSRFProbe":
                // if the array returned by the xsrfprobe tool isn't empty then we know something was found
                    if (isset($tool->getVulnTypes()[0])) {
                        // kind of a place holder output here but you get the idea
			    $output = "Application is potentially vulnerable to ";
			    foreach($tool->getVulnTypes() as $vulnType){
				$output .= $vulnType . ", ";
			    }
		    } else {
			    $output = "No potential CSRF vulnerabilities were found";
		    }
            
                    break;  // don't forget to break
                // we don't really need a default case, the condition should never occur.
            }

        // and the HTML:
        $this->html = "<p>Cross Site Request Forgery Results: " . $output . "</p>";

        }
    }

}
?>

