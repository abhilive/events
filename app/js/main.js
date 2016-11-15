/**
 * Must include the dependency on 'ngMaterial'
 */
var app = angular.module('SclApp', ['ngMaterial', 'ngAnimate', 'angular-flexslider', 'ngMessages', 'ui.router', 'material.svgAssetsCache']);

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
	      //console.log('Login Modal');
        loginModal()
          .then(function () {
            return $state.go(toState.name, toParams);
          })
          .catch(function () {
            return $state.go('index');
          });
    }
    $rootScope.title = pageTitle;
	  $rootScope.stateIsLoading = true;
  });
  
  $rootScope.$on('$stateChangeSuccess', function (event, toState, toParams) {
	  $rootScope.stateIsLoading = false;
  });

});


app.config(function($stateProvider, $urlRouterProvider) {

  $urlRouterProvider.when('', '/');
  // For any unmatched url, redirect to /state1
  $urlRouterProvider.otherwise("/404");
  
  $stateProvider
    .state('index', {
      url: "/",
      templateUrl: "partials/dashboard.html",
      controller: "UserDashboardCtrl",
	    data: {
        requireLogin: false,
        title: 'smartData Cultural League'
      }
    })
    .state('notifications', {
      url: '/notifications',
      templateUrl: "partials/notifications.html",
      data: {
       requireLogin: false,
       title: 'smartData Cultural League | Notifications'
      }
    })
	.state('showwhatshot', {
      url: '/showwhatshot',
      templateUrl: "partials/showwhatshot.html",
	    /*controller: "WhatshotCtrl",*/
      data: {
       requireLogin: false,
       title: 'smartData Cultural League | Whats Hot'
      }
    })
	.state('photosnvideos', {
      url: '/photosnvideos',
      templateUrl: "partials/photosnvideos.html",
	    /*controller: "PhotosnvideosCtrl",*/
      data: {
       requireLogin: false,
       title: 'smartData Cultural League | Photos & Videos'
      }
    })
	 .state('contactus', {
      url: '/contactus',
      templateUrl: "partials/contactus.html",
	    controller: "ContactusCtrl",
      data: {
       requireLogin: false,
       title: 'smartData Cultural League | Contact Us'
      }
    })
    .state('vote', {
      url: '/peoplechoiceaward',
      templateUrl: "partials/peoplechoiceaward.html",
      controller: 'peopleChoiceAwardCtrl',
      controllerAs: 'ctrl',
      data: {
      requireLogin: false,
      title: 'smartData Cultural League | People Choice Awards'
      }
    })
	 .state('photosnvideos.viewpics', {
      url: '/viewpics/:location/:forEvent',
		  templateUrl: "partials/participants/photos.html",
      controller: "PicsCtrl",
		  /*controller: function($scope, $stateParams) {
          $scope.partId = $stateParams.forEvent;
			    console.log($scope.partId);
		  },*/
		  data: {
			 requireLogin: false,
       title: 'smartData Cultural League | Participants Photos'
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
  .state('admin.userentries', {
    url: '/admin/userentries',
    templateUrl: "partials/admin/admin.userentries.html",
    data: {
      title: 'smartData Cultural League | Admin Dashboard | User Entries'
        },
    })
	.state('admin.dashboard', {
		url: '/admin/dashboard',
		templateUrl: "partials/admin/admin.dashboard.html",
		data: {
			title: 'smartData Cultural League | Admin Dashboard'
        },
    })
});

app.service('loginModal', function ($mdDialog, $rootScope, $state, authenticationSvc) {

  function assignCurrentUser (user) {
    $rootScope.currentUser = user;
    return user;
  }

  return function(ev) {
      var instance = $mdDialog.show({
        controller: function($scope, $mdDialog, $window, $state, authenticationSvc) {
            $scope.cancel = function () {
              $mdDialog.cancel();
            };

            $scope.confirm = function (email, password) {
              authenticationSvc.login(email, password).then(function (result) {
                  if (result.status==='success') {
                      $mdDialog.cancel();
                      $state.go('admin.dashboard');
                      /*Uncomment below if you want to open admin in new tab
                      Ref Lnk: http://stackoverflow.com/questions/23516289/angularjs-state-open-link-in-new-tab*/
                      /*var url = $state.href('admin.dashboard');
                      $window.open(url,'_blank');*/
                  } else {
                      $scope.email = $scope.password = '';
                      $rootScope.status = result.message;
                  }
              }, function (error) {
                  $window.alert("Invalid credentials");
                  $mdDialog.cancel();
                  //console.log(error);
              });
            };
        },
        //scope: $scope.$new(), // Get the parent controller scope // https://github.com/angular/material/issues/1531
        templateUrl: 'partials/admin/admin.login.html',
        parent: angular.element(document.body),
        targetEvent: ev,
        clickOutsideToClose: true,
        fullscreen: true
      })
      .then(function(adminuser) {
        /*console.log(adminuser);*/
      }, function() {
        //console.log('cancel');
      });
      return instance.then(assignCurrentUser);
	};

});

// App Main Controller
app.controller('AppCtrl', function($scope, $rootScope, $state, $mdDialog, $mdBottomSheet, $mdToast, Data, loginModal, authenticationSvc) {
  
  console.log('AppCtrl reporting for duty.');

  $scope.openAdmin = function(ev){
    loginModal();
  };

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
      //console.log(stateName);
      $state.go(stateName);
  }
  
  $scope.logout = function() {
  	authenticationSvc.logout()
  		.then(function (result) {
  			$state.go('index');
  		}, function (error) {
  			console.log(error);
		});
	};

  $scope.openBoxForUserEntry = function() {
    $mdBottomSheet.show({
      templateUrl: 'partials/form-add-user-entry.html',
      controller: 'BottomSheetCtrlForEntry',
      scope: $scope.$new(), // Get the parent controller scope // https://github.com/angular/material/issues/1531
    }).then(function(response) {
      console.log('test');
    });
  };

  //For feedback form
  $scope.showFeedbackPupup = function(ev) {
    $mdDialog.show({
      controller: 'FeedbackController',
      templateUrl: 'partials/feedback.tmpl.html',
      parent: angular.element(document.body),
      targetEvent: ev,
      clickOutsideToClose:true,
      fullscreen: true
    })
    .then(function(feedback) { //Resolved Promise
      Data.put("feedback/add", feedback)
          .then(function(result){
            if(result.status==='success') {
              $scope.showSimpleToast('Thanks for your feedback.');
            } else {
              $scope.showSimpleToast('Error Occured:'+result.message);
            }    
      });
      //$scope.status = 'You said the information was "' + answer + '".';
    }, function() { //Reject Promise
      //console.log('reject')
      //$scope.status = 'You cancelled the dialog.';
    });
  };

}); // End Controller - AppCtrl

app.controller('peopleChoiceAwardCtrl', function($scope, $http, $timeout, $q, Data) {
  console.log('PeopleChoiceAwardCtrl reporting for duty.');
      
      var self = this;
      self.selectedItem  = null;
      self.searchText    = null;
      self.querySearch   = querySearch;
      // ******************************
      // Internal methods
      // ******************************
      /**
       * Search for states... use $timeout to simulate
       * remote dataservice call.
       */
      function querySearch (query) {
        return Data.get('getemails/'+query).then(function(result){
            return result.data;
        });
      }
      
      self.validateUser = validateUser;
      function validateUser(user_email,emp_id) {
        return Data.get('verifyuser/'+user_email.$modelValue+'/'+emp_id).then(function(result){
            if(result.status=='success') {
              self.user = result.data;
            } else {
              self.user = false;
            }
            self.status = result.status;
            self.message = result.message;
            return result;
        });
      }

      // Retrieve all Activities
      Data.get('activities').then(function(result){
        self.perfgroup = result.data;
      });       
      
      self.getParticipants = getParticipants;
      function getParticipants() {
        return Data.get('getparticipants/'+self.group_id).then(function(result){
          self.part_id = undefined; //reset participant drop-down
          self.participants = result.data;
        });
      }
      //Main Function to cast vote
      self.castvote = castvote;
      function castvote() {
        Data.post('castvote',{ group_id: self.group_id, part_id: self.part_id }).then(function(response){
            /*console.log(response);*/
            if(response.status=='success') {
              //reset all fields - Upper Form
              /*self.searchText = null;
              $scope.emp_id = undefined;*/
              //app.copy({},searchForm);
              //reset form fields - Lower Form
              self.group_id = undefined;
              self.part_id = undefined;
              // Hide forms & status Message
              self.status = self.message = false;
              self.votingStatus = true;
            }
          //$scope.prod.imagePaths = data.data;
        });
      }
    });// END - People Choice Award Controller

app.controller('FeedbackController', function ($mdDialog, $mdToast, $scope, Data) {
    console.log('FeedbackController reporting for duty.');

    $scope.brloc = [
        { id: 1, name: 'Nagpur' },
        { id: 2, name: 'Mohali' },
        { id: 3, name: 'Dehradun' }
    ];

    $scope.confirm = function(feedback) {
      $mdDialog.hide(feedback);
    };

    $scope.cancel = function() {
      $mdDialog.cancel();
    };
});

app.controller('ContactusCtrl', function ($state, $scope, $rootScope, $mdDialog, Data) { 
  var imagePath = 'images/pics/60.jpeg';
   $scope.mohalihr = [
      {
        face : imagePath,
        what: 'davinder.kaur@smartdatainc.net',
        who: 'Davinder Kaur',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      }
    ];
    $scope.nagpurhr = [
      {
        face : imagePath,
        what: 'deepti.gaddamwar@smartdatainc.net',
        who: 'Deepti Gaddamwar',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      }
    ];
    $scope.dehradunhr = [
      {
        face : imagePath,
        what: 'siddharthg@smartdatainc.net',
        who: 'Siddharth Gupta',
        when: '3:08PM',
        notes: " I'll be in your neighborhood doing errands"
      }
    ];
    /*
    Ref Link1 : http://stackoverflow.com/questions/22925326/angularjs-mailto-not-sending-emails
    Ref Link2 : http://stackoverflow.com/questions/5620324/mailto-with-html-body
    */
    $scope.sendMailToHr = function (email, name) {
      var subject = 'Regarding SCL';
      var body = name;
      var formattedBody = name+ ",\n";
      var link = "mailto:"+ email
             + "?subject=" + escape(subject)
             + "&body=Hello " + encodeURIComponent(formattedBody); 

      window.location.href = link;
    };
});

app.controller('UserDashboardCtrl', function ($state, $scope, $rootScope, $mdDialog, Data) {
  console.log('UserDashboardCtrl reporting for duty.');

  $scope.brloc = [
        { id: 1, name: 'Nagpur' },
        { id: 2, name: 'Mohali' },
        { id: 3, name: 'Dehradun' }
    ];

  // Retrieve all Activities
  Data.get('activities').then(function(data){
      $scope.activities = data.data;
  });

  // Retrieve all Groups - Retrieved For Listing
  Data.get('groups').then(function(data){
      $scope.groups = data.data;
  });

  $scope.goToPerson = function(person, ev) {
    $mdDialog.show({
      controller: function($scope, $mdDialog, personInfo) {
        Data.get('viewparticipant/'+personInfo.id).then(function(data){
            $scope.participant = data.data;
        });
        $scope.hide = function() {
          $mdDialog.hide();
        };
      },
      templateUrl: 'partials/participants/viewparticipant.html',
      parent: angular.element(document.body),
      targetEvent: ev,
  	  locals: { personInfo: person },
        clickOutsideToClose:true
      })
      .then(function(answer) {
        $scope.status = 'You said the information was "' + answer + '".';
      }, function() {
        $scope.status = 'You cancelled the dialog.';
      });
  };

});

app.controller('WhatshotCtrl', function ($mdDialog, $state, $scope, $rootScope, Data) {
	console.log('WhatshotCtrl reporting for duty.');
});

app.controller('PicsCtrl', function ($mdDialog, $state, $stateParams, $scope, $rootScope, Data) {
  console.log('PicsCtrl reporting for duty.');

  $scope.prod = {imagePaths: []};

  Data.post('getpics',{ location: $stateParams.location, forEvent: $stateParams.forEvent }).then(function(data){
    $scope.prod.imagePaths = data.data;
  });

});

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
app.controller('LoginCtrl', function ($mdDialog, $state, $scope, $rootScope, $window, authenticationSvc) {

  console.log('LoginCtrl reporting for duty.');

  this.cancel = function () {
      $mdDialog.cancel();
  };

  this.submit = function (email, password) {
    authenticationSvc.login(email, password).then(function (result) {
        if (result.status==='success') {
            $mdDialog.cancel();
            $state.go('admin.dashboard');
        } else {
            $scope.email = $scope.password = '';
            $rootScope.status = result.message;
        }
    }, function (error) {
        $window.alert("Invalid credentials");
        $mdDialog.cancel();
        console.log(error);
    });
  };

});

/*All admin controllers*/
/*For Dashboard page*/
app.controller('DashboardCtrl', function ($scope, $rootScope, $mdDialog, $mdBottomSheet, Data, authenticationSvc) {
    console.log('DashboardCtrl reporting for duty.');
    // Retrieve all Performers
    var self = this;
    self.showEntries = function() {
        Data.get('participants').then(function(data){
          $scope.participants = data.data;
        });
    }

    $scope.deleteEntry = function (ev,entry) {
        var confirm = $mdDialog.confirm()
          .title('Would you really like to delete this entry?')
          .textContent("This action can't be undone.")
          .ariaLabel('Lucky day')
          .targetEvent(ev)
          .ok('Please do it!')
          .cancel('Cancel');
      $mdDialog.show(confirm).then(function() {
        Data.delete("participant/"+entry.id).then(function(result){
            //$scope.participants = _.without($scope.participants, _.findWhere($scope.participants, {id:entry.id}));
            self.showEntries();
        });
        $scope.showSimpleToast('Entry has been deleted.');
        $scope.status = 'You decided to get rid of your debt.';
      }, function() {
        $scope.status = 'You decided to keep your debt.';
      });
    };
    //Deprecated - We are not using it anymore - Group are fixed
    /*$scope.openBoxForGroup = function() {
      $mdBottomSheet.show({
          templateUrl: 'partials/form-add-group.html',
          controller: 'BottomSheetCtrlForGroup'
      }).then(function(clickedItem) {
          $scope.alert = clickedItem['name'] + ' clicked!';
      });
    };*/
    /*Open box for admin entry*/
    $scope.openBoxForAdminEntry = function() {
      $mdBottomSheet.show({
        templateUrl: 'partials/admin/form-add-admin-entry.html',
        controller: 'BottomSheetCtrlForEntry'
      }).then(function(clickedItem) {
        console.log('test');
      });
    };

    $scope.editEntry = function(person, ev) {
      //console.log(person);
      $mdDialog.show({
        controller: 'EditParticipantCtrl',
        templateUrl: 'partials/admin/editparticipant.html',
        parent: angular.element(document.body),
        scope: $scope.$new(), // Get the parent controller scope // https://github.com/angular/material/issues/1531
        targetEvent: ev,
      locals: { personInfo: person },
        clickOutsideToClose:true
      })
      .then(function(answer) {
        //$scope.status = 'You said the information was "' + answer + '".';
        //console.log('success');
      }, function() {
        //$scope.status = 'You cancelled the dialog.';
        //console.log('dialog cancelled');
        self.showEntries();
      });
    };
});

app.controller('UserEntriesCtrl', function ($scope, $rootScope, $mdDialog, $mdBottomSheet, $window, Data, authenticationSvc) {
    console.log('UserEntriesCtrl reporting for duty.');
    // Retrieve all Performers
    var access_token = JSON.parse($window.sessionStorage["userInfo"]).accessToken;

    var self = this;
    self.showEntries = function() {
        Data.post('users', { accessToken: access_token }).then(function(data){
          $scope.users = data.data;
        });
    }
    self.deleteEntry = function (ev,entry) {
        var confirm = $mdDialog.confirm()
          .title('Would you really like to delete this entry?')
          .textContent("This action can't be undone. All choices of this user will be removed.")
          .ariaLabel('Lucky day')
          .targetEvent(ev)
          .ok('Please do it!')
          .cancel('Cancel');
      $mdDialog.show(confirm).then(function() {
        Data.delete("user/"+entry.id+"/"+access_token).then(function(result){
            self.showEntries();
        });
        $scope.showSimpleToast('User is deleted.');
        //$scope.status = 'You decided to get rid of your debt.';
      }, function() {
        //$scope.status = 'You decided to keep your debt.';
      });
    };
    /*Open box for admin entry*/
    self.openBoxForUserEntry = function() {
      $mdBottomSheet.show({
        templateUrl: 'partials/admin/form-admin-add-user-entry.html',
        controller: 'BottomSheetCtrlForUserEntry',
        scope: $scope.$new(), // Get the parent controller scope // https://github.com/angular/material/issues/1531
      }).then(function(clickedItem) {
        self.showEntries();
      });
    };
    /*Open for Edit Entry - Not In Use*/
    /*self.editEntry = function(person, ev) {
      //console.log(person);
      $mdDialog.show({
        controller: 'EditParticipantCtrl',
        templateUrl: 'partials/admin/editparticipant.html',
        parent: angular.element(document.body),
        scope: $scope.$new(), // Get the parent controller scope // https://github.com/angular/material/issues/1531
        targetEvent: ev,
      locals: { personInfo: person },
        clickOutsideToClose:true
      })
      .then(function(answer) {
        $scope.status = 'You said the information was "' + answer + '".';
      }, function() {
        $scope.status = 'You cancelled the dialog.';
      });
    };*/
});

app.controller('EditParticipantCtrl', function ($location, $scope, $rootScope, $mdDialog, $mdToast, Data, personInfo) {
    //console.log(personInfo.id);

    Data.get('getparticipant/'+personInfo.id).then(function(data){
        $scope.participant = data.data;
    });

    $scope.brloc = [
        { id: 1, name: 'Nagpur' },
        { id: 2, name: 'Mohali' },
        { id: 3, name: 'Dehradun' }
    ];
    Data.get('activities').then(function(data){
        $scope.activities = data.data;
    });

    Data.get('statuses').then(function(data){
        $scope.statuses = data.data;
    });

    $scope.cancel = function() {
      $mdDialog.cancel();
    };

    $scope.updateParticipant = function() {
        //console.log($scope.participant);
        Data.put("participant/update", $scope.participant)
            .then(function(result){
              if(result.status==='success') {
                  $mdDialog.cancel();
                  $scope.showSimpleToast('Information Updated.');
              } else {
                  $scope.showSimpleToast('Error Occured:'+result.message);
              }    
        });
    }
});
/*Not In Use : Groups are fixed*/
/*app.controller('BottomSheetCtrlForGroup', function ($location, $scope, $rootScope, Data, authenticationSvc) {
    Data.get('activities').then(function(data){
        $scope.activities = data.data;
    });
});*/

app.controller('BottomSheetCtrlForUserEntry', function ($mdBottomSheet, $scope, $window, Data, authenticationSvc) {
  console.log('BottomSheetCtrlForUserEntry reporting for duty.');

    var access_token = JSON.parse($window.sessionStorage["userInfo"]).accessToken;
    var self = this;
    self.showAdminUserConfirm = function(ev) {
        //console.log(access_token);
        self.user.accessToken = access_token;
        Data.put("user/add", self.user)
            .then(function(result){
              if(result.status==='success') {
                  $scope.showSimpleToast('Entry added successfully.');
              } else {
                  $scope.showSimpleToast('Error Occured:'+result.message);
              }    
        });
        $mdBottomSheet.hide();
    };
});

app.controller('BottomSheetCtrlForEntry', function ($mdDialog, $mdBottomSheet, $scope, Data) {
  console.log('BottomSheetCtrlForEntry reporting for duty.');
    //Retrieve all locations
    $scope.brloc = [
        { id: 1, name: 'Nagpur' },
        { id: 2, name: 'Mohali' },
        { id: 3, name: 'Dehradun' }
    ];

    // Retrieve all Performers
    Data.get('activities').then(function(data){
        $scope.activities = data.data;
    });

    $scope.showUserConfirm = function(ev) {
      var confirm = $mdDialog.confirm()
            .title('Would you like to participate?')
            .textContent('Your entry will be first reviewd by HR Deparment.')
            .ariaLabel('Lucky day')
            .targetEvent(ev)
            .ok('Okay! I Agree')
            .cancel('Cancel');
      $mdDialog.show(confirm).then(function() { //Promise Resolved
        $scope.participant.status = '2'; //For wildcard Entry
        Data.put("participants/add", $scope.participant)
            .then(function(result){
              if(result.status==='success') {
                  $scope.showSimpleToast('HR Person will contact you shortly.');
              } else {
                  $scope.showSimpleToast('Error Occured:'+result.message);
              }    
        });
        $mdBottomSheet.hide();
      }, function() { //Promise reject

      });
    };

});