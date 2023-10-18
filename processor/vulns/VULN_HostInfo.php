<?php
class VULN_HostInfo extends VULN {
    /*
        Vulnerability:          Host Information
        Responsible:            SC
        OpenProject Phase #:    

        Summary:
            Gather target host information:
                - Web host software
                - Open ports

        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {
        // Analyse your vulnerability

        // Local variables here for using when analysing the tools
        $output = "<div>";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "Nmap":
                    // Server OS / Service Overview
                    $output .= "<h3>Web Server Host Information:</h3>";

                    if (isset($tool->GetHostInfo()["Service Info"])) {
                        $output .= "<h4>Service Information:</h4>";
                        $output .= "<p>" . $tool->GetHostInfo()["Service Info"]["Host"] . "<br />";
                        $output .= $tool->GetHostInfo()["Service Info"]["OS"] . "<br />";
                        $output .= $tool->GetHostInfo()["Service Info"]["CPE"] . "</p>";
                    }                   

                    // OS Details
                    if(isset($tool->GetHostInfo()["OS Details"])) {
                        // OS type / version if it was determined.
                        $output .= "<h4>OS Details:</h4><p>" . $tool->GetHostInfo()["OS Details"] . "<br />";
                    } elseif (isset($tool->GetHostInfo()["Aggressive OS guesses"])) {
                        // if OS wasn't determined, show guesses.
                        $output .= "<h4>OS Details:</h4><p>No exact OS version match found.</p><p><span style='font-weight:bold;'>Aggressive OS Guesses:</span> " . $tool->GetHostInfo()["Aggressive OS guesses"] . "<br />";
                    }
                    $output .= "</p>";

                    // Open Ports
                    $output .= "<h3>Open Ports:</h3>";
                    $output .= "<ul>";
                    foreach ($tool->GetPorts() as $item) {
                        $output .= "<li>" . $item . "</li>";
                    }

                    $output .= "</div>";
                    break;
            }
        }
        // ++ All tools have been analysed at this point
        
        // calculate the severities and store
        $this->severity = 0;

        // remember to construct the HTML used within the report:
        //   (the final report generated, that includes ALL vulnerabilities, will consist of all of these html segments displayed together)
        //   (We'll standardise this later!)
        $this->html = $output;
    }
}
?>