<?php

class CustomerCurrentQuarterSalesInfo {
    public $CustomerId;
    public $CustomerName;
    public $QuarterlyTotal;
    
    //constructor - so you can create new Employee()
    public function __construct($CustomerId = 0, $CustomerName = '', $QuarterlyTotal = 0)
    {
        $this->CustomerId = $CustomerId;
        $this->CustomerName = $CustomerName;
        $this->QuarterlyTotal = $QuarterlyTotal;

    }
}
?>