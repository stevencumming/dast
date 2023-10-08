<?php
class VULN_CryptographicFlrs extends VULN {
    /*
        Vulnerability:          Cryptographic Failures
        Responsible:            LC
        OpenProject Phase #:    448

        Summary:
            Cryptographic failures are any failure where the cryptography that should be applied either is not or is misconfigured


        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {
        // Analyse your vulnerability

        // Local variables here for using when analysing the tools
        // xxxx
        $curlOutput = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "cURL":
                // if the array returned by the xsrfprobe tool isn't empty then we know something was found
                    if (isset($tool->getRedirect()[0])) {
                        // kind of a place holder output here but you get the idea
                        $output = "Application is correctly redirecting http traffic to: ";
                        foreach($tool->getRedirect() as $redirect){
                            $output .= $redirect . ", ";
                        }
                     
		    }else{
			    $output = "Application is likley not redirecting http traffic to https. Manual checking will need to be performed to confirm";
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