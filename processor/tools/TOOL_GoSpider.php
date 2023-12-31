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
    private array $output;

    public function Execute() {
        echo "Executing Gospider...";

        // Initialize arrays for storing the output data
        $results["url"] = array();
        $results["form"] = array();
        $results["other"] = array();

        // Initialise the output buffer (array of lines) and execute the tool
        $command = "PATH=/usr/local/go/bin gospider -s " . $this->scan->getTarget() . " -c 10 -d 5 -t 50 --json";

        $CLI = array();
        exec($command, $CLI);

        foreach ($CLI as $line) {
            // Decode each line of the output buffer to an object
            $foundObject = json_decode($line);

            // echo "\n\n\n\n";
            // var_dump($foundObject);
            // echo "\n\n\n\n";

            if (isset($foundObject->type)) {
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
            
        }

        $this->output = $results;

        echo " Finished Gospider.\n";    
    }

    public function getOutput(){
        return $this->output;
    }
}
?>