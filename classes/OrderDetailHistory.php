<?php

class OrderDetailHistory {
    public $Id;
    public $ShipDate;
    public $OrderStatus;
    public $OrderTotal;
    
    //constructor - so you can create new Employee()
    public function __construct($Id = 0, $ShipDate = '', $OrderStatus = '', $OrderTotal = 0)
    {
        $this->Id = $Id;
        $this->ShipDate = $ShipDate;
        $this->OrderStatus = $OrderStatus;
        $this->OrderTotal = $OrderTotal;

    }
}
?>