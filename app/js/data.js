app.factory("Data", ['$http', '$location',
    function ($http, $q, $location) {

        var serviceBase = 'api/index.php/';

        var obj = {};

        obj.get = function (q) {
            return $http.get(serviceBase + q).then(function (results) {
                return results.data;
            });
        };
        obj.post = function (q, object) {
            return $http.post(serviceBase + q, object).then(function (results) {
                return results.data;
            });
        };
        obj.put = function (q, object) {
            return $http.put(serviceBase + q, object).then(function (results) {
                return results.data;
            });
        };
        obj.delete = function (q) {
            return $http.delete(serviceBase + q).then(function (results) {
                return results.data;
            });
        };
        return obj;
}]);

/**
 * Factory Method For Login Authentication
 */
app.factory("authenticationSvc", ["$http","$q","$window", "Data",function ($http, $q, $window, Data) {
    var userInfo;

    function login(userName, password) {
        var deferred = $q.defer();

        Data.post("login", { username: userName, password: password })
            .then(function(result){
                console.log(result);
                if(result.status==='success') {
                    userInfo = {
                        accessToken: result.data.access_token,
                        userName: result.data.userName
                    };
                $window.sessionStorage["userInfo"] = JSON.stringify(userInfo);
                }
                
                deferred.resolve(result);      
        }, function (error) {
                deferred.reject(error);
        });
        return deferred.promise;
    }

    function logout() {
        var deferred = $q.defer();
        Data.post("logout").then(function (result) {
            userInfo = null;
            $window.sessionStorage["userInfo"] = null;
            deferred.resolve(result);
        }, function (error) {
            deferred.reject(error);
        });

        return deferred.promise;
    }

    function getUserInfo() {
        return userInfo;
    }

    function isAuthenticated() {
        if(userInfo) {
            return true;
        } else {
            return false;
        }
    }

    function init() {
        if ($window.sessionStorage["userInfo"]) {
            userInfo = JSON.parse($window.sessionStorage["userInfo"]);
        }
    }
    init();

    return {
        login: login,
        logout: logout,
        getUserInfo: getUserInfo,
        isAuthenticated: isAuthenticated
    };
}]);