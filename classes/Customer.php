<?php
class Customer {
    public $Id;
    public $Name;
    public $BillAddress;
    public $ShipAddress;
    public $ContactDetail;
            
    
    //constructor - so you can create new Employee()
    public function __construct($Id = 0, $Name = '', $BillAddress = null, $ShipAddress = null,
            $ContactInfo = null)
    {
        $this->Id = $Id;
        $this->Name = $Name;
        //i think if we do this, the syntax will now be like this to manipulate in another file:
        
        //$cust = new Customer();
        //$cust->BillAddress->Line1 = 'test'
        
        //another way to use this one would be 
        //$addr = new Address(1,'123 main st', null, 'omaha', 'ne', '68105');
        //$cust = new Customer(...all the params up to billadrr, $addr);
        $this->BillAddress = $BillAddress == null ? new Address() : $BillAddress;
        $this->ShipAddress = $ShipAddress == null ? new Address() : $ShipAddress;;
        $this->ContactDetail = $ContactInfo == null ? new ContactDetail() : $ContactInfo;
    }
}
?>

  