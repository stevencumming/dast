<?php
// Abstract base class of TOOL
class TOOL {
    protected string $name;
    protected Scan $scan;

 
    function __construct(
        $scan,
        $name
    ) {
        $this->scan = $scan;
        $this->name = $name;
    }

      
    public function Execute() {}


    // Getters / Setters
    public function setName($name){
        $this->name = $name;
    }
    public function getName(){
        return $this->name;
    }
}
?>