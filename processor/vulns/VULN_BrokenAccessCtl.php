<?php
class VULN_BrokenAccessCtl extends VULN {
    /*
        Vulnerability:          Broken Access Control
        Responsible:            LC
        OpenProject Phase #:    438

        Summary:
            Broken access contains lots of different smaller vulnerabilities. This class doesnt call upon any new tools but instead
            uses exisiting tools and compiles the information


        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {
        // Analyse your vulnerability

        // Local variables here for using when analysing the tools
        // xxxx
        $XSRFProbeOutput = "";
        $curlOutput = "";
        $output = "Broken Access Control Vulnerabilities: ";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            
            switch ($tool->getName()) {
                case "XSRFProbe":
                // if the array returned by the xsrfprobe tool isn't empty then we know something was found
                    if (isset($tool->getVulnTypes()[0])) {
                        // Outputs that some information was found by XSRFProbe
                        $output .= "Broken access misconfigurations found. Please see Cross-Site Request Forgery section for more detail. ";
                   
		    }else{
			    $output .= "";
		    }
            
                    break;  // don't forget to break
                // we don't really need a default case, the condition should never occur.
            }
            switch ($tool->getName()) {
                case "cURL":
                // if the array returned by the cURL tool isn't empty then we know something was found
                    if ($tool->getReply()){
                        // Outputs that some information was found by cURL
                        $output .= "Able to access pages that should be restricted. Please see Server Side Request Forgery for more detail. ";
                   
		    }else{
			    $output .= "";
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