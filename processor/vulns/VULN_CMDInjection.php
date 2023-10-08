<?php
class VULN_CMDInjection extends VULN {
    /*
        Vulnerability:          CMD Injection
        Responsible:            SC
        OpenProject Phase #:    999

        Summary:
            ... quick summary on the vulnerability and what tools are required


        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {
        // Analyse your vulnerability

        // Local variables here for using when analysing the tools
        // xxxx
        $output = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "commix":
                    // Pull the output of sqlmap straight in

                    // TODO, it only shows when it is unsuccessful at the moment... TODO

                    $output .= $tool->getOutput();

                    break;
            }
        }

        // ++ All tools have been analysed at this point

        
        // calculate the severities and store
        $this->severity = 0;

        // remember to construct the HTML used within the report:
        $this->html = "<div><p>" . $output . "</p></div>";

    }

}
?>