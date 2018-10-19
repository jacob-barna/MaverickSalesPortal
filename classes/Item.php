<?php
class Item {
    public $Id;
    public $Name;
    public $Price;
    
    
    //constructor - so you can create new Employee()
    public function __construct($Id = 0, $Name = '', $Price = '')
    {
        $this->Id = $Id;
        $this->Name = $Name;
        $this->Price = $Price;
       

    }
}
?>