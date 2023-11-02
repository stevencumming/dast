<?php
class VULN_Sitemap extends VULN {
    /*
        Vulnerability:          Sitemap
        Responsible:            SC
        OpenProject Phase #:    999

        Summary:
            Discover the sitemap / directory structure of a target website.

        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {
        // Analyse your vulnerability

        // Local variables here for using when analysing the tools
        $output = "<div style='text-align: left;'>";

        // Start by reading the data from your tool(s)
        foreach ($this->tools as $tool) {
            // Loop through each of the tools that were passed to this vulnerability
            // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
            switch ($tool->getName()) {
                case "Gobuster":
                    $output .= "<h3>Discovered Sitemap:</h3>";

                    // Explainer
                    //$output .= "<p>A single-depth dictionary discover method is used to brute-force the files and directories of specified extensions. Additionally, a crawling technique is used to capture... TODO</p>";

                    // List each of the files / directories
                    $output .= "<ul>";
                    foreach ($tool->getOutput() as $item) {
                        // for each of the files and directories
                        $output .= "<li>";
                        
                        // list the file / directory
                        $output .= "<span style='font-weight:bold;'>" . $item["URL"] . "</span>";

                        // extra information, if available (open).
                        if(isset($item["status code"]) || isset($item["size"])) {
                            $output .= "<ul>";
                        }

                        // if the status code was captured, display it
                        if(isset($item["status code"])) {
                            $output .= "<li>";
                            $output .= "<span style='font-weight:bold;'>Status Code:</span> " . $item["status code"] . " (" . $this->LookupHttpCode($item["status code"]) . ")";
                            $output .= "</li>";
                        }

                        // if the size was captured, display it
                        if(isset($item["status code"])) {
                            $output .= "<li>";
                            $output .= "<span style='font-weight:bold;'>Size:</span> " . $item["size"] . " bytes";
                            $output .= "</li>";
                        }

                        // extra information, if available (close).
                        if(isset($item["status code"]) || isset($item["size"])) {
                            $output .= "</ul>";
                        }
                    }
                    $output .= "</ul>";
                    
                    break;
            }
        }

        $output .= "</div>";



        

        // ++ All tools have been analysed at this point
        
        // calculate the severities and store
        $this->severity = 0;

        // remember to construct the HTML used within the report:
        //   (the final report generated, that includes ALL vulnerabilities, will consist of all of these html segments displayed together)
        //   (We'll standardise this later!)
        //$this->html = $output;
        $this->html = $output;
    }

    private function LookupHttpCode($code) {
        // Helper function to return the http status code name
        switch ($code) {
            case 100: return 'Continue'; 
            case 101: return 'Switching Protocols'; 
            case 200: return 'OK'; 
            case 201: return 'Created'; 
            case 202: return 'Accepted'; 
            case 203: return 'Non-Authoritative Information'; 
            case 204: return 'No Content'; 
            case 205: return 'Reset Content'; 
            case 206: return 'Partial Content'; 
            case 300: return 'Multiple Choices'; 
            case 301: return 'Moved Permanently'; 
            case 302: return 'Moved Temporarily'; 
            case 303: return 'See Other'; 
            case 304: return 'Not Modified'; 
            case 305: return 'Use Proxy'; 
            case 400: return 'Bad Request'; 
            case 401: return 'Unauthorized'; 
            case 402: return 'Payment Required'; 
            case 403: return 'Forbidden'; 
            case 404: return 'Not Found'; 
            case 405: return 'Method Not Allowed'; 
            case 406: return 'Not Acceptable'; 
            case 407: return 'Proxy Authentication Required'; 
            case 408: return 'Request Time-out'; 
            case 409: return 'Conflict'; 
            case 410: return 'Gone'; 
            case 411: return 'Length Required'; 
            case 412: return 'Precondition Failed'; 
            case 413: return 'Request Entity Too Large'; 
            case 414: return 'Request-URI Too Large'; 
            case 415: return 'Unsupported Media Type'; 
            case 500: return 'Internal Server Error'; 
            case 501: return 'Not Implemented'; 
            case 502: return 'Bad Gateway'; 
            case 503: return 'Service Unavailable'; 
            case 504: return 'Gateway Time-out'; 
            case 505: return 'HTTP Version not supported'; 
            default:
                return 'Unknown http status code "' . htmlentities($code);
        }        
    }

}
?>