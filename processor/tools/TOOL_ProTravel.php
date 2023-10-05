<?php

class TOOL_ProTravel extends TOOL
{
    /*
        Tool Name:              ProTravel
        Responsible:            PY
        OpenProject Phase #:    

        Summary:
            Python CLI tool to assess whether a website is susceptible to path traversal vulnerability.


        Output:
            The output produced will depend on whether a vulnerability is found or not.
            A "Done" output from ProTravel indicates there is no present vulnerability.
            An output displaying either paths or files indicates that the program has found the presence of sensitive information through a path traversal exploit.

    */

    public function Execute() {
        $this->output = shell_exec("python protravel.py {$this->scan->getTarget()}?filename=../../../../..");
    }


    public function Output() {
        return $this->output;
    }
}