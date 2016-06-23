/**
 * Must include the dependency on 'ngMaterial'
 */
var app = angular.module('SclApp', ['ngMaterial', 'ngMessages', 'material.svgAssetsCache']);

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

app.controller('AppCtrl', function($scope, $mdDialog, $window, $mdToast, Data) {

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
        $window.open('http://172.10.55.66/events/admin/', '_blank');
    };

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

    // Retrieve all Performers
    Data.get('groups').then(function(data){
        $scope.groups = data.data;
        //$scope.foodCouponsInit = angular.copy($scope.foodCoupons);// Initialize to different object as we need to refresh after save
    });

    var last = {
      bottom: false,
      top: true,
      left: false,
      right: true
    };
    $scope.toastPosition = angular.extend({},last);
    $scope.getToastPosition = function() {
      sanitizePosition();
      return Object.keys($scope.toastPosition)
        .filter(function(pos) { return $scope.toastPosition[pos]; })
        .join(' ');
    };
    function sanitizePosition() {
      var current = $scope.toastPosition;
      if ( current.bottom && last.top ) current.top = false;
      if ( current.top && last.bottom ) current.bottom = false;
      if ( current.right && last.left ) current.left = false;
      if ( current.left && last.right ) current.right = false;
      last = angular.extend({},current);
    }

    $scope.showSimpleToast = function(message) {
      var pinTo = $scope.getToastPosition();
      $mdToast.show(
        $mdToast.simple()
          .textContent(message)
          .position(pinTo)
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

app.controller('ListCtrl', function($scope, $mdDialog, $filter, Data) {

    
    // Retrieve all Performers
    Data.get('participants').then(function(data){
        $scope.participants = data.data;
        //$scope.foodCouponsInit = angular.copy($scope.foodCoupons);// Initialize to different object as we need to refresh after save
    });
    $scope.perf = [
        { id: 1, name: 'Pref1', img: 'img/100-0.jpeg', newMessage: true },
        { id: 2, name: 'Pref2', img: 'img/100-1.jpeg', newMessage: false },
        { id: 3, name: 'Pref3', img: 'img/100-2.jpeg', newMessage: false },
        { id: 4, name: 'Pref4', img: 'img/100-2.jpeg', newMessage: false },
        { id: 5, name: 'Pref5', img: 'img/100-2.jpeg', newMessage: false },
        { id: 6, name: 'Pref6', img: 'img/100-2.jpeg', newMessage: false },
        { id: 7, name: 'Pref7', img: 'img/100-2.jpeg', newMessage: false },
        { id: 8, name: 'Pref8', img: 'img/100-2.jpeg', newMessage: false },
    ];

    $scope.selected = [];
    $scope.toggle = function (item, list) {
        var idx = list.indexOf(item);
        if (idx > -1) {
          list.splice(idx, 1);
        }
        else {
          list.push(item);
        }
        $scope.showMe();
    };
    $scope.exists = function (item, list) {
        return list.indexOf(item) > -1;
    };
    $scope.isIndeterminate = function() {
        return ($scope.selected.length !== 0 &&
            $scope.selected.length !== $scope.perf.length);
    };
    $scope.isChecked = function() {
        return $scope.selected.length === $scope.perf.length;
    };
    $scope.toggleAll = function() {
        //console.log($scope.selected.length);
        //console.log($scope.perf.length);
        if ($scope.selected.length === $scope.perf.length) {
          $scope.selected = [];
        } else if ($scope.selected.length === 0 || $scope.selected.length > 0) {
          $scope.selected = $scope.perf.slice(0);
        }
        $scope.showMe();
    };

    /*$scope.people = [
        { name: 'Janet Perkins', img: 'img/100-0.jpeg', newMessage: true },
        { name: 'Mary Johnson', img: 'img/100-1.jpeg', newMessage: false },
        { name: 'Peter Carlsson', img: 'img/100-2.jpeg', newMessage: false }
    ];*/

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

    $scope.showMe = function(event) {
        console.log($scope.data);
            console.log($scope.selected);
         $scope.albumNameArray = [];
          angular.forEach($scope.selected, function(perf){
            //console.log(perf.id);
            $scope.albumNameArray.push(perf.id);
          })
          console.log($scope.albumNameArray);
       /*Data.post("participants", $scope.order).then(function(result){
          if(result.status == 'success') {
            $scope.submitStatusSuccess = true;
            $scope.submitStatusError = false;
            $scope.message = result.message;
            $scope.orders.push(result.data);
            $scope.orders = $filter('orderBy')($scope.orders, 'order_id', 'reverse');
            // Set Order & Order form to it's initial state
            $scope.order = {};
            $scope.foodCoupons = angular.copy($scope.foodCouponsInit);
            $scope.orderForm.$setPristine();
          } else {
            $scope.submitStatusSuccess = false;
            $scope.submitStatusError = true;
            $scope.message = result.message;
          }
        });*/
    }

});