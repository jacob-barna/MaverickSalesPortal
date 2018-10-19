<?php
class Address {
    public $Id; //int
    public $Line1; //Varchar
    public $Line2; //Varchar
    public $City; //Varchar
    public $State; //instance of state class
    public $Zip; //char  68106
            
    
    //constructor - so you can create new Employee() 
    public function __construct($Id = 0, $Line1 = '', $Line2 = '', $City = '', $State = null,
            $Zip = '')
    {
        $this->Id = $Id;
        $this->Line1 = $Line1;
        $this->Line2 = $Line2;
        $this->City = $City;
        $this->State = $State==null?new State():$State;
        $this->Zip = $Zip;
    }
}
?>