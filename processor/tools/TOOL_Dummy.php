<?php
class TOOL_Dummy extends TOOL {
    /*
        Tool Name:              DummyTool
        Responsible:            AA
        OpenProject Phase #:    999

        Summary:
            ... quick summary of the tool's purpose. (Even if it's all written in PHP / Symfony, still describe it breifly)


        Output (Object):
            ... describe in psuedocode
            $domain_names = array of domain names
            $addresses = array of addresses (IPv4/6) corresponding to the matching $names[i]
    */

    // ==== Tool Output objects ====
    //   (used to grab the data in VULN_xxx when this object is passed to it)
    //   So don't forget getters
    private array $addresses = array();
    private array $domain_names  = array();

    public function Execute() {        
        
        // parse_url
        // https://www.php.net/manual/en/function.parse-url.php
        // parse_url($this->scan->getTarget())["host"] = google.com
        // parse_url($this->scan->getTarget())["scheme"] = https

        // Start output buffer and execute tool
        ob_start();
        passthru("nslookup " . parse_url($this->scan->getTarget())["host"]);
        $raw = ob_get_clean();
        
        $pattern = '/Name:\s*(\S*)\sAddress:\s*(\S*)\s/m';
        preg_match_all($pattern, $raw, $matches, PREG_SET_ORDER, 0);

        // normally, I'm not a fan of ternary or single-line if statements.. but it makes sense here.
        if (isset($matches[0][1])) array_push($this->domain_names, $matches[0][1]);
        if (isset($matches[0][2])) array_push($this->addresses, $matches[0][2]);
    }

    // Getters / Setters
    public function getAddresses() {
		return $this->addresses;
	}
	public function getDomain_names() {
		return $this->domain_names;
	}
}
?>