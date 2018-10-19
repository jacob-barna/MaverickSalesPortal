<?php
class SalesOrder {
    public $Id;
    public $CustomerId;
    public $ShipDate;
    public $ListOfItemsToOrder = array();
    
    //constructor - so you can create new Employee()
    public function __construct($Id = 0, $CustomerId = '', $ShipDate = '',  $ListOfItemsToOrder = null)
    {
        $this->Id = $Id;
        $this->CustomerId = $CustomerId;
        $this->ShipDate = $ShipDate;
        $this->ListOfItemsToOrder = $ListOfItemsToOrder;

    }
}
?>