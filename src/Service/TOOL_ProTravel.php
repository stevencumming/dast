<?php

namespace App\Service;

use App\Entity\Tool;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class TOOL_ProTravel {
    /*
        Tool Name:              ProTravel
        Responsible:            PY
        OpenProject Phase #:    

        Summary:
            Python CLI tool to assess whether a website is susceptible to path traversal vulnerability.


        Output:
            The output produced will depend on whether a vulnerability is found or not.
            A "Done" output from ProTravel indicates there is no present vulnerability.
            An output displaying either paths or files indicates that the program has found the presence of sensitive information through a path traversal exploit.

    */
    
    private $name;
    private $output;
    private Process $process;

    public function __construct(
        Tool $tool,
        Process $process,
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
            $this->output = "Process failed.";
            throw new ProcessFailedException($this->process);
            // TODO write something to the output about how it failed.
        }
        else {
            // Process (tool) was successful, now extract the data
            $this->output = $this->process->getOutput();
        }
        
    }


    public function Output() {
        return $this->output;
    }


    // Again, I need to stress here -- this ONLY processes the tool and formats the output (kind of like an API!)
    // There is NO mention of the vulnerability specifically here
    // Analysis for the vulnerability is handled in it's respective service
    
}