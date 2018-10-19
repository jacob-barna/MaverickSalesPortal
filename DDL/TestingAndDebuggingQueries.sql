select * 
from 
SalesOrder o join SalesOrderDetail od 
    on o.Id = od.OrderId 
join Item i 
    on od.ItemId = i.Id;

--gets orders and order totals for a given customer and given date range.
SELECT o.Id as OrderId, o.ShipDate, s.`Status`, SUM(od.Quantity * i.Price) as OrderTotal
FROM SalesOrder o
JOIN OrderStatus s
	on o.OrderStatus = s.Id
JOIN SalesOrderDetail od 
	on o.Id = od.OrderId
JOIN Item i
	on od.ItemId = i.Id
WHERE o.ShipDate between '2015-10-26' and '2015-11-25'
	and	o.CustomerId = 14
GROUP BY o.Id, o.ShipDate, s.`Status`
;

/* get order header */
SELECT 
	o.Id as OrderId,
    o.ShipDate, 
    c.`Name` as CustomerName,
    s.`Status` 
FROM SalesOrder o 
JOIN OrderStatus s
	on o.OrderStatus = s.Id
JOIN Customer c 
	on c.Id = o.CustomerId
WHERE o.Id = 14;
	
/* get order Detail */
SELECT 
	i.Id as ItemId,
    i.`Name` as ItemName,
    od.Quantity as QuantityOrdered,
    i.Price as UnitPrice
FROM SalesOrderDetail od 
JOIN Item i
	on od.ItemId = i.Id          
WHERE od.OrderId = 14;

/* Get top 10 customers in order of aggregate quarterly sales (current quarter) descending */
SELECT c.`Name` as CustomerName, SUM(od.Quantity * i.Price) as QuarterlyTotal
FROM SalesOrder o
JOIN SalesOrderDetail od 
	on o.Id = od.OrderId
JOIN Item i
	on od.ItemId = i.Id
JOIN Customer c 
	on o.CustomerId = c.Id
WHERE QUARTER(o.ShipDate) = QUARTER(now()) 
	AND YEAR(o.ShipDate) = YEAR(now())
GROUP BY c.`Name`
HAVING SUM(od.Quantity * i.Price) > 0
Order By SUM(od.Quantity * i.Price)  DESC 
LIMIT 10;

/** Get current year and last year sales **/
SELECT c.Id, c.`Name` as CustomerName, 
	SUM(currYearOrderDetails.Quantity * currYearItems.Price) as CurrentYearlyTotal,  
    COALESCE(lastYearSales.total,0) as PreviousYearlyTotal,
    (SELECT MAX(ShipDate) from SalesOrder where CustomerId = c.Id) as LastOrderDate
FROM SalesOrder currYearOrders
JOIN SalesOrderDetail currYearOrderDetails
	on currYearOrders.Id = currYearOrderDetails.OrderId
JOIN Item currYearItems
	on currYearOrderDetails.ItemId = currYearItems.Id
JOIN Customer c
	on currYearOrders.CustomerId = c.Id
LEFT JOIN 
(SELECT SUM(lastYearOrderDetails.Quantity * lastYearItems.Price) as total, lastYearsOrders.CustomerId
	FROM SalesOrder lastYearsOrders 
JOIN SalesOrderDetail lastYearOrderDetails
	on lastYearsOrders.Id = lastYearOrderDetails.OrderId
JOIN Item lastYearItems
	on lastYearOrderDetails.ItemId = lastYearItems.Id
WHERE YEAR(lastYearsOrders.ShipDate) = YEAR(date_add(now(), INTERVAL -1 YEAR))
group by lastYearsOrders.CustomerId
) lastYearSales
on c.Id = lastYearSales.CustomerId
WHERE 
	 YEAR(currYearOrders.ShipDate) = YEAR(now())	
GROUP BY c.`Name`
HAVING SUM(currYearOrderDetails.Quantity * currYearItems.Price) > 0
Order By SUM(currYearOrderDetails.Quantity * currYearItems.Price) DESC,
lastYearSales.total DESC
LIMIT 10;

/** Get the customers with the least current orders, and current year and last year sales **/
SELECT c.Id, c.`Name` as CustomerName, 
	DATEDIFF(now(), (SELECT COALESCE(MAX(ShipDate),'1000-01-01')  from SalesOrder where CustomerId = c.Id)) as NumberOfDaysSinceLastCall,
	SUM(currYearOrderDetails.Quantity * currYearItems.Price) as CurrentYearlyTotal,  
    COALESCE(lastYearSales.total,0) as PreviousYearlyTotal,
    (SELECT MAX(ShipDate) from SalesOrder where CustomerId = c.Id) as LastOrderDate
FROM SalesOrder currYearOrders
JOIN SalesOrderDetail currYearOrderDetails
	on currYearOrders.Id = currYearOrderDetails.OrderId
JOIN Item currYearItems
	on currYearOrderDetails.ItemId = currYearItems.Id
JOIN Customer c
	on currYearOrders.CustomerId = c.Id
LEFT JOIN 
(SELECT SUM(lastYearOrderDetails.Quantity * lastYearItems.Price) as total, lastYearsOrders.CustomerId
	FROM SalesOrder lastYearsOrders 
JOIN SalesOrderDetail lastYearOrderDetails
	on lastYearsOrders.Id = lastYearOrderDetails.OrderId
JOIN Item lastYearItems
	on lastYearOrderDetails.ItemId = lastYearItems.Id
WHERE YEAR(lastYearsOrders.ShipDate) = YEAR(date_add(now(), INTERVAL -1 YEAR))
group by lastYearsOrders.CustomerId
) lastYearSales
on c.Id = lastYearSales.CustomerId
WHERE 
	 YEAR(currYearOrders.ShipDate) = YEAR(now())	
GROUP BY c.`Name`
HAVING SUM(currYearOrderDetails.Quantity * currYearItems.Price) > 0
Order By DATEDIFF(now(), (SELECT COALESCE(MAX(ShipDate),'1000-01-01')  from SalesOrder where CustomerId = c.Id)) DESC,
SUM(currYearOrderDetails.Quantity * currYearItems.Price) DESC,
lastYearSales.total DESC
LIMIT 10;

