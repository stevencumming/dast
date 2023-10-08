<?php
class VULN_Sitemap extends VULN {
    /*
        Vulnerability:          Sitemap
        Responsible:            SC
        OpenProject Phase #:    999

        Summary:
            Discover the sitemap / directory structure of a target website.

        Output (HTML):
            HTML formatted output to go straight into the Report.
    */

    public function Analyse() {
        // Analyse your vulnerability

        echo "\n\nversion 1\n\n";

        // Local variables here for using when analysing the tools
        $this->html = "<div>";


        error_reporting(-1);

        $this->html .= "<h3>Directory / File listing</h3>";
        $this->html .= "<h4>Extended file information</h4>";
        $this->html .= "<ul>";

        $lverbose = $this->tools[1]->getOutput();

        $buffer = array();

        foreach ($this->tools[1]->getOutput() as &$item) {
            array_push($buffer,$item);
        }



        echo "\n\n\n\n=============\n\n\n\n";
        print_r($buffer);
        echo "\n\n\n\n=============\n\n\n\n";

        foreach ($buffer as $value) {
            echo "\n new test " . $value . "... \n";
        }

        for ($i=0; $i < sizeof($buffer); $i++) { 
            echo "\n .... ,, new test " . $buffer[$i] . "... \n"; 
        }

        // foreach ($lverbose as &$item) {
        //     echo "\n Item: " . $item . "\n"; 

        //     $this->html .= "<li>";
        //     $this->html .= ($item);
        //     $this->html .= "</li>";

        //     echo "test:" . $this->html . "\n";
        // }

        // // for ($i=0; $i < count($lverbose); $i++) { 
        // //     $output .= "<li>";
        // //     $output .= $lverbose[$i];
        // //     $output .= "</li>";
        // // }

        $this->html .= "</ul>";



        // // Start by reading the data from your tool(s)
        // foreach ($this->tools as $tool) {
        //     // Loop through each of the tools that were passed to this vulnerability
        //     // Index them (split them out) by their **name** (name is defined when the tool is CREATED / instantiated in ScanProcessor)
        //     switch ($tool->getName()) {
        //         case "GoSpider":
        //             // Do stuff with DummyTool
                    
        //             // E.g. DummyTool (object of type TOOL_DummyTool has $addresses and $domain_names private members (variables))
        //             // example, list all of the ip addresses found against their domain names reading in the array stored in the $tool TOOL_DummyTool
        //             // for ($i=0; $i < sizeof($tool->getAddresses()); $i++) { 
        //             //     $output .= $tool->getAddresses[$i] . ": " . $tool->getDomain_names[$i];
        //             // }

        //             break;

        //         case "Gobuster":
        //             $output .= "<h3>Directory / File listing</h3>";
        //             $output .= "<h4>Extended file information</h4>";
        //             $output .= "<ul>";

        //             $lverbose = $tool->getVerboseOutput();

        //             foreach ($lverbose as $item) {
        //                 $output .= "<li>";
        //                 $output .= $item;
        //                 $output .= "</li>";

        //                 echo "test:" . $output . "\n";
        //             }

        //             // for ($i=0; $i < count($lverbose); $i++) { 
        //             //     $output .= "<li>";
        //             //     $output .= $lverbose[$i];
        //             //     $output .= "</li>";
        //             // }

        //             $output .= "</ul>";
        //             break;

        //         case "another_tool_that_might_have_been_passed":
        //             // Do more stuff
        //             break;  // don't forget to break
        //         // we don't really need a default case, the condition should never occur.
        //     }
        // }

        $this->html .= "</div>";

        // ++ All tools have been analysed at this point
        
        // calculate the severities and store
        $this->severity = 0;

        // remember to construct the HTML used within the report:
        //   (the final report generated, that includes ALL vulnerabilities, will consist of all of these html segments displayed together)
        //   (We'll standardise this later!)
        //$this->html = $output;

    }

}
?>