<?php

namespace App\Service;

//use App\Entity\Process;
use App\Entity\Tool;

// DAST test feature
class TOOL_cdnCheck
{
    /*
        Tool Name:              CDNCheck
        Responsible:            PY
        OpenProject Phase #:    

        Summary:
            Custom PHP script written by Parker Young to assess whether a target is utilising a commercial CDN.


        Output:
            Fairly simple: the output will check for a popular commercial CDN by analying the headers.
            If a CDN is present, or no CDN is in use, the script will output the resulting string accordingly.

    */
    private $name;
    private $output;

    public function __construct(
        Tool $tool,
    ) {
    }
    
    public function Execute()
    {
        $target = $this->tool->getScan()->getTarget();
        $arr = get_headers($target);
        $result = array_filter($arr, function ($element) {
            return strpos($element, "Server: ") !== false;
        });

        // more CDNs can be implemented but this covers quite a majority of the major commercial CDNs used
        foreach ($result as $element) {
            if (preg_match('~\b(gws|Google|ESF)\b~i', $element)) {
                $this->output = 'Google CDN is being used. Target is DDoS Protected.';
                $this->vulnerable = 0;
            }
            else if (preg_match('~\b(cloudflare)\b~i', $element)) {
                $this->output = 'Cloudflare CDN is being used. Target is DDoS Protected.';
                $this->vulnerable = 0;
            }
            else if (preg_match('~\b(Amazon|CloudFront|S3)\b~i', $element)) {
                $this->output = 'Amazon CloudFront/S3 CDN is being used. Target is DDoS Protected.';
                $this->vulnerable = 0;
            }
            else if (preg_match('~\b(Fastly)\b~i', $element)) {
                $this->output = 'Fastly CDN is being used. Target is DDoS Protected.';
                $this->vulnerable = 0;
            }
            else {
                $this->output = 'No commercial CDN is in use. Target may be vulnerable to DDoS attacks.';
                $this->vulnerable = 1;
            }
        }
    }

    public function getOutput() {
        
        return $this->output;

    }

    public function getVulnerable() {

        return $this->vulnerable;

    }
}