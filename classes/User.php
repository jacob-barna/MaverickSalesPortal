<?php
class User {
    public $Id;
    public $Email;
    public $FirstName;
    public $LastName;
    public $Approved;
    public $Active;
    public $Supervisor;
    public $LinkId;
            
    
    //constructor - so you can create new Employee()
    public function __construct($Id = 0, $Email = '', $FirstName = '', $LastName = '', $Approved = 0, $Active = 0, $Supervisor = 0,
            $LinkId = '')
    {
        $this->Id = $Id;
        $this->Email = $Email;
        $this->FirstName = $FirstName;
        $this->LastName = $LastName;
        $this->Approved = $Approved;
        $this->Active = $Active;
        $this->Supervisor = $Supervisor;
        $this->LinkId = $LinkId;
    }
}
?>