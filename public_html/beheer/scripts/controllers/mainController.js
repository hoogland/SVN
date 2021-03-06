/**
 * Created by Rob on 28-9-2015.
 */
(function(){
    'use strict'

    angular.module('app')
        .controller('mainController', mainController);

    mainController.$inject = ['$scope', '$stateParams', '$state', '$rootScope', 'SeasonsService', 'CompetitionService', 'genericDataService', 'DataFactory'];

    function mainController($scope, $stateParams, $state, $rootScope, SeasonsService, CompetitionService, genericDataService, DataFactory) {

        var getConfig = function(){
            $scope.config = genericDataService.queryConfig();
        };

        $scope.getSeasons = function(){
            $scope.seasons = SeasonsService.querySeasons();
            DataFactory.seasons = $scope.seasons;
        };

        $scope.queryMembers = function(){
            $scope.members = genericDataService.queryMembers();
        };

        $scope.getCompetitions = function(){
            $scope.competitions = CompetitionService.queryCompetitions({season : $scope.seasonSelect.id});
        };

        $scope.getByeTypes = function(){
            $scope.byeTypes = genericDataService.queryByeTypes();
        };

        $scope.getColumns = function(compType){
            $scope.availableColumns = genericDataService.queryColumns(compType);
        };


        getConfig();
        $scope.getSeasons();
        $scope.getByeTypes();


    }
})();