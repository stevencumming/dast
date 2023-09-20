<?php

namespace App\Service;

use App\Entity\Process;

// DAST test feature
class DdosService
{
    public function __construct(
        private Process $process,
    ) {
    }
    
    public function cdnCheck()
    {
        $target = this->process->getScan()->getTarget();
        $arr = get_headers($target);
        $result = array_filter($arr, function ($element) {
            return strpos($element, "Server: ") !== false;
        });

        // more CDNs can be implemented but this covers quite a majority of the major commercial CDNs used
        foreach ($result as $element) {
            if (preg_match('~\b(gws|Google|ESF)\b~i', $element)) {
                $cdnOutput = 'Google CDN is being used. Target is DDoS Protected.';
            }
            else if (preg_match('~\b(cloudflare)\b~i', $element)) {
                $cdnOutput = 'Cloudflare CDN is being used. Target is DDoS Protected.';
            }
            else if (preg_match('~\b(Amazon|CloudFront|S3)\b~i', $element)) {
                $cdnOutput = 'Amazon CloudFront/S3 CDN is being used. Target is DDoS Protected.';
            }
            else if (preg_match('~\b(Fastly)\b~i', $element)) {
                $cdnOutput = 'Fastly CDN is being used. Target is DDoS Protected.';
            }
            else {
                $cdnOutput = 'No commercial CDN is in use. Target may be vulnerable to DDoS attacks.';
            }
        }

        return $cdnOutput;
    }


}