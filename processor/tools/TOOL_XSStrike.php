<?php


class TOOL_XSRFProbe extends TOOL {
    /*
        Tool Name:              XSStrike
        Responsible:            LC
        OpenProject Phase #:    XXX

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)
            A tool used to scan for XSS vulnerabilities that can also generate a payload of attacks for the vulnerability


        Output (array): the vulnTypes array will 
            ... describe in psuedocode
            $vulnTypes: an array containing the types of csrf vulnerability found
           
    */
    private array $CLI;
    private array $vulnTypes;

    public function Execute() {
        // Run the process(es)
        $this->vulnTypes = [];

        $command = 'python xsstrike.py -u "http://example.com/page.php" --crawl';
        exec($command, $CLI);
        
        $patternTypes = '#Possible Vulnerability Type:#';

        foreach($CLI as $line){

            preg_match_all($patternTypes, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                // it actually might be better to push the whole line to the array since the match string doesn't really give us anything useful
                array_push($this->vulnTypes, $line);
            }

        }
    
    }

    // Getter for vulnTypes array
    // no need for setter since only the tool class can set its arrays


    public function getVulnTypes() {

        return $this->vulnTypes;

    }

    // won't need the below function if we aren't persisting to database
    public function Output() {
        // Persist this whole object as JSON
        // TODO
        // return json_encode($this);
   
    }
}