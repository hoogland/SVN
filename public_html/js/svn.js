var app = angular.module("SVNpublic", ['ngResource','ngSanitize',  'ui.select']);



app.controller("externCompetitie", function(match, matches, team, season, defaultData, player, $scope, $resource, $filter){
    $scope.defaultData = [];
    $scope.seasons = [];
    $scope.teams = [];
    $scope.topscorers = [];
    $scope.players = [];

    $scope.selectedTeam = [];
    $scope.selectedSeason = [];

    $scope.$location = {};

    $scope.$watch('selectedTeam', function(){
        getMatches();
    }) ;
    $scope.$watch('selectedSeason', function(){
        getMatches();
    }) ;

    //Get seasons
    season.query().success(function(data, status, headers, config) {
        $scope.seasons = data;
    }).
        error(function(data, status, headers, config) {
            // log error
        });

    function getMatches(){
        if($scope.selectedSeason.id && $scope.selectedTeam.id) {
            matches.query($scope.selectedSeason.id, $scope.selectedTeam.id).success(function (data, status, headers, config) {
                $scope.matches = data;
            }).
                error(function (data, status, headers, config) {
                    // log error
                });
           match.topscorers($scope.selectedSeason.id, $scope.selectedTeam.id).success(function (data, status, headers, config) {
                $scope.topscorers = data;
            }).
                error(function (data, status, headers, config) {
                    // log error
                });
        }
    }



    //Get Team
    team.query().success(function(data, status, headers, config) {
        $scope.teams = data;
    }).
        error(function(data, status, headers, config) {
            // log error
        });

    //Get Players
    player.query().success(function(data, status, headers, config) {
        $scope.players = data;
    }).
        error(function(data, status, headers, config) {
            // log error
        });

    defaultData.get().success(function(data, status, headers, config) {
        $scope.defaultData = data.data;
    }).
    error(function(data, status, headers, config) {
        // log error
    });

});

app.factory("match", ['$http', function($http){
    var obj = {};

    obj.query = function(season, team){
        if (season && team) {
            return $http.post('../archief/webservice.php',
                {
                    "method": "GET",
                    "action": "extern",
                    "subaction": "matches",
                    "data": {
                        "season": season,
                        "team": team,
                        "details": true
                    }
                })
        }
    };
    obj.topscorers = function(season, team){
        if (season && team) {
            return $http.post('../archief/webservice.php',
                {
                    "method": "GET",
                    "action": "extern",
                    "subaction": "topScorers",
                    "data": {
                        "season": season,
                        "team": team
                    }
                })
        }
    };

    return obj;
}]);

app.factory("matches", ['$http', function($http){
    var obj = {};

    obj.query = function(season, team){
        return $http.post('../archief/webservice.php',
            {
                "method": "GET",
                "action": "extern",
                "subaction": "matches",
                "data": {
                    "season": season,
                    "team": team,
                    "details": true
                }
            })
    };

    return obj;
}]);


app.factory("player", ['$http', function($http){
    var obj = {};

    obj.query = function(){
        return $http.post('../archief/webservice.php',
            {
                "method": "GET",
                "action": "data",
                "subaction": "players",
                "data": {
                    "details": true
                }
            })
    };

    return obj;
}]);


app.factory("season", ['$http', function($http){
    var obj = {};

    obj.query = function(){
        return  $http.post('../archief/webservice.php',
        {
            "method" : "GET", 
            "action" : "data", 
            "subaction" : "seasons"
        })
    };
    return obj;    
}]);

app.factory("defaultData", ['$http', function($http){
    var obj = {};

    obj.get = function(){
        return  $http.post('../archief/webservice.php',
        {
            "method" : "GET", 
            "action" : "data", 
            "subaction" : "defaultData"
        })
    };
    return obj;    
}]);

app.factory("team", ['$http', function($http){
    var obj = {};

    obj.query = function(){
        return  $http.post('../archief/webservice.php',
        {
            "method" : "GET", 
            "action" : "data", 
            "subaction" : "teams"
        })
    };
    return obj;    
}]);

app.directive('navigation', function() {
    return {
        restrict: 'E',
        templateUrl: 'menu.html'
    };
});
