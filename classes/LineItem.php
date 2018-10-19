<?php
class LineItem {
    public $Item;
    public $QtyOrdered;
    
    
    //constructor - so you can create new Employee()
    public function __construct($Item = null, $QtyOrdered = 0)
    {
        $this->Item = ($Item == null)? new Item() : $Item;
        $this->QtyOrdered = $QtyOrdered;
       

    }
}
?>