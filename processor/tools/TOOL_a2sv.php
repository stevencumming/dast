<?php

class TOOL_a2sv extends TOOL
{
    /*
        Tool Name:              a2sv
        Responsible:            PY
        OpenProject Phase #:    

        Summary:
            Python CLI tool to assess whether a website is susceptible to path traversal vulnerability.


        Output:
            a2sv checks for SSL vulnerabilities and returns result in array for each vulnerability potentially exploitable.

    */
    private string $output;                                     // SC added private member

    public function Execute() {
        echo "Executing a2sv...";

        // SC: is the syntax correct? PHP concat
        exec("python a2sv.py -t {$this->scan->getTarget()}", $arr);

        // $this->output = array_slice($array,-18,18,true);
        $this->output = array_slice($arr,-18,18,true);          // SC renamed $array to $arr

        echo " Finished a2sv.\n";
    }

    public function Output() {
        return $this->output;
    }
}