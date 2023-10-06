<?php
class VULN_SSRF extends VULN{
    /*
        Vulnerability:          Server Side Request Forgery
        Responsible:            MG
        OpenProject Phase #:    456

        Summary:
            ... quick summary on the vulnerability and what tools are required
            Making the server send a request to an unauthorised destination without first validating the target URL
            This can be used to do things like read the contents of etc/passwd or gain access to the admin dashboard of a web app
            cURL will be used to request the target to display the contents of its etc/passwd 
            and will also be used to navigate to the admin console if a /admin page was found during directory busting


        Output (HTML): TODO
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {
        $output = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "cURL":
                // check the value of the curl tool's reply flag to see if etc/passwd was returned
                    if ($tool->getReply()){
                        $output = "Contents of /etc/passwd discovered using a malicious request.";
                        $this->severity = 2;
		    }
		    else{
			    $output = "Nothing was found";
		    }
               
                    break;  // don't forget to break
                // we don't really need a default case, the condition should never occur.
            }


        // this might have to go into the above if statement if we don't want to return anything if nothing is found
        // and the HTML:
        $this->html = "<p>Server Side Request Forgery results: " . $output . "</p>";

        }
    }
}
?>