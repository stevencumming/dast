<?php
class VULN_SecurityMscfg extends VULN{
    /*
        Vulnerability:          Security Misconfiguration
        Responsible:            MG
        OpenProject Phase #:    451

        Summary:
            ... quick summary on the vulnerability and what tools are required
            Broad term for any kind of poor configuration leaving systems vulnerable
            This includes (but is not limited to) default credentials, out of date software, unnecessary ports being open etc.
            Implementation of this vulnerability will involve the use of Nmap with the vulners script, as well as (potentially) the results of a directory busting tool.


        Output (HTML): TODO
            HTML formatted output to go straight into the Report.
    */


    // Information output
   public function Analyse() {
        // Analyse your vulnerability

        // Local variables here for using when analysing the tools
        // xxxx
        $nmapOutput = "";
        $dirbusterOutput = "";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                
                case "Nmap":
			// if the array of cves returned by nmap tool isn't empty then we know something was found
                    if (isset($tool->getCVEs()[0])) {
			    // Just using the overall number of cves found since in my example there were 60+, don't want to clutter the html
                        $nmapOutput = "There were " . count($tool->getCVEs()) . " potential CVEs found during port scanning.";
                        $nmapOutput .= "</p><ul>";
                        foreach($tool->GetCVEs() as $cve){
                            $nmapOutput .= "<li>" . $cve . "</li>";
                        }
                        $nmapOutput .= "</ul><p>";
		    }else{
			    $nmapOutput = "There were no CVEs found during port scanning.";
		    }
                    break;
                case "Gobuster":

                    // SC

                    // admin directory located flag
                    $admin_located = FALSE;
                    $admin_url = "";

                    // loop through each of the found files and directories
                    foreach ($tool->getOutput() as $item) {
                        // for each of the files and directories

                        // $item["URL"] has the full URL
                        // https://www.php.net/manual/en/function.parse-url.php
                        // parse_url($item["URL"])["path"] would = "/admin" for example.org/admin

                        // Searching for that exact string (either "/admin/" or "/admin") here should be sufficient to detect if a /admin/ page exists on the target host
                        if ((parse_url($item["URL"])["path"] == "/admin/") || (parse_url($item["URL"])["path"] == "/admin")) {
                            $admin_located = TRUE;
                            $admin_url = $item["URL"];
                        }
                    }

                    if ($admin_located) {
                        $dirbusterOutput = "<br/><span style='color:darkred'>An Admin page was detected on the target host.</span><br/>The admin page URL was found: <span style='font-weight:bold'><ul><li>" . $admin_url . "</li></ul></span>";
                    } else {
                        $dirbusterOutput = "No admin page was found in the target directories";
                    }

                    break;  // don't forget to break
                // we don't really need a default case, the condition should never occur.
            }
        }

       
        // do we still want to have output for vulnerabilities that aren't found?
        // remember to construct the HTML used within the report:
        //   (the final report generated, that includes ALL vulnerabilities, will consist of all of these html segments displayed together)
        //   (We'll standardise this later!)
        $this->html = "<p>Security Misconfiguration results: " . $nmapOutput . " \n " . $dirbusterOutput . "</p>";

    }
}
?>

