/**
 * Must include the dependency on 'ngMaterial'
 */
var app = angular.module('SclApp', ['ngMaterial', 'ui.router', 'ngMessages', 'material.svgAssetsCache']);

app.config( function($mdThemingProvider) {  
    $mdThemingProvider.theme('default')
          .primaryPalette('blue')
          .accentPalette('indigo')
          .warnPalette('red')
          .backgroundPalette('grey');

});

app.service('loginModal', function ($mdDialog, $rootScope) {

  function assignCurrentUser (user) {
    $rootScope.currentUser = user;
    return user;
  }

  return function() {
    var instance = $mdDialog.show({
      controller: DialogController,
      templateUrl: 'dialog1.tmpl.html',
      parent: angular.element(document.body),
      targetEvent: ev,
      clickOutsideToClose:true,
      fullscreen: useFullScreen
    })
    .then(function(answer) {
      $scope.status = 'You said the information was "' + answer + '".';
    }, function() {
      $scope.status = 'You cancelled the dialog.';
    });

    /*var instance = $mdDialog.open({
      templateUrl: 'views/loginModalTemplate.html',
      controller: 'LoginModalCtrl',
      controllerAs: 'LoginModalCtrl'
    })*/

    return instance.result.then(assignCurrentUser);
  };

});

app.run(function ($rootScope, $state, authenticationSvc) {

  $rootScope.$on('$stateChangeStart', function (event, toState, toParams) {
    console.log('test-app-run');
    var requireLogin = toState.data.requireLogin;

    var userInfo = authenticationSvc.getUserInfo();

	console.log(userInfo);
    console.log(requireLogin);
	//delete $scope.userInfo;
    if (requireLogin && userInfo === null) {
      console.log($rootScope.currentUser);
      console.log(requireLogin);
      event.preventDefault();

      console.log(toState.name);
      console.log(toParams);

      $state.go('login');

      /*loginModal()
        .then(function () {
          return $state.go(toState.name, toParams);
        })
        .catch(function () {
          return $state.go('welcome');
        });*/
    }

  });

});

app.config(function ($stateProvider, $urlRouterProvider) {
  // the known route, with missing '/' - let's create alias
  $urlRouterProvider.when('', '/');
  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/login"); 

  $stateProvider
    .state('login', {
      url: '/login',
      templateUrl: "partials/login.html",
      data: {
        requireLogin: false
      }
    })
    .state('dashboard', {
      url: '/dashboard',
      templateUrl: "partials/dashboard.html",
      data: {
        requireLogin: true
      }
    })

});

app.controller('MainCtrl', function ($scope, $rootScope, $location, $mdToast, $mdDialog, authenticationSvc) {
  console.log('MainCtrl reporting for duty.');
  
    var userInfo = authenticationSvc.getUserInfo();
    if(userInfo != null) { $rootScope.isLoggedIn=true; } else { $rootScope.isLoggedIn = false; }

    $scope.showSimpleToast = function(message) {
      $mdToast.show(
        $mdToast.simple()
          .textContent(message)
          .position('top right')
          .hideDelay(3000)
      );
    };
	
	var vm = this;
  this.logout = function() {
	authenticationSvc.logout()
		.then(function (result) {
			$scope.userInfo = null;
			$rootScope.isLoggedIn = false;//Added For check
			$location.path("/login");
		}, function (error) {
			console.log(error);
		});
	};

}); 

app.controller('DashboardCtrl', function ($scope, $rootScope, $mdDialog, $mdBottomSheet, Data, authenticationSvc) {
    console.log('DashboardCtrl reporting for duty.');

	var userInfo = authenticationSvc.getUserInfo();
    if(userInfo != null) { $rootScope.isLoggedIn=true; } else { $rootScope.isLoggedIn = false; }
	
    // Retrieve all Performers
    Data.get('participants').then(function(data){
        $scope.participants = data.data;
    });

    $scope.deleteEntry = function (ev,entry) {
        var confirm = $mdDialog.confirm()
          .title('Would you really like to delete this entry?')
          .textContent("This action can't be undone.")
          .ariaLabel('Lucky day')
          .targetEvent(ev)
          .ok('Please do it!')
          .cancel('Cancel');
      $mdDialog.show(confirm).then(function() {
        console.log(entry);
        Data.delete("participants/"+entry.id).then(function(result){
            console.log(result);
            $scope.participants = _.without($scope.participants, _.findWhere($scope.participants, {id:entry.id}));
        });
        $scope.showSimpleToast('Entry has been deleted.');
        $scope.status = 'You decided to get rid of your debt.';
      }, function() {
        $scope.status = 'You decided to keep your debt.';
      });
    };

    $scope.openBoxForGroup = function() {
      $mdBottomSheet.show({
		  templateUrl: '../partials/form-add-group.html',
		  controller: 'BottomSheetCtrlForGroup'
		}).then(function(clickedItem) {
		  $scope.alert = clickedItem['name'] + ' clicked!';
		});
    };
	
	$scope.openBoxForEntry = function() {
		$mdBottomSheet.show({
		  templateUrl: '../partials/form-add-entry.html',
		  controller: 'BottomSheetCtrlForEntry'
		}).then(function(clickedItem) {
		  console.log('test');
		});
	  };

});

app.controller('BottomSheetCtrlForGroup', function ($location, $scope, $rootScope, Data, authenticationSvc) {
	// Retrieve all Performers
    Data.get('activities').then(function(data){
        $scope.activities = data.data;
        //$scope.foodCouponsInit = angular.copy($scope.foodCoupons);// Initialize to different object as we need to refresh after save
    });
});

app.controller('BottomSheetCtrlForEntry', function ($mdDialog, $mdToast, $mdBottomSheet, $location, $scope, $rootScope, Data, authenticationSvc) {
	console.log('BottomSheetCtrlForEntry reporting for duty.');
	$scope.brloc = [
        { id: 1, name: 'Nagpur' },
        { id: 2, name: 'Mohali' },
        { id: 3, name: 'Dehradun' }
    ];

    // Retrieve all Performers
    Data.get('activities').then(function(data){
        $scope.activities = data.data;
        //$scope.foodCouponsInit = angular.copy($scope.foodCoupons);// Initialize to different object as we need to refresh after save
    });

    $scope.showSimpleToast = function(message) {
      $mdToast.show(
        $mdToast.simple()
          .textContent(message)
          .position('top right')
          .hideDelay(3000)
      );
    };

    $scope.showConfirm = function(ev) {
      var confirm = $mdDialog.confirm()
            .title('Are you sure to consider this entry for mega event?')
            .textContent('This entry is only for semi finals.')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Yes! Consider this Entry')
            .cancel('Cancel');
      $mdDialog.show(confirm).then(function() {
        /*Data.post("participants/add", $scope.participant)
            .then(function(result){
                if(result.status==='success') {
                    $scope.showSimpleToast('Entry is confirmed for semifinals.');
                    Data.get('participants').then(function(data){
                        $scope.participants = data.data;
                    });
                } else {
                    $scope.showSimpleToast('Error Occured:'+result.message);
                }    
        });*/
        /*$scope.showRegister = false;
        $scope.participant = $scope.user.name = $scope.user.email = {};*/
        $mdBottomSheet.cancel();
      }, function() {
        //$scope.status = '';
        $scope.showRegister = false;
        $scope.user = $scope.user.name = $scope.user.email = {};
      });
    };
});

app.controller('LoginCtrl', function ($location, $scope, $rootScope, authenticationSvc) {

  console.log('LoginCtrl reporting for duty.');
  //this.cancel = $scope.$dismiss;

  this.submit = function (email, password) {
    authenticationSvc.login(email, password).then(function (result) {
        if (result.status==='success') {
            //console.log(result.data);
            $rootScope.currentUser = result.data;
            $location.path("/dashboard");
        } else {
            $scope.email = $scope.password = '';
            //$rootScope.status = result.message;
            $scope.showSimpleToast(result.message);
        }
        //$rootScope.currentUser = user;
        //$scope.$close(user);
        //$rootScope.userInfo = user; //Send Data From One Controller to another
        //console.log($rootScope.userName);
        //$location.path("/");
    }, function (error) {
        $window.alert("Invalid credentials");
        console.log(error);
    });
  };

});

function DialogController($scope, $mdDialog, Data) {

    $scope.brloc = [
        { id: 1, name: 'Nagpur' },
        { id: 2, name: 'Mohali' },
        { id: 3, name: 'Dehradun' }
    ];

    // Retrieve all Performers
    Data.get('activities').then(function(data){
        $scope.activities = data.data;
        //$scope.foodCouponsInit = angular.copy($scope.foodCoupons);// Initialize to different object as we need to refresh after save
    });

  $scope.hide = function() {
    $mdDialog.hide();
  };
  $scope.cancel = function() {
    $mdDialog.cancel();
  };
  $scope.answer = function(answer) {
    $mdDialog.hide(answer);
  };
}