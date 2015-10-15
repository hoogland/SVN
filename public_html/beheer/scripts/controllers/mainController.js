/**
 * Created by Rob on 28-9-2015.
 */
(function(){
    'use strict'

    angular.module('app')
        .controller('mainController', mainController);

    mainController.$inject = ['$scope', '$stateParams', '$state', '$rootScope', 'SeasonsService', 'CompetitionService', 'genericDataService'];

    function mainController($scope, $stateParams, $state, $rootScope, SeasonsService, CompetitionService, genericDataService) {

        $scope.getSeasons = function(){
            $scope.seasons = SeasonsService.querySeasons();
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


        $scope.getSeasons();
        $scope.getByeTypes();


    }
})();