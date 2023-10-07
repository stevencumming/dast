<?php
class VULN_XSS extends VULN {
    /*
        Vulnerability:          CrossSiteScripting
        Responsible:            LC
        OpenProject Phase #:    428

        Summary:
            ... quick summary on the vulnerability and what tools are required


        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {
        // Analyse your vulnerability

        // Local variables here for using when analysing the tools
        // xxxx
        $xsstrikeOutput = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "XSStrike":
                // if the array returned by the xsrfprobe tool isn't empty then we know something was found
                    if (isset($tool->getComponents()[0])) {
                        // kind of a place holder output here but you get the idea
			    $output = "Application is potentially vulnerable to ";
			    foreach($tool->getComponents() as $components){
				$output .= $components . ", ";
			    }
		    }else{
			    $output = "No potential XSS vulnerabilities were found";
		    }
            
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
        $this->html = "$output";
        

    }

}
?>