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

        //Calls python3 tool XSStrike and crawls the site looking for vulnerabilities
        $command = 'python3 ./assets/XSStrike/xsstrike.py -u'. parse_url($this->scan->getTarget())["host"] . '--crawl';
        exec($command, $CLI);
        
        // Tool nativley outputs in colour. This gets formatted weirdly in HTML so gets stripped with regex here
        function stripAnsiEscapeCodes($text) {
            return preg_replace('/\e\[[\d;]+m/', '', $text);
        }

        // Regex patterns for vulnerable components and their associated CVE's
        $patternComponents = '#Vulnerable component:\s+([^\n]+)#';
        $patternCves = '#CVE:\s+([^\n]+)#';

        // Calls function to strip the colour from the output
        foreach($CLI as $line){
            $line = stripAnsiEscapeCodes($line);

            // Checks to see if there were any vulnerable components found
            // Pushes to a multi dimensional array as only the second regex group should be matched, filtering out unnecessary text 
            preg_match_all($patternComponents, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                array_push($this->components, $result[0][1]);
            }

            // Checks to see if any CVE's were found relating to the vulnerable components
            // Pushes to a multi dimensional array as only the second regex group should be matched, filtering out unnecessary text 
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