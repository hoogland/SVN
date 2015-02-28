/**
 * GENERAL
 * - All functions that are used in both sites
 */
angular.module("svnGeneric", ['ngResource', 'ngSanitize'])
    .controller('svnDataController', ['$scope', 'dataFactory',
        function ($scope, dataFactory) {
            $scope.status;
            $scope.seasons;
            $scope.competitions;

            getSeasons();
            $scope.$watch('seasonSelected', function(newValue, oldValue){
                if(newValue)
                    getCompetitions(newValue);
            });


            function getSeasons() {
                dataFactory.getSeasons()
                    .success(function (seasons) {
                        $scope.seasons = seasons;
                    })
                    .error(function (error) {
                        $scope.status = 'Unable to load seasons: ' + error.message;
                    });
            }
            function getCompetitions(season) {
                dataFactory.getCompetitions(season)
                    .success(function (competitions) {
                        $scope.competitions = competitions;
                    })
                    .error(function (error) {
                        $scope.status = 'Unable to load seasons: ' + error.message;
                    });
            }
        }
    ])

    .factory("dataFactory", ['$http', function ($http) {
        var dataFactory = {};
        var urlBase = '../archief/webservice.php';
        dataFactory.getSeasons = function () {
            return $http.post(urlBase,
                {
                    "method": "GET",
                    "action": "data",
                    "subaction": "seasons"
                })
        };
        dataFactory.getCompetitions = function (season) {
            return $http.post(urlBase,
                {
                    "method": "GET",
                    "action": "data",
                    "subaction": "competitions",
                    "data": {
                        "season": season.id,
                    }
                })
        };
        return dataFactory;
    }])

    .directive('selectSeason', function () {
        return {
            restrict: 'E',
            templateUrl: '/beheer/algemeen/selectSeason.html',
            replace: true
        };
    })
    .directive('selectCompetition', function () {
        return {
            restrict: 'E',
            templateUrl: '/beheer/algemeen/selectCompetition.html',
            replace: true
        };
    });


/**
 * BEHEER
 * - Alle functionality that is used in the Admin section
 */
angular.module("svnAdmin", ['svnGeneric','ngRoute'])

    .config(['$routeProvider',
        function ($routeProvider, $locationProvider) {
            $routeProvider
                .when('/Extern/Wedstrijd', {
                    templateUrl: 'extern/wedstrijd.html',
                    // controller: 'externalWedstrijdController'
                }
            )
                .when('/Extern/Teams', {
                    templateUrl: 'extern/teams.html',
                    // controller: 'externalTeamsController'
                }).
                otherwise({
                    redirectTo: '/Extern/Teams'
                });
        }])

    .directive('navigation', function () {
        return {
            restrict: 'E',
            templateUrl: '/beheer/algemeen/menu.html',
            replace: true
        };
    });
