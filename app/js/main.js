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


app.run(function ($rootScope, $state, loginModal, authenticationSvc) {

  $rootScope.$on('$stateChangeStart', function (event, toState, toParams) {
    var requireLogin = toState.data.requireLogin;
    var pageTitle = toState.data.title;

    if (requireLogin && !authenticationSvc.isAuthenticated()) {
        event.preventDefault();
	      //$state.go('admin.login');
	      console.log('Login Modal');
        loginModal()
          .then(function () {
            console.log(toState.name);
            console.log('1');
            return $state.go(toState.name, toParams);
          })
          .catch(function () {
            console.log('2');
            return $state.go('index');
          });
    }
    $rootScope.title = pageTitle;
  });

});

app.config(function($stateProvider, $urlRouterProvider) {

	// the known route, with missing '/' - let's create alias
$urlRouterProvider.when('', '/');
  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/404");
  
  $stateProvider
    .state('index', {
      url: "/",
      templateUrl: "partials/dashboard.html",
	    data: {
        requireLogin: false,
        title: 'smartData Cultural League'
      }
    })
    .state('notification', {
      url: '/notification',
      templateUrl: "partials/notification.html",
      data: {
       requireLogin: false,
       title: 'smartData Cultural League | Notifications'
      }
    })
	 .state('viewpics', {
      url: '/viewpics/:participantId',
		  templateUrl: "partials/participants/viewpics.html",
		  controller: function($scope, $stateParams) {
          $scope.partId = $stateParams.participantId;
			    console.log($scope.partId);
        },
		  data: {
			 requireLogin: false,
       title: 'smartData Cultural League | Participants Pictures'
		  }
    })
    .state('404', {
      url: "/404",
      templateUrl: "partials/404.html",
	  data: {
        requireLogin: false,
        title: 'smartData Cultural League | 404 Page Not Found'
      }
    })
    .state('admin', {
  	  abstract: true,
      templateUrl: 'partials/admin/admin.html',
      data: {
        requireLogin: true
      }
    })
	.state('admin.login', {
		url: '/login',
	    templateUrl: "partials/login.html",
    })
	.state('admin.dashboard', {
		url: '/admin/dashboard',
		templateUrl: "partials/admin/admin.dashboard.html",
		data: {
			title: 'smartData Cultural League | Admin Dashboard'
        },
    })
});

app.service('loginModal', function ($mdDialog, $rootScope) {

  function assignCurrentUser (user) {
    $rootScope.currentUser = user;
    return user;
  }

  return function(ev) {

      var instance = $mdDialog.show({
        controller: DialogController,
        templateUrl: 'partials/admin/admin.login.html',
        parent: angular.element(document.body),
        targetEvent: ev,
        clickOutsideToClose:true,
        fullscreen: true
      })
      .then(function(answer) {
        console.log('success');
      }, function() {
        console.log('cancel');
      });

      return instance.then(assignCurrentUser);
  };

});

// App Main Controller
app.controller('AppCtrl', function($scope, $rootScope, $state, $mdDialog, $window, $mdToast, Data, loginModal, authenticationSvc) {
    console.log('AppCtrl reporting for duty.');

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
      loginModal();
		  //$state.go('admin.login');
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
  //To show toast messages
  $scope.showSimpleToast = function(message) {
    $mdToast.show(
      $mdToast.simple()
        .textContent(message)
        .position('top right')
        .hideDelay(3000)
    );
  };
  //Transisition to state
  $scope.goToState = function(stateName) {
      console.log(stateName);
      $state.go(stateName);
  }
  //Get All Participants Listings
  $scope.showEntries = function() {
      Data.get('participants').then(function(data){
        $scope.participants = data.data;
      });
  }
  
  $scope.logout = function() {
  console.log('test=lout');
	authenticationSvc.logout()
		.then(function (result) {
			//$location.path("/");
			$state.go('index');
		}, function (error) {
			console.log(error);
		});
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

//Admin- Login Controller
app.controller('LoginCtrl', function ($mdDialog, $state, $scope, $rootScope, authenticationSvc) {

  console.log('LoginCtrl reporting for duty.');

  this.cancel = function () {
      $mdDialog.cancel();
  };

  this.submit = function (email, password) {
    authenticationSvc.login(email, password).then(function (result) {
        if (result.status==='success') {
            //$rootScope.currentUser = result.data;
            $mdDialog.cancel();
            $state.go('admin.dashboard');
            //$scope.$close(result.data);
        } else {
            $scope.email = $scope.password = '';
            $rootScope.status = result.message;
        }
    }, function (error) {
        $window.alert("Invalid credentials");
        console.log(error);
    });
  };

});

app.controller('ListCtrl', function($scope, $mdDialog, Data) {

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

app.controller('DashboardCtrl', function ($scope, $rootScope, $mdDialog, $mdBottomSheet, Data) {
    console.log('DashboardCtrl reporting for duty.');

    // Retrieve all Performers

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
      templateUrl: 'partials/form-add-group.html',
      controller: 'BottomSheetCtrlForGroup'
    }).then(function(clickedItem) {
      $scope.alert = clickedItem['name'] + ' clicked!';
    });
    };
  
  $scope.openBoxForEntry = function() {
    $mdBottomSheet.show({
      templateUrl: 'partials/form-add-entry.html',
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
        Data.post("participants/add", $scope.participant)
            .then(function(result){
                if(result.status==='success') {
                    $scope.showSimpleToast('Entry is confirmed for semifinals.');
                    $scope.showEntries();
                } else {
                    $scope.showSimpleToast('Error Occured:'+result.message);
                }    
        });
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