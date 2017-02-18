app.controller('authCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Users) {
    //initially set those objects to null to avoid undefined error
    $("#dateCtrl").addClass("hideForm").removeClass("showForm");
    $rootScope.title = 'Login';
    $scope.login = {};
    $scope.signup = {};
    $scope.doLogin = function (user) {
        Users.post('login', {
            user: user
        }).then(function (results) {
            Users.toast(results);
            if (results.status == "success") {
                $location.path('records');
            }
        });
    };
    $scope.signup = {email:'',password:'',name:'',username:''};
    $scope.signUp = function (user) {
        Users.post('register', {
            user: user
        }).then(function (results) {
            Users.toast(results);
            if (results.status == "success") {
                $location.path('login');
            }
        });
    };
});
