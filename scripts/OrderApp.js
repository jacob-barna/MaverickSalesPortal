///todo figure out how to get userService shared by this and salesPortalApp
var orderApp = angular.module('salesPortalOrderApp', ['ngRoute','ngAnimate','toastr','ui.bootstrap'],
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

orderApp.config(function ($routeProvider) {
	$routeProvider.when("/View/:orderId", {
		controller: "OrderController",
		templateUrl: "../templates/viewOrderDetail.html"
	});
	//$routeProvider.otherwise({ redirectTo: "/" });
});
        
orderApp.factory('orderService', function($http) {
	var baseUrl = '../services/';
        var orderServicesUrl = 'Order/';
        
        var _getOrderById = function(salesOrderId) {
            return $http.post(baseUrl + orderServicesUrl + 'getOrderById.php', 
            { 
                orderId: salesOrderId
            });
        };
        
        return { 
                    getOrderById: _getOrderById
		};		
        });

orderApp.controller('OrderController', function($scope, $routeParams, orderService, toastrFactory) {
    $scope.order = null;
    $scope.currentOrderTotal = 0;
    

    orderService.getOrderById($routeParams.orderId).then( 
            function(o) {
                $scope.order = o.data;   
                updateTotal();
            }, 
            function() {
                toastrFactory.error("There was an error retrieving the order.  Please contact Technical Support.");
            });
    
    var updateTotal = function () { 
        var total = 0;
        angular.forEach($scope.order.OrderDetail, function (value) {
           total+=(value.Item.Price*1)*(value.QtyOrdered*1);
        });
        
        $scope.currentOrderTotal = total;
    };
});

orderApp.factory("toastrFactory", function (toastr) {
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
