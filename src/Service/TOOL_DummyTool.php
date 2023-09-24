<?php

namespace App\Service;

use App\Entity\Tool;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class TOOL_DummyTool {
    /*
        Tool Name:              DummyTool
        Responsible:            AA
        OpenProject Phase #:    999

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)


        Output (JSON):
            ... describe in psuedocode
            $names = array of domain names
            $addresses = array of addresses (IPv4/6) corresponding to the matching $names[i]

    */
    private Process $process;
    private array $names;
    private array $addresses;
    // where required, you may have more complex data structures here!!
    //   (which is why we are returning this object JSON encoded)


    public function __construct(private Tool $tool){
        // Initialise the process
        
        // Example: file listing (generic command plus argument)
        //$process = new Process(['ls', '-lsa']);

        // Example: nslookup of Swinburne
        $this->process = new Process(['nslookup', 'swin.edu.au']);

        // Example: nslookup of target
        $this->process = new Process(['nslookup', $tool->getScanId()->getTarget()]); // idk if getScanId actually returns the scan object...

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

        $pattern = '/Non-authoritative answer:\sName:\s*(\S*)\sAddress:\s*(\S*)\sName:\s*(\S*)\sAddress:\s*(\S*)/m';
        preg_match_all($pattern, $CLI, $matches, PREG_SET_ORDER, 0);

        // normally, I'm not a fan of ternary or single-line if statements.. but it makes sense here.
        if (isset($matches[0])) array_push($this->names, $matches[0]);
        if (isset($matches[1])) array_push($this->addresses, $matches[0]);
        if (isset($matches[2])) array_push($this->names, $matches[1]);
        if (isset($matches[3])) array_push($this->addresses, $matches[1]);
    }


    public function Output() {
        // Persist this whole object as JSON
        // TODO
        // return json_encode($this);

        
    }


    // Again, I need to stress here -- this ONLY processes the tool and formats the output (kind of like an API!)
    // There is NO mention of the vulnerability specifically here
    // Analysis for the vulnerability is handled in it's respective service
    
}