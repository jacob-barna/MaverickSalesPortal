<?php

//if time for refactoring, this is very similar to sales order and we probably shoudl've just made one object ... the only thing blocking that is to make CustomerId into Customer object instead.
class Order {
    public $Id;
    public $CustomerName;
    public $ShipDate;
    public $Status;
    public $OrderDetail = array();
    
    //constructor - so you can create new Employee()
    public function __construct($Id = 0, $CustomerName = '', $ShipDate = '', $Status = '', $OrderDetail = null)
    {
        $this->Id = $Id;
        $this->CustomerName = $CustomerName;
        $this->ShipDate = $ShipDate;
        $this->Status = $Status;
        $this->OrderDetail = $OrderDetail;

    }
}
?>