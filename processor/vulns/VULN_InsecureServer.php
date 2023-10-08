<?php

class VULN_InsecureServer extends VULN {
    /*
        Vulnerability:          Path Traversal Vulnerability
        Responsible:            PY
        OpenProject Phase #:    --

        Summary:
            Determines whether server configuration is susceptible to SSL and security exploits.
            Finds vulnerabilities and their class.
            Severity and output is calculated and stored for use in report.

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
        $severity = 0;
        $count = 0;

        foreach ($arr as $value) {
            if(substr_count($value, "Vulnerable!") > 0) {
                $count++;
            }
            if ($severity = 0) {
                if(substr_count($value, "Vulnerable!") > 0) {
                    $severity = 2;
                }
            } elseif ($severity = 2) {
                if(substr_count($value, "Vulnerable!") > 0) {
                    $severity = 3;
                }
            }
        }
        
        // and the HTML:
        $this->html = nl2br("Results from Insecure Server Configuration/SSL check:\n" . $output . "\nSeverity rating: " . $this->severity);
    }
}