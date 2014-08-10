var app = angular.module("svnKNSB", []);

app.controller("knsbLists", function($scope, $http) { 
    $scope.knsbList;
    
    $http.post('dataService.php', { 'action' : "getLists"}).
    success(function(data, status, headers, config) {
        $scope.ratingLists = data;
    }).
    error(function(data, status, headers, config) {
        // log error
    });

    $scope.knsbListGet = function(knsbList) {
        this.knsbList = knsbList
        $http.post('dataService.php', {"action" : "getList", "list" : knsbList.id}).
        success(function(data, status, headers, config) {
            $scope.userRatings = data;
        }).
        error(function(data, status, headers, config) {
            // log error
        });

    } 

    $scope.knsbInsertRating = function(){
        
        angular.forEach($scope.userRatings, function(userRating){
            
            $http.post('dataService.php', {"action" : "insertRating", "date" : $scope.knsbList.value+"-01","player" : userRating.knsb,"rating": userRating.rating}).
            success(function(data, status, headers, config) {
                userRating.result = data;
            }).
            error(function(data, status, headers, config) {
                // log error
            });
        })     

    }
});

