<?php

namespace App\Service;

// DAST test feature
class DdosService
{
    public function checkDns($url)
    {
        $array = dns_get_record($url);
        return $array;
    }
}