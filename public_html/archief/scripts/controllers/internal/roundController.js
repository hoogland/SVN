/**
 * Created by Rob on 2-8-2015.
 */

(function(){
    'use strict'

    angular.module('app')
        .controller('roundController', roundController);

    roundController.$inject = ['$scope', '$stateParams', '$state', '$rootScope', 'svnCompetitionService'];

    function roundController($scope, $stateParams, $state, $rootScope, svnCompetitionService) {
        var vm = this;

        $rootScope.roundSelected = $stateParams.roundSelected;

        if($rootScope.competitionSelected && $state.is('intern.competition.round.standings'))
            getStandings($rootScope.competitionSelected, $rootScope.roundSelected);
        if($rootScope.competitionSelected && $rootScope.roundSelected && $state.is('intern.competition.round.matches'))
            getMatches($rootScope.competitionSelected, [$rootScope.roundSelected,$rootScope.roundSelected]);


        function getStandings(competitionId, round){
            svnCompetitionService.standing(competitionId, round).success(function(data, status, headers, config) {
                $scope.standings = data;
            });
        }
        function getMatches(competitionId, round){
            svnCompetitionService.matches(competitionId, round).success(function(data, status, headers, config) {
                $scope.matches = data;
            });
        }
    }

})();