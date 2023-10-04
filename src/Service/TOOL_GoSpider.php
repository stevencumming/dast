<?php

namespace App\Service;

use App\Entity\Scan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class TOOL_GoSpider {
    /*
        Tool Name:              GoSpider
        Responsible:            SC
        OpenProject Phase #:    

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)

            https://github.com/jaeles-project/gospider



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

        // GoSpider
        $CONCURRENT_REQS = 10;          //  number of the maximum allowed concurrent requests of the matching domains
        $DEPTH = 0;                     //  MaxDepth limits the recursion depth of visited URLs. (Set it to 0 for infinite recursion) (default 1)
        $THREADS = 5;                   //  The number of threads to use
        $this->process = new Process(['gospider', '-s ' . $scan->getTarget(), , '-c ' . $CONCURRENT_REQS, '-d ' . $DEPTH, '-t ' . $THREADS, '--json']);


        // Tset the process timeout (max execution time)
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


        // The GoSpider output is already formatted as JSON

        echo $CLI;

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