<?php
class TOOL_Gobuster extends TOOL {
    /*
        Tool Name:              Gobuster
        Responsible:            SC
        OpenProject Phase #:    

        Summary:
            Brute force directory / file scanning that uses a word list (dictionary) and a handful
            of defined file extensions (see const below).
            
        Output (Object):
            getOutput()
                returns the found files / directories as an array.
                each element is one file/directory (URL)
                e.g.:
                    https://stevencumming.io/header.php
                    https://stevencumming.io/index.php


            getVerboseOutput()
                returns the found file/directory URL, HTTP response code, and size.
                e.g.:
                    https://stevencumming.io/.html                (Status: 403) [Size: 282]
                    https://stevencumming.io/assets               (Status: 301) [Size: 323] [--> https://stevencumming.io/assets/]

    */
    const SEARCH_EXTENSIONS = "php,txt,html,css,js";
    private array $output;
    private array $output_verbose;

    public function Execute() {
        echo "Executing Gobuster...\n";

        // Initialise the output buffer (array of lines) and execute the tool
        $command = "PATH=/usr/local/go/bin gobuster dir -q -e -x " . self::SEARCH_EXTENSIONS . " -u " . $this->scan->getTarget() . " -t 50 -w assets/wordlist.txt";
        $CLI = array();
        exec($command, $CLI);

        // initialise output arrays
        $this->output = array();
        $this->output_verbose = array();

        // Gor each line reformat and store in output array
        foreach ($CLI as $line) {
            // Push verbose output to array
            array_push($this->output_verbose, $line);

            // Trim off the excess from the result and push to output array
            $line = strtok($line,' ');
            array_push($this->output, $line);
        }

        echo "\nFinished Gobuster.\n";        
    }

    public function getOutput(){
        return $this->output;
    }
    public function getVerboseOutput(){
        return $this->output_verbose;
    }
}
?>