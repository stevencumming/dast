<?php

namespace App\Service;

use App\Entity\Tool;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class TOOL_XSRFProbe {
    /*
        Tool Name:              XSRFProbe
        Responsible:            MG (and whoever else wants to use)
        OpenProject Phase #:    XXX

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)
            A recon type scanner that looks at a target machine for any potential cross site request forgery vulnerabilities
            will obviously be used for the csrf vulnerability but may be used for others 


        Output (JSON):
            ... describe in psuedocode
            $vulnTypes: the type of csrf vulnerability found
           

    */
    private Process $process;
    private array $vulnTypes;
    // where required, you may have more complex data structures here!!
    //   (which is why we are returning this object JSON encoded)


    public function __construct(private Tool $tool){
        // Initialise the process
        
        // Example: file listing (generic command plus argument)
        //$process = new Process(['ls', '-lsa']);

        // Example: nslookup of Swinburne
        //$this->process = new Process(['nslookup', 'swin.edu.au']);

        $this->process = new Process(['xsrfprobe', '-u', $tool->getScanId()->getTarget()]); // idk if getScanId actually returns the scan object...

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
        
        // will need to work out proper regex but this is the phrase that flags a potential vuln
        $pattern = 'Possible Vulnerability Type:';
        preg_match_all($pattern, $CLI, $matches, PREG_SET_ORDER, 0);

        // normally, I'm not a fan of ternary or single-line if statements.. but it makes sense here.
        if (isset($matches[0])) array_push($this->vulnTypes, $matches[0]);
        if (isset($matches[1])) array_push($this->vulnTypes, $matches[1]);
        // might be worth implementing a for each loop to just push any finding to the arrray         
    
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