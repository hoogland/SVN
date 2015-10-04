/**
 * Created by Rob on 28-9-2015.
 */
(function(){
    'use strict'

    angular.module('app')
        .controller('mainController', mainController);

    mainController.$inject = ['$scope', '$stateParams', '$state', '$rootScope', 'SeasonsService', 'CompetitionService'];

    function mainController($scope, $stateParams, $state, $rootScope, SeasonsService, CompetitionService) {
        var vm = this;

        $scope.getSeasons = function(){
            $scope.seasons = SeasonsService.querySeasons();
        };

        $scope.getCompetitions = function(){
            $scope.competitions = CompetitionService.queryCompetitions({season : $scope.seasonSelect.id});
        }


        $scope.getSeasons();


    }
})();