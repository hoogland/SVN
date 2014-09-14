var app = angular.module("svnKNSB", []);

app.controller("knsbLists", function($scope, $http) { 
    $scope.knsbList;
    $scope.knsbClubs;
    $scope.knsbClubProgress = 0;
    $scope.knsbClubsProgress = 0;
    $scope.knsbPlayers = 0;
    $scope.result = {"sql" : "Geen"};

    $http.post('dataService.php', { 'action' : "getLists"}).
    success(function(data, status, headers, config) {
        $scope.ratingLists = data;
    }).
    error(function(data, status, headers, config) {
        // log error
    });
    $http.post('dataService.php', { 'action' : "getClubs"}).
    success(function(data, status, headers, config) {
        $scope.knsbClubs = data;
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
    $scope.knsbClubListGet = function(club,knsbList) {

        this.knsbList = $scope.knsbList;
        $http.post('dataService.php', {"action" : "getKNSBList", "list" : $scope.knsbList.id, "club" : club}).
        success(function(data, status, headers, config) {
            $scope.userRatings = data;
            return data;
        }).
        error(function(data, status, headers, config) {
            // log error
        });        
    }

    $scope.knsbProcessExternalRatings = function(knsbList)
    {
        $scope.knsbClubsProgress = 0;

        angular.forEach($scope.knsbClubs, function(club){
            $scope.club = club;
            $http.post('dataService.php', {"action" : "getKNSBList", "list" : knsbList.id, "club" : club.id, "date": knsbList.value+"-01"}).
            success(function(data, status, headers, config) {
               // club.spelers = data;
                $scope.knsbClubProgress++;
              //  $scope.knsbInsertExternalRating(data, club.id);
                 

            })
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

    $scope.knsbInsertExternalRating = function(userRatings, club){
           
        $scope.knsbPlayers += userRatings.length;
        angular.forEach(userRatings, function(userRating){  
            $http.post('dataService.php', {"action" : "insertKNSBRating", "date" : $scope.knsbList.value+"-01","player" : userRating.knsb,"rating": userRating.rating, "club" : club, "name" : userRating.name}).
            success(function(data, status, headers, config) {
               $scope.knsbClubsProgress++; 
               $scope.postResults = data;
            }).
            error(function(data, status, headers, config) {

                // log error
                //$scope.result.sql = "headers"; 
            });
        })     
    }
})

app.factory('knsbData', function($scope, $http, $q)
{
    return{
        getKnsbClubList: function(club)
        {
            $http.post('dataService.php', {"action" : "getKNSBList", "list" : $scope.knsbList.id, "club" : club}).
            success(function(data, status, headers, config) {
                $scope.userRatings = data;
                return data;
            }).
            error(function(data, status, headers, config) {
                // log error
                return $q.reject($data);
            });            
        }

    }


});