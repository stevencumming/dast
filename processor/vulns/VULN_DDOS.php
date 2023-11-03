<?php

class VULN_DDOS extends VULN {
    /*
        Vulnerability:          DDoS Vulnerability
        Responsible:            PY
        OpenProject Phase #:    --

        Summary:
            DDoS Vulnerability check that uses a cdnCheck method as part of TOOL_cdnTool
            to check the target's susceptibility to a DDoS attack. The logic behind
            this method is that targets that employ the use of a CDN are protected
            against DDoS attacks. Severity and output is calculated and stored for use in report.

        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    // Information output
    public function Analyse() {

        $output = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "cdnCheck":
                    $output = $tool->getOutput();
            }
        }

        // calculate the severities and store
        if($output == 'No commercial CDN is in use. Target may be vulnerable to DDoS attacks.') {
            $this->severity = 2;
        }
        else {
            $this->severity = 0;
        }

        // and the HTML:
        $this->html = nl2br("Results from DDoS check:\n" . $output . "\nSeverity rating: " . $this->severity);
    }
}