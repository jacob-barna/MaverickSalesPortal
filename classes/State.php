<?php
class State {
    public $Id;
    public $Name;
    public $Abbreviation;
    
    //constructor - so you can create new Employee()
    public function __construct($Id = 0, $Name = '', $Abbreviation = '')
    {
        $this->Id = $Id;
        $this->Name = $Name;
        $this->Abbreviation = $Abbreviation;
    }
}
?>
