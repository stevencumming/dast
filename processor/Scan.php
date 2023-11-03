<?php
class Scan {
    private string $target;
    private int $scan_id;
    private int $user_id;


    function __construct(
        $target,
        $scan_id,
        $user_id
    ){
        $this->target = $target;
        $this->scan_id = $scan_id;
        $this->user_id = $user_id;
    }

    // Getters / Setters
    public function setTarget($target){
        $this->target = $target;
    }
    public function getTarget(){
        return $this->target;
    }
    public function setScanID($scan_id){
        $this->scan_id = $scan_id;
    }
    public function getScanID(){
        return $this->scan_id;
    }
    public function setUserID($user_id){
        $this->user_id = $user_id;
    }
    public function getUserID(){
        return $this->user_id;
    }
}
?>
