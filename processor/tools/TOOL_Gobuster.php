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
                    which contains:
                    [URL]
                    [status code]
                    [size] (bytes)
                e.g.:
                    [0] => Array
                        (
                            [URL] => https://stevencumming.io/.html
                            [status code] => 403
                            [size] => 282
                        )

                    [1] => Array
                        (
                            [URL] => https://stevencumming.io/.php
                            [status code] => 403
                            [size] => 282
                        )
    */
    const SEARCH_EXTENSIONS = "php,txt,html,css,js";
    private array $output;

    public function Execute() {
        echo "Executing Gobuster...";

        $command = "";
        if (DEMO) {
            // if in demo mode, use shorter wordlist.
            // gobuster command
            echo "  !-- DEMO MODE --!  ";
            $command = "PATH=/usr/local/go/bin gobuster dir -q -e -x " . self::SEARCH_EXTENSIONS . " -u " . $this->scan->getTarget() . " -t 50 -w assets/demo_wordlist.txt";
            
        } else {
            // gobuster command
            $command = "PATH=/usr/local/go/bin gobuster dir -q -e -x " . self::SEARCH_EXTENSIONS . " -u " . $this->scan->getTarget() . " -t 50 -w assets/wordlist.txt";
        }

        // Initialise the output buffer (array of lines) and execute the tool
        $output = array();
        $CLI = array();
        exec($command, $CLI);
      
        // Gor each line reformat and store in output array
        foreach ($CLI as $line) {
            $results = array();

            // After like 5 hours of debugging (read, pulling my hair out...) Gobuster was injecting some non-visible ASCII control characters at the
            //   beginning of each line, so PHP foreach wasn't working at all. Anyway, stripping the first 4 chars of the string fixes it.
            $line = substr($line, 5);
            
            // truncate after first space (SP char = 0x20 in hex) for just the URL
            $url = substr($line, 0, strpos($line, chr(0x20)));
            $results["URL"] = $url;

            // extract the http status code and also the file / resource size if found
            $pattern_StatusCode_Size = '/\(Status: ([\d]+)\) \[Size: ([\d]+)\]/m';
            preg_match_all($pattern_StatusCode_Size, $line, $temp);
            $results["status code"] = $temp[1][0];
            $results["size"] = $temp[2][0];

            // Push verbose output to array
            array_push($output, $results);
        }

        $this->output = $output;
        
        echo " Finished Gobuster.\n";
    }

    public function getOutput(){
        return $this->output;
    }
}
?>