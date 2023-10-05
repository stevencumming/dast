<?php
class TOOL_GoSpider extends TOOL {
    /*
        Tool Name:              GoSpider
        Responsible:            SC
        OpenProject Phase #:    

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)

            https://github.com/jaeles-project/gospider


        Output (Object):
            ... describe in psuedocode
            $output as an array with three elements, which are themselves arrays of objects from GoSpider's output.
                $output["url"]
                $output["form"]
                $output["other"]
    */

    public function Execute() {        
        // Start output buffer and execute tool
        ob_start();
        passthru("PATH=/usr/local/go/bin gospider -s " . $this->scan->getTarget() . " -c 10 -d 0 -t 5 --json");
        // Split the curly braces out
        preg_match_all('~{[^}]*}~', ob_get_clean(), $output);

        // Initialize arrays for storing the output data
        $results["url"] = array();
        $results["form"] = array();
        $results["other"] = array();

        // Loop through each line of the CLI output
        for ($i=0; $i < sizeof($output[0]); $i++) {
            // Decode each line of the output buffer to an object
            $foundObject = json_decode($output[0][$i]);

            // sort by type into an array
            switch ($foundObject->type) {
                case 'url':
                    array_push($results["url"], $foundObject);
                    break;

                case 'form':
                    array_push($results["form"], $foundObject);
                    break;
                
                default:
                    array_push($results["other"], $foundObject);
                    break;
            }
        }
        
        // Store in $output field of tool.
        $this->output = $results;
    }


    public function getOutput(){
        return $this->output;
    }
}
?>