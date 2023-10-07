<?php


class TOOL_XSStrike extends TOOL {
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
    private array $components;
    private array $cves;
    

    public function Execute() {
        
        // Run the process(es)
        $this->components = [];
        $this->cves = [];

        $command = 'python3 ./assets/XSStrike/xsstrike.py -u "http://127.0.0.1/mutillidae/" --crawl';
        exec($command, $CLI);
        
        function stripAnsiEscapeCodes($text) {
            return preg_replace('/\e\[[\d;]+m/', '', $text);
        }
        
        $patternComponents = '#Vulnerable component:\s+([^\n]+)#';
        $patternCves = '#CVE:\s+([^\n]+)#';

        foreach($CLI as $line){
            $line = stripAnsiEscapeCodes($line);

            preg_match_all($patternComponents, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                array_push($this->components, $result[0][1]);
            }

            preg_match_all($patternCves, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                array_push($this->cves, $result[0][1]);
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