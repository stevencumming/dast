<?php

namespace App\Service;

use App\Entity\Tool;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class TOOL_Nmap {
    /*
        Tool Name:              Nmap
        Responsible:            MG (and whoever else will use it)
        OpenProject Phase #:    XXX

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)
            Nmap is a versatile port scanner that can also be used for things like vulnerability checking and victim host information gathering
            This tool will be used by multiple different vulnerabilities


        Output (JSON):
            ... describe in psuedocode
            $ports- open ports on the target machine
            $hostInfo- recon info on the host machine (os etc.)
            $cves- cves found with nmap vulners against each open port
            feel free to add more

    */
    private Process $process;
    private array $ports;
    private array $hostInfo;
    private array $cves;
    // where required, you may have more complex data structures here!!
    //   (which is why we are returning this object JSON encoded)


    public function __construct(private Tool $tool){
        // Initialise the process
        
        // Example: file listing (generic command plus argument)
        //$process = new Process(['ls', '-lsa']);

        
        $this->process = new Process(['nmap', '-T4', '-A', '--script vulners', $tool->getScanId()->getTarget()]); // idk if getScanId actually returns the scan object...

        // ... where there are multiple processes, name them '$process_nslookup' and '$process_namp' for example.
        // nmap -T4 -A --script vulners 192.168.5.134


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

        //not sure if we want to use multiple patterns like this, will have to look into the preg_match_all function more deeply to determine

        // regex looking for 1-5 digits followed by / followed by tcp or udp
        $patternPorts = '\d{1,5}[/](tcp|udp)';
        // regex looking for CVE followed by -, then 4 numbers, another - and then 4 to 5 numbers then white space
        $patternCves = 'CVE-\d{4}-\d{4,5}\s';
        // regex wil need to check for multiple things so can't just use one pattern
        $patternHostOS = 'TODO';
        
        $patternServerVersion = 'TODO';

        // TODO need to figure out how this needs to be implemented, whether or not there should be multiple preg_match_all or not
        
        preg_match_all($patternPorts, $CLI, $ports, PREG_SET_ORDER, 0);
        preg_match_all($patternCves, $CLI, $cves, PREG_SET_ORDER, 0);
        preg_match_all($patternHostOS, $CLI, $hostInfo, PREG_SET_ORDER, 0);
        preg_match_all($patternServerVersion, $CLI, $hostInfo, PREG_SET_ORDER, 0);


        // if multiple preg_match statements can be used I don't think we will need the below, since those statements populate their respective arrays if they are taken in as arguments 
        // instead of using a generic "matches" array and then adding certain entries to the correct arrays

        // normally, I'm not a fan of ternary or single-line if statements.. but it makes sense here.
        //if (isset($matches[0])) array_push($this->names, $matches[0]);
        //if (isset($matches[1])) array_push($this->addresses, $matches[0]);
        //if (isset($matches[2])) array_push($this->names, $matches[1]);
        //if (isset($matches[3])) array_push($this->addresses, $matches[1]);
    }

    // I have added a few getters here that are for specific arrays like ports and cves etc, it might be helpful to have for when the vulnerabilities need to be called they can just check relevant output 
    

    public function GetPorts() {
        
        return this->ports;

    }

    public function GetCVEs() {
    
        return $this->cves;

    }

    public function GetHostInfo() {

        return $this->hostInfo;

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