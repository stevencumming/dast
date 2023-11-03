<?php


class TOOL_Nikto extends TOOL{
    /*
        Tool Name:              nikto
        Responsible:            SH (and whoever else wants to use)
        OpenProject Phase #:    

        Summary:
            nikto scans websites by analysing server responses to a series of HTTP requests, checking for outdated software and 
            misconfigurations, finding if there are common exploits available to take advantage of. Is able to also test for XSS and 
            SQL injection potential.

        Output (either array or just a boolean):
            Example output:
            + /path/to/web/file: A PHP backdoor was identified

    */
    private array $InsecDes;
    private array $VulnComp;
    private array $IDAuth;

    public function Execute() {
        echo "Executing nikto...";

        // this command will need to be edited to add in the target url, although I doubt it will ever work...

        //$command = 'perl nikto/program/nikto.pl -h' . $this->scan->getTarget() . '-Tuning 2 a b c';
        $command = "perl ./assets/nikto/program/nikto.pl -h " . $this->scan->getTarget() . " -Tuning 2 a b c";

        $this->InsecDes = [];
        $this->VulnComp = [];
        $this->IDAuth = [];

        exec($command, $CLI);

        $patternInsecureDesign = '#\/.git|Directory\sindexing\sfound|phpInfo\(\)|Readme|brute\sforce#';
        $patternVulnOutComp = '#outdated#';
        $patternIDAuthFailures = '#(login)#';
        
        foreach($CLI as $line){
            $line = preg_replace('/\+\s/', '', $line);
            $line = preg_replace('/OSVDB\-[\d]+\:\s/', '', $line);

            preg_match_all($patternInsecureDesign, $line, $result, PREG_SET_ORDER);
            if(isset($result[0])) {
                array_push($this->InsecDes, $line);
            }

            preg_match_all($patternVulnOutComp, $line, $result, PREG_SET_ORDER);
            if(isset($result[0])) {
                array_push($this->VulnComp, $line);
            }

            preg_match_all($patternIDAuthFailures, $line, $result, PREG_SET_ORDER);
            if(isset($result[0])) {
                array_push($this->IDAuth, $line);
            }
        }

        echo " Finished nikto.\n";
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
}
