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
    
    private array $CLI;

    private array $InsecDes;
    private array $VulnComp;
    private array $IDAuth;

    public function Execute() {
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

Insecure design:                    2 c
Vulnerable and outdated components: b
Identification and auth failures:   a
Soft and Data integrity failures:   5 8 0 **Not useful**

*/

        //$command = 'perl nikto/program/nikto.pl -h' . $this->scan->getTarget() . '-Tuning 2 a b c';
        $command = 'perl nikto/program/nikto.pl -h https://localhost/mutillidae/ -Tuning 2 a b c';

        $this->InsecDes = [];
        $this->VulnComp = [];
        $this->IDAuth = [];

        exec($command, $CLI);

        $patternInsecureDesign = '#\/.git|Directory indexing found|phpInfo\(\)|Readme|brute force#';
        $patternVulnOutComp = '#outdated#';
        $patternIDAuthFailures = '#login#';
        
        foreach($CLI as $line){
            $line = preg_replace('/\+', '', $line);
            $line = preg_replace('/OSVDB\-[\d]+\: ', '', $line);

            preg_match_all($patternInsecureDesign, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                array_push($this->InsecDes, $line);
            }
            preg_match_all($patternVulnOutComp, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                array_push($this->VulnComp, $line);
            }
            preg_match_all($patternIDAuthFailures, $line, $result, PREG_SET_ORDER, 0);
            if(isset($result[0])) {
                array_push($this->IDAuth, $line);
            }
        }

        //$this->output = $results;
    }

    public function getInsecDes(){
        return $this->InsecDes;
    }

    public function getVulnComp(){
        return $this->VulnComp;
    }

    public function getIDAuth(){
        return $this->IDAuth;
    }
    
    // below function deprecated
    public function getOutput() {
        //return $this->output;
    }
}
