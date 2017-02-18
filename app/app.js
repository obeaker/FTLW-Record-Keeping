var app = angular.module('myApp', ['ngRoute', 'ngAnimate', 'ui.grid', 'ui.grid.grouping', 'toaster']);

app.factory("services", ['$http', function($http) {
  var serviceBase = 'api/services/'
    var obj = {};
    obj.getRecords = function(){
        return $http.get(serviceBase + 'records');
    }

    obj.getAttendances = function(){
        return $http.get(serviceBase + 'attendances');
    }

    obj.getAllRecords = function(){
        return $http.get(serviceBase + 'allrecords');
    }

    obj.getAllUsers = function(){
        return $http.get(serviceBase + 'allusers');
    }

    obj.getRecord = function(recordID){
        return $http.get(serviceBase + 'record?id=' + recordID);
    }

    obj.getGivingFund = function(){
        return $http.get(serviceBase + 'givingfund');
    }

    obj.getStatus = function(recordDate){
        return $http.get(serviceBase + 'status?date=' + recordDate);
    }

    obj.getAttendance = function(attendanceID){
        return $http.get(serviceBase + 'attendance?id=' + attendanceID);
    }

    obj.insertRecord = function (record) {
      return $http.post(serviceBase + 'insertRecord', record).then(function (results) {
          return results;
      });
  	};

	obj.updateRecord = function (id,record) {
	    return $http.post(serviceBase + 'updateRecord', {id:id, record:record}).then(function (status) {
	        return status.data;
	    });
	};

  obj.insertAttendance = function (attendance) {
    return $http.post(serviceBase + 'insertAttendance', attendance).then(function (results) {
        return results;
    });
  };

  obj.updateAttendance = function (id,attendance) {
      return $http.post(serviceBase + 'updateAttendance', {id:id, attendance:attendance}).then(function (status) {
          return status.data;
      });
  };

  obj.insertDate = function (daterecord) {
    return $http.post(serviceBase + 'insertDate', daterecord).then(function (results) {
        return results;
    });
  };

  obj.updateDate = function (date,daterecord) {
      return $http.post(serviceBase + 'updateDate', {date:date, daterecord:daterecord}).then(function (status) {
          return status.data;
      });
  };

  obj.updateRole = function (id,attendance) {
      return $http.post(serviceBase + 'updateRole', {id:id, attendance:attendance}).then(function (status) {
          return status.data;
      });
  };

  obj.updateUser = function (id,user) {
      return $http.post(serviceBase + 'updateUser', {id:id, user:user}).then(function (status) {
          return status.data;
      });
  };

  obj.unlockUser = function (id,username) {
      return $http.post(serviceBase + 'unlockUser', {id:id, username:username}).then(function (status) {
          return status.data;
      });
  };

	obj.deleteRecord = function (id) {
	    return $http.delete(serviceBase + 'deleteRecord?id=' + id).then(function (status) {
	        return status.data;
	    });
	};

    return obj;
}]);

app.factory('dataShare',function ($rootScope){
  var service = {};
  service.data = false;
  service.sendData = function(data){
      this.data = data;
      $rootScope.$broadcast('data_shared');
  };
  service.getData = function(){
    return this.data;
  };
  return service;
});

app.factory("checkAuth", function(Users){
  return function (deferred) {
    Users.get('session').then(function (results) {
      if (results.uid) {
        deferred.resolve('complete');
      }
    })
  }
});

app.controller('dateCtrl', ['$scope','dataShare', 'Users', function ($scope, dataShare, Users) {
    var d = new Date();
    $scope.formatedDate = function (date) {
      var year = date.getFullYear();
      var month = (1 + date.getMonth()).toString();
      month = month.length > 1 ? month : '0' + month;
      var day = date.getDate().toString();
      day = day.length > 1 ? day : '0' + day;
      return month + '/' + day + '/' + year;
    }

    $scope.recordDate = $scope.formatedDate(d);

   $scope.send = function(){
     $scope.recordDate = $('#recordDate').val();
     dataShare.sendData($scope.recordDate);
   };
}]);

app.controller('listCtrl', function ($scope, services, dataShare, $window, $rootScope, $filter) {
  $("#dateCtrl, #navMenu").addClass("showForm").removeClass("hideForm");
  var d = new Date($('#recordDate').val());
  $scope.formatedDate = function (date) {
    var year = date.getFullYear();
    var month = (1 + date.getMonth()).toString();
    month = month.length > 1 ? month : '0' + month;
    var day = date.getDate().toString();
    day = day.length > 1 ? day : '0' + day;
    return year + '-' + month + '-' + day;
  }

  $scope.showModal = false;
  $scope.recordDate = $scope.formatedDate(d);

  var daterecord = {};
  daterecord.status = "Saved";
  daterecord.recordDate = $scope.recordTodayDate;

  $scope.$on('data_shared',function(){
    var text =  dataShare.getData();
    $scope.recordDate = $scope.formatedDate(new Date(text));

    services.getRecords().then(function(data){
        $scope.records = data.data;
        $scope.getTotal = function(){
          var total = 0;
          for(var i = 0; i < $scope.records.length; i++){
              var records = $scope.records[i];
              if(records.recordDate == $scope.recordDate){
                total += parseFloat(records.total);
              }
          }
          return total;
      }
    });

    services.getAttendances().then(function(data){
        $scope.attendances = data.data;
    });

    services.getStatus($scope.recordDate).then(function(data){
        $scope.saved = data.data.status;
    });

    $rootScope.title = 'Showing Record for ' + $scope.recordDate;
  });

  $scope.showAttendanceBtn = function(value, value1) {
    if (value > 0) {
      if (value1 < 1) {
        return true;
      }
      else
        return false;
    }
  };

  $scope.showSaveBtn = function(value, value1) {
    if (value > 0) {
      if (value1 != "Saved") {
        return true;
      }
      else
        return false;
    }
    else {
      return false;
    }
  };

  $scope.showModal = false;
  $scope.toggleModal = function(){
    //console.log(attendanceID);
    console.log($('#attendid').val());
      $scope.attendid = $('#attendid').val();
      $scope.showModal = !$scope.showModal;
  };

  $rootScope.title = 'Showing Record for ' + $scope.recordDate;

    services.getRecords().then(function(data){
        $scope.records = data.data;
        $scope.getTotal = function(){
          var total = 0;
          for(var i = 0; i < $scope.records.length; i++){
              var records = $scope.records[i];
              if(records.recordDate == $scope.recordDate){
                total += parseFloat(records.total);
              }
          }
          return total;
      }
    });

    services.getStatus($scope.recordDate).then(function(data){
        $scope.saved = data.data.status;
    });

    services.getAttendances().then(function(data){
        $scope.attendances = data.data;
    });
    $scope.attendid = $('#attendid').val();

    $scope.SaveYes = function() {
      $('#areYouSure').addClass("hideForm");
      $('#saveForm').addClass("showForm").removeClass("hideForm");
    };

    $scope.SaveNo = function() {
      $scope.showModal = false;
    };

    $scope.saveRecorder = function(attendance) {
      services.updateRole($scope.attendid, attendance);
      services.updateDate($scope.recordDate, daterecord);
      $scope.showModal = false;
        $window.location.reload();
    };
});

app.controller('alllistCtrl', function ($timeout, $scope, services, dataShare, uiGridConstants, uiGridGroupingConstants, $rootScope) {
  $rootScope.title = 'Showing All Records';
  $("#dateCtrl").addClass("hideForm").removeClass("showForm");
  $("#navMenu").addClass("showForm").removeClass("hideForm");
  $scope.gridOptions = {
    enableColumnMenus: false,
    enableRowSelection: false,
    enableGroupHeaderSelection: false,
    enableRowHeaderSelection: false,
    enableSorting: true,
    enableFiltering: true,
    columnDefs: [
      //{ name: 'recordNumber', width: '10%' },
      { name: 'recordName', width: '16%' },
      { name: 'cash', cellFilter: 'currency', enableFiltering: false, width: '8%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'less_expense', cellFilter: 'currency', displayName: "Expense", enableFiltering: false, width: '8%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'new_cash', cellFilter: 'currency', enableFiltering: false, width: '8%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'checks', cellFilter: 'currency', enableFiltering: false, width: '8%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'recordDate', cellFilter: 'date', type: 'date', width: '10%', sort: { priority: 0, direction: 'desc' }, cellTemplate: '<div ng-if="!col.grouping || col.grouping.groupPriority === undefined || col.grouping.groupPriority === null || ( row.groupHeader && col.grouping.groupPriority === row.treeLevel )" class="ui-grid-cell-contents" title="TOOLTIP">{{COL_FIELD CUSTOM_FILTERS}}</div>' },
      { name: 'total', cellFilter: 'currency', enableFiltering: false, width: '9%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'hundredsCt', enableFiltering: false, displayName: "$100", width: '5%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'fiftysCt', enableFiltering: false, displayName: "$50", width: '4%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'twentysCt', enableFiltering: false, displayName: "$20", width: '4%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'tensCt', enableFiltering: false, displayName: "$10", width: '4%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'fivesCt', enableFiltering: false, displayName: "$5", width: '4%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'onesCt', enableFiltering: false, displayName: "$1", width: '4%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } },
      { name: 'coinsCt', enableFiltering: false, displayName: "Coins", width: '5%', treeAggregationType: uiGridGroupingConstants.aggregation.SUM,
        customTreeAggregationFinalizerFn: function (aggregation) {
          aggregation.rendered = aggregation.value;
        } }
    ],
    onRegisterApi: function( gridApi ) {
      $timeout(function () {
        gridApi.grouping.clearGrouping();
        gridApi.grouping.groupColumn('recordDate');
        gridApi.core.notifyDataChange(uiGridConstants.dataChange.COLUMN);
      });
    }
  };

    services.getAllRecords().then(function(data){
        $scope.gridOptions.data = data.data;
    });
});

app.controller('editCtrl', function ($scope, $rootScope, $location, $routeParams, services, record, dataShare, $window, Users) {
  $("#dateCtrl, #navMenu").addClass("showForm").removeClass("hideForm");
    var d = new Date($('#recordDate').val());
    $scope.formatedDate = function (date) {
      var year = date.getFullYear();
      var month = (1 + date.getMonth()).toString();
      month = month.length > 1 ? month : '0' + month;
      var day = date.getDate().toString();
      day = day.length > 1 ? day : '0' + day;
      return year + '-' + month + '-' + day;
    }
    $scope.recordTodayDate = $scope.formatedDate(d);

    var daterecord = {};
    daterecord.status = "Not Saved";
    daterecord.recordDate = $scope.recordTodayDate;

    $scope.$on('data_shared',function(){
      var text =  dataShare.getData();
      $scope.recordTodayDate = $scope.formatedDate(new Date(text));
      daterecord.recordDate = $scope.formatedDate(new Date(text));
    });

    services.getGivingFund().then(function(data){
        $scope.names = data.data;
    });

    var recordID = ($routeParams.recordID) ? parseInt($routeParams.recordID) : 0;
    $rootScope.title = (recordID > 0) ? 'Edit Record' : 'Add Record';
    $scope.buttonText = (recordID > 0) ? 'Update Record' : 'Add New Record';
      var original = record.data;
      original._id = recordID;
      $scope.record = angular.copy(original);
      $scope.record._id = recordID;

      $scope.isClean = function() {
        return angular.equals(original, $scope.record);
      }

      $scope.hundred = 100;
      $scope.fifty = 50;
      $scope.twenty = 20;
      $scope.ten = 10;
      $scope.five = 5;
      $scope.one = 1;
      $scope.coins = "Coins";

      $scope.calculateHundred = function() {
        $scope.hundredTotal = $scope.hundred * $scope.record.hundredsCt;
      }

      $scope.getNewCash = function() {
              var a = $scope.record.cash;
              var b = $scope.record.less_expense;
              $scope.record.new_cash = a - b;
      }

      $scope.getTotalCash = function() {
              var a = $scope.record.new_cash;
              var b = $scope.record.checks;
              $scope.record.total = +a + +b;
      }

      $scope.deleteRecord = function(record) {
        $location.path('/');
        if(confirm("Are you sure to delete record number: "+$scope.record._id)==true)
        services.deleteRecord(record.recordNumber);
        var results = {
          "message":"Your have successfully deleted the record..",
          "status":"info"
        };

        Users.toast(results);
      };

      $scope.saveRecord = function(record) {
        var sumHundred = parseInt(record.hundredsCt * $scope.hundred) || 0 ;
        //var sumHundred = parseInt((record.hundredsCt === undefined) ? 0 : record.hundredsCt * $scope.hundred);
        var sumFifty = parseInt(record.fiftysCt * $scope.fifty) || 0 ;
        var sumTwenty = parseInt(record.twentysCt* $scope.twenty) || 0 ;
        var sumTen = parseInt(record.tensCt * $scope.ten) || 0 ;
        var sumFive = parseInt(record.fivesCt * $scope.five) || 0 ;
        var sumOne = parseInt(record.onesCt * $scope.one) || 0 ;
        var sumCoins = parseFloat(record.coinsCt) || 0 ;
        var sumTotal = sumHundred + sumFifty + sumTwenty + sumTen +sumFive + sumOne + sumCoins ;

         $scope.verifyTotal = function () {
           if(record.new_cash !== sumTotal)
             {return false;}
           else {
             return true;
           }
         }

        var isValid = $scope.verifyTotal();
        if (isValid === false) {
          var result = {
            'status': "error",
            'message': 'The New Cash amount does not equal the currency breakdown of $'+ record.new_cash
          }

          Users.toast(result);
          return false;
        }
        $location.path('/');
        if (recordID <= 0) {
            //console.log("record id < 0");
            record.recordName = record.recordName.replace("'","''");
            parseFloat(record.coinsCt) || 0 ;
            record.recordDate = $scope.recordTodayDate;
            services.insertRecord(record);
            services.insertDate(daterecord);
            var result = {
              'status': "info",
              'message': 'Record has been successfully entered'
            }

            Users.toast(result);
        }
        else {
            parseFloat(record.coinsCt) || 0 ;
            services.updateRecord(recordID, record);
        }
    };
});

app.controller('attendCtrl', function ($scope, $rootScope, $location, $routeParams, services, attendance, dataShare) {
  $("#dateCtrl, #navMenu").addClass("showForm").removeClass("hideForm");
    var d = new Date($('#recordDate').val());
    $scope.formatedDate = function (date) {
      var year = date.getFullYear();
      var month = (1 + date.getMonth()).toString();
      month = month.length > 1 ? month : '0' + month;
      var day = date.getDate().toString();
      day = day.length > 1 ? day : '0' + day;
      return year + '-' + month + '-' + day;
    }
    $scope.recordTodayDate = $scope.formatedDate(d);

    $scope.$on('data_shared',function(){
      var text =  dataShare.getData();
      $scope.recordTodayDate = $scope.formatedDate(new Date(text));;
    });

    var attendanceID = ($routeParams.attendanceID) ? parseInt($routeParams.attendanceID) : 0;
    $rootScope.title = (attendanceID > 0) ? 'Edit Attendance' : 'Add Attendance';
    $scope.buttonText = (attendanceID > 0) ? 'Update Attendance' : 'Add New Attendance';
      var original = attendance.data;
      original._id = attendanceID;
      $scope.attendance = angular.copy(original);
      $scope.attendance._id = attendanceID;

      $scope.isClean = function() {
        return angular.equals(original, $scope.attendance);
      }

      $scope.saveAttendance = function(attendance) {
        $location.path('/');
        if (attendanceID <= 0) {
            attendance.attendanceDate = $scope.recordTodayDate;
            attendance.total = +attendance.adults + +attendance.children;
            console.log(attendance);
            services.insertAttendance(attendance);
        }
        else {
            attendance.total = +attendance.adults + +attendance.children;
            attendance.attendanceDate = $scope.recordTodayDate;
            services.updateAttendance(attendanceID, attendance);
        }
    };
});

app.controller('logoutCtrl', function ($scope, $rootScope, $routeParams, $location, $http, Users) {
  $("#navMenu").addClass("hideForm").removeClass("showForm");
    $scope.logout = function () {
      Users.get('logout').then(function (results) {
        Users.toast(results);
        $location.path('login');
      });
    }
});

app.controller('allusersCtrl', function ($timeout, $route, $scope, services, dataShare, uiGridConstants, uiGridGroupingConstants, $rootScope) {
  $rootScope.title = 'Showing All Records';
  $scope.selected = {};

  $("#dateCtrl").addClass("hideForm").removeClass("showForm");
  $("#navMenu").addClass("showForm").removeClass("hideForm");

  $scope.getEditTmp = function (user) {
     if (user.uid === $scope.selected.uid){
      return 'edit';
     }
     else
      return 'display';
  };

  $scope.editUser = function (user) {
    $scope.selected = angular.copy(user);
  };

  $scope.reset = function () {
    $scope.selected = {};
  };

  $scope.updateUser = function (user) {
    services.updateUser(user.uid, user);
    $scope.reset();
  };

  $scope.unlockUser = function (user) {
    services.unlockUser(user.uid, user.username);
    $route.reload();
  };

  services.getAllUsers().then(function(data){
      $scope.allusers = data.data;
  });
});

app.filter('active', function(){
	return function(input){
	if(input == '1')
		return 'Yes';
	else
		return 'No';
	};
});

app.config(['$routeProvider',
  function($routeProvider) {
    $routeProvider.
      when('/records', {
        title: 'Records',
        templateUrl: 'partials/records.html',
        controller: 'listCtrl',
        resolve: ['checkAuth', '$q', function(checkAuth, $q){
          var deferred = $q.defer();
          checkAuth(deferred);
          return deferred.promise;
          }]
      })
      .when('/attendance/:attendanceID', {
        title: 'Attendance',
        templateUrl: 'partials/attendance.html',
        controller: 'attendCtrl',
        resolve: {
          attendance: function(services, $route){
            var attendanceID = $route.current.params.attendanceID;
            return services.getAttendance(attendanceID);
          }
        }
      })
      .when('/allrecords/', {
        title: 'All Records',
        templateUrl: 'partials/allrecords.html',
        controller: 'alllistCtrl',
        resolve: ['checkAuth', '$q', function(checkAuth, $q){
          var deferred = $q.defer();
          checkAuth(deferred);
          return deferred.promise;
          }]
      })
      .when('/allusers', {
        title: 'All Users',
        templateUrl: 'partials/allusers.html',
        controller: 'allusersCtrl',
        resolve: ['checkAuth', '$q', function(checkAuth, $q){
          var deferred = $q.defer();
          checkAuth(deferred);
          return deferred.promise;
          }]
      })
      .when('/edit-record/:recordID', {
        title: 'Edit Records',
        templateUrl: 'partials/edit-record.html',
        controller: 'editCtrl',
        resolve: {
          record: function(services, $route){
            var recordID = $route.current.params.recordID;
            return services.getRecord(recordID);
          }
        }
      })
      .when('/login', {
          title: 'Login',
          templateUrl: 'partials/login.html',
          controller: 'authCtrl'
      })
      .when('/logout', {
          title: 'Logout',
          templateUrl: 'partials/login.html',
          controller: 'logoutCtrl'
      })
      .when('/register', {
          title: 'Register',
          templateUrl: 'partials/register.html',
          controller: 'authCtrl'
      })
      .when('/', {
          title: 'Records',
          templateUrl: 'partials/records.html',
          controller: 'listCtrl',
          resolve: ['checkAuth', '$q', function(checkAuth, $q){
            var deferred = $q.defer();
            checkAuth(deferred);
            return deferred.promise;
            }]
      })
      .otherwise({
          redirectTo: '/login'
      });
}]);

app.run(['$location', '$rootScope','Users', function($location, $rootScope, Users) {
    $rootScope.$on("$routeChangeStart", function (event, next, current) {
            $rootScope.authenticated = false;
            Users.get('session').then(function (results) {
                if (results.uid) {
                    $rootScope.authenticated = true;
                    $rootScope.uid = results.uid;
                    $rootScope.name = results.name;
                    $rootScope.email = results.email;
                    $rootScope.role = results.role;
                    $rootScope.title = current.$$route.title;
                }
                else {
                    var nextUrl = next.$$route.originalPath;
                    if (nextUrl == '/register' || nextUrl == '/login') {

                    }
                    else {
                      var login_session_duration = 3600;
                      if((results.current_time - results.loggedin_time) > login_session_duration) {
                        var results1 = {
                          "message":"Your session has timed out. You can log back in again.",
                          "status":"info"
                        };
                        Users.toast(results1);
                      }


                        $location.path("/login");
                        $("#navMenu").addClass("hideForm").removeClass("showForm");
                    }
                }
            });
        });
}]);
