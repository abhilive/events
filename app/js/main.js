/**
 * Must include the dependency on 'ngMaterial'
 */
var app = angular.module('SclApp', ['ngMaterial', 'ngMessages', 'ui.router', 'material.svgAssetsCache']);

app.config( function($mdThemingProvider) {  
    /*$mdThemingProvider.theme('default')
          .primaryPalette('blue')
          .accentPalette('indigo')
          .warnPalette('red')
          .backgroundPalette('grey');*/
    $mdThemingProvider.theme('dark-grey').backgroundPalette('grey').dark();
    $mdThemingProvider.theme('dark-orange').backgroundPalette('orange').dark();
    $mdThemingProvider.theme('dark-purple').backgroundPalette('deep-purple').dark();
    $mdThemingProvider.theme('dark-blue').backgroundPalette('blue').dark();
    $mdThemingProvider.theme('dark-yellow').backgroundPalette('yellow').dark();

});

app.config(function($mdIconProvider) {
  $mdIconProvider
    .iconSet('social', 'img/icons/sets/social-icons.svg', 24)
    .iconSet('device', 'img/icons/sets/device-icons.svg', 24)
    .iconSet('communication', 'img/icons/sets/communication-icons.svg', 24)
    .icon('md-close', 'img/icons/ic_close_24px.svg', 24)
    .defaultIconSet('img/icons/sets/core-icons.svg', 24);
});

app.run(function ($rootScope, $state) {

  $rootScope.$on('$stateChangeStart', function (event, toState, toParams) {
    var requireLogin = toState.data.requireLogin;

    if (requireLogin && typeof $rootScope.currentUser === 'undefined') {
      event.preventDefault();
	  $state.go('admin.login');
	  console.log('Login Modal');
      // get me a login modal!
    }
  });

});

app.config(function($stateProvider, $urlRouterProvider) {

	// For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/404");
  
  $stateProvider
    .state('index', {
      url: "",
      templateUrl: "partials/dashboard.html",
	  data: {
        requireLogin: false
      }
    })
    .state('404', {
      url: "/404",
      templateUrl: "partials/404.html",
	  data: {
        requireLogin: false
      }
    })
    .state('admin', {
	  abstract: true,
      data: {
        requireLogin: true
      }
    })
	.state('admin.login', {
      url: '/login',
	  templateUrl: "partials/login.html"
    })
	.state('admin.dashboard', {
      url: '/admin/dashboard',
	  templateUrl: "partials/admin.dashboard.html"
    })
});

// App Main Controller
app.controller('AppCtrl', function($scope, $state, $mdDialog, $window, $mdToast, Data) {

    $scope.user = {};
    $scope.showConfirm = function(ev) {
      var confirm = $mdDialog.confirm()
            .title('Would you like to participate?')
            .textContent('Your entry will be first reviewd by HR Deparment.')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Okay! I Agree')
            .cancel('Cancel');
      $mdDialog.show(confirm).then(function() {
        console.log($scope.user);
        //$scope.status = 'HR Person will contact you shortly.';
        $scope.showSimpleToast('HR Person will contact you shortly.');
        $scope.showRegister = false;
        $scope.user = $scope.user.name = $scope.user.email = {};
      }, function() {
        //$scope.status = '';
        $scope.showRegister = false;
        $scope.user = $scope.user.name = $scope.user.email = {};
      });
    };

    $scope.showRegister = false;
    $scope.register = function(ev) {
        console.log($scope.showRegister);
        if($scope.showRegister){
          $scope.showRegister = false;
        } else {
          //$scope.showSimpleToast();
          $scope.showRegister = true;
        }
    };

    $scope.openAdmin = function(ev){
		$state.go('admin.login');
        //$window.open('http://172.10.55.66/events/admin/', '_blank');
    };

    $scope.brloc = [
        { id: 1, name: 'Nagpur' },
        { id: 2, name: 'Mohali' },
        { id: 3, name: 'Dehradun' }
    ];

    // Retrieve all Activities
    Data.get('activities').then(function(data){
        $scope.activities = data.data;
	});

    // Retrieve all Groups
    Data.get('groups').then(function(data){
        $scope.groups = data.data;
	});

    $scope.showSimpleToast = function(message) {
      $mdToast.show(
        $mdToast.simple()
          .textContent(message)
          .position('top right')
          .hideDelay(3000)
      );
    };

}); // End Controller - AppCtrl

function DialogController($scope, $mdDialog) {
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

app.controller('ListCtrl', function($scope, $mdDialog, Data) {

    // Retrieve all Performers
    Data.get('participants').then(function(data){
        $scope.participants = data.data;
	});

    $scope.doSecondaryAction = function(event) {
        $mdDialog.show(
          $mdDialog.alert()
            .title('Secondary Action')
            .textContent('Secondary actions can be used for one click actions')
            .ariaLabel('Secondary click demo')
            .ok('Neat!')
            .targetEvent(event)
        );
    };

});

app.controller('LoginCtrl', function ($state, $scope, $rootScope, authenticationSvc) {

  console.log('LoginCtrl reporting for duty.');
  //this.cancel = $scope.$dismiss;

  this.submit = function (email, password) {
    authenticationSvc.login(email, password).then(function (result) {
        if (result.status==='success') {
            //console.log(result.data);
            $rootScope.currentUser = result.data;
            $state.go("admin.dashboard");
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