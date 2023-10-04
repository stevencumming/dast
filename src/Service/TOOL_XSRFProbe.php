<?php

namespace App\Service;

use App\Entity\Scan;
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


        Output (array): the vulnTypes array will 
            ... describe in psuedocode
            $vulnTypes: an array containing the types of csrf vulnerability found
           

    */
    private $name;
    private Process $process;
    private array $vulnTypes;
    // where required, you may have more complex data structures here!!
    //   (which is why we are returning this object JSON encoded)


    public function __construct($aName, Scan $scan){
        // Initialise the process
        
        // Example: file listing (generic command plus argument)
        //$process = new Process(['ls', '-lsa']);

        // Example: nslookup of Swinburne
        //$this->process = new Process(['nslookup', 'swin.edu.au']);

        $this->name = $aName;

        $this->process = new Process(['xsrfprobe', '-u', $scan->getScanId()->getTarget()]); // idk if getScanId actually returns the scan object...

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
        // have changed the argument of preg_match to take in the vulnTypes array
        $patternTypes = 'Possible Vulnerability Type:';
        preg_match_all($patternTypes, $CLI, $vulnTypes, PREG_SET_ORDER, 0);

        // not really sure if we will need the below statements if we can just push straight to the vulnTypes array
        // normally, I'm not a fan of ternary or single-line if statements.. but it makes sense here.
        //if (isset($matches[0])) array_push($this->vulnTypes, $matches[0]);
        //if (isset($matches[1])) array_push($this->vulnTypes, $matches[1]);
         
    
    }

    // Getter for vulnTypes array
    // no need for setter since only the tool class can set its arrays

    public function getName() {
        
        return this->name;

    }

    public function getVulnTypes() {

        return $this->vulnTypes;

    }

    // won't need the below function if we aren't persisting to database
    public function Output() {
        // Persist this whole object as JSON
        // TODO
        // return json_encode($this);

        
    }


    // Again, I need to stress here -- this ONLY processes the tool and formats the output (kind of like an API!)
    // There is NO mention of the vulnerability specifically here
    // Analysis for the vulnerability is handled in it's respective service
    
}