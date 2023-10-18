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

        $output = array();

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "a2sv":
                    $output = $tool->Output();
            }
        }

        // TODO
        // calculate the severities and store
        $this->severity = 0;

        foreach ($output as $value) {
            if ($this->severity = 0) {
                if(substr_count($value, "Vulnerable!") > 0) {
                    $this->severity = 2;
                }
            } elseif ($this->severity = 2) {
                if(substr_count($value, "Vulnerable!") > 0) {
                    $this->severity = 3;
                }
            }
        }
        
        $temp = "<p>";
        foreach ($output as $line) {
            $temp .= $line;
        }
        $temp .= "</p>";

        // and the HTML:
        $this->html = nl2br("Results from Insecure Server Configuration/SSL check:\n" . $temp . "\nSeverity rating: " . $this->severity);
    }
}