///todo figure out how to get userService shared by this and salesPortalApp
var customerApp = angular.module('salesPortalCRM', ['ngRoute','ngAnimate','toastr','ui.bootstrap','angucomplete-alt', 'chart.js'],
		function($httpProvider) {
	  // Use x-www-form-urlencoded Content-Type
	  $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded;charset=utf-8';
	 
	  /**
	   * The workhorse; converts an object to x-www-form-urlencoded serialization.
	   * @param {Object} obj
	   * @return {String}
	   */ 
	  var param = function(obj) {
	    var query = '', name, value, fullSubName, subName, subValue, innerObj, i;
	      
	    for(name in obj) {
	      value = obj[name];
	        
	      if(value instanceof Array) {
	        for(i=0; i<value.length; ++i) {
	          subValue = value[i];
	          fullSubName = name + '[' + i + ']';
	          innerObj = {};
	          innerObj[fullSubName] = subValue;
	          query += param(innerObj) + '&';
	        }
	      }
	      else if(value instanceof Object) {
	        for(subName in value) {
	          subValue = value[subName];
	          fullSubName = name + '[' + subName + ']';
	          innerObj = {};
	          innerObj[fullSubName] = subValue;
	          query += param(innerObj) + '&';
	        }
	      }
	      else if(value !== undefined && value !== null)
	        query += encodeURIComponent(name) + '=' + encodeURIComponent(value) + '&';
	    }
	      
	    return query.length ? query.substr(0, query.length - 1) : query;
	  };
	 
	  // Override $http service's default transformRequest
	  $httpProvider.defaults.transformRequest = [function(data) {
	    return angular.isObject(data) && String(data) !== '[object File]' ? param(data) : data;
	  }];
	}		
);

customerApp.config(function ($routeProvider) {
	$routeProvider.when("/Dashboard", {
		controller: "CustomerDashboardController",
		templateUrl: "../templates/customerDashboard.html"
	});
        
        $routeProvider.when("/Create", {
		controller: "CustomerController",
		templateUrl: "../../templates/createCustomer.html"
	});
        
        $routeProvider.when("/Display/:custId", {
		controller: "CustomerSalesController",
		templateUrl: "../../templates/customerDisplay.html"
	});
        
        $routeProvider.when("/Directory", {
		controller: "CustomerDashboardController",
		templateUrl: "../../templates/customerDirectory.html"
	});
        
	$routeProvider.otherwise({ redirectTo: "/Dashboard" });
});

customerApp.factory('addressService', function($http) {
	var baseUrl = '../services/';
        var addressServicesUrl = 'Address/';
        
        var _getStates = function() {
            return $http.get(baseUrl + addressServicesUrl + 'getStates.php');
        }
        
        return { 
		getStates: _getStates
		};		
        });
        
customerApp.factory('itemService', function($http) {
	var baseUrl = '../services/';
        var itemServicesUrl = 'Item/';
        
        var _getItemById = function(id) {
            return $http.get(baseUrl + itemServicesUrl + 'getItemById.php?Id=' + id);
        };
        
        return { 
		getItemById: _getItemById
		};		
        });
        
customerApp.factory('salesService', function($http) {
	var baseUrl = '../services/';
        var salesServicesUrl = 'Sales/';
        
        var _createOrder = function(customerId, orderShipDate, lineItemJSONArray) {
            return $http.post(baseUrl + salesServicesUrl + 'createOrder.php', 
            { 
                custId: customerId,
                shipDate: orderShipDate,
                lineItems: lineItemJSONArray
            });
        };
        
        var _getSalesHistory = function(startFilterDate,endFilterDate,customerId) {
            return $http.post(baseUrl + salesServicesUrl + 'getSalesHistory.php',
            { 
                custId: customerId,
                startDate: startFilterDate,
                endDate: endFilterDate
            });
        };
        
        var _getCustomerQuarterlySalesFigures = function() {
            return $http.post(baseUrl + salesServicesUrl + 'getCurrentQuarterSalesInfo.php',
            { 
                limitTo: 10
            });
        };
        
        var _GetCurrentAndLastYearCustomerSalesInfo = function() {
            return $http.post(baseUrl + salesServicesUrl + 'getCurrentAndLastYearCustomerSalesInfo.php',
            { 
                limitTo: 10
            });
        };
        
        var _GetNeglectedCustomerSalesInfo = function() {
            return $http.post(baseUrl + salesServicesUrl + 'getNeglectedCustomerSalesInfo.php',
            { 
                limitTo: 10
            });
        };
        
        return { 
                    createOrder: _createOrder,
                    getSalesHistory: _getSalesHistory,
                    getCustomerQuarterlySalesFigures: _getCustomerQuarterlySalesFigures,
                    GetCurrentAndLastYearCustomerSalesInfo: _GetCurrentAndLastYearCustomerSalesInfo,
                    GetNeglectedCustomerSalesInfo: _GetNeglectedCustomerSalesInfo
		};		
        });

customerApp.factory('customerService', function($http) {
	var baseUrl = '../services/';
        var customerServicesUrl = 'Customer/';
        
        var _createCustomer = function(customer) {
            return $http.post(baseUrl + customerServicesUrl + 'createCustomer.php', 
            { 
                CustomerName: customer.Name, 
                BillAddress_Line1: customer.BillAddress.Line1, 
                BillAddress_Line2: customer.BillAddress.Line2, 
                BillAddress_City: customer.BillAddress.City, 
                BillAddress_State: customer.BillAddress.StateId, 
                BillAddress_Zip: customer.BillAddress.Zip, 
                ShipAddress_Line1: customer.ShipAddress.Line1, 
                ShipAddress_Line2: customer.ShipAddress.Line2, 
                ShipAddress_City: customer.ShipAddress.City, 
                ShipAddress_State: customer.ShipAddress.StateId, 
                ShipAddress_Zip: customer.ShipAddress.Zip, 
                ContactInfo_Phone: isUndefinedOrNull(customer.ContactInfo) ? null : customer.ContactInfo.Phone,
                ContactInfo_Fax: isUndefinedOrNull(customer.ContactInfo) ? null : customer.ContactInfo.Fax,
                ContactInfo_Email: isUndefinedOrNull(customer.ContactInfo) ? null : customer.ContactInfo.Email,
                ContactInfo_WebAddress: isUndefinedOrNull(customer.ContactInfo) ? null : customer.ContactInfo.WebAddress
            });
        };
        
        var isUndefinedOrNull = function(obj){
            //javscript type coercion will check for undefined or null 
            return obj == null;
        };
        
        var _getCustomerById = function(id) {
            return $http.post(baseUrl + customerServicesUrl + 'getCustomerById.php', { CustomerId: id });
        };
        
        var _getCustomerByName = function(name) {
            return $http.post(baseUrl + customerServicesUrl + 'getCustomerByName.php', { CustomerName: name });
        };
        
        var _getCustomersByName = function(name) {
            return $http.get(baseUrl + customerServicesUrl + 'getCustomersByName.php?CustomerName=' +  name);
        };
        
        var _getAllCustomers = function(name) {
            return $http.get(baseUrl + customerServicesUrl + 'getAllCustomers.php');
        };
	
	return { 
		createCustomer: _createCustomer,
                getCustomerById: _getCustomerById,
                getCustomerByName: _getCustomerByName,
                getCustomersByName: _getCustomersByName,
                getAllCustomers: _getAllCustomers
		};		
});

customerApp.controller('CustomerDashboardController', function($scope, customerService, salesService, $location, toastrFactory) {
    
//    $scope.labels = ["ABCDEFGHIJKLM", "BikeWorld", "Cycle Guys", "Bike O' Rama"];
//    $scope.data =     [65, 59, 80, 81];
    $scope.labels = [];
    $scope.data =     [];
    
    $scope.highGrossingCustomers = [];
    $scope.neglectedCustomers = [];
  
    salesService.getCustomerQuarterlySalesFigures().then(
                function(sales) {
                    angular.forEach(sales.data, function (value) {
                        $scope.labels.push(value.CustomerName);
                        $scope.data.push(value.QuarterlyTotal);
                });
            });

    //todo: it'd be even cooler to go to the Sales History tab and pull up the quarterly sales if time permits
    $scope.onChartClick = function (points, evt) {
    customerService.getCustomerByName(points[0].label).then(function(response){
        $location.path('/Display/' + response.data.Id);
    },
    function () {
        toastrFactory.error("Could not retrieve customer.  Please contact Technical Support.");
    });
  };
  
  salesService.GetCurrentAndLastYearCustomerSalesInfo().then(function(yrSales){
      $scope.highGrossingCustomers = yrSales.data;
  },
  function () {
      toastrFactory.error("Could not retrieve yearly sales info.  Please contact Technical Support.");
  });
  
  salesService.GetNeglectedCustomerSalesInfo().then(function(negSales){
      $scope.neglectedCustomers = negSales.data;
  },
  function () {
      toastrFactory.error("Could not retrieve customers without recent orders.  Please contact Technical Support.");
  });
  
  $scope.customerSearchResult = [];
  
  
  $scope.customerSelected = function (selected) {
        if (typeof selected !== "undefined") {
            customerService.getCustomerById(selected.originalObject.Id).then(function(customer) {
                $scope.customerSearchResult = [];
                $scope.showSearchResults = true;
                $scope.customerSearchResult.push(customer.data);
            },
            function() {
                 toastrFactory.error("Could not retrieve customer.  Please contact Technical Support.");
            });
          
            $scope.$broadcast('angucomplete-alt:clearInput');
        }
  };
  
  $scope.showSearchResults = false;
  
  
  
  //todo: thought it would be nice to just update the search results based on the current input but running out of time 
//  $scope.updateCustomerResults = function (str) {
//    customerService.getCustomersByName(str).then(function(customers){
//        $scope.showSearchResults = true;
//        $scope.customerSearchResults = customers.results;
//        
//    },
//    function(){
//        $scope.showSearchResults = false;
//        toastrFactory.error("Could not retrieve customer search results.  Please contact Technical Support.");
//    });
    
          //};
          
$scope.customerSearchResults = [];
  
  customerService.getAllCustomers().then(function(customers) {
      $scope.customerSearchResults = customers.data;

  },
  function(){
        toastrFactory.error("Could not retrieve customer directory.  Please contact Technical Support.");
  });          

$scope.showCustomerDirectory = function () {
          $location.path('/Directory');
};
  
});



customerApp.controller('CustomerController', function($scope, $location, $routeParams, customerService, addressService, toastrFactory) {
    
    $scope.stateList = [];
   
    addressService.getStates().then( 
            function(states) {
             $scope.stateList = states.data;   
            });
      
    $scope.CreateCustomer = function() {
        
          customerService.createCustomer($scope.Customer)
                .then(function (response) {
                   $scope.customerCreated = response.data[0].isSuccess;
                
              if($scope.customerCreated === "true") {
                 $location.path('/Dashboard');
            }
            else
            {
                toastrFactory.error("There was an error saving the customer.  Please contact Technical Support.");
            }
      },
      function () {
          toastrFactory.error("There was an error saving the customer.  Please contact Technical Support.");
      });
    };
    
   
});

customerApp.controller('CustomerSalesController', function($scope, $routeParams, customerService, itemService, salesService, toastrFactory) {
    $scope.Customer = null;
    $scope.shipdate = new Date();
    $scope.startdate = new Date();
    $scope.startdate.setDate($scope.startdate.getDate() - 30); //initialize the sales history to return the last 30 days by default
    $scope.enddate = new Date();
    $scope.minDate = new Date();
    $scope.minStartEndDate = new Date("2010-01-01");
    $scope.currentOrderLineItems = [];
    $scope.currentOrderTotal = 0;
    
    $scope.status = {
        opened: false
    };
    
    $scope.open = function($event) {
        $scope.status.opened = true;
    };
    
    $scope.statusStartDate = {
        opened: false
    };
    
    $scope.openStartDate = function($event) {
        $scope.statusStartDate.opened = true;
    };
    
    $scope.statusEndDate = {
        opened: false
    };
    
    $scope.openEndDate = function($event) {
        $scope.statusEndDate.opened = true;
    };
    
    customerService.getCustomerById($routeParams.custId).then( 
            function(cust) {
             $scope.Customer = cust.data;   
            }, 
            function() {
                toastrFactory.error("There was an error retrieving the customer.  Please contact Technical Support.");
            });
            
    $scope.currentLineItem = 
            {
              Price: 0,
              Qty: 0,
              Name: '',
              Id: 0
            };
            
    $scope.itemSelected = function (selected) {
        if (typeof selected !== "undefined") {

            itemService.getItemById(selected.originalObject.Id)
                .then(function (item) {
                    //update the ui with the price of the item and qty 1
                    $scope.currentLineItem.Price = item.data.Price;
                    $scope.currentLineItem.Name = item.data.Name;
                    $scope.currentLineItem.Id = item.data.Id;
                    $scope.currentLineItem.Qty = 1;
                },
                function () {
                    toastrFactory.error("Could not load items from database.");
                });
        }
    };
    
    $scope.addItemToOrder = function () {
        //if they already added this item to the order, just add the quantity to the existing line
        var index = -1;
        index = $scope.currentOrderLineItems.map(function(l) { return l.Id }).indexOf($scope.currentLineItem.Id);
        
        if(index === -1)
        {
            $scope.currentOrderLineItems.push(
             { 
                    Price: $scope.currentLineItem.Price,
                    Qty: $scope.currentLineItem.Qty,
                    Name: $scope.currentLineItem.Name,
                    Id: $scope.currentLineItem.Id 
            });
        }
        else {
            $scope.currentOrderLineItems[index].Qty += $scope.currentLineItem.Qty;
        }
        
        
        updateTotal();
        $scope.clearCurrentLineItem();
        $scope.$broadcast('angucomplete-alt:clearInput');
    };
    
    $scope.removeLineItem = function (lineItem) { 
        $scope.currentOrderLineItems.splice($scope.currentOrderLineItems.indexOf(lineItem),1);
        updateTotal();
    };
    
    var updateTotal = function () { 
        var total = 0;
        angular.forEach($scope.currentOrderLineItems, function (value) {
           total+=(value.Price*1)*(value.Qty*1);
        });
        
        $scope.currentOrderTotal = total;
    };
    
    $scope.clearCurrentLineItem = function () {
      $scope.currentLineItem = {
              Price: 0,
              Qty: 0,
              Name: '',
              Id: 0
            };  
    };
    
    $scope.saveOrder = function () {
        salesService.createOrder($scope.Customer.Id, $scope.shipdate.toISOString().substring(0, 10), $scope.currentOrderLineItems)
                .then(function (orderData) {
                    var orderId = orderData.data.orderId;
                    
                    //clear the order screen
                    $scope.clearCurrentLineItem();
                    $scope.$broadcast('angucomplete-alt:clearInput');
                    $scope.currentOrderLineItems = [];
                    $scope.currentOrderTotal = 0;
                    $scope.shipdate = new Date();
                    
                    //pop up the order id (in a real system we'd have a confirmation page)
                    toastrFactory.successWithMessage("Order Saved!  Order Id: " + orderId);
                    
                },
                function () {
                    toastrFactory.error("Could not save the order, please contact Technical Support.");
                });
    };
    
    $scope.currentSalesHistoryPage = 1;
    $scope.salesHistoryCount = 0;
    $scope.salesHistoryRecords = [];
    
//    $scope.salesHistoryPageChange = function () {
//      //get the next set of data
//      //display those orders 
//    };
//    
    $scope.searchSalesHistory = function () {
        salesService.getSalesHistory($scope.startdate.toISOString().substring(0, 10),$scope.enddate.toISOString().substring(0, 10),$scope.Customer.Id)
                .then(function (salesHistory) {
                    $scope.salesHistoryRecords = salesHistory.data;
                    $scope.salesHistoryCount = $scope.salesHistoryRecords.length;
                },
                function () {
                    toastrFactory.error("Could not retrieve order history.  Please contact Technical Support.");
                });
        
         //set back to default page
        $scope.currentSalesHistoryPage = 1;
        //get the count for the pager 
        //$scope.salesHistoryCount = $scope.salesHistoryRecords.length;
    };
    
});

customerApp.factory("toastrFactory", function (toastr) {
    return {
        success: function () {
            toastr.success("Changes Saved");
        },
        successWithMessage: function (text) { 
            toastr["success"](text);
        },
        error: function (text) {
            toastr.error(text, "Error!");
        }
    };
 
    
});

customerApp.filter('startFrom', function() {
    return function(input, start) {
        start = +start; //parse to int
        return input.slice(start);
    }
});  
