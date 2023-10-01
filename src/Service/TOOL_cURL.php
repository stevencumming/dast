<?php

namespace App\Service;

use App\Entity\Scan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class TOOL_cURL {
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
    private $name;
    private Process $process;
    //private array $reply;
    private boolean $reply;

    // where required, you may have more complex data structures here!!
    //   (which is why we are returning this object JSON encoded)


    public function __construct($aName, Scan $scan){
        
        // Initialise the reply flag to false when the tool is created 
        $reply = false;
        // Initialise the process
        
        // Example: file listing (generic command plus argument)
        //$process = new Process(['ls', '-lsa']);
        $this->name = $aName;

        // curl of target 
        // TODO fine tune curl request to be a legitimate ssrf vulnerability that MAY work...
        // below is a placeholder command that uses the localhost as the url and gets your own etc/passwd
        $this->process = new Process(['curl', 'file://127.0.0.1/etc/passwd']); // idk if getScanId actually returns the scan object...


        // TODO Process timeout
        // https://symfony.com/doc/current/components/process.html#process-timeout
        $this->process->setTimeout(3600);
    }

    public function Execute() {
        // Run the process(es)
        $this->process->run();

        
        // TODO Process timeout
        // https://symfony.com/doc/current/components/process.html#process-timeout
        // while ($condition) {
        //     // ...
        
        //     // check if the timeout is reached
        //     $process->checkTimeout();
        
        //     usleep(200000);
        // }
        

        // If the process (tool) failed, throw an exception
        if (!$this->process->isSuccessful()) {
            throw new ProcessFailedException($this->process);
            // TODO write something to the output about how it failed.
        }
        
        // Process (tool) was successful, now extract the data
        $CLI = $this->process->getOutput();

        // do the whole preg_match regex stuff here...
        // for nslookup, the pattern would be something like:
        //   Non-authoritative answer:\sName:\s*(\S*)\sAddress:\s*(\S*)\sName:\s*(\S*)\sAddress:\s*(\S*)
        // use https://regex101.com/ or something similar and use their generation tool

        // the below will look for a common element of an etc/passwd file

        $pattern = '/root:/bin/bash';
        preg_match_all($pattern, $CLI, $matches, PREG_SET_ORDER, 0);

        // if the pattern above is found in the reply to the curl request then we know /etc/passwd is being displayed, set the reply flag to true so the ssrf vuln can use it
        if (isset($matches[0])) {

            $reply = true;

        }

 
        
    }

    public function GetName() {
        
        return $this->name;

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


    // Again, I need to stress here -- this ONLY processes the tool and formats the output (kind of like an API!)
    // There is NO mention of the vulnerability specifically here
    // Analysis for the vulnerability is handled in it's respective service
    
}