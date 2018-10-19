//the function($httpPRovider) is only there because PHP and Angular don't agree on how to pass JSON so we'll make them agree
///ngRoute is "injecting" angular routing into our module so that we can use the routing features of angular
var app = angular.module('salesPortal', ['ngRoute','ngAnimate','toastr','ui.bootstrap'],
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

app.config(function ($routeProvider) {
    //This is how you route in Angular -- this says that whenever someoen comes to the root page 
    //http://mavericksalesportal.ddns.net/ then you use the template login.html and the controller UserController
	$routeProvider.when("/", {
		controller: "LoginController",
		templateUrl: "templates/login.html"
	});
        $routeProvider.when("/ApproveUsers", {
		controller: "UserController",
		templateUrl: "templates/Approve.html"
	});
        $routeProvider.when("/Dashboard", {
		controller: "DashboardController",
		templateUrl: "templates/dashboard.html"
	});
        
        $routeProvider.when("/RegisterConfirm", {
		controller: "LoginController",
		templateUrl: "templates/registerConfirm.html"
	});
        
        $routeProvider.when("/Activate/:linkId", {
                controller: "ActivateController",
                templateUrl: "templates/activate.html"
        });
    
	//this is the default route - if you type in a nonsense url 
        //http://mavericksalesportal.ddns.net/nonsenseblahblah303480 or any other unknown
        //route it will just direct you to the root of the website
	$routeProvider.otherwise({ redirectTo: "/" });
});

//todo: this should be turned into shared library 
app.factory("toastrFactory", function (toastr) {
    return {
        success: function () {
            toastr.success("Changes Saved");
        },
        error: function (text) {
            toastr.error(text, "Error!");
        }
    };
});

app.factory('userService', function($http) {
	var baseUrl = 'services/';
        var userServicesUrl = 'User/';
        var isAuthorized = false;
	var isAdmin = false;
        var User = null;
        
	var _getUser = function(userName) {
		return $http.post(baseUrl + userServicesUrl + 'getUser.php', { userName: userName });
	};
        
        var _getCurrentUser = function() {
                return User;
	};
        
        var _setCurrentUser = function(usr) {
		User = usr;
	};
        
        var _authorizeUser = function(email, pswd) {
		return $http.post(baseUrl + userServicesUrl + 'authorizeUser.php', { userName: email, password: pswd});
	};
        
        var _isUserAuthorized = function() {
             return isAuthorized;
        };
        
        var _isAdmin = function() {
            return isAdmin;
        };
        
        var _setUserAuthorization = function(isAuth)
        {
            isAuthorized = isAuth;
        };
        
        var _setAdmin = function(isAdmn)
        {
            isAdmin = isAdmn;
        };
        
        var _registerUser = function(email, pswd, fname, lname) {
            return $http.post(baseUrl + userServicesUrl + 'registerUser.php', { userName: email, password: pswd, firstName: fname, lastName: lname });
        };
        
        var _activateUser = function(lnkId) {
            return $http.post(baseUrl + userServicesUrl + 'activateUser.php', { linkId: lnkId });
        };
        
        var _approveUser = function(usr, supvEmail, supvPw) {
            return $http.post(baseUrl + userServicesUrl + 'approveUser.php', { requestorEmail: usr.Email, supervisorEmail: supvEmail, supervisorPassword: supvPw });
        };
        
        var _rejectUser = function(usr, supvEmail, supvPw) {
            return $http.post(baseUrl + userServicesUrl + 'rejectUser.php', { Id: usr.Id, supervisorEmail: supvEmail, supervisorPassword: supvPw });
        };
        
        var _getUnapprovedUsers = function() {
            return $http.get(baseUrl + userServicesUrl + 'getUnapprovedUsers.php');
        };
	
	return { 
		getUser: _getUser,
                authorizeUser: _authorizeUser,
                isUserAuthorized: _isUserAuthorized,
                setUserAuthorization: _setUserAuthorization,
                registerUser: _registerUser,
                setAdmin: _setAdmin,
                isAdmin: _isAdmin,
                setCurrentUser: _setCurrentUser,
                getCurrentUser: _getCurrentUser,
                approveUser: _approveUser,
                rejectUser: _rejectUser, 
                getUnapprovedUsers: _getUnapprovedUsers,
                activateUser: _activateUser
		};		
});

app.controller('LoginController', function($scope, $location, userService) {
    $scope.userIsAuthorized = "false";
    $scope.loginAttemptCount = 0;
    $scope.showLoginError = false;
    $scope.showConfirmPasswordError = false;
    $scope.showRegisterError = false;
    $scope.registerError = "";
    $scope.userIsRegistered = "false";
    
    $scope.Authorize = function() {
       // $window.alert($scope.userName);
       userService.authorizeUser($scope.userName,$scope.password)
         .success(function (data) {
                $scope.userIsAuthorized = data[0].isValid;
                $scope.loginAttemptCount++;
                
        if($scope.userIsAuthorized === "true") {
            //$window.alert("Success login");
            //userService.isAuthorized = true;
            userService.setUserAuthorization(true);
            
            userService.getUser($scope.userName)
                    .success(function (user) {
                            userService.setCurrentUser(user);
                    
                            if(user.Supervisor == 1) {
                                userService.setAdmin(true);
                            }
                                
                            $location.path('/Dashboard');
                        });
               }
               
               $scope.showLoginError = $scope.userIsAuthorized === "false" && $scope.loginAttemptCount > 0;
            });
            
            
            //thinking i may need to pass these to service and then to php service???
            //just stubbed out here, not working
            //userSErvice.usr=$scope.userName;
            //userService.pass=$scope.password;
    };
    
    $scope.Register = function() {
        if($scope.registerPassword !== $scope.registerConfirmPassword){
            $scope.showConfirmPasswordError = true;
        } else {
            $scope.showConfirmPasswordError = false;
            
            userService.registerUser($scope.registerUserName,$scope.registerPassword, $scope.registerFirstName, $scope.registerLastName)
                .then(function (response) {
                   $scope.userIsRegistered = response.data[0].isSuccess;
                
              if($scope.userIsRegistered === "true") {
                 $location.path('/RegisterConfirm');
            }
            else
            {
                //our service sent back that they are not registered, so let's tell them
                $scope.showRegisterError = true; 
                $scope.registerError = "We could not register your account.  This may be because your account has already been registered.  Please contact Technical Support.";
            }
            
            
        }, //anything after this will happen only on error (e.g. a connection error where our service could not be reached)
        function() {
           $scope.showRegisterError = true; 
           $scope.registerError = "An unexpected error occured while processing your registration.  Please try again.";
        });        
          
        }
    };
    
    //todo: how to protect the php services? Will we need to post to them with username and password?
});

app.controller('DashboardController', function($scope,$location, userService) {
    //if they are trying to go to any page where they are not authorized, we'll kick them to the login page 
    //This is from here: http://stackoverflow.com/questions/11541695/redirecting-to-a-certain-route-based-on-condition
    //if for some reason this doesn't work out, check this answer here by user sp00m
    //http://stackoverflow.com/questions/20969835/angularjs-login-and-authentication-in-each-route-and-controller
//    $scope.$watch(function() { return $location.path(); }, function(newValue, oldValue){
//       if(userService.isUserAuthorized() === false && newValue !== '/'){
//           $location.path('/');
//       }           
//   });
   $scope.currentUser = null;
          
    if(!userService.isUserAuthorized()) {
        $location.path('/');
        }
//   
   var usr = userService.getCurrentUser();
    $scope.currentUser = usr;
    $scope.isUserAdmin = usr.Supervisor == 1;
//        
   //$scope.isUserAdmin = function() {  return userService.isAdmin() };

     
//    userService.getCategories().success(function(success){
//		$scope.isAuthorized = 
//		$scope.selectedCategory = $scope.categories[0];
//	});
});

app.controller('UserController', function($scope,$location,userService,$uibModal,toastrFactory) {
    
//todo: reenable security when done coding
//    if(!userService.isUserAuthorized()){
//		$location.path('/');
//	} else {
//        
//        if(!userService.isAdmin()){
//		$location.path('/Dashboard');
//	}
//    }
    $scope.usersToApprove = [];
    var supervisor = userService.getCurrentUser();
    $scope.currentUser = supervisor;
    
    userService.getUnapprovedUsers()    
        .then(function (users) {   
            
            $scope.usersToApprove = users.data;
        }, //this next function only happens on error
        function ()
        {
            toastrFactory.error("There was a problem retrieving the list of unapproved users.");
        });


 
        //we first need to get the password from the supervisor to confirm the approval
        $scope.openSupervisorConfirmDialog = function (usr,adminFunc) {
            
            
            var modalSupervisorConfirmDialog = $uibModal.open({
                templateUrl: 'supervisorConfirmModal.html',
                controller: 'UserAdminModalInstanceController',
                resolve: {
                    adminFuncName: function() {
                        return adminFunc;
                    },
                    usr: function() {
                        return usr;
                    },
                    supervisorEmail: function () {
;
                        return $scope.currentUser.Email;
                    }
                }
            });
            
            modalSupervisorConfirmDialog.result.then(function(resultCode){
                if(resultCode === 1) {
                    toastrFactory.success();
                    //remove from the model so the row is deleted from the table
                    $scope.usersToApprove.splice($scope.usersToApprove.indexOf(usr),1);
                } else {
                    toastrFactory.error("The request could not be completed.  Please check that the user you are trying to approve exists and is not already approved.");
                }
            });
        };
        
        
        
       
   
});

app.controller('UserAdminModalInstanceController', function($scope, $uibModalInstance, usr, supervisorEmail, adminFuncName, userService,toastrFactory) {
    $scope.adminFunction = adminFuncName;
     
    
    if($scope.adminFunction === "Approve"){
        $scope.Ok = function() {
            //call the approve operation and return the result to the user controller 
            userService.approveUser(usr, supervisorEmail, $scope.pswd)
               .then(function(response) {
                  if(response.data[0].isSuccess === "true") {
                       $uibModalInstance.close(1);
                   } else {
                       $uibModalInstance.close(0);
                   }
               },
               function () { 
                   toastrFactory.error("The user approval request could not be completed due to an unexpected error.");
                   $uibModalInstance.dismiss('cancel');
               });
        };
    }
    
    if($scope.adminFunction === "Reject"){
        $scope.Ok = function () {
            //call the reject operation and return the result to user controller 
             userService.rejectUser(usr, supervisorEmail, $scope.pswd)
               .then(function(response) {
                  if(response.data[0].isSuccess === "true") {
                       $uibModalInstance.close(1);
                   } else {
                       $uibModalInstance.close(0);
                   }
               },
               function () { 
                   toastrFactory.error("The user reject request could not be completed due to an unexpected error.");
                   $uibModalInstance.dismiss('cancel');
               });
        };
    }
    
    $scope.Cancel = function() {
        $uibModalInstance.dismiss('cancel');
    }
});

app.controller('ActivateController', function($scope,userService, $routeParams) {
    $scope.ActivationMessage = "";
    userService.activateUser($routeParams.linkId)    
        .then(function (response) {   
            
              if(response.data[0].isSuccess === "true") {
                       $scope.ActivationMessage = "Your account has been activated!";
                   } else {
                       $scope.ActivationMessage = "Your account could not be activated. Please make sure your account has been approved by your supervisor.";
                   }
        }, //this next function only happens on error
        function ()
        {
            $scope.ActivationMessage = "There was an error when trying to activate your account.";
        });
   
});



