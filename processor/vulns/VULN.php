<?php
// Abstract base class of VULN
class VULN {
    protected array $tools;
    protected Scan $scan;

    // Output
    protected $severity;
    protected $html;

 
    function __construct(
        $scan,
        $tools
    ) {
        $this->scan = $scan;
        $this->tools = $tools;

        // Initialise severity to default value (0: Information)
        $this->severity = 0;
    }

      
    public function Analyse() {}


    // Getters / Setters
    public function getSeverity() {
        return $this->severity;
    }
    public function getHTML() {
        return $this->html;
    }
}
?>