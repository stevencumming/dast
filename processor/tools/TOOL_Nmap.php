<?php

class TOOL_Nmap extends TOOL {
    /*
        Tool Name:              Nmap
        Responsible:            MG (and whoever else will use it)
        OpenProject Phase #:    XXX

        Summary:
            Nmap is a versatile port scanner that can also be used for things like vulnerability checking and victim host information gathering
            This tool will be used by multiple different vulnerabilities


        Output (arrays): the arrays that are populated after the process has been run will be used by each vulnerability that requires them
            $ports- open ports on the target machine
            $hostInfo- recon info on the host machine (os etc.)
            $cves- cves found with nmap vulners against each open port
            feel free to add more

     */
    private array $CLI;
    private array $ports;
    private array $hostInfo;
    private array $cves;
    // where required, you may have more complex data structures here!!
    //   (which is why we are returning this object JSON encoded) (not anymore though)



    public function Execute() {
        // initialising the arrays as empty, not sure if this is necessary
	    $this->ports = [];
	    $this->cves = [];
        // below script is just targeting the local host, will need to be changed for actual scans
        $command = 'nmap -T4 -A --script vulners 127.0.0.1';
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
        // regex wil need to check for multiple things so can't just use one pattern
        $patternHostOS = '#TODO#';
        
	    $patternServerVersion = '#TODO#';

	    // not sure if there is a better way to do this but this seems to work
        // basically checking through every line of the output and doing the necessary regex matching
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
