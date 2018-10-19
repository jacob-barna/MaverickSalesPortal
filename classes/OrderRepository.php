<?php
    require_once 'GenericRepository.php'; 
    require_once 'Order.php';
    require_once 'LineItem.php';
    require_once 'Item.php';

    class OrderRepository {
        
        public static function GetOrderById($OrderId)        
        {
            $Order = null;
            try
            {
                $OrderHeader = self::GetOrderHeaderByOrderId($OrderId);
                $OrderLines = self::GetOrderLinesByOrderId($OrderId);
                
                $OrderHeader->OrderDetail = $OrderLines;
                
                $Order = $OrderHeader;
            }
            catch (Exception $e)
            {
                throw $e;
            }
            
            return $Order;
        }
        
        public static function GetOrderLinesByOrderId($OrderId)
        {
            $Lines = array();
            
            $db = GenericRepository::getConnection();
            
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare("SELECT "
                	. "i.Id as ItemId, "
                        . "i.`Name` as ItemName, "
                        . "od.Quantity as QuantityOrdered, "
                        . "i.Price as UnitPrice "
                        . "FROM SalesOrderDetail od "
                        . "JOIN Item i "
                        . "	on od.ItemId = i.Id "
                        . "WHERE od.OrderId = ?"
                ))
                {
                    $query->bind_param('i',$OrderId);
                    if($result=$query->execute())
                    {
                        $query->bind_result($ItemId, $ItemName, $QuantityOrdered, $UnitPrice);
                        
                        while($query->fetch()){
                            array_push($Lines,new LineItem(new Item($ItemId, $ItemName, $UnitPrice),$QuantityOrdered));
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
            
            return $Lines;
        }
        
        public static function GetOrderHeaderByOrderId($OrderId)
        {
            $OrderHeader = new Order();
            
            $db = GenericRepository::getConnection();
            
            mysqli_report(MYSQLI_REPORT_STRICT);
            try
            {
                if($query = $db->prepare("SELECT "
                    . "o.Id as OrderId, "
                    . "o.ShipDate, "
                    . "c.`Name` as CustomerName, "
                    . "s.`Status` "
                    . " FROM SalesOrder o "
                    . " JOIN OrderStatus s "
                    . "	on o.OrderStatus = s.Id "
                    . " JOIN Customer c "
                    . "	on c.Id = o.CustomerId "
                    . " WHERE o.Id = ?"
                ))
                {
                    $query->bind_param('i',$OrderId);
                    if($result=$query->execute())
                    {
                        $query->bind_result($OrderId, $ShipDate, $CustomerName, $Status);
                        
                        $query->fetch();
                        $query->close();
                        
                        $OrderHeader->Id = $OrderId;
                        $OrderHeader->CustomerName = $CustomerName;
                        $OrderHeader->ShipDate = $ShipDate;
                        $OrderHeader->Status = $Status;
                            
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
            
            return $OrderHeader;
        }
    }
?>
