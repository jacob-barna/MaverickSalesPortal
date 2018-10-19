<?php
class ContactDetail {
    public $Id; //int
    public $Phone; //Varchar
    public $Fax; //Varchar
    public $Email; //Varchar
    public $WebAddress; //Varchar

            
    
    //constructor - so you can create new Employee() 
    public function __construct($Id = 0, $Phone = '', $Fax = '', $Email = '', $WebAddress = '')
    {
        $this->Id = $Id;
        $this->Phone = $Phone;
        $this->Fax = $Fax;
        $this->Email = $Email;
        $this->WebAddress = $WebAddress;
    }
}
?>