<?php
class TOOL_Commix extends TOOL {
    /*
        Tool Name:              TOOL_Commix
        Responsible:            SC
        OpenProject Phase #:    

        Summary:
            //
            
        Output (Object):
            //
    */
    private string $output;

    public function Execute() {
        echo "Executing Commix...";

        // sqlmap command
        $command = "python3 ~/commix/commix.py -h";
        // -u " . $this->scan->getTarget() . " -crawl=5 --batch --level=3 --risk=3 -dbs";

        // python3 ~/sqlmap-dev/sqlmap.py -u https://web.ctflearn.com/web4/ --crawl=2 --batch
        // http://hackbox-1.duckdns.org:3000/#/

        //python3 ~/sqlmap-dev/sqlmap.py -u http://hackbox-1.duckdns.org:3000/#/ --crawl=5 --batch --level=3 --risk=3 -dbs
       
        // Initialise the output buffer (array of lines) and execute the tool
        $output = "";
        $CLI = array();
        exec($command, $CLI);

        // Regex patterns
        
      
        // Gor each line reformat and store in output array
        foreach ($CLI as $line) {

            //$output .= $line . "\n";

            // preg_match_all($pattern_noMatch, $line, $result);
            // if(isset($result[0][0])) {
            //     // no useable links found
            //     $output .= "SQL Vulnerability Checker: no usable links found (with GET parameters)";
            // }
            
        }

        $this->output = $output;
        
        echo " Finished Commix.\n";
    }

    public function getOutput(){
        return $this->output;
    }
}
?>