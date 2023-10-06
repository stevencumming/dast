<?php

namespace App\Service;

use App\Entity\Scan;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;


class TOOL_nikto extends TOOL{
    /*
        Tool Name:              nikto
        Responsible:            SH (and whoever else wants to use)
        OpenProject Phase #:    XXX

        Summary:
            nikto scans websites by analysing server responses to a series of HTTP requests, checking for outdated software and 
            misconfigurations, finding if there are common exploits available to take advantage of. Is able to also test for XSS and 
            SQL injection potential.

        Output (either array or just a boolean):
            Example output:
            + /path/to/web/file: A PHP backdoor was identified

    */

    private array $output;
    private bool $reply;

    public function Execute() {
        $this->reply = false;
        // this command will need to be edited to add in the target url, although I doubt it will ever work...

/*

-Tuning+:
            1     Interesting File / Seen in logs
            2     Misconfiguration / Default File
            3     Information Disclosure
            4     Injection (XSS/Script/HTML)
            5     Remote File Retrieval - Inside Web Root
            6     Denial of Service
            7     Remote File Retrieval - Server Wide
            8     Command Execution / Remote Shell
            9     SQL Injection
            0     File Upload
            a     Authentication Bypass
            b     Software Identification
            c     Remote Source Inclusion
            x     Reverse Tuning Options (i.e., include all except specified)

*/
//Insecure design
//$command = 'perl nikto/program/nikto.pl -h' . $this->scan->getTarget() . '-Tuning 1 2 5 7 8 0';

//Vulnerable Outdated components
//$command = 'perl nikto/program/nikto.pl -h' . $this->scan->getTarget() . '-Tuning b';

//ID Auth Failures
//$command = 'perl nikto/program/nikto.pl -h' . $this->scan->getTarget() . '-Tuning a';

        $command = 'perl nikto/program/nikto.pl -h' . $this->scan->getTarget() . '-Tuning x 4 6 9';
        $CLI = array();
        exec($command, $CLI);

        $patternComponents = '#OSVDB\d{1,4}:#';
        
        foreach($CLI as $line){

            preg_match_all($patternComponents, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                array_push($this->vulnTypes, $line);
            }
        }

        //$this->output = $results;
    }
    
    // below function deprecated
    public function getOutput() {
        //return $this->output;
    }
}