<?php

class VULN_PathTraversal extends VULN {
    /*
        Vulnerability:          Path Traversal Vulnerability
        Responsible:            PY
        OpenProject Phase #:    --

        Summary:
            Checks to see whether the target is susceptible and/or able to be attacked via path traversal exploitation techniques.
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
                case "ProTravel":
                    $rawOutput = $tool->Output();
            }
        }

        // calculate the severities and store
        if($rawOutput == 'Done') {
            $this->severity = 0;
            $output = "Path Traversal attempted and unsuccessful. No vulnerability found.";
        }
        else {
            $this->severity = 3;
            $output = "Path Traversal attempted and successful. Major vulnerability found. Results not shown for security purposes.";
        }

        // and the HTML:
        $this->html = nl2br("Results from Path Traversal check:\n" . $output . "\nSeverity rating: " . $this->severity);
    }
}