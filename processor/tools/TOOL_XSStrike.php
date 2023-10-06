<?php


class TOOL_XSRFProbe extends TOOL {
    /*
        Tool Name:              XSStrike
        Responsible:            LC
        OpenProject Phase #:    428

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
        $this->component = [];
        $this->cves = [];

        $command = 'python xsstrike.py -u "http://127.0.0.1/mutillidae/" --crawl';
        exec($command, $CLI);
        
        $patternComponents = '#Vulnerable component:.*+#';
        $patternCves = '#CVE:.*+#';

        foreach($CLI as $line){

            preg_match_all($patternComponents, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                array_push($this->vulnTypes, $line);
            }

            preg_match_all($patternCves, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                array_push($this->vulnTypes, $line);
            }

        }
    
    }

    // Getter for vulnTypes array
    // no need for setter since only the tool class can set its arrays


    public function getComponents() {

        return $this->components;

    }

    public function getCves() {

        return $this->cves;

    }

    // won't need the below function if we aren't persisting to database
    public function Output() {
        // Persist this whole object as JSON
        // TODO
        // return json_encode($this);
   
    }
}