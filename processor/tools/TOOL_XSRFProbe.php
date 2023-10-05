<?php


class TOOL_XSRFProbe extends TOOL {
    /*
        Tool Name:              XSRFProbe
        Responsible:            MG (and whoever else wants to use)
        OpenProject Phase #:    XXX

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)
            A recon type scanner that looks at a target machine for any potential cross site request forgery vulnerabilities
            will obviously be used for the csrf vulnerability but may be used for others 


        Output (array): the vulnTypes array will 
            ... describe in psuedocode
            $vulnTypes: an array containing the types of csrf vulnerability found
           
    */
    private array $CLI;
    private array $vulnTypes;

    public function Execute() {
        // Run the process(es)
        $this->vulnTypes = [];
        // on my machine there is an initial deprecation warning message output to the command line about pkg_resources, doesn't really do anything it's just annoying
        // ALSO keep in mind that my version of xsrfprobe is edited to remover the colour values from the ouput so we will need to implement this on the DAST machine as well
        // below command only executes againt your own local host, will need to edit for actual scan
        $command = 'xsrfprobe -u 127.0.0.1';
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