<?php

class TOOL_cURL extends TOOL {
    /*
        Tool Name:              cURL
        Responsible:            MG (and whoever else wants to use)
        OpenProject Phase #:    XXX

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)
            cURL is a way to send requests to a web server using specified headers and bodies, and getting the contents of the reply
            This will be helpful for the server side request forgery vulnerabilty as it can be used to send a malicious request to a server
            and determine whether it is vulnerable based on the reply received


        Output (either array or just a boolean):
            ... describe in psuedocode
            $reply: I'm thinking that it might be simpler to just return a boolean if the regex for etc/passwd matches instead of an array 
            but if anyone else is going to use cURL then that will change 

    */
    private array $CLI;
    private bool $reply;

    public function Execute() {
        $this->reply = false;
        // this command will need to be edited to add in the target url, although I doubt it will ever work...
        $command = 'curl file://127.0.0.1/etc/passwd';
        exec($command, $CLI);

        $pattern = '#/root:/bin/bash#';
        
        foreach($CLI as $line) {
            preg_match_all($pattern, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                $this->reply = true;
            }
        }

    }

    public function GetReply() {

        return $this->reply;

    }
    
    // below function deprecated
    public function Output() {
        // Persist this whole object as JSON
        // TODO
        // return json_encode($this);

        // or if we want to do regex checking in the vuln class we should just return the $reply value
    }

}