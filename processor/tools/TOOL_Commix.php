<?php
class TOOL_Commix extends TOOL {
    /*
        Tool Name:              TOOL_Commix
        Responsible:            SC
        OpenProject Phase #:    

        Summary:
            //
            
        Output (Object):
            getOutput() returns the scan result string.
    */
    private string $output;

    public function Execute() {
        echo "Executing Commix...";

        // sqlmap command
        $command = "python3 ./assets/commix/commix.py -u " . $this->scan->getTarget() . " --all --batch --crawl=2";
        // python3 commix.py -u stevencumming.io --all --batch --crawl=2
    
       
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
                $output .= "Commix Command Injection: no usable links found (with GET parameters).";
            }
            
        }

        $this->output = $output;
        
        echo " Finished Commix.\n";
    }

    public function getOutput(){
        return $this->output;
    }
}
?>