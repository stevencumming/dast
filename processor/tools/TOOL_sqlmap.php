<?php
class TOOL_sqlmap extends TOOL {
    /*
        Tool Name:              sqlmap
        Responsible:            SC
        OpenProject Phase #:    

        Summary:
            //
            
        Output (Object):
            getOutput() returns the scan result string.

    */
    private string $output;

    public function Execute() {
        echo "Executing sqlmap...";

        // sqlmap command
        $command = "python3 ~/sqlmap-dev/sqlmap.py -u " . $this->scan->getTarget() . " --crawl=5 --batch --level=3 --risk=3 -dbs";
        // python3 ~/sqlmap-dev/sqlmap.py -u https://web.ctflearn.com/web4/ --crawl=2 --batch
        // http://hackbox-1.duckdns.org:3000/#/

        //python3 ~/sqlmap-dev/sqlmap.py -u http://hackbox-1.duckdns.org:3000/#/ --crawl=5 --batch --level=3 --risk=3 -dbs
       
        // Initialise the output buffer (array of lines) and execute the tool
        $output = "";
        $CLI = array();
        exec($command, $CLI);

        // Regex patterns
        $pattern_noMatch = '/\[WARNING\] no usable links found \(with GET parameters\)/m';
      
        // Gor each line reformat and store in output array
        foreach ($CLI as $line) {

            //echo $line . "\n";

            preg_match_all($pattern_noMatch, $line, $result);
            if(isset($result[0][0])) {
                // no useable links found
                $output .= "SQL Vulnerability Checker: no usable links found (with GET parameters)";
            }
            
        }

        $this->output = $output;
        
        echo " Finished sqlmap.\n";
    }

    public function getOutput(){
        return $this->output;
    }
}
?>