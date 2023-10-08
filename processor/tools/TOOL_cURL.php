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
            NOTE FROM LC - Also using cURL however I also just need to check the regex when run with the -I flag and return a boolean if specific text matches 

    */
    private array $CLI;
    private bool $reply;

    public function Execute() {
        $this->reply = false;
        // this command will need to be edited to add in the target url, although I doubt it will ever work...
        $command = 'curl file://' . parse_url($this->scan->getTarget())["host"] . '/127.0.0.1/etc/passwd';
        exec($command, $CLI);

        $pattern = '#/root:/bin/bash#';
        
        foreach($CLI as $line) {
            preg_match_all($pattern, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                $this->reply = true;
            }
        }
        $this->redirect = [];
        // this command will need to be edited to add in the target url, although I doubt it will ever work...
        $command = 'curl -I http://127.0.0.1/mutillidae';
        exec($command, $CLI);

        $pattern = '#HTTP\/1.1 301 Moved Permanently#';
        
        foreach($CLI as $line) {
            preg_match_all($pattern, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                $pattern = '#Location: https#';        
                foreach($CLI as $line) {
                    preg_match_all($pattern, $line, $result, PREG_SET_ORDER, 0);
                    if(isset($result[0])) {
                        $pattern = '#Location:\s+([^\n]+)#';
                        preg_match_all($pattern, $line, $result, PREG_SET_ORDER, 0);
                        if(isset($result[0])) {
                            array_push($this->redirect, $result[0][1]);
                        }
                    }
            }
        }

    }
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