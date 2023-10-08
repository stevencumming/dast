<?php

class TOOL_Nmap extends TOOL {
    /*
        Tool Name:              Nmap
        Responsible:            MG, SC
        OpenProject Phase #:    

        Summary:
            Nmap is a versatile port scanner that can also be used for things like vulnerability checking and victim host information gathering
            This tool will be used by multiple different vulnerabilities


        Output (arrays): the arrays that are populated after the process has been run will be used by each vulnerability that requires them
            $ports- open ports on the target machine
            $cves- cves found with nmap vulners against each open port
            $hostInfo- recon info on the host machine (os etc.)

            For example:
            $hostInfo[OS Details] => Microsoft Windows Server 2016
            $hostInfo[Service Info][OS] => Windows
            $hostInfo[Service Info][CPE] => cpe:/o:microsoft:windows
            $hostInfo[Aggressive OS guesses] => Linux 2.6.32 (94%), Linux 3.10 - 4.11 (94%), Linux 3.2 - 4.9 (94%), Linux 3.4 - 3.10 (94%), Linux 2.6.32 - 3.10 (93%), Linux 2.6.32 - 3.13 (93%), Linux 3.10 (93%), Synology DiskStation Manager 5.2-5644 (92%), Linux 2.6.22 - 2.6.36 (91%), Linux 2.6.39 (91%)

     */
    private array $ports;
    private array $hostInfo;
    private array $cves;
    // where required, you may have more complex data structures here!!
    //   (which is why we are returning this object JSON encoded) (not anymore though)



    public function Execute() {
        // initialising the arrays as empty, not sure if this is necessary
	    $this->ports = [];
	    $this->cves = [];
        $this->hostInfo = array();
        // below script is just targeting the local host, will need to be changed for actual scans
        $command = 'nmap -T4 -A --script vulners ' . parse_url($this->scan->getTarget())["host"];
        echo "\nnmap command: " . $command . "\n";
        // execute the specified command and put the ouput into an array called $CLI
        exec($command, $CLI);	

        // do the whole preg_match regex stuff here...
        // for nslookup, the pattern would be something like:
        //   Non-authoritative answer:\sName:\s*(\S*)\sAddress:\s*(\S*)\sName:\s*(\S*)\sAddress:\s*(\S*)
        // use https://regex101.com/ or something similar and use their generation tool

        // BE CAREFUL WITH DELIMITERS, I AM USING #
        // for now just looking for tcp matches, can add a udp option later if need be
        $patternPorts = '#\d{1,4}/tcp#';
        // regex looking for CVE followed by -, then 4 numbers, another - and then 4 to 5 numbers then white space
        $patternCves = '#CVE-\d{4}-\d{4,5}\s#';
        
        // Host Information
        $patternHostInfo_ServiceInfo = '/Service Info: (.*)/m';
        $patternHostInfo_OSGuesses = '/Aggressive OS guesses: (.*)/m';
        $patternHostInfo_OSDetails = '/OS details: (.*)/m';


        // For each line returned by nmap, check the output data:
        foreach($CLI as $line){
		    preg_match_all($patternPorts, $line, $result, PREG_SET_ORDER, 0);
		    if(isset($result[0])) {
                // this is a pretty hacky way to go about this but I haven't figured out a cleaner way yet
                // basically the result array gets reset every time the preg_match_all function is called, so if there is entry in [0] you know a match has been found
                // also for whatecer reason the result will be an array of arrays so I have made sure it only returns the the 0th of the 0th entry (since that is all there should be)
                // ALSO ALSO MAKE SURE YOU ARE PUSHING TO $THIS->WHATEVER INSTEAD OF $WHATEVER, AS OTHERWISE THE GETTER WON'T WORK...
			    array_push($this->ports, $result[0][0]);
		    }

		    preg_match_all($patternCves, $line, $result, PREG_SET_ORDER, 0);
		    if(isset($result[0])) {
			    array_push($this->cves, $result[0][0]);
		    }

            preg_match_all($patternHostInfo_ServiceInfo, $line, $result);
            if(isset($result[1][0])) {
                $temp = explode(";", $result[1][0]);

                $this->hostInfo["Service Info"]["Host"] = $temp[0];
                $this->hostInfo["Service Info"]["OS"] = $temp[1];
                $this->hostInfo["Service Info"]["CPE"] = $temp[2];
            }
            
            preg_match_all($patternHostInfo_OSGuesses, $line, $result);
            if(isset($result[1][0])) {
                $this->hostInfo["Aggressive OS guesses"] = $result[1][0];
            }

            preg_match_all($patternHostInfo_OSDetails, $line, $result);
            if(isset($result[1][0])) {
                $this->hostInfo["OS Details"] = $result[1][0];
            }
	    }
    }

    // I have added a few getters here that are for specific arrays like ports and cves etc, it might be helpful to have for when the vulnerabilities need to be called they can just check relevant output 
    // no need for Setters since no other class will be able to change any of the arrays except for the tool itself

    public function GetPorts() {
        
        return $this->ports;

    }

    public function GetCVEs() {
    
        return $this->cves;

    }

    public function GetHostInfo() {

        return $this->hostInfo;

    }

    
    // the below function will be deprecated now that there is no persistence
    
    public function Output() {

        

        // Persist this whole object as JSON
        // TODO
        // return json_encode($this);

        
    }

    
}
