<?php

class CustomerYearlySalesInfo {
    public $CustomerId;
    public $CustomerName;
    public $CurrentYearlyTotal;
    public $PreviousYearlyTotal;
    public $LastOrderDate;
    
    //constructor - so you can create new Employee()
    public function __construct($CustomerId = 0, $CustomerName = '', $CurrentYearlyTotal = 0, $PreviousYearlyTotal = 0, $LastOrderDate = '')
    {
        $this->CustomerId = $CustomerId;
        $this->CustomerName = $CustomerName;
        $this->CurrentYearlyTotal = $CurrentYearlyTotal;
        $this->PreviousYearlyTotal = $PreviousYearlyTotal;
        $this->LastOrderDate = $LastOrderDate;
    }
}
?>