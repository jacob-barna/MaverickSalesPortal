<?php
    require_once 'GenericRepository.php'; 
    require_once 'OrderDetailHistory.php';
    require_once 'CustomerQuarterlySalesInfo.php';
    require_once 'CustomerYearlySalesInfo.php';
   

    class SalesRepository {
        
        //since this is a small project, we're going to let the UI handle the paging and sorting and just return all
        //we'll let the db do the grouping.  If this were a bigger table then you'd want to do a SKIP/TAKE pattern so 
        //you only return one page at a time at the time the page is changed on the ui
        public static function GetSalesHistory($StartDate, $EndDate, $CustomerId)        
        {
            $SalesHistoryRecords = array();
            $db = GenericRepository::getConnection();
            
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare("SELECT o.Id as OrderId, o.ShipDate, "
                    . "s.`Status`, SUM(od.Quantity * i.Price) as OrderTotal "
                    . "FROM SalesOrder o "
                    . "JOIN OrderStatus s "
                    . "	on o.OrderStatus = s.Id "
                    . "JOIN SalesOrderDetail od "
                    . "	on o.Id = od.OrderId "
                    . "JOIN Item i "
                    . "	on od.ItemId = i.Id "
                    . "WHERE o.ShipDate between ? and ? "
                    . "	and	o.CustomerId = ? "
                    . "GROUP BY o.Id, o.ShipDate, s.`Status`"
                ))
                {
                    $query->bind_param('sss',$StartDate,$EndDate,$CustomerId);
                    if($result=$query->execute())
                    {

                        $query->bind_result($Id, $ShipDate, $Status, $OrderTotal);
                        
                        while($query->fetch()){
                            array_push($SalesHistoryRecords,new OrderDetailHistory($Id, $ShipDate, $Status, $OrderTotal));
                        }
                            
                        $query->close();
                    }
                }
            }
            catch (Exception $e)
            {
                throw $e;
            }
            finally
            {
                $db->close();
            }
            
            return $SalesHistoryRecords;
            
        }
        
        
        //This method returns $LimitResultsTo number of customers by this quarter's sales descending
        //If a customer has $0 sales this quarter, they will be excluded
        public static function GetCurrentQuarterSalesInfo($LimitResultsTo)        
        {
            $QuarterlySales = array();
            $db = GenericRepository::getConnection();
            
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare(" SELECT c.Id as CustomerId, c.`Name` as CustomerName, SUM(od.Quantity * i.Price) as QuarterlyTotal "
                                            . " FROM SalesOrder o "
                                            . " JOIN SalesOrderDetail od  "
                                            . " 	on o.Id = od.OrderId "
                                            . " JOIN Item i "
                                            . " 	on od.ItemId = i.Id "
                                            . " JOIN Customer c  "
                                            . " 	on o.CustomerId = c.Id "
                                            . " WHERE QUARTER(o.ShipDate) = QUARTER(now())  "
                                            . " 	AND YEAR(o.ShipDate) = YEAR(now()) "
                                            . " GROUP BY c.Id, c.`Name` "
                                            . " HAVING SUM(od.Quantity * i.Price) > 0 "
                                            . " Order By SUM(od.Quantity * i.Price)  DESC  "
                                            . " LIMIT ?  "))
                {
                    $query->bind_param('i',$LimitResultsTo);

                    if($result=$query->execute())
                    {
                        $query->bind_result($CustomerId, $CustomerName,$QuarterlyTotal);
                        
                        while($query->fetch()){
                            array_push($QuarterlySales,new CustomerCurrentQuarterSalesInfo($CustomerId, $CustomerName, $QuarterlyTotal));
                        }
                            
                        $query->close();
                    }
                }
            }
            catch (Exception $e)
            {
                throw $e;
            }
            finally
            {
                $db->close();
            }
            
            return $QuarterlySales;
            
        }
       
                //This method returns $LimitResultsTo number of customers by this quarter's sales descending
        //If a customer has $0 sales this quarter, they will be excluded
        public static function GetCurrentAndLastYearCustomerSalesInfo($LimitResultsTo)        
        {
            $YearlySales = array();
            $db = GenericRepository::getConnection();
            
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare("SELECT c.Id as CustomerId, c.`Name` as CustomerName, " 
	                        . " SUM(currYearOrderDetails.Quantity * currYearItems.Price) as CurrentYearlyTotal,   " 
                            . " COALESCE(lastYearSales.total,0) as PreviousYearlyTotal, " 
                            . " (SELECT MAX(ShipDate) from SalesOrder where CustomerId = c.Id) as LastOrderDate "
                        . " FROM SalesOrder currYearOrders " 
                        . " JOIN SalesOrderDetail currYearOrderDetails " 
	                        . " on currYearOrders.Id = currYearOrderDetails.OrderId " 
                        . " JOIN Item currYearItems " 
	                        . " on currYearOrderDetails.ItemId = currYearItems.Id " 
                        . " JOIN Customer c " 
                            . " on currYearOrders.CustomerId = c.Id " 
                        . " LEFT JOIN  " 
                        . " (SELECT SUM(lastYearOrderDetails.Quantity * lastYearItems.Price) as total, lastYearsOrders.CustomerId " 
	                        . " FROM SalesOrder lastYearsOrders  " 
                        . " JOIN SalesOrderDetail lastYearOrderDetails " 
	                        . " on lastYearsOrders.Id = lastYearOrderDetails.OrderId " 
                        . " JOIN Item lastYearItems " 
	                        . " on lastYearOrderDetails.ItemId = lastYearItems.Id " 
                        . " WHERE YEAR(lastYearsOrders.ShipDate) = YEAR(date_add(now(), INTERVAL -1 YEAR)) " 
                        . " group by lastYearsOrders.CustomerId " 
                        . " ) lastYearSales " 
                        . " on c.Id = lastYearSales.CustomerId " 
                        . " WHERE  " 
	                         . " YEAR(currYearOrders.ShipDate) = YEAR(now())	 " 
                        . " GROUP BY c.`Name` " 
                        . " HAVING SUM(currYearOrderDetails.Quantity * currYearItems.Price) > 0 " 
                        . " Order By SUM(currYearOrderDetails.Quantity * currYearItems.Price) DESC, " 
                        . " lastYearSales.total DESC " 
                        . " LIMIT ? "))
                {
                    $query->bind_param('i',$LimitResultsTo);

                    if($result=$query->execute())
                    {
                        $query->bind_result($CustomerId, $CustomerName, $CurrentYearlyTotal,$PreviousYearlyTotal, $LastOrderDate);
                        
                        while($query->fetch()){
                            array_push($YearlySales,new CustomerYearlySalesInfo($CustomerId, $CustomerName, $CurrentYearlyTotal, $PreviousYearlyTotal, $LastOrderDate));
                        }
                            
                        $query->close();
                    }
                }
            }
            catch (Exception $e)
            {
                throw $e;
            }
            finally
            {
                $db->close();
            }
            
            return $YearlySales;
            
        }
        
        public static function GetNeglectedCustomerSalesInfo($LimitResultsTo)        
        {
            $YearlySales = array();
            $db = GenericRepository::getConnection();
            
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare("SELECT c.Id, c.`Name` as CustomerName, "
                        . " 	SUM(currYearOrderDetails.Quantity * currYearItems.Price) as CurrentYearlyTotal,   "
                        . "     COALESCE(lastYearSales.total,0) as PreviousYearlyTotal, "
                        . "     (SELECT MAX(ShipDate) from SalesOrder where CustomerId = c.Id) as LastOrderDate "
                        . " FROM SalesOrder currYearOrders "
                        . " JOIN SalesOrderDetail currYearOrderDetails "
                        . " 	on currYearOrders.Id = currYearOrderDetails.OrderId "
                        . " JOIN Item currYearItems "
                        . " 	on currYearOrderDetails.ItemId = currYearItems.Id "
                        . " JOIN Customer c "
                        . " 	on currYearOrders.CustomerId = c.Id "
                        . " LEFT JOIN  "
                        . " (SELECT SUM(lastYearOrderDetails.Quantity * lastYearItems.Price) as total, lastYearsOrders.CustomerId "
                        . " 	FROM SalesOrder lastYearsOrders  "
                        . " JOIN SalesOrderDetail lastYearOrderDetails "
                        . " 	on lastYearsOrders.Id = lastYearOrderDetails.OrderId "
                        . " JOIN Item lastYearItems "
                        . " 	on lastYearOrderDetails.ItemId = lastYearItems.Id "
                        . " WHERE YEAR(lastYearsOrders.ShipDate) = YEAR(date_add(now(), INTERVAL -1 YEAR)) "
                        . " group by lastYearsOrders.CustomerId "
                        . " ) lastYearSales "
                        . " on c.Id = lastYearSales.CustomerId "
                        . " WHERE  "
	                         . " YEAR(currYearOrders.ShipDate) = YEAR(now())	 "
                        . " GROUP BY c.`Name` " 
                        . " HAVING SUM(currYearOrderDetails.Quantity * currYearItems.Price) > 0 "
                        . " Order By DATEDIFF(now(), (SELECT COALESCE(MAX(ShipDate),'1000-01-01')  from SalesOrder where CustomerId = c.Id)) DESC, "
                        . " SUM(currYearOrderDetails.Quantity * currYearItems.Price) DESC, "
                        . " lastYearSales.total DESC "
                        . " LIMIT ?")) 
                {
                    $query->bind_param('i',$LimitResultsTo);

                    if($result=$query->execute())
                    {
                        $query->bind_result($CustomerId, $CustomerName, $CurrentYearlyTotal,$PreviousYearlyTotal, $LastOrderDate);
                        
                        while($query->fetch()){
                            array_push($YearlySales,new CustomerYearlySalesInfo($CustomerId, $CustomerName, $CurrentYearlyTotal, $PreviousYearlyTotal, $LastOrderDate));
                        }
                            
                        $query->close();
                    }
                }
            }
            catch (Exception $e)
            {
                throw $e;
            }
            finally
            {
                $db->close();
            }
            
            return $YearlySales;
            
        }
       
       
        
        
//        public static function GetSales($StartDate, $EndDate, $CustomerId, $NumberPerPage, $PageNumber, $OrderBy)        
//        {
//            $SalesOrders = array();
//            $db = GenericRepository::getConnection();
//            
//            mysqli_report(MYSQLI_REPORT_STRICT);
//            try
//            {
//                if($query = $db->prepare("SELECT Id, Name, Price FROM Item
//                    WHERE Name LIKE ?"
//                ))
//                {
//                    $query->bind_param('s',$ItemSearch);
//                    if($result=$query->execute())
//                    {
//
//                        $query->bind_result($Id, $Name, $Price);
//                        // $query->fetch();
//                        while($query->fetch()){
//                            array_push($Items,new Item($Id,$Name,$Price));
//                        }
//                            
//                        $query->close();
//                    }
//                   
//                }
//                
//                
//            }
//            catch (Exception $e)
//            {
//                throw $e;
//            }
//            finally
//            {
//                $db->close();
//            }
//            
//            return $SalesOrders;
//            
//        }
        
        public static function CreateOrder ($CustomerId, $ShipDate, $ListOfItemsToOrder)
        {

            $OrderId = 0;
             //turn on errors so we get an exception if something goes wrong, otherwise it fails silently
            mysqli_report(MYSQLI_REPORT_STRICT);
            
            //don't create the customer if they already exist;
            
            try
            {
                $TempOrderId = self::CreateOrderHeader($CustomerId, $ShipDate);
                
                self::CreateOrderDetail($TempOrderId, $ListOfItemsToOrder);
                        
                $OrderId = $TempOrderId;
                
                
            }
            catch (Exception $e)
            {
                throw $e;
                return 0;
            }
            
            return $OrderId;
            
            
        }
    
        public static function CreateOrderHeader($CustomerId, $ShipDate)
        {
            $db = GenericRepository::getConnection();
            $insertId = 0;
            mysqli_report(MYSQLI_REPORT_STRICT);
            $OrderStatus=2;
            try
            {
                if($query = $db->prepare("INSERT INTO SalesOrder (CustomerId, ShipDate, OrderStatus)"
                        . " VALUES (?,?,?)"
                        ))
                {
                    $query->bind_param('isi', $CustomerId, $ShipDate, $OrderStatus);
                    if($result=$query->execute())
                    {
                        $insertId = $db->insert_id;
                        $query->close();
                    }

                }
            }
            catch (Exception $e)
            {
                throw $e;
                return 0;
            }
            finally
            {
                $db->close();
            }
            
            return $insertId;  
        }
        
        
        public static function CreateOrderDetail($OrderId, $ListOfItemsToOrder)
        {
            
            $count = 0;
            $db = GenericRepository::getConnection();
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                foreach ($ListOfItemsToOrder as $LineItem)
                {

                    if($query = $db->prepare("INSERT INTO SalesOrderDetail (OrderId, Quantity, ItemId)"
                        . " VALUES (?, ?, ?)"
                        ))
                    {
                        $query->bind_param('iii', $OrderId, $LineItem->QtyOrdered, $LineItem->Item->Id);
                        if($result=$query->execute())
                        {
                        // $query->bind_result($success);

                        // store into variable count the number of affacted rows
                            $count += $query->affected_rows;
                            $query->fetch();
                            $query->close();
                        }

                    }         
                }
            }
            catch (Exception $e)
             {
                    throw $e;
                }

            finally
                {
                    $db->close();
                }
            
        
            return ($count==count($ListOfItemsToOrder))? true:false;
        }
            
            
}
    
?>
