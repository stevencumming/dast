<?php

namespace App\Service;

use App\Entity\Tool;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class TOOL_ProTravel {
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
    
    // where required, you may have more complex data structures here!!
    //   (which is why we are returning this object JSON encoded)

    private $cliOutput;

    public function __construct(
        private Tool $tool,
        private Process $process
        ){
    }

    public function Execute() {
        // Initialise the process

        $this->process = new Process(['python protravel.py', $tool->getScanId()->getTarget() . "?filename=../../../../.."]); // idk if getScanId actually returns the scan object...

        // ... where there are multiple processes, name them '$process_nslookup' and '$process_namp' for example.
        
        
        // TODO Process timeout
        // https://symfony.com/doc/current/components/process.html#process-timeout
        $this->process->setTimeout(3600);
        
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
            $cliOutput = "Process failed.";
            throw new ProcessFailedException($this->process);
            // TODO write something to the output about how it failed.
        }
        else {
            // Process (tool) was successful, now extract the data
            $cliOutput = $this->process->getOutput();
        }
        
    }


    public function Output() {
        return $cliOutput;
    }


    // Again, I need to stress here -- this ONLY processes the tool and formats the output (kind of like an API!)
    // There is NO mention of the vulnerability specifically here
    // Analysis for the vulnerability is handled in it's respective service
    
}