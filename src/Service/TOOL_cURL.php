<?php

namespace App\Service;

use App\Entity\Tool;
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


        Output (JSON): TODO
            ... describe in psuedocode
            $reply = the reply to the curl request 

    */
    private Process $process;
    private array $reply;
    // where required, you may have more complex data structures here!!
    //   (which is why we are returning this object JSON encoded)


    public function __construct(private Tool $tool){
        // Initialise the process
        
        // Example: file listing (generic command plus argument)
        //$process = new Process(['ls', '-lsa']);

        // Example: nslookup of Swinburne
        //$this->process = new Process(['nslookup', 'swin.edu.au']);

        // curl of target 
        // TODO actually work out what the curl request needs to look like for both the header and the body
        $this->process = new Process(['curl', 'arguments', $tool->getScanId()->getTarget()]); // idk if getScanId actually returns the scan object...

        
        // There shouldn't be multiple processes though right? It's just 1 per tool?
        // ... where there are multiple processes, name them '$process_nslookup' and '$process_namp' for example.


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

        // this check may need to be moved to the ssrf vuln class depending on how we want to implement the regex checking

        $pattern = 'The pattern that matches a successful request';
        preg_match_all($pattern, $CLI, $matches, PREG_SET_ORDER, 0);

     
        if (isset($matches[0])) array_push($this->reply, $matches[0]);
        
    }


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