<?php

class VULN_InsecureServer extends VULN {
    /*
        Vulnerability:          Path Traversal Vulnerability
        Responsible:            PY
        OpenProject Phase #:    --

        Summary:
            

        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {

        $rawOutput = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "a2sv":
                    $rawOutput = $tool->Output();
            }
        }

        // TODO
        // calculate the severities and store
        if($rawOutput == '') {
            $this->severity = 0;
            $output = "";
        }
        else {
            $this->severity = 2;
            $output = "";
        }

        // and the HTML:
        $this->html = "Results from Insecure Server Configuration/SSL check: " . $output . " Severity rating: " . $this->severity;
    }
}