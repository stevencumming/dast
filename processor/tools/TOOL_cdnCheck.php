<?php

class TOOL_cdnCheck extends TOOL
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
    
    public function Execute()
    {
        $target = $this->scan->getTarget();
        $arr = get_headers($target);
        $result = array_filter($arr, function ($element) {
            return strpos($element, "Server: ") !== false;
        });

        // more CDNs can be implemented but this covers quite a majority of the major commercial CDNs used
        foreach ($result as $element) {
            if (preg_match('~\b(gws|Google|ESF)\b~i', $element)) {
                $this->output = 'Google CDN is being used. Target is DDoS Protected.';
            }
            else if (preg_match('~\b(cloudflare)\b~i', $element)) {
                $this->output = 'Cloudflare CDN is being used. Target is DDoS Protected.';
            }
            else if (preg_match('~\b(Amazon|CloudFront|S3)\b~i', $element)) {
                $this->output = 'Amazon CloudFront/S3 CDN is being used. Target is DDoS Protected.';
            }
            else if (preg_match('~\b(Fastly)\b~i', $element)) {
                $this->output = 'Fastly CDN is being used. Target is DDoS Protected.';
            }
            else {
                $this->output = 'No commercial CDN is in use. Target may be vulnerable to DDoS attacks.';
            }
        }
    }

    public function getOutput() {
        
        return $this->output;

    }
}