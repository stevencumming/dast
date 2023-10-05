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
        $output = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                    case "cURL":
                        $output = $tool->Output();
                }
        }

        // ++ All tools have been analysed at this point


        
        // calculate the severities and store
        if($output == '') {
            $this->severity = 2;
        }
        else {
            $this->severity = 0;
        }

        // and the HTML:
        $this->html = "Results from DDoS check: " . $output . " Severity rating: " . $this->severity;
        }
        }
?>