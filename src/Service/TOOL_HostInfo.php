<?php

namespace App\Service;

use App\Entity\Scan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class TOOL_DummyTool {
    /*
        Tool Name:              DummyTool
        Responsible:            AA
        OpenProject Phase #:    999

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)


        Output (Object):
            ... describe in psuedocode
            $domain_names = array of domain names
            $addresses = array of addresses (IPv4/6) corresponding to the matching $names[i]
    */

    /*      [DELETEME] REFACTOR SEP28: The tool output no longer gets persisted to the database in between being executed (here)
            and analysed (in VULN_xxx).

            The tool object is simply passed in ScanProcessor to the VULN_xxx service
            Actually, an array of tools are passed. The array could have one tool, it could have n tools.
            They are indexed in VULN_xx by their **name**
    */

    // Name (what the tool is indexed by in VULN_xxx)
    private string $name;

    // ==== Tool Output objects ====
    //   (used to grab the data in VULN_xxx when this object is passed to it)
    //   So don't forget getters
    
    private array $addresses;
    private array $domain_names;
    // where required, you may have more complex data structures here!

    
    // ==== Symfony Processes ====
    private Process $process;
    

    
    public function __construct(
        $aName,
        Scan $scan
    ){
        // Name the tool on creation
        //   so that it ** can be indexed in VULN_xxx switch statement **
        $this->name = $aName;


        // Initialise the Symfony process(es)

        // Example: file listing (generic command plus argument)
        //$process = new Process(['ls', '-lsa']);

        // Example: nslookup of Swinburne
        $this->process = new Process(['nslookup', 'swin.edu.au']);

        // Example: nslookup of target
        $this->process = new Process(['nslookup', $scan->getTarget()]);

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

	/**
	 * @return array
	 */
	public function getAddresses(): array {
		return $this->addresses;
	}

	/**
	 * @return array
	 */
	public function getDomain_names(): array {
		return $this->domain_names;
	}

    /**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}
}