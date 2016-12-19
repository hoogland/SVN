/**
 * Created by Rob on 28-9-2015.
 */
(function () {
    'use strict'

    angular.module('app')
        .controller('mainController', mainController);

    mainController.$inject = ['$scope', '$state', '$filter', '$rootScope', 'SeasonsService', 'CompetitionService', 'genericDataService'];

    function mainController($scope, $state, $filter, $rootScope, SeasonsService, CompetitionService, genericDataService) {
        var getConfig = function(){
            $scope.config = genericDataService.queryConfig();
        };
        getConfig();

        $scope.setState = function (state) {
            $scope.state = state;
        }

        $scope.getSeasons = function () {
            SeasonsService.querySeasons().$promise.then(function (value) {
                $scope.seasons = value;
                var data = $filter('filter')(value, {id: parseInt($scope.state.seasonId)})[0];
                $scope.seasonSelect = $scope.seasons[$scope.seasons.indexOf(data)];
            });
        };

        $scope.getCompetitions = function (seasonId) {
            CompetitionService.queryCompetitions({season: seasonId}).$promise.then(function (value) {
                $scope.competitions = value;
                if ($scope.state.competitionId) {
                    var data = $filter('filter')(value, {id: parseInt($scope.state.competitionId)})[0];
                    $scope.competitionSelect = $scope.competitions[$scope.competitions.indexOf(data)];
                }
            });
        };

        $scope.getByeTypes = function () {
            $scope.byeTypes = genericDataService.queryByeTypes();
        };

        $scope.getColumns = function (compType) {
            $scope.availableColumns = {};
            genericDataService.queryColumns(compType).$promise.then(function (value) {
                value.forEach(function (column) {
                    $scope.availableColumns[column.name] = column;
                });
            });
        };




        $scope.getSeasons();
        $scope.getByeTypes();
        $scope.setState($state.params);

        if ($scope.state.seasonId)
            $scope.getCompetitions($scope.state.seasonId);

        //Add watcher for seasonId
        $scope.$watch('state.seasonId', function (newValue) {
            if (newValue) {
                //Set SeasonSelect
                $scope.seasonSelect = $filter('filter')($scope.seasons, {id: parseInt(newValue)})[0];
                //Get competitions
                $scope.getCompetitions(newValue);
            }
        });
    }
})();