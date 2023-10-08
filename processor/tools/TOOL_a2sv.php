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

    public function Execute() {
        exec("python a2sv.py -t {$this->scan->getTarget()}", $arr);

        $this->output = array_slice($array,-18,18,true);
    }


    public function Output() {
        return $this->output;
    }
}